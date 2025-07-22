<?php

namespace App\Models;

use CodeIgniter\Model;

class QrCodeSettingModel extends Model
{
  protected $table = 'qr_code_settings';
  protected $primaryKey = 'id';
  protected $allowedFields = ['upi_handler_name', 'upi_number', 'upi_id', 'qr_code'];
}
