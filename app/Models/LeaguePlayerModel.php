<?php

namespace App\Models;

use CodeIgniter\Model;

class LeaguePlayerModel extends Model
{
    protected $table            = 'league_players';
    protected $primaryKey       = 'id';

    protected $allowedFields    = [
        'name',
        'age',
        'mobile',
        'email',
        'cricketer_type',
        'age_group',
        'state',
        'city',
        'trial_city_id',
        'aadhar_document',
        'marksheet_document',
        'dob_proof',
        'photo',
        'payment_status',
        'verified_at',
        'created_at',
    ];

    // Set default values
    protected $beforeInsert = ['setDefaults'];

    protected function setDefaults(array $data)
    {
        if (!isset($data['data']['payment_status'])) {
            $data['data']['payment_status'] = 'no_payment';
        }
        return $data;
    }

    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = '';

    protected $returnType       = 'array';

    // No validation applied
    protected $skipValidation   = true;
}
