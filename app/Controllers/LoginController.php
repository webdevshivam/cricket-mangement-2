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
            'identity' => 'required|min_length[3]',
            'password' => 'required|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            $errorMessage = implode(' ', $errors);
            return redirect()->to('/login')->withInput()->with('error', $errorMessage);
        }

        $identity = trim($this->request->getPost('identity'));
        $password = $this->request->getPost('password');

        // Basic input sanitization
        if (empty($identity) || empty($password)) {
            return redirect()->to('/login')->withInput()->with('error', 'Please enter both email/mobile and password.');
        }

        $userModel = new UserModel();

        try {
            // Search by email or mobile
            $user = $userModel->where('email', $identity)
                ->orWhere('mobile', $identity)
                ->first();
        } catch (\Exception $e) {
            log_message('error', 'Database error during login: ' . $e->getMessage());
            return redirect()->to('/login')->with('error', 'System error. Please try again later.');
        }

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
            return redirect()->to('/login')->withInput()->with('error', 'Invalid email/mobile or password. Please try again.');
        }
    }
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}
