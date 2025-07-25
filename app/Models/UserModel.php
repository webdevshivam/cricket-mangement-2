<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $allowedFields = ['name', 'email', 'mobile', 'password', 'role', 'created_at', 'updated_at'];
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get all admin users
     */
    public function getAdmins()
    {
        return $this->where('role', 'admin')->findAll();
    }

    /**
     * Check if email exists (excluding current user)
     */
    public function isEmailExists($email, $excludeId = null)
    {
        $builder = $this->where('email', $email);
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        return $builder->countAllResults() > 0;
    }

    /**
     * Check if mobile exists (excluding current user)
     */
    public function isMobileExists($mobile, $excludeId = null)
    {
        $builder = $this->where('mobile', $mobile);
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        return $builder->countAllResults() > 0;
    }
}
