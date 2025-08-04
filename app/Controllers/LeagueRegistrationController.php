<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\QrCodeSettingModel;
use App\Models\TrialcitiesModel;
use App\Models\LeaguePlayerModel;
use App\Models\OtpSettingModel;
use App\Models\OtpVerificationModel;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;

class LeagueRegistrationController extends BaseController
{
    public function index()
    {
        $model = new TrialcitiesModel();
        $data['trial_cities'] = $model->where('status', 'enabled')->findAll();
        return view('frontend/league/registration', $data);
    }

    public function register()
    {
        $validation = \Config\Services::validation();

        $validation->setRules([
            'name' => 'required|min_length[3]',
            'email' => 'required|valid_email',
            'mobile' => 'required|min_length[10]|max_length[10]',
            'age' => 'required|integer|greater_than[7]',
            'cricketer_type' => 'required',
            'age_group' => 'required',
            'state' => 'required',
            'city' => 'required',

            'aadhar_document' => 'uploaded[aadhar_document]|max_size[aadhar_document,5120]|ext_in[aadhar_document,pdf,jpg,jpeg,png]',
            'marksheet_document' => 'uploaded[marksheet_document]|max_size[marksheet_document,5120]|ext_in[marksheet_document,pdf]',
            'dob_proof' => 'uploaded[dob_proof]|max_size[dob_proof,5120]|ext_in[dob_proof,pdf,jpg,jpeg,png]',
            'photo' => 'uploaded[photo]|max_size[photo,2048]|ext_in[photo,jpg,jpeg,png]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Check OTP settings
        $otpSettingModel = new OtpSettingModel();
        $otpSettings = $otpSettingModel->getSettings();

        // Handle file uploads
        $uploadPath = WRITEPATH . 'uploads/league_documents/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $uploadedFiles = [];
        $fileFields = ['aadhar_document', 'marksheet_document', 'dob_proof', 'photo'];

        foreach ($fileFields as $field) {
            $file = $this->request->getFile($field);
            if ($file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move($uploadPath, $newName);
                $uploadedFiles[$field] = $newName;
            }
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'mobile' => $this->request->getPost('mobile'),
            'age' => $this->request->getPost('age'),
            'cricketer_type' => $this->request->getPost('cricketer_type'),
            'age_group' => $this->request->getPost('age_group'),
            'state' => $this->request->getPost('state'),
            'city' => $this->request->getPost('city'),

            'aadhar_document' => $uploadedFiles['aadhar_document'] ?? null,
            'marksheet_document' => $uploadedFiles['marksheet_document'] ?? null,
            'dob_proof' => $uploadedFiles['dob_proof'] ?? null,
            'photo' => $uploadedFiles['photo'] ?? null,
            'payment_status' => 'unpaid'
        ];

        $model = new LeaguePlayerModel();

        // Check for existing player with same phone or email
        $existingPlayer = $model->where('mobile', $data['mobile'])
                               ->orWhere('email', $data['email'])
                               ->first();

        if ($existingPlayer) {
            return redirect()->back()->withInput()->with('error', 'A player with this mobile number or email already exists.');
        }

        // Check if OTP verification is enabled for league registration
        if ($otpSettings['league_otp_enabled']) {
            // Store uploaded files temporarily and generate OTP
            $tempData = $data;
            $tempData['uploaded_files'] = $uploadedFiles;
            
            $otpModel = new OtpVerificationModel();
            $otp = $otpModel->generateOTP($data['email'], 'league', $tempData);

            // Send OTP email
            if ($this->sendOTPEmail($data['email'], $data['name'], $otp, 'league')) {
                session()->setTempdata('league_registration_email', $data['email'], 300); // 5 minutes
                return redirect()->to('/league-otp-verification')->with('success', 'OTP has been sent to your email. Please verify to complete registration.');
            } else {
                // Clean up uploaded files if email fails
                foreach ($uploadedFiles as $file) {
                    if (file_exists($uploadPath . $file)) {
                        unlink($uploadPath . $file);
                    }
                }
                return redirect()->back()->withInput()->with('error', 'Failed to send OTP email. Please try again.');
            }
        } else {
            // Direct registration without OTP
            $playerId = $model->insert($data);
            if ($playerId === false) {
                return redirect()->back()->with('error', 'Registration failed. Please try again.');
            } else {
                return redirect()->to('/league-registration')->with('success', 'Registration successful!');
            }
        }
    }

    public function adminIndex()
    {
        $model = new LeaguePlayerModel();
        $trialCitiesModel = new TrialcitiesModel();
        $gradeModel = new \App\Models\GradeModel();
        $gradeAssignModel = new \App\Models\GradeAssignModel();

        // Get search filters
        $phone = $this->request->getGet('phone');
        $paymentStatus = $this->request->getGet('payment_status');
        $trialCity = $this->request->getGet('trial_city');
        $ageGroup = $this->request->getGet('age_group');

        // Build query with grade assignment
        $builder = $model->select('league_players.*, ga.grade_id as assigned_grade_id')
                        ->join('grade_assign ga', 'ga.player_id = league_players.id', 'left');

        if ($phone) {
            $builder->like('league_players.mobile', $phone);
        }

        if ($paymentStatus && $paymentStatus !== 'all') {
            $builder->where('league_players.payment_status', $paymentStatus);
        }

        if ($ageGroup) {
            $builder->where('league_players.age_group', $ageGroup);
        }

        $data['registrations'] = $builder->orderBy('league_players.id', 'DESC')->paginate(10);
        $data['pager'] = $model->pager;
        $data['trial_cities'] = $trialCitiesModel->where('status', 'enabled')->findAll();
        $data['grades'] = $gradeModel->where('status', 'active')->findAll();

        // Pass filter values to view
        $data['phone'] = $phone;
        $data['payment_status'] = $paymentStatus;
        $data['trial_city'] = $trialCity;
        $data['age_group'] = $ageGroup;

        return view('admin/league/registration', $data);
    }

    public function updatePaymentStatus()
    {
        $this->response->setHeader('Content-Type', 'application/json');

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }

        try {
            $data = $this->request->getJSON(true);

            if (empty($data['id']) || empty($data['payment_status'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Missing required data'
                ]);
            }

            $validStatuses = ['unpaid', 'paid'];
            $model = new LeaguePlayerModel();
            $player = $model->find($data['id']);

            if (!$player) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Player not found'
                ]);
            }

