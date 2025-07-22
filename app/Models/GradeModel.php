<?php

namespace App\Models;

use CodeIgniter\Model;

class GradeModel extends Model
{
  protected $table = 'grades';
  protected $primaryKey = 'id';
  protected $allowedFields = ['title', 'description', 'league_fee', 'status'];
  protected $useTimestamps = true;
}
