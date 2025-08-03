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
        if (session()->get('isLoggedIn') && session()->get('role') === 'trial_manager') {
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
            // Set trial manager session
            $sessionData = [
                'user_id' => $manager['id'],
                'name' => $manager['name'],
                'email' => $manager['email'],
                'trial_name' => $manager['trial_name'],
                'trial_city_id' => $manager['trial_city_id'],
                'role' => 'trial_manager',
                'isLoggedIn' => true,
                'login_time' => time(),
                'last_activity' => time()
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
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'trial_manager') {
            return redirect()->to('/trial-manager/login');
        }

        $managerId = session()->get('user_id');

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
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'trial_manager') {
            return redirect()->to('/trial-manager/login');
        }

        $data = [
            'title' => 'Player Verification'
        ];

        return view('trial_manager/player_verification', $data);
    }

    // Search Player by Mobile
    public function searchPlayer()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'trial_manager') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $input = $this->request->getJSON(true);
        $mobile = $input['mobile'] ?? '';

        if (empty($mobile)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Mobile number is required']);
        }

        $player = $this->trialPlayerModel->where('mobile', $mobile)->first();

        if ($player) {
            // Calculate fees based on cricket type
            $fees = $this->calculateFees($player['cricket_type']);

            return $this->response->setJSON([
                'success' => true,
                'found' => true,
                'player' => $player,
                'fees' => $fees
            ]);
        } else {
            return $this->response->setJSON([
                'success' => true,
                'found' => false,
                'message' => 'Player not found. You can register them manually.'
            ]);
        }
    }

    // Manual Player Registration
    public function registerPlayer()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'trial_manager') {
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

        $managerId = session()->get('user_id');
        $trialCityId = session()->get('trial_city_id');

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

        if ($this->trialPlayerModel->insert($playerData)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Player registered successfully!'
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
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'trial_manager') {
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

        $managerId = session()->get('user_id');

        // Insert payment record
        $paymentModel = new \App\Models\TrialPaymentModel();
        $paymentData = [
            'trial_player_id' => $playerId,
            'trial_manager_id' => $managerId,
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'collected_by' => session()->get('name'),
            'payment_date' => date('Y-m-d H:i:s')
        ];

        // Determine new payment status
        $fees = $this->calculateFees($player['cricket_type']);
        $totalRequired = $fees['total'];

        if ($amount >= $totalRequired) {
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
        session()->destroy();
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