            $validStatuses = ['unpaid', 'paid'];
            if (!in_array($data['payment_status'], $validStatuses)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid payment status. Valid statuses are: unpaid, paid'
                ]);
            }

            $updateData = [
                'payment_status' => $data['payment_status'],
                'verified_at' => date('Y-m-d H:i:s')
            ];

            $update = $model->update($data['id'], $updateData);

            if ($update) {
                // Send email if status changed to paid
                if ($data['payment_status'] === 'paid') {
                    $this->sendPaymentConfirmationEmail($player['email'], $player['name']);
                }

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Payment status updated successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to update payment status'
                ]);
            }
        } catch (Exception $e) {
            log_message('error', 'League payment status update error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while updating payment status'
            ]);
        }
    }

    public function viewDocument($playerId, $documentType)
    {
        $model = new LeaguePlayerModel();
        $player = $model->find($playerId);

        if (!$player || !isset($player[$documentType])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Document not found');
        }

        $filePath = WRITEPATH . 'uploads/league_documents/' . $player[$documentType];

        if (!file_exists($filePath)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('File not found');
        }

        $mimeType = mime_content_type($filePath);

        return $this->response
            ->setHeader('Content-Type', $mimeType)
            ->setHeader('Content-Disposition', 'inline; filename="' . $player[$documentType] . '"')
            ->setBody(file_get_contents($filePath));
    }

    public function deletePlayer()
    {
        $this->response->setHeader('Content-Type', 'application/json');

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }

        try {
            $data = $this->request->getJSON(true);

            if (empty($data['player_id'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Player ID is required'
                ]);
            }

            $model = new LeaguePlayerModel();
            $player = $model->find($data['player_id']);

            if (!$player) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Player not found'
                ]);
            }

            // Delete associated documents
            $uploadPath = WRITEPATH . 'uploads/league_documents/';
            $documentFields = ['aadhar_document', 'marksheet_document', 'dob_proof', 'photo'];

            foreach ($documentFields as $field) {
                if (!empty($player[$field]) && file_exists($uploadPath . $player[$field])) {
                    unlink($uploadPath . $player[$field]);
                }
            }

            if ($model->delete($data['player_id'])) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Player deleted successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to delete player'
                ]);
            }
        } catch (Exception $e) {
            log_message('error', 'Delete league player error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while deleting player'
            ]);
        }
    }

    public function updateStatus()
    {
        $this->response->setHeader('Content-Type', 'application/json');

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }

        try {
            $data = $this->request->getJSON(true);

            if (empty($data['player_id']) || !isset($data['status'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Missing required data'
                ]);
            }

            $validStatuses = ['not_selected', 'selected'];
            if (!in_array($data['status'], $validStatuses)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid status'
                ]);
            }

            $model = new LeaguePlayerModel();
            $updateData = [
                'status' => $data['status']
            ];

            $update = $model->update($data['player_id'], $updateData);

            if ($update) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Status updated successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to update status'
                ]);
            }
        } catch (Exception $e) {
            log_message('error', 'League status update error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while updating status'
            ]);
        }
    }

    public function bulkUpdateStatus()
    {
        $this->response->setHeader('Content-Type', 'application/json');

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }

        try {
            $data = $this->request->getJSON(true);

            if (empty($data['player_ids']) || !isset($data['status']) || !is_array($data['player_ids'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Missing required data'
                ]);
            }

            $validStatuses = ['not_selected', 'selected'];
            if (!in_array($data['status'], $validStatuses)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid status'
                ]);
            }

            $model = new LeaguePlayerModel();
            $updatedCount = 0;

            foreach ($data['player_ids'] as $playerId) {
                $updateData = [
                    'status' => $data['status']
                ];

                if ($model->update($playerId, $updateData)) {
                    $updatedCount++;
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => "Successfully updated {$updatedCount} player(s) status",
                'updated_count' => $updatedCount
            ]);
        } catch (Exception $e) {
            log_message('error', 'Bulk status update error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while updating status'
            ]);
        }
    }

    public function updateGrade()
    {
        $this->response->setHeader('Content-Type', 'application/json');

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }

        try {
            $data = $this->request->getJSON(true);

            if (empty($data['player_id']) || empty($data['grade_id'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Missing required data'
                ]);
            }

            $gradeAssignModel = new \App\Models\GradeAssignModel();
            $playerId = $data['player_id'];
            $gradeId = $data['grade_id'];

            // Check if grade assignment already exists
            $existingAssignment = $gradeAssignModel->where('player_id', $playerId)->first();

            if ($existingAssignment) {
                // Update existing assignment
                $update = $gradeAssignModel->update($existingAssignment['id'], [
                    'grade_id' => $gradeId,
                    'assigned_at' => date('Y-m-d H:i:s'),
                    'assigned_by' => session()->get('user_id') ?? 'admin',
                    'status' => 'active'
                ]);
            } else {
                // Create new assignment
                $update = $gradeAssignModel->insert([
                    'player_id' => $playerId,
                    'grade_id' => $gradeId,
                    'assigned_at' => date('Y-m-d H:i:s'),
                    'assigned_by' => session()->get('user_id') ?? 'admin',
                    'status' => 'active'
                ]);
            }

            if ($update) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Grade updated successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to update grade'
                ]);
            }
        } catch (Exception $e) {
            log_message('error', 'League grade update error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while updating grade'
            ]);
        }
    }

    public function bulkUpdatePaymentStatus()
    {
        $this->response->setHeader('Content-Type', 'application/json');

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }

        try {
            $data = $this->request->getJSON(true);

            if (empty($data['player_ids']) || !isset($data['payment_status']) || !is_array($data['player_ids'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Missing required data'
                ]);
            }

            $validStatuses = ['unpaid', 'paid'];
            if (!in_array($data['payment_status'], $validStatuses)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid payment status'
                ]);
            }

            $model = new LeaguePlayerModel();
            $updatedCount = 0;

            foreach ($data['player_ids'] as $playerId) {
                $updateData = [
                    'payment_status' => $data['payment_status'],
                    'verified_at' => date('Y-m-d H:i:s')
                ];

                if ($model->update($playerId, $updateData)) {
                    $updatedCount++;

                    // Send email if status changed to paid
                    if ($data['payment_status'] === 'paid') {
                        $player = $model->find($playerId);
                        if ($player) {
                            $this->sendPaymentConfirmationEmail($player['email'], $player['name']);
                        }
                    }
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => "Successfully updated {$updatedCount} player(s) payment status",
                'updated_count' => $updatedCount
            ]);
        } catch (Exception $e) {
            log_message('error', 'Bulk payment status update error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while updating payment status'
            ]);
        }
    }

    public function bulkUpdateGrade()
    {
        $this->response->setHeader('Content-Type', 'application/json');

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }

        try {
            $data = $this->request->getJSON(true);

            if (empty($data['player_ids']) || empty($data['grade_id']) || !is_array($data['player_ids'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Missing required data'
                ]);
            }

            $gradeAssignModel = new \App\Models\GradeAssignModel();
            $gradeId = $data['grade_id'];
            $updatedCount = 0;

            foreach ($data['player_ids'] as $playerId) {
                // Check if grade assignment already exists
                $existingAssignment = $gradeAssignModel->where('player_id', $playerId)->first();

                if ($existingAssignment) {
                    // Update existing assignment
                    $update = $gradeAssignModel->update($existingAssignment['id'], [
                        'grade_id' => $gradeId,
                        'assigned_at' => date('Y-m-d H:i:s'),
                        'assigned_by' => session()->get('user_id') ?? 'admin',
                        'status' => 'active'
                    ]);
                } else {
                    // Create new assignment
                    $update = $gradeAssignModel->insert([
                        'player_id' => $playerId,
                        'grade_id' => $gradeId,
                        'assigned_at' => date('Y-m-d H:i:s'),
                        'assigned_by' => session()->get('user_id') ?? 'admin',
                        'status' => 'active'
                    ]);
                }

                if ($update) {
                    $updatedCount++;
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => "Successfully assigned grade to {$updatedCount} player(s)",
                'updated_count' => $updatedCount
            ]);
        } catch (Exception $e) {
            log_message('error', 'Bulk grade update error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while updating grades'
            ]);
        }
    }

    private function autoAssignGrade($playerId, $playerData)
    {
        $gradeModel = new \App\Models\GradeModel();
        $gradeAssignModel = new \App\Models\GradeAssignModel();

        // Get available grades based on age group
        $grades = $gradeModel->where('status', 'active')->findAll();

        if (!empty($grades)) {
            // Auto assign first available grade (you can modify this logic)
            $gradeId = $grades[0]['id'];

            $assignData = [
                'grade_id' => $gradeId,
                'player_id' => $playerId,
                'assigned_date' => date('Y-m-d H:i:s'),
                'assigned_by' => 'auto_system'
            ];

            $gradeAssignModel->insert($assignData);
        }
    }

    public function checkStatus()
    {
        return view('frontend/league/check_status');
    }

    public function getStatus()
    {
        $mobile = $this->request->getPost('mobile');

        if (!$mobile) {
            return redirect()->back()->with('error', 'Mobile number is required.');
        }

        $model = new LeaguePlayerModel();
        $gradeAssignModel = new \App\Models\GradeAssignModel();
        $gradeModel = new \App\Models\GradeModel();

        $player = $model->where('mobile', $mobile)->first();

        if (!$player) {
            return redirect()->back()->with('error', 'No registration found with mobile number ' . $mobile . '. Please check the number or register for the league first.');
        }

        // Get assigned grade if exists
        $gradeAssignModel = new \App\Models\GradeAssignModel();
        $gradeModel = new \App\Models\GradeModel();

        // Check for grade assignment using player_id (for league players)
        $gradeAssignment = $gradeAssignModel->where('player_id', $player['id'])
                                           ->where('status', 'active')
                                           ->first();

        $data['player'] = $player;
        $data['grade'] = null;

        if ($gradeAssignment) {
            $data['grade'] = $gradeModel->find($gradeAssignment['grade_id']);
        }

        // Add random motivation lines
        $motivationLines = [
            "Your cricket journey starts here! Keep practicing and stay dedicated!",
            "Champions are made through consistent effort. Keep pushing forward!",
            "Every great cricketer started with a dream. Make yours a reality!",
            "Success in cricket comes to those who never give up. Stay strong!",
            "Your potential is unlimited. Keep working hard and believe in yourself!",
            "Cricket is not just a game, it's a passion. Follow yours!",
            "The field is waiting for your talent. Show them what you've got!",
            "Practice makes perfect. Keep honing your skills every day!",
            "Great cricketers are made, not born. You're on the right path!",
            "Your dedication today will be your success tomorrow!"
        ];

        $data['motivation'] = $motivationLines[array_rand($motivationLines)];

        return view('frontend/league/status_result', $data);
    }

    private function sendPaymentConfirmationEmail($email, $playerName)
    {
        $emailService = \Config\Services::email();

        $emailService->setFrom('noreply@megastarpremiercricketleague.com', 'MegaStar Premier Cricket League');
        $emailService->setTo($email);
        $emailService->setSubject('Payment Confirmation - MPCL League');

        $message = "
        <h2>Payment Confirmation</h2>
        <p>Dear {$playerName},</p>
        <p>We are pleased to inform you that your payment has been successfully received for the MegaStar Premier Cricket League.</p>
        <p><strong>Status:</strong> You have paid all the fees. Your match will be scheduled soon and we will inform you.</p>
        <p>Thank you for joining MPCL!</p>
        <p>Best regards,<br>MPCL Team</p>
        ";

        $emailService->setMessage($message);
        $emailService->setMailType('html');

        try {
            $emailService->send();
        } catch (Exception $e) {
            log_message('error', 'Failed to send payment confirmation email: ' . $e->getMessage());
        }
    }

    public function exportPDF()
    {
        $model = new LeaguePlayerModel();
        $gradeModel = new \App\Models\GradeModel();

        // Get search filters
        $phone = $this->request->getGet('phone');
        $paymentStatus = $this->request->getGet('payment_status');
        $ageGroup = $this->request->getGet('age_group');

        // Build query with grade assignment
        $builder = $model->select('league_players.*, ga.grade_id as assigned_grade_id, g.title as grade_title')
                        ->join('grade_assign ga', 'ga.player_id = league_players.id', 'left')
                        ->join('grades g', 'g.id = ga.grade_id', 'left');

        if ($phone) {
            $builder->like('league_players.mobile', $phone);
        }

        if ($paymentStatus && $paymentStatus !== 'all') {
            $builder->where('league_players.payment_status', $paymentStatus);
        }

        if ($ageGroup) {
            $builder->where('league_players.age_group', $ageGroup);
        }

        $registrations = $builder->orderBy('league_players.id', 'DESC')->findAll();

        // Generate PDF content
        $html = $this->generateLeaguePDFContent($registrations);

        // Set headers for PDF download
        $this->response->setHeader('Content-Type', 'application/pdf');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="league-registrations-' . date('Y-m-d') . '.pdf"');

        // Use TCPDF or simple HTML to PDF conversion
        return $this->generatePDFFromHTML($html);
    }

    private function generateLeaguePDFContent($registrations)
    {
        $html = '
        <!DOCTYPE html>
        <html>
        <head>
            <style>
                body { font-family: Arial, sans-serif; font-size: 12px; }
                table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; font-weight: bold; }
                .header { text-align: center; margin-bottom: 30px; }
                .stats { margin-bottom: 20px; }
                .badge { padding: 2px 6px; border-radius: 3px; font-size: 10px; }
                .badge-paid { background-color: #d4edda; color: #155724; }
                .badge-unpaid { background-color: #f8d7da; color: #721c24; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>MegaStar Premier Cricket League</h1>
                <h2>League Player Registrations Report</h2>
                <p>Generated on: ' . date('F j, Y, g:i a') . '</p>
            </div>
            
            <div class="stats">
                <p><strong>Total Registrations:</strong> ' . count($registrations) . '</p>
                <p><strong>Export Date:</strong> ' . date('Y-m-d H:i:s') . '</p>
            </div>

            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th>Age</th>
                        <th>Cricketer Type</th>
                        <th>Age Group</th>
                        <th>Payment Status</th>
                        <th>Assigned Grade</th>
                        <th>Registered On</th>
                    </tr>
                </thead>
                <tbody>';

        $i = 1;
        foreach ($registrations as $reg) {
            $paymentBadge = $reg['payment_status'] === 'paid' ? 'badge-paid' : 'badge-unpaid';
            $paymentText = $reg['payment_status'] === 'paid' ? 'Paid' : 'Unpaid';
            
            $html .= '
                    <tr>
                        <td>' . $i++ . '</td>
                        <td>' . esc($reg['name']) . '</td>
                        <td>' . esc($reg['mobile']) . '</td>
                        <td>' . esc($reg['email']) . '</td>
                        <td>' . esc($reg['age']) . ' years</td>
                        <td>' . esc($reg['cricketer_type']) . '</td>
                        <td>' . esc(str_replace('_', ' ', ucfirst($reg['age_group']))) . '</td>
                        <td><span class="badge ' . $paymentBadge . '">' . $paymentText . '</span></td>
                        <td>' . esc($reg['grade_title'] ?? 'Not Assigned') . '</td>
                        <td>' . date('d M Y', strtotime($reg['created_at'])) . '</td>
                    </tr>';
        }

        $html .= '
                </tbody>
            </table>
        </body>
        </html>';

        return $html;
    }

    private function generatePDFFromHTML($html)
    {
        // Using DomPDF library for PDF generation
        require_once APPPATH . '../vendor/autoload.php';
        
        try {
            $dompdf = new \Dompdf\Dompdf();
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
            
            return $dompdf->output();
        } catch (Exception $e) {
            // Fallback: return HTML content with PDF headers
            return $html;
        }
    }

    public function otpVerification()
    {
        $email = session()->getTempdata('league_registration_email');
        if (!$email) {
            return redirect()->to('/league-registration')->with('error', 'OTP verification session expired. Please register again.');
        }
        
        $data['email'] = $email;
        return view('frontend/league/otp_verification', $data);
    }

    public function verifyOTP()
    {
        $email = session()->getTempdata('league_registration_email');
        $otp = $this->request->getPost('otp');

        if (!$email || !$otp) {
            return redirect()->back()->with('error', 'Invalid OTP verification request.');
        }

        $otpModel = new OtpVerificationModel();
        $registrationData = $otpModel->verifyOTP($email, $otp, 'league');

        if ($registrationData) {
            // OTP verified, complete registration
            $model = new LeaguePlayerModel();
            
            // Extract uploaded files data
            $uploadedFiles = $registrationData['uploaded_files'] ?? [];
            unset($registrationData['uploaded_files']);
            
            $playerId = $model->insert($registrationData);
            if ($playerId !== false) {
                session()->removeTempdata('league_registration_email');
                return redirect()->to('/league-registration')->with('success', 'Email verified and registration completed successfully!');
            } else {
                return redirect()->back()->with('error', 'Failed to complete registration. Please try again.');
            }
        } else {
            return redirect()->back()->with('error', 'Invalid or expired OTP. Please try again.');
        }
    }

    public function resendOTP()
    {
        $this->response->setHeader('Content-Type', 'application/json');

        $email = session()->getTempdata('league_registration_email');
        if (!$email) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'OTP verification session expired.'
            ]);
        }

        // Get registration data from existing OTP record
        $otpModel = new OtpVerificationModel();
        $existingRecord = $otpModel->where('email', $email)
                                  ->where('registration_type', 'league')
                                  ->orderBy('created_at', 'DESC')
                                  ->first();

        if ($existingRecord) {
            $registrationData = json_decode($existingRecord['registration_data'], true);
            $otp = $otpModel->generateOTP($email, 'league', $registrationData);

            if ($this->sendOTPEmail($email, $registrationData['name'], $otp, 'league')) {
                session()->setTempdata('league_registration_email', $email, 300); // Extend session
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'OTP has been resent to your email.'
                ]);
            }
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to resend OTP. Please try again.'
        ]);
    }

    private function sendOTPEmail($email, $name, $otp, $type)
    {
        helper('email');
        
        $subject = "OTP Verification - MPCL " . ucfirst($type) . " Registration";
        $message = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;'>
            <div style='text-align: center; margin-bottom: 30px;'>
                <img src='https://megastarpremiercricketleague.com/registration/mccl/images/logo.png' alt='MPCL Logo' style='height: 60px;'>
                <h2 style='color: #ff6b35; margin: 10px 0;'>Email Verification</h2>
            </div>
            
            <div style='background-color: #f8f9fa; padding: 20px; border-radius: 5px; margin-bottom: 20px;'>
                <h3 style='color: #333; margin-top: 0;'>Hello {$name},</h3>
                <p style='color: #666; line-height: 1.6;'>
                    Thank you for registering for the MegaStar Premier Cricket League " . ucfirst($type) . ".
                    To complete your registration, please verify your email address using the OTP below:
                </p>
            </div>
            
            <div style='text-align: center; margin: 30px 0;'>
                <div style='background-color: #ff6b35; color: white; padding: 15px 30px; border-radius: 5px; display: inline-block; font-size: 24px; font-weight: bold; letter-spacing: 3px;'>
                    {$otp}
                </div>
            </div>
            
            <div style='background-color: #fff3cd; border: 1px solid #ffeaa7; border-radius: 5px; padding: 15px; margin: 20px 0;'>
                <p style='color: #856404; margin: 0; font-size: 14px;'>
                    <strong>Important:</strong> This OTP will expire in 10 minutes. Do not share this code with anyone.
                </p>
            </div>
            
            <div style='text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;'>
                <p style='color: #999; font-size: 12px; margin: 0;'>
                    Best regards,<br>
                    MegaStar Premier Cricket League Team<br>
                    <a href='https://megastarpremiercricketleague.com' style='color: #ff6b35;'>www.megastarpremiercricketleague.com</a>
                </p>
            </div>
        </div>
        ";

        return sendCustomMail($email, $subject, $message);
    }
}