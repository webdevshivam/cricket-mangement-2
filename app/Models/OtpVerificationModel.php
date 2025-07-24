<?php

namespace App\Models;

use CodeIgniter\Model;

class OtpVerificationModel extends Model
{
    protected $table = 'otp_verifications';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'email',
        'otp_code',
        'registration_type',
        'registration_data',
        'is_verified',
        'expires_at',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function generateOTP($email, $registrationType, $registrationData)
    {
        // Generate 6-digit OTP
        $otp = sprintf("%06d", mt_rand(1, 999999));

        // Delete any existing OTP for this email and registration type
        $this->where('email', $email)
            ->where('registration_type', $registrationType)
            ->delete();

        // Create new OTP record
        $data = [
            'email' => $email,
            'otp_code' => $otp,
            'registration_type' => $registrationType,
            'registration_data' => json_encode($registrationData),
            'is_verified' => 0,
            'expires_at' => date('Y-m-d H:i:s', strtotime('+10 minutes'))
        ];

        $this->insert($data);
        return $otp;
    }

    public function verifyOTP($email, $otp, $registrationType)
    {
        $record = $this->where('email', $email)
            ->where('otp_code', $otp)
            ->where('registration_type', $registrationType)
            ->where('is_verified', 0)
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->first();

        if ($record) {
            // Mark as verified
            $this->update($record['id'], ['is_verified' => 1]);
            return json_decode($record['registration_data'], true);
        }

        return false;
    }

    public function cleanExpiredOTPs()
    {
        $this->where('expires_at <', date('Y-m-d H:i:s'))->delete();
    }
}
