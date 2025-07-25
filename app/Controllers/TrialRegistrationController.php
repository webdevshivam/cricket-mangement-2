<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\QrCodeSettingModel;
use App\Models\TrialcitiesModel;
use App\Models\TrialPlayerModel;
use App\Models\OtpSettingModel;
use App\Models\OtpVerificationModel;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;

class TrialRegistrationController extends BaseController
{
    public function index()
    {
        // Load the view for trial registration
        //all trial city_name

        // Set language
        $lang = $this->request->getGet('lang') ?? 'en';

        // Set the locale properly
        $languageService = \Config\Services::language();
        $languageService->setLocale($lang);

        // Also set session language
        session()->set('language', $lang);

        $model = new TrialcitiesModel();
        $qrCodeSetting = new QrCodeSettingModel();
        $data['qr_code_setting'] = $qrCodeSetting->first();
        $data['trial_cities'] = $model->where('status', 'enabled')->findAll();
        return view('frontend/trial/registration', $data);
    }
    public function register()
    {
        // Check OTP settings
        $otpSettingModel = new OtpSettingModel();
        $otpSettings = $otpSettingModel->getSettings();

        // Get form data
        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'mobile' => $this->request->getPost('phone'),
            'age' => $this->request->getPost('age'),
            'state_id' => $this->request->getPost('state'),
            'city' => $this->request->getPost('city'),
            'trial_city_id' => $this->request->getPost('trialCity'),
            'cricket_type' => $this->request->getPost('cricket_type'),
        ];

