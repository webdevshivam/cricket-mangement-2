<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('welcome_message');
    }

    public function dashboardStats()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }

        $data = [
            'success' => true,
            'totalPlayers' => 0,
            'trialStudents' => 0,
            'leaguePlayers' => 0,
            'totalRevenue' => 0,
            'todayRevenue' => 0
        ];

        try {
            $playersModel = new \App\Models\PlayersModel();
            $trialPlayerModel = new \App\Models\TrialPlayerModel();
            $leaguePlayerModel = new \App\Models\LeaguePlayerModel();

            $data['totalPlayers'] = $playersModel->countAll() ?: 0;
            $data['trialStudents'] = $trialPlayerModel->countAll() ?: 0;
            $data['leaguePlayers'] = $leaguePlayerModel->countAll() ?: 0;
            $data['totalRevenue'] = $this->calculateTotalRevenue();
            $data['todayRevenue'] = $this->calculateTodayRevenue();
        } catch (\Exception $e) {
            $data['success'] = false;
            $data['error'] = $e->getMessage();
        }

        return $this->response->setJSON($data);
    }

    public function dashboard()
    {
        // Get statistics for dashboard
        $trialPlayerModel = new \App\Models\TrialPlayerModel();
        $leaguePlayerModel = new \App\Models\LeaguePlayerModel();
        $gradeModel = new \App\Models\GradeModel();
        $trialCitiesModel = new \App\Models\TrialcitiesModel();

        // Trial statistics
        $data['total_trial_players'] = $trialPlayerModel->countAll();
        $data['trial_pending'] = $trialPlayerModel->where('payment_status', 'no_payment')->countAllResults();
        $data['trial_verified'] = $trialPlayerModel->where('payment_status', 'full')->countAllResults();
        $data['trial_partial'] = $trialPlayerModel->where('payment_status', 'partial')->countAllResults();

        // League statistics
        $data['total_league_players'] = $leaguePlayerModel->countAll();
        $data['league_pending'] = $leaguePlayerModel->where('payment_status', 'pending')->countAllResults();
        $data['league_verified'] = $leaguePlayerModel->where('payment_status', 'verified')->countAllResults();

        // Revenue calculation (excluding T-shirt fees for partial payments)
        $totalRevenue = $trialPlayerModel->select('
            SUM(CASE
                WHEN cricket_type IN ("bowler", "batsman") AND payment_status = "full" THEN 999
                WHEN cricket_type IN ("all-rounder", "wicket-keeper") AND payment_status = "full" THEN 1199
                WHEN payment_status = "partial" THEN 0
                ELSE 0
            END) as total_trial_revenue
        ')->first();

        $todayRevenue = $trialPlayerModel->select('
            SUM(CASE
                WHEN cricket_type IN ("bowler", "batsman") AND payment_status = "full" THEN 999
                WHEN cricket_type IN ("all-rounder", "wicket-keeper") AND payment_status = "full" THEN 1199
                WHEN payment_status = "partial" THEN 0
                ELSE 0
            END) as today_trial_revenue
        ')->where('DATE(verified_at)', date('Y-m-d'))
            ->where('payment_status !=', 'no_payment')
            ->first();

        $data['total_revenue'] = $totalRevenue['total_trial_revenue'] ?? 0;
        $data['today_revenue'] = $todayRevenue['today_trial_revenue'] ?? 0;

        // Other statistics
        $data['totalGrades'] = $gradeModel->countAll();
        $data['totalCities'] = $trialCitiesModel->countAll();

        // Calculate revenue
        $data['totalRevenue'] = $this->calculateTotalRevenue();
        $data['todayRevenue'] = $this->calculateTodayRevenue();

        // Recent activities and pending tasks
        $data['recentActivities'] = $this->getRecentActivities();
        $data['pendingTasks'] = $this->getPendingTasks();

        return view('admin/dashboard', $data);
    }

    public function trialPlayers()
    {
        $trialPlayerModel = new \App\Models\TrialPlayerModel();
        $trialCitiesModel = new \App\Models\TrialcitiesModel();

        // Get search filters
        $search = $this->request->getGet('search');
        $paymentStatus = $this->request->getGet('payment_status');
        $trialCity = $this->request->getGet('trial_city');

        // Build query with joins
        $builder = $trialPlayerModel->select('trial_players.*, trial_cities.city_name as trial_city_name')
            ->join('trial_cities', 'trial_cities.id = trial_players.trial_city_id', 'left');

        if ($search) {
            $builder->groupStart()
                ->like('trial_players.name', $search)
                ->orLike('trial_players.mobile', $search)
                ->orLike('trial_players.email', $search)
                ->groupEnd();
        }

        if ($paymentStatus && $paymentStatus !== 'all') {
            $builder->where('trial_players.payment_status', $paymentStatus);
        }

        if ($trialCity) {
            $builder->where('trial_players.trial_city_id', $trialCity);
        }

        $data['players'] = $builder->orderBy('trial_players.id', 'DESC')->paginate(20);
        $data['pager'] = $trialPlayerModel->pager;
        $data['trial_cities'] = $trialCitiesModel->where('status', 'enabled')->findAll();

        // Pass filter values to view
        $data['search'] = $search;
        $data['payment_status'] = $paymentStatus;
        $data['trial_city'] = $trialCity;

        return view('admin/trial/players', $data);
    }

    private function getNewPlayersThisWeek()
    {
        $trialPlayerModel = new \App\Models\TrialPlayerModel();
        $leaguePlayerModel = new \App\Models\LeaguePlayerModel();

        $weekAgo = date('Y-m-d', strtotime('-7 days'));

        $trialCount = $trialPlayerModel->where('DATE(created_at) >=', $weekAgo)->countAllResults();
        $leagueCount = $leaguePlayerModel->where('DATE(created_at) >=', $weekAgo)->countAllResults();

        return $trialCount + $leagueCount;
    }

    private function calculateTotalRevenue()
    {
        try {
            $db = \Config\Database::connect();

            // Try multiple table possibilities
            $queries = [
                "SELECT COALESCE(SUM(CASE
                    WHEN payment_status = 'paid' OR payment_status = 'full' THEN 500
                    WHEN payment_status = 'partial' THEN 250
                    ELSE 0
                END), 0) as total
                FROM trial_players",

                "SELECT COALESCE(SUM(CASE
                    WHEN payment_status = 'paid' THEN 1000
                    ELSE 0
                END), 0) as total
                FROM league_players",

                "SELECT COALESCE(COUNT(*) * 500, 0) as total
                FROM players
                WHERE payment_status = 'paid'"
            ];

            $totalRevenue = 0;
            foreach ($queries as $queryStr) {
                try {
                    $query = $db->query($queryStr);
                    $result = $query->getRow();
                    if ($result && isset($result->total)) {
                        $totalRevenue += $result->total;
                    }
                } catch (\Exception $e) {
                    // Continue to next query if this one fails
                    continue;
                }
            }

            return $totalRevenue;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function calculateTodayRevenue()
    {
        try {
            $db = \Config\Database::connect();

            // Get today's revenue from multiple sources
            $queries = [
                "SELECT COALESCE(COUNT(*) * 500, 0) as total
                FROM trial_players
                WHERE DATE(created_at) = CURDATE()
                AND payment_status = 'paid'",

                "SELECT COALESCE(COUNT(*) * 1000, 0) as total
                FROM league_players
                WHERE DATE(created_at) = CURDATE()
                AND payment_status = 'paid'"
            ];

            $todayRevenue = 0;
            foreach ($queries as $queryStr) {
                try {
                    $query = $db->query($queryStr);
                    $result = $query->getRow();
                    if ($result && isset($result->total)) {
                        $todayRevenue += $result->total;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            return $todayRevenue;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getPaidStudentsCount()
    {
        try {
            $db = \Config\Database::connect();

            $queries = [
                "SELECT COUNT(*) as count FROM trial_players WHERE payment_status = 'paid'",
                "SELECT COUNT(*) as count FROM league_players WHERE payment_status = 'paid'",
                "SELECT COUNT(*) as count FROM players WHERE payment_status = 'paid'"
            ];

            $totalPaid = 0;
            foreach ($queries as $queryStr) {
                try {
                    $query = $db->query($queryStr);
                    $result = $query->getRow();
                    if ($result && isset($result->count)) {
                        $totalPaid += $result->count;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            return $totalPaid;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getUnpaidStudentsCount()
    {
        try {
            $db = \Config\Database::connect();

            $queries = [
                "SELECT COUNT(*) as count FROM trial_players WHERE payment_status = 'no_payment' OR payment_status = 'pending'",
                "SELECT COUNT(*) as count FROM league_players WHERE payment_status = 'unpaid'",
                "SELECT COUNT(*) as count FROM players WHERE payment_status = 'pending' OR payment_status IS NULL"
            ];

            $totalUnpaid = 0;
            foreach ($queries as $queryStr) {
                try {
                    $query = $db->query($queryStr);
                    $result = $query->getRow();
                    if ($result && isset($result->count)) {
                        $totalUnpaid += $result->count;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            return $totalUnpaid;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getVerifiedPlayersCount()
    {
        try {
            $db = \Config\Database::connect();

            $queries = [
                "SELECT COUNT(*) as count FROM trial_players WHERE verified_at IS NOT NULL",
                "SELECT COUNT(*) as count FROM league_players WHERE verified_at IS NOT NULL",
                "SELECT COUNT(*) as count FROM players WHERE status = 'verified'"
            ];

            $totalVerified = 0;
            foreach ($queries as $queryStr) {
                try {
                    $query = $db->query($queryStr);
                    $result = $query->getRow();
                    if ($result && isset($result->count)) {
                        $totalVerified += $result->count;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            return $totalVerified;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getPendingVerificationsCount()
    {
        try {
            $db = \Config\Database::connect();

            $queries = [
                "SELECT COUNT(*) as count FROM trial_players WHERE verified_at IS NULL",
                "SELECT COUNT(*) as count FROM league_players WHERE verified_at IS NULL",
                "SELECT COUNT(*) as count FROM players WHERE status = 'pending' OR status IS NULL"
            ];

            $totalPending = 0;
            foreach ($queries as $queryStr) {
                try {
                    $query = $db->query($queryStr);
                    $result = $query->getRow();
                    if ($result && isset($result->count)) {
                        $totalPending += $result->count;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            return $totalPending;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getPendingTrialsCount()
    {
        try {
            $db = \Config\Database::connect();
            $query = $db->query("SELECT COUNT(*) as count FROM trial_players WHERE trial_completed = 0 OR trial_completed IS NULL");
            $result = $query->getRow();
            return $result ? $result->count : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    private function getRecentActivities()
    {
        try {
            $db = \Config\Database::connect();
            $activities = [];

            // Get recent trial registrations
            try {
                $query = $db->query("
                    SELECT
                        CONCAT('New trial registration: ', name) as description,
                        'fas fa-user-plus' as icon,
                        created_at,
                        'success' as type
                    FROM trial_players
                    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                    ORDER BY created_at DESC
                    LIMIT 5
                ");
                $activities = array_merge($activities, $query->getResultArray());
            } catch (\Exception $e) {
                // Continue if this query fails
            }

            // Get recent league registrations
            try {
                $query = $db->query("
                    SELECT
                        CONCAT('League registration: ', name) as description,
                        'fas fa-trophy' as icon,
                        created_at,
                        'info' as type
                    FROM league_players
                    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                    ORDER BY created_at DESC
                    LIMIT 5
                ");
                $activities = array_merge($activities, $query->getResultArray());
            } catch (\Exception $e) {
                // Continue if this query fails
            }

            // Get recent payments
            try {
                $query = $db->query("
                    SELECT
                        'Payment received' as description,
                        'fas fa-credit-card' as icon,
                        updated_at as created_at,
                        'warning' as type
                    FROM trial_players
                    WHERE payment_status = 'paid'
                    AND updated_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
                    ORDER BY updated_at DESC
                    LIMIT 3
                ");
                $activities = array_merge($activities, $query->getResultArray());
            } catch (\Exception $e) {
                // Continue if this query fails
            }

            // Sort all activities by date and limit
            usort($activities, function ($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });

            return array_slice($activities, 0, 10);
        } catch (\Exception $e) {
            return [
                [
                    'description' => 'Welcome to the dashboard!',
                    'icon' => 'fas fa-home',
                    'created_at' => date('Y-m-d H:i:s'),
                    'type' => 'info'
                ]
            ];
        }
    }

    private function getPendingTasks()
    {
        $tasks = [];

        try {
            // Check for pending verifications
            $pendingVerifications = $this->getPendingVerificationsCount();
            if ($pendingVerifications > 0) {
                $tasks[] = [
                    'title' => 'Pending Verifications',
                    'description' => "$pendingVerifications students need verification",
                    'priority' => 'high',
                    'action_url' => base_url('admin/trial/verification'),
                    'icon' => 'fas fa-user-check'
                ];
            }

            // Check for pending payments
            $pendingPayments = $this->getUnpaidStudentsCount();
            if ($pendingPayments > 0) {
                $tasks[] = [
                    'title' => 'Pending Payments',
                    'description' => "$pendingPayments students haven't paid",
                    'priority' => 'medium',
                    'action_url' => base_url('admin/trial/payment-tracking'),
                    'icon' => 'fas fa-credit-card'
                ];
            }

            // Check for pending trials
            $pendingTrials = $this->getPendingTrialsCount();
            if ($pendingTrials > 0) {
                $tasks[] = [
                    'title' => 'Pending Trials',
                    'description' => "$pendingTrials trials need to be completed",
                    'priority' => 'medium',
                    'action_url' => base_url('admin/trial/registration'),
                    'icon' => 'fas fa-clipboard-list'
                ];
            }

            // Check for grade assignments needed
            try {
                $db = \Config\Database::connect();
                $query = $db->query("
                    SELECT COUNT(*) as count
                    FROM trial_players tp
                    LEFT JOIN grade_assignments ga ON tp.id = ga.student_id
                    WHERE tp.verified_at IS NOT NULL
                    AND ga.id IS NULL
                ");
                $unassignedGrades = $query->getRow()->count ?? 0;

                if ($unassignedGrades > 0) {
                    $tasks[] = [
                        'title' => 'Grade Assignments',
                        'description' => "$unassignedGrades students need grade assignment",
                        'priority' => 'low',
                        'action_url' => base_url('admin/grades/assign'),
                        'icon' => 'fas fa-graduation-cap'
                    ];
                }
            } catch (\Exception $e) {
                // Continue if grade query fails
            }

            // Add some default tasks if none found
            if (empty($tasks)) {
                $tasks[] = [
                    'title' => 'All Clear!',
                    'description' => 'No pending tasks at the moment',
                    'priority' => 'info',
                    'action_url' => base_url('admin/dashboard'),
                    'icon' => 'fas fa-check-circle'
                ];
            }
        } catch (\Exception $e) {
            log_message('error', 'Error getting pending tasks: ' . $e->getMessage());
        }

        return $tasks;
    }
}
