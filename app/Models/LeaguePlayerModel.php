<?php

// Adding payment status validation to LeaguePlayerModel.
namespace App\Models;

use CodeIgniter\Model;

class LeaguePlayerModel extends Model
{
    protected $table            = 'league_players';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name', 'email', 'mobile', 'age', 'cricketer_type', 'age_group', 
        'state', 'city', 'aadhar_document', 'marksheet_document', 
        'dob_proof', 'photo', 'payment_status', 'verified_at'
    ];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'payment_status' => 'in_list[unpaid,paid]'
    ];
    protected $validationMessages   = [
        'payment_status' => [
            'in_list' => 'Payment status must be either unpaid or paid'
        ]
    ];
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