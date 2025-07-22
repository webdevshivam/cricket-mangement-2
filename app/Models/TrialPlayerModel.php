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
    'cricket_type',
    'created_at',
  ];

  protected $useTimestamps    = true;
  protected $createdField     = 'created_at';
  protected $updatedField     = '';

  protected $returnType       = 'array';

  // No validation applied
  protected $skipValidation   = true;
}
