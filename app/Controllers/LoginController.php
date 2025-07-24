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
            // Set session with extended expiration for admin users
            $sessionData = [
                'user_id' => $user['id'],
                'name'    => $user['name'],
                'role'    => $user['role'],
                'isLoggedIn' => true,
                'login_time' => time(),
                'last_activity' => time()
            ];
            
            // For admin users, set persistent session
            if ($user['role'] === 'admin') {
                $sessionData['persistent_login'] = true;
                $sessionData['remember_until'] = time() + (30 * 24 * 60 * 60); // 30 days
            }
            
            $session->set($sessionData);
            
            // Set remember me cookie for admin users
            if ($user['role'] === 'admin') {
                $this->setRememberCookie($user['id']);
            }

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
        // Remove remember cookie if it exists
        delete_cookie('admin_remember');
        session()->destroy();
        return redirect()->to('/login');
    }
    
    private function setRememberCookie($userId)
    {
        $expires = time() + (30 * 24 * 60 * 60); // 30 days
        $token = bin2hex(random_bytes(32));
        
        $cookieData = [
            'user_id' => $userId,
            'token' => $token,
            'expires' => $expires
        ];
        
        $cookieValue = base64_encode(json_encode($cookieData));
        
        set_cookie([
            'name' => 'admin_remember',
            'value' => $cookieValue,
            'expire' => $expires,
            'secure' => false, // Set to true in production with HTTPS
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
    }
}
