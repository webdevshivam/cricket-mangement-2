
<?php

namespace App\Models;

use CodeIgniter\Model;

class ApiSettingModel extends Model
{
    protected $table = 'api_settings';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'openweather_enabled',
        'openweather_api_key',
        'openweather_api_url',
        'razorpay_enabled',
        'razorpay_key_id',
        'razorpay_key_secret',
        'sms_enabled',
        'sms_api_key',
        'sms_api_secret',
        'sms_api_url',
        'email_enabled',
        'email_host',
        'email_port',
        'email_username',
        'email_password',
        'email_encryption',
        'email_from_address',
        'email_from_name',
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
                'openweather_enabled' => 0,
                'openweather_api_key' => '',
                'openweather_api_url' => 'https://api.openweathermap.org/data/2.5',
                'razorpay_enabled' => 0,
                'razorpay_key_id' => '',
                'razorpay_key_secret' => '',
                'sms_enabled' => 0,
                'sms_api_key' => '',
                'sms_api_secret' => '',
                'sms_api_url' => '',
                'email_enabled' => 1,
                'email_host' => 'smtp.gmail.com',
                'email_port' => 587,
                'email_username' => '',
                'email_password' => '',
                'email_encryption' => 'tls',
                'email_from_address' => '',
                'email_from_name' => 'Cricket League'
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
