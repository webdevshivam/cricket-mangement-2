<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\QrCodeSettingModel;
use App\Models\TrialcitiesModel;
use App\Models\LeaguePlayerModel;
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

        $playerId = $model->insert($data);
        if ($playerId === false) {
            return redirect()->back()->with('error', 'Registration failed. Please try again.');
        } else {
            // Automatic grade assignment
            $this->autoAssignGrade($playerId, $data);

            return redirect()->to('/league-registration')->with('success', 'Registration successful!');
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

        $data['registrations'] = $builder->orderBy('league_players.id', 'DESC')->paginate(20);
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
            return redirect()->back()->with('error', 'No player found with this mobile number.');
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
}