
<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use App\Models\UserModel;

class PersistentLoginFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        
        // If user is already logged in, update last activity
        if ($session->get('isLoggedIn')) {
            // Update last activity time
            $session->set('last_activity', time());
            
            // Check if session has expired for non-persistent logins
            if (!$session->get('persistent_login')) {
                $loginTime = $session->get('login_time', 0);
                $maxInactivity = 2 * 60 * 60; // 2 hours for regular users
                
                if ((time() - $loginTime) > $maxInactivity) {
                    $session->destroy();
                    return redirect()->to('/login')->with('error', 'Session expired. Please login again.');
                }
            }
            
            return;
        }
        
        // Check for persistent login cookie for admin users
        $remember = $request->getCookie('admin_remember');
        if ($remember) {
            $userModel = new UserModel();
            
            try {
                // Decrypt and validate the remember token
                $data = json_decode(base64_decode($remember), true);
                
                if ($data && isset($data['user_id'], $data['token'], $data['expires'])) {
                    // Check if token hasn't expired
                    if (time() < $data['expires']) {
                        $user = $userModel->find($data['user_id']);
                        
                        // Verify user exists and is admin
                        if ($user && $user['role'] === 'admin') {
                            // Regenerate session for security
                            $session->regenerate();
                            
                            // Set session data
                            $session->set([
                                'user_id' => $user['id'],
                                'name' => $user['name'],
                                'role' => $user['role'],
                                'isLoggedIn' => true,
                                'login_time' => time(),
                                'last_activity' => time(),
                                'persistent_login' => true,
                                'remember_until' => $data['expires']
                            ]);
                            
                            // Refresh the remember cookie
                            $this->setRememberCookie($user['id']);
                        }
                    } else {
                        // Remove expired cookie
                        $response = service('response');
                        $response->deleteCookie('admin_remember');
                    }
                }
            } catch (\Exception $e) {
                // Remove invalid cookie
                $response = service('response');
                $response->deleteCookie('admin_remember');
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing to do here
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
        
        $response = service('response');
        $response->setCookie([
            'name' => 'admin_remember',
            'value' => $cookieValue,
            'expire' => $expires,
            'secure' => false, // Set to true in production with HTTPS
            'httponly' => true,
            'samesite' => 'Strict'
        ]);
    }
}
