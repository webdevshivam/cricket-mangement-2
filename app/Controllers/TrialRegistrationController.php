<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\QrCodeSettingModel;
use App\Models\TrialcitiesModel;
use App\Models\TrialPlayerModel;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;

class TrialRegistrationController extends BaseController
{
    public function index()
    {
        // Load the view for trial registration
        //all trial city_name

        $model = new TrialcitiesModel();
        $qrCodeSetting = new QrCodeSettingModel();
        $data['qr_code_setting'] = $qrCodeSetting->first();
        $data['trial_cities'] = $model->where('status', 'enabled')->findAll();
        return view('frontend/trial/registration', $data);
    }
    public function register()
    {
        //get form data
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
        $model = new TrialPlayerModel();
        if ($model->insert($data) === false) {
            echo "Error: " . $model->errors();
        } else {
            return redirect()->to('/trial-registration')->with('success', 'Registration successful!');
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

        $data['registrations'] = $builder->orderBy('trial_players.id', 'DESC')->paginate(20);
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

        $data['registrations'] = $builder->orderBy('trial_players.id', 'DESC')->paginate(20);
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
            'cash' => $totalCollection * 0.6, // Assume 60% cash
            'upi' => $totalCollection * 0.3,  // Assume 30% UPI
            'card' => $totalCollection * 0.08, // Assume 8% card
            'online' => $totalCollection * 0.02, // Assume 2% online
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
}
