<?php

namespace App\Models;

use CodeIgniter\Model;

class TrialPaymentModel extends Model
{
    protected $table            = 'trial_payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields    = [
        'trial_player_id',
        'trial_manager_id',
        'amount',
        'payment_method',
        'transaction_ref',
        'notes',
        'collected_on_trial_day',
        'collected_by',
        'payment_date',
        'remaining_amount'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = '';

    protected $validationRules = [
        'trial_player_id' => 'required|integer',
        'amount'          => 'required|decimal|greater_than[0]',
        'payment_method'  => 'required|in_list[online,offline]'
    ];

    protected $skipValidation = false;

    // Get payments by trial manager
    public function getByTrialManager($managerId)
    {
        return $this->select('trial_payments.*, trial_players.name, trial_players.mobile')
            ->join('trial_players', 'trial_players.id = trial_payments.trial_player_id')
            ->where('trial_payments.trial_manager_id', $managerId)
            ->orderBy('trial_payments.payment_date', 'DESC')
            ->findAll();
    }

    // Get payment summary by trial manager
    public function getPaymentSummary($managerId)
    {
        return $this->select('
            payment_method,
            COUNT(*) as transaction_count,
            SUM(amount) as total_amount
        ')
            ->join('trial_players', 'trial_players.id = trial_payments.trial_player_id')
            ->where('trial_players.trial_manager_id', $managerId)
            ->groupBy('payment_method')
            ->findAll();
    }
}
<?php

namespace App\Models;

use CodeIgniter\Model;

class TrialPaymentModel extends Model
{
    protected $table            = 'trial_payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'trial_player_id',
        'trial_manager_id',
        'amount',
        'payment_method',
        'transaction_id',
        'notes',
        'collected_by',
        'payment_date',
        'created_at',
        'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];
}
