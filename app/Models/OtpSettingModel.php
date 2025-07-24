<?php

namespace App\Models;

use CodeIgniter\Model;

class OtpSettingModel extends Model
{
    protected $table = 'otp_settings';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'trial_otp_enabled',
        'league_otp_enabled',
        'otp_expiry_minutes',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getSettings()
    {
        $settings = $this->first();
        if (!$settings) {
            // Create default settings if none exist
            $defaultSettings = [
                'trial_otp_enabled' => 0,
                'league_otp_enabled' => 0,
                'otp_expiry_minutes' => 10
            ];
            $this->insert($defaultSettings);
            return $defaultSettings;
        }
        return $settings;
    }

    public function updateSettings($data)
    {
        $settings = $this->first();
        if ($settings) {
            return $this->update($settings['id'], $data);
        } else {
            return $this->insert($data);
        }
    }
}
