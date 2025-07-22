<?php

namespace App\Models;

use CodeIgniter\Model;

class GradeAssignModel extends Model
{
  protected $table = 'grade_assign';
  protected $primaryKey = 'id';

  protected $allowedFields = [
    'player_id',
    'grade_id',
    'assigned_at',
    'assigned_by',
    'status'
  ];

  protected $useTimestamps = false;

  protected $returnType = 'array';
}
