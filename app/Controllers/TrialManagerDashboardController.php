<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TrialManagerModel;
use App\Models\TrialPlayerModel;
use CodeIgniter\HTTP\ResponseInterface;

class TrialManagerDashboardController extends BaseController
{
    protected $trialManagerModel;
    protected $trialPlayerModel;

    public function __construct()
    {
        $this->trialManagerModel = new TrialManagerModel();
        $this->trialPlayerModel = new TrialPlayerModel();
    }

    // Trial Manager Login Page
    public function login()
    {
        // Redirect if already logged in as trial manager
        if (session()->get('tm_isLoggedIn') && session()->get('tm_role') === 'trial_manager') {
            return redirect()->to('/trial-manager/dashboard');
        }

        return view('trial_manager/login');
    }

    // Trial Manager Login Process
    public function authenticate()
    {
        $validation = \Config\Services::validation();

        $rules = [
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');

        $manager = $this->trialManagerModel->getByEmail($email);

        if ($manager && password_verify($password, $manager['password']) && $manager['status'] === 'active') {
            // Set trial manager session with distinct keys
            $sessionData = [
                'tm_user_id' => $manager['id'],
                'tm_name' => $manager['name'],
                'tm_email' => $manager['email'],
                'tm_trial_name' => $manager['trial_name'],
                'tm_trial_city_id' => $manager['trial_city_id'],
                'tm_role' => 'trial_manager',
                'tm_isLoggedIn' => true,
                'tm_login_time' => time(),
                'tm_last_activity' => time()
            ];

            session()->set($sessionData);

            return redirect()->to('/trial-manager/dashboard');
        } else {
            return redirect()->back()->withInput()->with('error', 'Invalid email or password, or account is inactive.');
        }
    }

    // Trial Manager Dashboard
    public function dashboard()
    {
        if (!session()->get('tm_isLoggedIn') || session()->get('tm_role') !== 'trial_manager') {
            return redirect()->to('/trial-manager/login');
        }

        $managerId = session()->get('tm_user_id');

        // Get dashboard statistics
        $stats = $this->getDashboardStats($managerId);

        // Get recent players
        $recentPlayers = $this->trialPlayerModel
            ->select('name, mobile, payment_status, created_at')
            ->where('trial_manager_id', $managerId)
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->findAll();

        $data = [
            'title' => 'Trial Manager Dashboard',
            'stats' => $stats,
            'recent_players' => $recentPlayers
        ];

        return view('trial_manager/dashboard', $data);
    }

    // Player Verification Page
    public function playerVerification()
    {
        if (!session()->get('tm_isLoggedIn') || session()->get('tm_role') !== 'trial_manager') {
            return redirect()->to('/trial-manager/login');
        }

        $data = [
            'title' => 'Player Verification'
        ];

        return view('trial_manager/player_verification', $data);
    }

    // Search player by mobile
    public function searchPlayer()
    {
        if (!session()->get('tm_isLoggedIn') || session()->get('tm_role') !== 'trial_manager') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $input = $this->request->getJSON(true);
        $mobile = $input['mobile'] ?? '';

        if (empty($mobile)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Mobile number is required']);
        }

        $managerId = session()->get('tm_user_id');

        // Search for player assigned to this trial manager or unassigned players
        $player = $this->trialPlayerModel
            ->select('trial_players.*, trial_cities.city_name as trial_city_name')
            ->join('trial_cities', 'trial_cities.id = trial_players.trial_city_id', 'left')
            ->where('trial_players.mobile', $mobile)
            ->groupStart()
                ->where('trial_players.trial_manager_id', $managerId)
                ->orWhere('trial_players.trial_manager_id IS NULL')
            ->groupEnd()
            ->first();

        if ($player) {
            // Calculate total amount paid so far
            $paymentModel = new \App\Models\TrialPaymentModel();
            $totalPaid = $paymentModel->where('trial_player_id', $player['id'])->selectSum('amount')->first();
            $player['total_paid'] = $totalPaid['amount'] ?? 0;

             // Calculate remaining amount based on payment status
            $fees = $this->calculateFees($player['cricket_type']);
            $remainingAmount = 0;

            if ($player['payment_status'] === 'no_payment') {
                $remainingAmount = $fees['total']; // T-shirt (199) + cricket type fees
            } elseif ($player['payment_status'] === 'partial') {
                // Partial payment means T-shirt fee is already paid, only show cricket type fee
                $remainingAmount = $fees['trial']; // Only cricket type fees
            }
            // If full payment, remaining amount stays 0

            $player['remaining_amount'] = $remainingAmount;
            $player['fees_breakdown'] = $fees;
            return $this->response->setJSON([
                'success' => true,
                'player' => $player
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Player not found or not assigned to you'
            ]);
        }
    }

    // Manual Player Registration
    public function registerPlayer()
    {
        if (!session()->get('tm_isLoggedIn') || session()->get('tm_role') !== 'trial_manager') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $input = $this->request->getJSON(true);

        $validation = \Config\Services::validation();

        $rules = [
            'name' => 'required|min_length[3]',
            'mobile' => 'required|exact_length[10]|is_unique[trial_players.mobile]',
            'email' => 'permit_empty|valid_email',
            'age' => 'required|integer|greater_than[10]|less_than[50]',
            'cricket_type' => 'required|in_list[bowler,batsman,all-rounder,wicket-keeper]'
        ];

        if (!$validation->setRules($rules)->run($input)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validation->getErrors()
            ]);
        }

        $managerId = session()->get('tm_user_id');
        $trialCityId = session()->get('tm_trial_city_id');

