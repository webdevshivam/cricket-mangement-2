<?php

namespace App\Models;

use CodeIgniter\Model;

class TrialManagerModel extends Model
{
    protected $table            = 'trial_managers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields    = [
        'name',
        'email',
        'password',
        'trial_name',
        'trial_city_id',
        'status',
        'created_by'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'name'       => 'required|min_length[3]|max_length[100]',
        'email'      => 'required|valid_email|is_unique[trial_managers.email,id,{id}]',
        'trial_name' => 'required|min_length[3]|max_length[150]',
        'status'     => 'in_list[active,inactive]'
    ];

    protected $validationMessages = [
        'email' => [
            'is_unique' => 'This email is already registered as a trial manager.'
        ]
    ];

    protected $skipValidation = false;

    // Get trial manager with city details
    public function getWithCity($id)
    {
        return $this->select('trial_managers.*, trial_cities.city_name')
            ->join('trial_cities', 'trial_cities.id = trial_managers.trial_city_id', 'left')
            ->where('trial_managers.id', $id)
            ->first();
    }

    // Get all trial managers with city details
    public function getAllWithCity()
    {
        return $this->select('trial_managers.*, trial_cities.city_name, COUNT(tp.id) as total_players')
            ->join('trial_cities', 'trial_cities.id = trial_managers.trial_city_id', 'left')
            ->join('trial_players tp', 'tp.trial_manager_id = trial_managers.id', 'left')
            ->groupBy('trial_managers.id')
            ->orderBy('trial_managers.created_at', 'DESC')
            ->findAll();
    }

    // Get trial manager by email
    public function getByEmail($email)
    {
        return $this->where('email', $email)->first();
    }

    // Generate random password
    public function generatePassword($length = 10)
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $password;
    }
}
