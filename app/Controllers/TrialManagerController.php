<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TrialManagerModel;
use App\Models\TrialcitiesModel;
use App\Models\TrialPlayerModel;
use CodeIgniter\HTTP\ResponseInterface;

class TrialManagerController extends BaseController
{
    protected $trialManagerModel;
    protected $trialCitiesModel;
    protected $trialPlayerModel;

    public function __construct()
    {
        $this->trialManagerModel = new TrialManagerModel();
        $this->trialCitiesModel = new TrialcitiesModel();
        $this->trialPlayerModel = new TrialPlayerModel();
    }

    // Admin - List all trial managers
    public function index()
    {
        // Check admin access
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/unauthorized');
        }

        $data = [
            'title' => 'Trial Managers',
            'managers' => $this->trialManagerModel->getAllWithCity()
        ];

        return view('admin/trial_managers/index', $data);
    }

    // Admin - Create trial manager form
    public function create()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/unauthorized');
        }

        $data = [
            'title' => 'Create Trial Manager',
            'trial_cities' => $this->trialCitiesModel->where('status', 'enabled')->findAll()
        ];

        return view('admin/trial_managers/create', $data);
    }

    // Admin - Store trial manager
    public function store()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/unauthorized');
        }

        $validation = \Config\Services::validation();

        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email|is_unique[trial_managers.email]',
            'trial_name' => 'required|min_length[3]|max_length[150]',
            'trial_city_id' => 'permit_empty|integer',
            'password_type' => 'required|in_list[auto,manual]',
            'manual_password' => 'permit_empty|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Generate or use manual password
        $password = '';
        if ($this->request->getPost('password_type') === 'auto') {
            $password = $this->trialManagerModel->generatePassword();
        } else {
            $password = $this->request->getPost('manual_password');
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'trial_name' => $this->request->getPost('trial_name'),
            'trial_city_id' => $this->request->getPost('trial_city_id') ?: null,
            'status' => 'active',
            'created_by' => session()->get('user_id')
        ];

        if ($this->trialManagerModel->insert($data)) {
            // Store plain password in session to show to admin
            session()->setFlashdata('success', 'Trial Manager created successfully!');
            session()->setFlashdata('generated_password', $password);
            session()->setFlashdata('manager_email', $data['email']);

            return redirect()->to('/admin/trial-managers');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to create trial manager.');
        }
    }

    // Admin - Edit trial manager
    public function edit($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/unauthorized');
        }

        $manager = $this->trialManagerModel->find($id);
        if (!$manager) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Trial Manager not found');
        }

        $data = [
            'title' => 'Edit Trial Manager',
            'manager' => $manager,
            'trial_cities' => $this->trialCitiesModel->where('status', 'enabled')->findAll()
        ];

        return view('admin/trial_managers/edit', $data);
    }

    // Admin - Update trial manager
    public function update($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/unauthorized');
        }

        $manager = $this->trialManagerModel->find($id);
        if (!$manager) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Trial Manager not found');
        }

        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'email' => "required|valid_email|is_unique[trial_managers.email,id,{$id}]",
            'trial_name' => 'required|min_length[3]|max_length[150]',
            'trial_city_id' => 'permit_empty|integer',
            'status' => 'required|in_list[active,inactive]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'trial_name' => $this->request->getPost('trial_name'),
            'trial_city_id' => $this->request->getPost('trial_city_id') ?: null,
            'status' => $this->request->getPost('status')
        ];

        // Update password if provided
        $newPassword = $this->request->getPost('new_password');
        if (!empty($newPassword)) {
            $data['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        if ($this->trialManagerModel->update($id, $data)) {
            session()->setFlashdata('success', 'Trial Manager updated successfully!');
            return redirect()->to('/admin/trial-managers');
        } else {
            return redirect()->back()->withInput()->with('error', 'Failed to update trial manager.');
        }
    }

    // Admin - Delete trial manager
    public function delete($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $manager = $this->trialManagerModel->find($id);
        if (!$manager) {
            return $this->response->setJSON(['success' => false, 'message' => 'Trial Manager not found']);
        }

        if ($this->trialManagerModel->delete($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Trial Manager deleted successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to delete trial manager']);
        }
    }

    // Admin - View trial manager details with players
    public function view($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/unauthorized');
        }

        $manager = $this->trialManagerModel->getWithCity($id);
        if (!$manager) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Trial Manager not found');
        }

        // Get players assigned to this trial manager
        $players = $this->trialPlayerModel
            ->select('trial_players.*, trial_cities.city_name as trial_city_name')
            ->join('trial_cities', 'trial_cities.id = trial_players.trial_city_id', 'left')
            ->where('trial_players.trial_manager_id', $id)
            ->orderBy('trial_players.created_at', 'DESC')
            ->findAll();

        // Calculate payment statistics
        $stats = $this->calculateManagerStats($id);

        $data = [
            'title' => 'Trial Manager Details',
            'manager' => $manager,
            'players' => $players,
            'stats' => $stats
        ];

        return view('admin/trial_managers/view', $data);
    }

    // Calculate statistics for trial manager
    private function calculateManagerStats($managerId)
    {
        $db = \Config\Database::connect();

        // Payment status counts
        $statusCounts = $db->query("
            SELECT
                payment_status,
                COUNT(*) as count
            FROM trial_players
            WHERE trial_manager_id = ?
            GROUP BY payment_status
        ", [$managerId])->getResultArray();

        // Payment collection by method
        $paymentStats = $db->query("
            SELECT
                payment_method,
                SUM(amount) as total_amount,
                COUNT(*) as transaction_count
            FROM trial_payments tp
            JOIN trial_players tpl ON tp.trial_player_id = tpl.id
            WHERE tpl.trial_manager_id = ?
            GROUP BY payment_method
        ", [$managerId])->getResultArray();

        $stats = [
            'total_players' => 0,
            'full_payment' => 0,
            'partial_payment' => 0,
            'no_payment' => 0,
            'online_collection' => 0,
            'offline_collection' => 0,
            'total_collection' => 0
        ];

        // Process status counts
        foreach ($statusCounts as $status) {
            $stats['total_players'] += $status['count'];
            $stats[$status['payment_status'] . '_payment'] = $status['count'];
        }

        // Process payment stats
        foreach ($paymentStats as $payment) {
            $stats[$payment['payment_method'] . '_collection'] = $payment['total_amount'];
            $stats['total_collection'] += $payment['total_amount'];
        }

        return $stats;
    }

    // Search players for assignment
    public function searchPlayers()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $input = $this->request->getJSON(true);
        $query = $input['query'] ?? '';

        if (strlen($query) < 2) {
            return $this->response->setJSON(['success' => false, 'message' => 'Query must be at least 2 characters']);
        }

        $players = $this->trialPlayerModel
            ->select('trial_players.*, trial_managers.name as manager_name')
            ->join('trial_managers', 'trial_managers.id = trial_players.trial_manager_id', 'left')
            ->groupStart()
                ->like('trial_players.name', $query)
                ->orLike('trial_players.mobile', $query)
                ->orLike('trial_players.email', $query)
            ->groupEnd()
            ->orderBy('trial_players.created_at', 'DESC')
            ->limit(50)
            ->findAll();

        return $this->response->setJSON(['success' => true, 'players' => $players]);
    }

    // Get unassigned players
    public function getUnassignedPlayers()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $players = $this->trialPlayerModel
            ->select('trial_players.*, trial_cities.city_name as trial_city_name')
            ->join('trial_cities', 'trial_cities.id = trial_players.trial_city_id', 'left')
            ->where('trial_players.trial_manager_id IS NULL')
            ->orderBy('trial_players.created_at', 'DESC')
            ->findAll();

        return $this->response->setJSON(['success' => true, 'players' => $players]);
    }

    // Assign players to trial manager
    public function assignPlayers()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $input = $this->request->getJSON(true);
        $managerId = $input['manager_id'] ?? null;
        $playerIds = $input['player_ids'] ?? [];

        if (!$managerId || empty($playerIds)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Manager ID and player IDs are required']);
        }

        // Verify manager exists and is active
        $manager = $this->trialManagerModel->find($managerId);
        if (!$manager || $manager['status'] !== 'active') {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid trial manager']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            $assignedCount = 0;
            $skippedCount = 0;
            $cityMismatchCount = 0;

            foreach ($playerIds as $playerId) {
                // Check if player exists
                $player = $this->trialPlayerModel->find($playerId);
                if (!$player) {
                    $skippedCount++;
                    continue;
                }

                // Check if player's trial city matches manager's trial city
                if ($manager['trial_city_id'] && $player['trial_city_id'] && 
                    $player['trial_city_id'] != $manager['trial_city_id']) {
                    $cityMismatchCount++;
                    continue;
                }

                // Update player assignment
                $updateData = [
                    'trial_manager_id' => $managerId
                ];

                // If player doesn't have a trial city, set it to manager's city
                if (!$player['trial_city_id'] && $manager['trial_city_id']) {
                    $updateData['trial_city_id'] = $manager['trial_city_id'];
                }

                if ($this->trialPlayerModel->update($playerId, $updateData)) {
                    $assignedCount++;
                } else {
                    $skippedCount++;
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON(['success' => false, 'message' => 'Database transaction failed']);
            }

            $message = "Successfully assigned {$assignedCount} player(s) to the trial manager.";
            if ($skippedCount > 0) {
                $message .= " {$skippedCount} player(s) were skipped.";
            }
            if ($cityMismatchCount > 0) {
                $message .= " {$cityMismatchCount} player(s) skipped due to trial city mismatch.";
            }

            return $this->response->setJSON(['success' => true, 'message' => $message]);

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Player assignment error: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'An error occurred during assignment']);
        }
    }

    // Unassign player from trial manager
    public function unassignPlayer($playerId)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $player = $this->trialPlayerModel->find($playerId);
        if (!$player) {
            return $this->response->setJSON(['success' => false, 'message' => 'Player not found']);
        }

        if ($this->trialPlayerModel->update($playerId, ['trial_manager_id' => null])) {
            return $this->response->setJSON(['success' => true, 'message' => 'Player unassigned successfully']);
        } else {
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to unassign player']);
        }
    }

    // Search single player by mobile
    public function searchSinglePlayer()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $input = $this->request->getJSON(true);
        $mobile = $input['mobile'] ?? '';

        if (strlen($mobile) !== 10 || !ctype_digit($mobile)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Please enter a valid 10-digit mobile number']);
        }

        $player = $this->trialPlayerModel
            ->select('trial_players.*, trial_managers.name as manager_name')
            ->join('trial_managers', 'trial_managers.id = trial_players.trial_manager_id', 'left')
            ->where('trial_players.mobile', $mobile)
            ->first();

        if (!$player) {
            return $this->response->setJSON(['success' => false, 'message' => 'No player found with this mobile number']);
        }

        return $this->response->setJSON(['success' => true, 'player' => $player]);
    }

    // Get all trial cities with player counts
    public function getTrialCities()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $db = \Config\Database::connect();
        
        $cities = $db->query("
            SELECT 
                tc.id,
                tc.city_name,
                COUNT(tp.id) as player_count
            FROM trial_cities tc
            LEFT JOIN trial_players tp ON tc.id = tp.trial_city_id
            WHERE tc.status = 'enabled'
            GROUP BY tc.id, tc.city_name
            ORDER BY tc.city_name
        ")->getResultArray();

        return $this->response->setJSON(['success' => true, 'cities' => $cities]);
    }

    // Get players from specific trial city
    public function getTrialPlayers($cityId)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        if (!$cityId || !is_numeric($cityId)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid city ID']);
        }

        $players = $this->trialPlayerModel
            ->select('trial_players.*, trial_managers.name as manager_name')
            ->join('trial_managers', 'trial_managers.id = trial_players.trial_manager_id', 'left')
            ->where('trial_players.trial_city_id', $cityId)
            ->orderBy('trial_players.created_at', 'DESC')
            ->findAll();

        return $this->response->setJSON(['success' => true, 'players' => $players]);
    }

    // Show unassigned players page
    public function unassignedPlayers()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/unauthorized');
        }

        $data = [
            'title' => 'Unassigned Players',
        ];

        return view('admin/trial_managers/unassigned', $data);
    }

    // Get active trial managers for assignment dropdowns
    public function getActiveManagers()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $managers = $this->trialManagerModel
            ->select('trial_managers.*, trial_cities.city_name')
            ->join('trial_cities', 'trial_cities.id = trial_managers.trial_city_id', 'left')
            ->where('trial_managers.status', 'active')
            ->orderBy('trial_managers.name')
            ->findAll();

        return $this->response->setJSON(['success' => true, 'managers' => $managers]);
    }

    // Get unassigned players
    public function getUnassignedPlayers()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $players = $this->trialPlayerModel
            ->select('trial_players.*, trial_cities.city_name as trial_city_name')
            ->join('trial_cities', 'trial_cities.id = trial_players.trial_city_id', 'left')
            ->where('trial_players.trial_manager_id IS NULL')
            ->orderBy('trial_players.created_at', 'DESC')
            ->findAll();

        return $this->response->setJSON(['success' => true, 'players' => $players]);
    }

    // Assign players to trial manager
    public function assignPlayers()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $input = $this->request->getJSON(true);
        $playerIds = $input['player_ids'] ?? [];
        $managerId = $input['manager_id'] ?? null;

        if (empty($playerIds) || !$managerId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid data provided']);
        }

        // Verify manager exists and is active
        $manager = $this->trialManagerModel->find($managerId);
        if (!$manager || $manager['status'] !== 'active') {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid or inactive trial manager']);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Get manager's trial city for validation
            $managerCityId = $manager['trial_city_id'];
            
            foreach ($playerIds as $playerId) {
                $player = $this->trialPlayerModel->find($playerId);
                
                if (!$player) {
                    continue;
                }

                // Check if player's trial city matches manager's city (optional validation)
                if ($managerCityId && $player['trial_city_id'] && $player['trial_city_id'] != $managerCityId) {
                    // Log warning but still allow assignment
                    log_message('warning', "Player {$playerId} trial city doesn't match manager {$managerId} city");
                }

                // Update player assignment
                $this->trialPlayerModel->update($playerId, [
                    'trial_manager_id' => $managerId,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON(['success' => false, 'message' => 'Failed to assign players']);
            }

            return $this->response->setJSON([
                'success' => true, 
                'message' => count($playerIds) . ' player(s) assigned successfully'
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error assigning players: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'An error occurred while assigning players']);
        }
    }
}