        $playerData = [
            'name' => $input['name'],
            'mobile' => $input['mobile'],
            'email' => $input['email'] ?? null,
            'age' => $input['age'],
            'cricket_type' => $input['cricket_type'],
            'trial_city_id' => $trialCityId,
            'trial_manager_id' => $managerId,
            'payment_status' => 'full', // TM registered players are considered full paid
            'registered_by_tm' => 1,
            'verified_at' => date('Y-m-d H:i:s')
        ];

        $playerId = $this->trialPlayerModel->insert($playerData);

        if ($playerId) {
            // Add payment record for the full amount
            $fees = $this->calculateFees($input['cricket_type']);
            $paymentModel = new \App\Models\TrialPaymentModel();

            $paymentData = [
                'trial_player_id' => $playerId,
                'trial_manager_id' => $managerId,
                'amount' => $fees['total'],
                'payment_method' => 'offline', // Default to offline for trial manager registration
                'collected_by' => session()->get('tm_name'),
                'payment_date' => date('Y-m-d H:i:s'),
                'notes' => 'Registration by Trial Manager with full payment'
            ];

            $paymentModel->insert($paymentData);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Player registered successfully with full payment!',
                'fees_collected' => $fees['total']
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to register player'
            ]);
        }
    }

    // Collect Payment
    public function collectPayment()
    {
        if (!session()->get('tm_isLoggedIn') || session()->get('tm_role') !== 'trial_manager') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $input = $this->request->getJSON(true);
        $playerId = $input['player_id'] ?? '';
        $amount = $input['amount'] ?? '';
        $paymentMethod = $input['payment_method'] ?? '';

        $player = $this->trialPlayerModel->find($playerId);
        if (!$player) {
            return $this->response->setJSON(['success' => false, 'message' => 'Player not found']);
        }

        $managerId = session()->get('tm_user_id');

        // Insert payment record
        $paymentModel = new \App\Models\TrialPaymentModel();
        $paymentData = [
            'trial_player_id' => $playerId,
            'trial_manager_id' => $managerId,
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'collected_by' => session()->get('tm_name'),
            'payment_date' => date('Y-m-d H:i:s')
        ];

        // Determine new payment status
        $fees = $this->calculateFees($player['cricket_type']);
        $totalRequired = $fees['total'];

        // Calculate total amount paid so far
        $totalPaid = $paymentModel->where('trial_player_id', $playerId)->selectSum('amount')->first();
        $totalPaidAmount = $totalPaid['amount'] ?? 0;

        if (($totalPaidAmount + $amount) >= $totalRequired) {
            $newStatus = 'full';
        } else {
            $newStatus = 'partial';
        }

        // Update player payment status
        $updateData = [
            'payment_status' => $newStatus,
            'verified_at' => date('Y-m-d H:i:s')
        ];

        if ($paymentModel->insert($paymentData) && $this->trialPlayerModel->update($playerId, $updateData)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Payment collected successfully!',
                'new_status' => $newStatus
            ]);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to record payment']);
        }
    }

    // Calculate fees based on cricket type
    private function calculateFees($cricketType)
    {
        $fees = [
            'trial' => 999,
            'tshirt' => 199,
            'total' => 1198
        ];

        if (in_array($cricketType, ['all-rounder', 'wicket-keeper'])) {
            $fees['trial'] = 1199;
            $fees['total'] = 1398;
        }

        return $fees;
    }

    // Logout
    public function logout()
    {
        // Remove only trial manager session data
        session()->remove(['tm_user_id', 'tm_name', 'tm_email', 'tm_trial_name', 'tm_trial_city_id', 'tm_role', 'tm_isLoggedIn', 'tm_login_time', 'tm_last_activity']);
        return redirect()->to('/trial-manager/login');
    }

    // Get dashboard statistics
    private function getDashboardStats($managerId)
    {
        $db = \Config\Database::connect();

        // Player counts by status
        $statusQuery = $db->query("
            SELECT
                payment_status,
                COUNT(*) as count
            FROM trial_players
            WHERE trial_manager_id = ?
            GROUP BY payment_status
        ", [$managerId]);

        $statusCounts = $statusQuery->getResultArray();

        // Payment collection
        $collectionQuery = $db->query("
            SELECT
                tp.payment_method,
                SUM(tp.amount) as total_amount
            FROM trial_payments tp
            JOIN trial_players tpl ON tp.trial_player_id = tpl.id
            WHERE tpl.trial_manager_id = ?
            GROUP BY tp.payment_method
        ", [$managerId]);

        $collections = $collectionQuery->getResultArray();

        // Initialize stats
        $stats = [
            'total_players' => 0,
            'full_payment' => 0,
            'partial_payment' => 0,
            'no_payment' => 0,
            'total_collection' => 0,
            'online_collection' => 0,
            'offline_collection' => 0
        ];

        // Process status counts
        foreach ($statusCounts as $status) {
            $stats['total_players'] += $status['count'];
            switch ($status['payment_status']) {
                case 'full':
                    $stats['full_payment'] = $status['count'];
                    break;
                case 'partial':
                    $stats['partial_payment'] = $status['count'];
                    break;
                case 'no_payment':
                    $stats['no_payment'] = $status['count'];
                    break;
            }
        }

        // Process collections
        foreach ($collections as $collection) {
            $stats['total_collection'] += $collection['total_amount'];
            if ($collection['payment_method'] === 'online') {
                $stats['online_collection'] = $collection['total_amount'];
            } else {
                $stats['offline_collection'] = $collection['total_amount'];
            }
        }

        return $stats;
    }
}