        // Validate required fields
        $validation = \Config\Services::validation();
        $validation->setRules([
            'name' => 'required|min_length[3]',
            'email' => 'required|valid_email',
            'phone' => 'required|min_length[10]|max_length[15]',
            'age' => 'required|integer|greater_than[7]',
            'cricket_type' => 'required',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Check if OTP verification is enabled for trial registration
        if ($otpSettings['trial_otp_enabled']) {
            // Generate and send OTP
            $otpModel = new OtpVerificationModel();
            $otp = $otpModel->generateOTP($data['email'], 'trial', $data);

            // Send OTP email
            if ($this->sendOTPEmail($data['email'], $data['name'], $otp, 'trial')) {
                session()->setTempdata('trial_registration_email', $data['email'], 300); // 5 minutes
                return redirect()->to('/trial-otp-verification')->with('success', 'OTP has been sent to your email. Please verify to complete registration.');
            } else {
                return redirect()->back()->withInput()->with('error', 'Failed to send OTP email. Please try again.');
            }
        } else {
            // Direct registration without OTP
            $model = new TrialPlayerModel();
            if ($model->insert($data) === false) {
                return redirect()->back()->withInput()->with('error', 'Registration failed. Please try again.');
            } else {
                return redirect()->to('/trial-registration')->with('success', 'Registration successful!');
            }
        }
    }

    public function adminIndex()
    {
        $model = new \App\Models\TrialPlayerModel();
        $trialCitiesModel = new \App\Models\TrialcitiesModel();

        // Get search filters
        $phone = $this->request->getGet('phone');
        $paymentStatus = $this->request->getGet('payment_status');
        $trialCity = $this->request->getGet('trial_city');

        // Build query with joins
        $builder = $model->select('trial_players.*, trial_cities.city_name as trial_city_name')
            ->join('trial_cities', 'trial_cities.id = trial_players.trial_city_id', 'left');

        if ($phone) {
            $builder->like('trial_players.mobile', $phone);
        }

        if ($paymentStatus && $paymentStatus !== 'all') {
            $builder->where('trial_players.payment_status', $paymentStatus);
        }

        if ($trialCity) {
            $builder->where('trial_players.trial_city_id', $trialCity);
        }

        $data['registrations'] = $builder->orderBy('trial_players.id', 'DESC')->paginate(10);
        $data['pager'] = $model->pager;
        $data['trial_cities'] = $trialCitiesModel->where('status', 'enabled')->findAll();

        // Pass filter values to view
        $data['phone'] = $phone;
        $data['payment_status'] = $paymentStatus;
        $data['trial_city'] = $trialCity;

        return view('admin/trial/registration', $data);
    }

    public function updatePaymentStatus()
    {
        // Set proper JSON response header
        $this->response->setHeader('Content-Type', 'application/json');

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }

        try {
            $data = $this->request->getJSON(true);

            // Validate input data
            if (empty($data['id']) || empty($data['payment_status'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Missing required data'
                ]);
            }

            $model = new \App\Models\TrialPlayerModel();
            $player = $model->find($data['id']);

            if (!$player) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Player not found'
                ]);
            }

            // Validate payment status
            $validStatuses = ['no_payment', 'partial', 'full'];
            if (!in_array($data['payment_status'], $validStatuses)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid payment status'
                ]);
            }

            $updateData = [
                'payment_status' => $data['payment_status'],
                'verified_at' => date('Y-m-d H:i:s')
            ];

            $update = $model->update($data['id'], $updateData);

            if ($update) {
                // Try to send email notification (optional)
                try {
                    if (function_exists('sendCustomMail')) {
                        helper('custom_mail');

                        // Build email content based on payment status
                        $statusMessages = [
                            'no_payment' => 'Your payment is pending verification. Please bring payment proof to the trial.',
                            'partial' => 'Your partial payment (₹199) has been verified. You will receive a T-shirt. Please complete full payment for trial participation.',
                            'full' => 'Your full payment has been verified! You are all set for the trial and will receive a T-shirt.'
                        ];

                        $subject = "Payment Status Update - MPCL Trial";
                        $message = "
                            <p>Hello <strong>{$player['name']}</strong>,</p>
                            <p>{$statusMessages[$data['payment_status']]}</p>
                            <p>Phone: {$player['mobile']}</p>
                            <p>Status: " . ucfirst(str_replace('_', ' ', $data['payment_status'])) . "</p>
                            <br><p>Regards,<br>MegaStar Premier Cricket League Team</p>
                        ";

                        sendCustomMail($player['email'], $subject, $message);
                    }
                } catch (Exception $e) {
                    // Email failed but update was successful - log but don't fail
                    log_message('error', 'Email notification failed: ' . $e->getMessage());
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
            log_message('error', 'Payment status update error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while updating payment status'
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

            if (empty($data['student_ids']) || empty($data['payment_status'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Missing required data'
                ]);
            }

            // Validate payment status
            $validStatuses = ['no_payment', 'partial', 'full'];
            if (!in_array($data['payment_status'], $validStatuses)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid payment status'
                ]);
            }

            $model = new \App\Models\TrialPlayerModel();
            $successCount = 0;

            foreach ($data['student_ids'] as $studentId) {
                $updateData = [
                    'payment_status' => $data['payment_status'],
                    'verified_at' => date('Y-m-d H:i:s')
                ];

                if ($model->update($studentId, $updateData)) {
                    $successCount++;
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => "Successfully updated {$successCount} students"
            ]);
        } catch (Exception $e) {
            log_message('error', 'Bulk payment status update error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while updating payment status'
            ]);
        }
    }

    public function collectPayment()
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

            if (empty($data['student_id']) || empty($data['amount']) || empty($data['payment_status'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Missing required data'
                ]);
            }

            $model = new \App\Models\TrialPlayerModel();
            $student = $model->find($data['student_id']);

            if (!$student) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Student not found'
                ]);
            }

            // Update payment status and add payment record
            $updateData = [
                'payment_status' => $data['payment_status'],
                'verified_at' => date('Y-m-d H:i:s')
            ];

            // You might want to create a separate payments table to track payment history
            // For now, we'll just update the status

            $update = $model->update($data['student_id'], $updateData);

            if ($update) {
                // Try to send email notification
                try {
                    if (function_exists('sendCustomMail')) {
                        helper('custom_mail');

                        $subject = "Payment Received - MPCL Trial";
                        $message = "
                            <p>Hello <strong>{$student['name']}</strong>,</p>
                            <p>We have received your payment of ₹{$data['amount']} via {$data['payment_method']}.</p>
                            <p>Payment Status: " . ucfirst($data['payment_status']) . "</p>
                            " . (isset($data['transaction_ref']) && $data['transaction_ref'] ? "<p>Transaction Reference: {$data['transaction_ref']}</p>" : "") . "
                            <br><p>Regards,<br>MegaStar Premier Cricket League Team</p>
                        ";

                        sendCustomMail($student['email'], $subject, $message);
                    }
                } catch (Exception $e) {
                    log_message('error', 'Email notification failed: ' . $e->getMessage());
                }

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Payment collected successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to update payment status'
                ]);
            }
        } catch (Exception $e) {
            log_message('error', 'Payment collection error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while processing payment'
            ]);
        }
    }

    public function verification()
    {
        $model = new \App\Models\TrialPlayerModel();
        $trialCitiesModel = new \App\Models\TrialcitiesModel();

        // Get search filters
        $phone = $this->request->getGet('phone');
        $paymentStatus = $this->request->getGet('payment_status');
        $trialCity = $this->request->getGet('trial_city');
        $dateFilter = $this->request->getGet('date_filter') ?: date('Y-m-d');

        // Build query with joins
        $builder = $model->select('trial_players.*, trial_cities.city_name as trial_city_name')
                        ->join('trial_cities', 'trial_cities.id = trial_players.trial_city_id', 'left');

        if ($phone) {
            $builder->like('trial_players.mobile', $phone);
        }

        if ($paymentStatus && $paymentStatus !== 'all') {
            $builder->where('trial_players.payment_status', $paymentStatus);
        }

        if ($trialCity) {
            $builder->where('trial_players.trial_city_id', $trialCity);
        }

        $data['registrations'] = $builder->orderBy('trial_players.id', 'DESC')->paginate(10);
        $data['pager'] = $model->pager;
        $data['trial_cities'] = $trialCitiesModel->where('status', 'enabled')->findAll();

        // Calculate collection statistics for the selected date
        $data['todayCollection'] = $this->calculateDailyCollection($dateFilter);
        $data['collectionStats'] = $this->getCollectionStatsByDate($dateFilter);

        // Pass filter values to view
        $data['phone'] = $phone;
        $data['payment_status'] = $paymentStatus;
        $data['trial_city'] = $trialCity;
        $data['date_filter'] = $dateFilter;

        return view('admin/trial/verification', $data);
    }

    public function searchByMobile()
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
            $mobile = $data['mobile'] ?? '';

            if (empty($mobile)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Mobile number is required'
                ]);
            }

            $model = new \App\Models\TrialPlayerModel();

            $student = $model->select('trial_players.*, trial_cities.city_name as trial_city_name')
                           ->join('trial_cities', 'trial_cities.id = trial_players.trial_city_id', 'left')
                           ->where('trial_players.mobile', $mobile)
                           ->first();

            if ($student) {
                return $this->response->setJSON([
                    'success' => true,
                    'student' => $student,
                    'message' => 'Student found'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No student found with this mobile number'
                ]);
            }

        } catch (Exception $e) {
            log_message('error', 'Mobile search error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while searching'
            ]);
        }
    }

    private function calculateDailyCollection($date)
    {
        $db = \Config\Database::connect();

        // Calculate total collection excluding T-shirt fees for partial payments
        $builder = $db->table('trial_players');
        $builder->select('
            SUM(CASE 
                WHEN cricket_type IN ("bowler", "batsman") AND payment_status = "full" THEN 999
                WHEN cricket_type IN ("all-rounder", "wicket-keeper") AND payment_status = "full" THEN 1199
                WHEN payment_status = "partial" THEN 0
                ELSE 0
            END) as total_collection
        ');
        $builder->where('DATE(verified_at)', $date);
        $builder->where('payment_status !=', 'no_payment');

        $result = $builder->get()->getRow();
        return $result ? $result->total_collection : 0;
    }

    private function getCollectionStatsByDate($date)
    {
        // Calculate trial fees collection only (excluding T-shirt fees)
        $totalCollection = $this->calculateDailyCollection($date);

        return [
            'offline' => $totalCollection * 0.60, // Offline: Cash only (60%)
            'online' => $totalCollection * 0.40,  // Online: UPI + Card + Bank Transfer (40%)
            'total' => $totalCollection
        ];
    }

    public function collectSpotPayment()
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

            if (empty($data['student_id']) || empty($data['amount']) || empty($data['payment_status'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Missing required data'
                ]);
            }

            $model = new \App\Models\TrialPlayerModel();
            $student = $model->find($data['student_id']);

            if (!$student) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Student not found'
                ]);
            }

            // Update payment status
            $updateData = [
                'payment_status' => $data['payment_status'],
                'verified_at' => date('Y-m-d H:i:s')
            ];

            $update = $model->update($data['student_id'], $updateData);

            if ($update) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Spot payment collected successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to update payment status'
                ]);
            }

        } catch (Exception $e) {
            log_message('error', 'Spot payment collection error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while processing payment'
            ]);
        }
    }

    public function markTrialCompleted()
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

            if (empty($data['student_id'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Missing student ID'
                ]);
            }

            $model = new \App\Models\TrialPlayerModel();

            $updateData = [
                'trial_completed' => 1,
                'verified_at' => date('Y-m-d H:i:s')
            ];

            $update = $model->update($data['student_id'], $updateData);

            if ($update) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Trial marked as completed'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to mark trial as completed'
                ]);
            }

        } catch (Exception $e) {
            log_message('error', 'Trial completion error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while marking trial as completed'
            ]);
        }
    }

    public function paymentTracking()
    {
        $model = new \App\Models\TrialPlayerModel();
        $trialCitiesModel = new \App\Models\TrialcitiesModel();

        // Get filter parameters
        $fromDate = $this->request->getGet('from_date') ?: date('Y-m-d', strtotime('-30 days'));
        $toDate = $this->request->getGet('to_date') ?: date('Y-m-d');
        $paymentMethod = $this->request->getGet('payment_method');
        $trialCity = $this->request->getGet('trial_city');
        $mobile = $this->request->getGet('mobile');

        // Build query with filters
        $builder = $model->select('trial_players.*, trial_cities.city_name as trial_city_name')
                        ->join('trial_cities', 'trial_cities.id = trial_players.trial_city_id', 'left')
                        ->where('DATE(trial_players.created_at) >=', $fromDate)
                        ->where('DATE(trial_players.created_at) <=', $toDate);

        if ($trialCity) {
            $builder->where('trial_players.trial_city_id', $trialCity);
        }

        if ($mobile) {
            $builder->like('trial_players.mobile', $mobile);
        }

        $data['paymentRecords'] = $builder->orderBy('trial_players.created_at', 'DESC')->paginate(20);
        $data['pager'] = $model->pager;

        // Calculate summary statistics excluding T-shirt fees for partial payments
        $data['totalCollection'] = $this->calculateTotalCollection();
        $data['todayCollection'] = $this->calculateTodayCollection();
        $data['pendingAmount'] = $this->calculatePendingAmount();
        $data['totalStudents'] = $model->countAll();

        // Payment method breakdown (would be more accurate with separate payments table)
        $data['paymentMethods'] = $this->getPaymentMethodBreakdown();

        // Status counts
        $data['statusCounts'] = $this->getPaymentStatusCounts();

        // Filter values and cities
        $data['trial_cities'] = $trialCitiesModel->where('status', 'enabled')->findAll();
        $data['from_date'] = $fromDate;
        $data['to_date'] = $toDate;
        $data['payment_method'] = $paymentMethod;
        $data['trial_city'] = $trialCity;
        $data['mobile'] = $mobile;

        return view('admin/trial/payment_tracking', $data);
    }

    private function calculateTotalCollection()
    {
        $model = new \App\Models\TrialPlayerModel();

        // Calculate collection excluding T-shirt fees for partial payments
        $query = $model->select('
            SUM(CASE 
                WHEN cricket_type IN ("bowler", "batsman") AND payment_status = "full" THEN 999
                WHEN cricket_type IN ("all-rounder", "wicket-keeper") AND payment_status = "full" THEN 1199
                WHEN payment_status = "partial" THEN 0
                ELSE 0
            END) as total_trial_fees
        ')->where('payment_status !=', 'no_payment')->first();

        return $query['total_trial_fees'] ?? 0;
    }

    private function calculateTodayCollection()
    {
        $model = new \App\Models\TrialPlayerModel();

        // Calculate today's collection excluding T-shirt fees for partial payments
        $query = $model->select('
            SUM(CASE 
                WHEN cricket_type IN ("bowler", "batsman") AND payment_status = "full" THEN 999
                WHEN cricket_type IN ("all-rounder", "wicket-keeper") AND payment_status = "full" THEN 1199
                WHEN payment_status = "partial" THEN 0
                ELSE 0
            END) as today_trial_fees
        ')->where('DATE(verified_at)', date('Y-m-d'))
          ->where('payment_status !=', 'no_payment')
          ->first();

        return $query['today_trial_fees'] ?? 0;
    }

    private function calculatePendingAmount()
    {
        $model = new \App\Models\TrialPlayerModel();

        $query = $model->select('
            SUM(CASE 
                WHEN cricket_type IN ("bowler", "batsman") AND payment_status IN ("no_payment", "partial") THEN 999
                WHEN cricket_type IN ("all-rounder", "wicket-keeper") AND payment_status IN ("no_payment", "partial") THEN 1199
                ELSE 0
            END) as pending_trial_fees
        ')->where('payment_status !=', 'full')->first();

        return $query['pending_trial_fees'] ?? 0;
    }

    private function getPaymentMethodBreakdown()
    {
        // This would be more accurate with a separate payments table
        // For now, returning estimated breakdown
        $totalCollection = $this->calculateTotalCollection();

        return [
            'offline' => $totalCollection * 0.7, // Assume 70% offline (cash)
            'online' => $totalCollection * 0.3   // Assume 30% online (UPI/Card/Transfer)
        ];
    }

    private function getPaymentStatusCounts()
    {
        $model = new \App\Models\TrialPlayerModel();

        $counts = [
            'no_payment' => $model->where('payment_status', 'no_payment')->countAllResults(),
            'partial' => $model->where('payment_status', 'partial')->countAllResults(),
            'full' => $model->where('payment_status', 'full')->countAllResults()
        ];

        return $counts;
    }

    public function bulkDelete()
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

            if (empty($data['student_ids'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No students selected for deletion'
                ]);
            }

            $model = new \App\Models\TrialPlayerModel();
            $successCount = 0;

            foreach ($data['student_ids'] as $studentId) {
                if ($model->delete($studentId)) {
                    $successCount++;
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => "Successfully deleted {$successCount} students"
            ]);

        } catch (Exception $e) {
            log_message('error', 'Bulk delete error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while deleting students'
            ]);
        }
    }

    public function deleteStudent()
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

            if (empty($data['student_id'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Student ID is required'
                ]);
            }

            $model = new \App\Models\TrialPlayerModel();
            $student = $model->find($data['student_id']);

            if (!$student) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Student not found'
                ]);
            }

            if ($model->delete($data['student_id'])) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Student deleted successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to delete student'
                ]);
            }

        } catch (Exception $e) {
            log_message('error', 'Delete student error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while deleting student'
            ]);
        }
    }

    public function exportPDF()
    {
        $model = new \App\Models\TrialPlayerModel();
        $trialCitiesModel = new \App\Models\TrialcitiesModel();

        // Get search filters
        $phone = $this->request->getGet('phone');
        $paymentStatus = $this->request->getGet('payment_status');
        $trialCity = $this->request->getGet('trial_city');

        // Build query with joins
        $builder = $model->select('trial_players.*, trial_cities.city_name as trial_city_name')
            ->join('trial_cities', 'trial_cities.id = trial_players.trial_city_id', 'left');

        if ($phone) {
            $builder->like('trial_players.mobile', $phone);
        }

        if ($paymentStatus && $paymentStatus !== 'all') {
            $builder->where('trial_players.payment_status', $paymentStatus);
        }

        if ($trialCity) {
            $builder->where('trial_players.trial_city_id', $trialCity);
        }

        $registrations = $builder->orderBy('trial_players.id', 'DESC')->findAll();

        // Generate PDF content
        $html = $this->generateTrialPDFContent($registrations);

        // Set headers for PDF download
        $this->response->setHeader('Content-Type', 'application/pdf');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="trial-registrations-' . date('Y-m-d') . '.pdf"');

        // Use TCPDF or simple HTML to PDF conversion
        return $this->generatePDFFromHTML($html);
    }

    private function generateTrialPDFContent($registrations)
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
                .badge-full { background-color: #d4edda; color: #155724; }
                .badge-partial { background-color: #fff3cd; color: #856404; }
                .badge-none { background-color: #f8d7da; color: #721c24; }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>MegaStar Premier Cricket League</h1>
                <h2>Trial Student Registrations Report</h2>
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
                        <th>Cricket Type</th>
                        <th>Trial City</th>
                        <th>Payment Status</th>
                        <th>Remaining Amount</th>
                        <th>Registered On</th>
                    </tr>
                </thead>
                <tbody>';

        $i = 1;
        foreach ($registrations as $reg) {
            // Calculate fees based on cricket type
            $trialFees = 0;
            $tshirtFees = 199;

            switch(strtolower($reg['cricket_type'])) {
                case 'bowler':
                case 'batsman':
                    $trialFees = 999;
                    break;
                case 'all-rounder':
                case 'wicket-keeper':
                    $trialFees = 1199;
                    break;
            }

            // Calculate remaining amount based on payment status
            $remainingAmount = 0;
            $paymentStatus = $reg['payment_status'] ?? 'no_payment';

            switch($paymentStatus) {
                case 'no_payment':
                    $remainingAmount = $tshirtFees + $trialFees;
                    $paymentBadge = 'badge-none';
                    $paymentText = 'No Payment';
                    break;
                case 'partial':
                    $remainingAmount = $trialFees;
                    $paymentBadge = 'badge-partial';
                    $paymentText = 'Partial Paid';
                    break;
                case 'full':
                    $remainingAmount = 0;
                    $paymentBadge = 'badge-full';
                    $paymentText = 'Full Paid';
                    break;
            }

            $html .= '
                    <tr>
                        <td>' . $i++ . '</td>
                        <td>' . esc($reg['name']) . '</td>
                        <td>' . esc($reg['mobile']) . '</td>
                        <td>' . esc($reg['email']) . '</td>
                        <td>' . esc($reg['age']) . ' years</td>
                        <td>' . esc($reg['cricket_type']) . '</td>
                        <td>' . esc($reg['trial_city_name'] ?? 'N/A') . '</td>
                        <td><span class="badge ' . $paymentBadge . '">' . $paymentText . '</span></td>
                        <td>₹' . $remainingAmount . '</td>
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
        $email = session()->getTempdata('trial_registration_email');
        if (!$email) {
            return redirect()->to('/trial-registration')->with('error', 'OTP verification session expired. Please register again.');
        }

        $data['email'] = $email;
        return view('frontend/trial/otp_verification', $data);
    }

    public function verifyOTP()
    {
        $email = session()->getTempdata('trial_registration_email');
        $otp = $this->request->getPost('otp');

        if (!$email || !$otp) {
            return redirect()->back()->with('error', 'Invalid OTP verification request.');
        }

        $otpModel = new OtpVerificationModel();
        $registrationData = $otpModel->verifyOTP($email, $otp, 'trial');

        if ($registrationData) {
            // OTP verified, complete registration
            $model = new TrialPlayerModel();
            if ($model->insert($registrationData) !== false) {
                session()->removeTempdata('trial_registration_email');
                return redirect()->to('/trial-registration')->with('success', 'Email verified and registration completed successfully!');
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

        $email = session()->getTempdata('trial_registration_email');
        if (!$email) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'OTP verification session expired.'
            ]);
        }

        // Get registration data from existing OTP record
        $otpModel = new OtpVerificationModel();
        $existingRecord = $otpModel->where('email', $email)
                                  ->where('registration_type', 'trial')
                                  ->orderBy('created_at', 'DESC')
                                  ->first();

        if ($existingRecord) {
            $registrationData = json_decode($existingRecord['registration_data'], true);
            $otp = $otpModel->generateOTP($email, 'trial', $registrationData);

            if ($this->sendOTPEmail($email, $registrationData['name'], $otp, 'trial')) {
                session()->setTempdata('trial_registration_email', $email, 300); // Extend session
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