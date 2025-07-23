<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('welcome_message');
    }
    
    public function dashboard(): string
    {
        // Load models
        $playersModel = new \App\Models\PlayersModel();
        $trialPlayerModel = new \App\Models\TrialPlayerModel();
        $leaguePlayerModel = new \App\Models\LeaguePlayerModel();
        $gradeModel = new \App\Models\GradeModel();
        $trialCitiesModel = new \App\Models\TrialcitiesModel();

        // Get statistics
        $data = [
            'totalPlayers' => $playersModel->countAll(),
            'trialStudents' => $trialPlayerModel->countAll(),
            'leaguePlayers' => $leaguePlayerModel->countAll(),
            'totalGrades' => $gradeModel->countAll(),
            'totalCities' => $trialCitiesModel->countAll(),
            
            // Payment statistics
            'totalRevenue' => $this->calculateTotalRevenue(),
            'todayRevenue' => $this->calculateTodayRevenue(),
            'paidStudents' => $this->getPaidStudentsCount(),
            'unpaidStudents' => $this->getUnpaidStudentsCount(),
            
            // Status counts
            'verifiedPlayers' => $this->getVerifiedPlayersCount(),
            'pendingVerifications' => $this->getPendingVerificationsCount(),
            'pendingTrials' => $this->getPendingTrialsCount(),
            'newPlayersThisWeek' => $this->getNewPlayersThisWeek(),
            
            // Activities and tasks
            'recentActivities' => $this->getRecentActivities(),
            'pendingTasks' => $this->getPendingTasks(),
        ];

        return view('admin/dashboard', $data);
    }

    private function calculateTotalRevenue()
    {
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT COALESCE(SUM(CASE 
                WHEN payment_status = 'full' THEN amount_paid 
                WHEN payment_status = 'partial' THEN amount_paid 
                ELSE 0 
            END), 0) as total 
            FROM trial_registrations 
            WHERE payment_status IN ('full', 'partial')
        ");
        $result = $query->getRow();
        return $result ? $result->total : 0;
    }

    private function calculateTodayRevenue()
    {
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT COALESCE(SUM(amount_paid), 0) as total 
            FROM trial_registrations 
            WHERE DATE(payment_date) = CURDATE() 
            AND payment_status IN ('full', 'partial')
        ");
        $result = $query->getRow();
        return $result ? $result->total : 0;
    }

    private function getPaidStudentsCount()
    {
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT COUNT(*) as count 
            FROM trial_registrations 
            WHERE payment_status = 'full'
        ");
        $result = $query->getRow();
        return $result ? $result->count : 0;
    }

    private function getUnpaidStudentsCount()
    {
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT COUNT(*) as count 
            FROM trial_registrations 
            WHERE payment_status = 'pending'
        ");
        $result = $query->getRow();
        return $result ? $result->count : 0;
    }

    private function getVerifiedPlayersCount()
    {
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT COUNT(*) as count 
            FROM trial_registrations 
            WHERE verification_status = 'verified'
        ");
        $result = $query->getRow();
        return $result ? $result->count : 0;
    }

    private function getPendingVerificationsCount()
    {
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT COUNT(*) as count 
            FROM trial_registrations 
            WHERE verification_status = 'pending'
        ");
        $result = $query->getRow();
        return $result ? $result->count : 0;
    }

    private function getPendingTrialsCount()
    {
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT COUNT(*) as count 
            FROM trial_registrations 
            WHERE status = 'registered'
        ");
        $result = $query->getRow();
        return $result ? $result->count : 0;
    }

    private function getNewPlayersThisWeek()
    {
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT COUNT(*) as count 
            FROM trial_registrations 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        ");
        $result = $query->getRow();
        return $result ? $result->count : 0;
    }

    private function getRecentActivities()
    {
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT 
                'New trial registration' as description,
                'fas fa-user-plus' as icon,
                created_at
            FROM trial_registrations 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
            
            UNION ALL
            
            SELECT 
                'Payment received' as description,
                'fas fa-credit-card' as icon,
                payment_date as created_at
            FROM trial_registrations 
            WHERE payment_date >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
            AND payment_status IN ('full', 'partial')
            
            ORDER BY created_at DESC 
            LIMIT 10
        ");
        return $query->getResultArray();
    }

    private function getPendingTasks()
    {
        $tasks = [];

        // Check for pending verifications
        $pendingVerifications = $this->getPendingVerificationsCount();
        if ($pendingVerifications > 0) {
            $tasks[] = [
                'title' => 'Pending Verifications',
                'description' => "$pendingVerifications students need verification",
                'priority' => 'high',
                'action_url' => base_url('admin/trial/verification')
            ];
        }

        // Check for pending payments
        $pendingPayments = $this->getUnpaidStudentsCount();
        if ($pendingPayments > 0) {
            $tasks[] = [
                'title' => 'Pending Payments',
                'description' => "$pendingPayments students haven't paid",
                'priority' => 'medium',
                'action_url' => base_url('admin/trial/payment-tracking')
            ];
        }

        // Check for grade assignments needed
        $db = \Config\Database::connect();
        $query = $db->query("
            SELECT COUNT(*) as count 
            FROM trial_registrations tr
            LEFT JOIN grade_assignments ga ON tr.id = ga.student_id
            WHERE tr.verification_status = 'verified' 
            AND ga.id IS NULL
        ");
        $unassignedGrades = $query->getRow()->count ?? 0;
        
        if ($unassignedGrades > 0) {
            $tasks[] = [
                'title' => 'Grade Assignments',
                'description' => "$unassignedGrades students need grade assignment",
                'priority' => 'medium',
                'action_url' => base_url('admin/grades/assign')
            ];
        }

        return $tasks;
    }
}
