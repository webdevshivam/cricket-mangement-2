<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class LoginController extends BaseController
{
    public function index()
    {
        return view('login');
    }

    public function login()
    {
        $session = session();
        $validation = \Config\Services::validation();

        // Validate input
        $rules = [
            'identity' => 'required',
            'password' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'All fields are required.');
        }

        $identity = $this->request->getPost('identity');
        $password = $this->request->getPost('password');

        $userModel = new UserModel();

        // Search by email or mobile
        $user = $userModel->where('email', $identity)
            ->orWhere('mobile', $identity)
            ->first();

        if ($user && password_verify($password, $user['password'])) {
            // Set session
            $session->set([
                'user_id' => $user['id'],
                'name'    => $user['name'],
                'role'    => $user['role'],
                'isLoggedIn' => true
            ]);

            // Redirect based on role
            switch ($user['role']) {
                case 'admin':
                    return redirect()->to('/admin/dashboard');
                case 'manager':
                    return redirect()->to('/manager/dashboard');
                case 'coach':
                    return redirect()->to('/coach/dashboard');
                case 'player':
                    return redirect()->to('/player/dashboard');
                default:
                    return redirect()->to('/');
            }
        } else {
            return redirect()->back()->with('error', 'Invalid credentials.');
        }
    }
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
