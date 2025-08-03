<?php

namespace App\Models;

use CodeIgniter\Model;

class TrialPlayerModel extends Model
{
  protected $table            = 'trial_players';
  protected $primaryKey       = 'id';

  protected $allowedFields    = [
    'name',
    'age',
    'mobile',
    'email',
    'state_id',
    'city',
    'trial_city_id',
    'trial_manager_id',
    'registered_by_tm',
    'cricket_type',
    'payment_status',
    'verified_at',
    'trial_completed',
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
