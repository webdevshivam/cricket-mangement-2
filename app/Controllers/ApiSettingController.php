
<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ApiSettingModel;
use App\Models\UserModel;

class ApiSettingController extends BaseController
{
    public function index()
    {
        // Check if the user is logged in and has the admin role
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/unauthorized');
        }

        $model = new ApiSettingModel();
        $data['settings'] = $model->getSettings();
        return view('admin/api_settings/index', $data);
    }

    public function update()
    {
        // Check if the user is logged in and has the admin role
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/unauthorized');
        }

        $model = new ApiSettingModel();

        $data = [
            // OpenWeather Settings
            'openweather_enabled' => $this->request->getPost('openweather_enabled') ? 1 : 0,
            'openweather_api_key' => $this->request->getPost('openweather_api_key') ?: '',
            'openweather_api_url' => $this->request->getPost('openweather_api_url') ?: 'https://api.openweathermap.org/data/2.5',
            
            // Razorpay Settings
            'razorpay_enabled' => $this->request->getPost('razorpay_enabled') ? 1 : 0,
            'razorpay_key_id' => $this->request->getPost('razorpay_key_id') ?: '',
            'razorpay_key_secret' => $this->request->getPost('razorpay_key_secret') ?: '',
            
            // SMS Settings
            'sms_enabled' => $this->request->getPost('sms_enabled') ? 1 : 0,
            'sms_api_key' => $this->request->getPost('sms_api_key') ?: '',
            'sms_api_secret' => $this->request->getPost('sms_api_secret') ?: '',
            'sms_api_url' => $this->request->getPost('sms_api_url') ?: '',
            
            // Email Settings
            'email_enabled' => $this->request->getPost('email_enabled') ? 1 : 0,
            'email_host' => $this->request->getPost('email_host') ?: 'smtp.gmail.com',
            'email_port' => $this->request->getPost('email_port') ?: 587,
            'email_username' => $this->request->getPost('email_username') ?: '',
            'email_password' => $this->request->getPost('email_password') ?: '',
            'email_encryption' => $this->request->getPost('email_encryption') ?: 'tls',
            'email_from_address' => $this->request->getPost('email_from_address') ?: '',
            'email_from_name' => $this->request->getPost('email_from_name') ?: 'Cricket League'
        ];

        if ($model->updateSettings($data)) {
            return redirect()->to('/admin/api-settings')->with('success', 'API settings updated successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to update API settings.');
        }
    }

    public function changePassword()
    {
        // Check if the user is logged in and has the admin role
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            return redirect()->to('/unauthorized');
        }

        $userModel = new UserModel();
        $userId = session()->get('user_id');
        
        $currentPassword = $this->request->getPost('current_password');
        $newPassword = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_password');

        // Validation
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            return redirect()->back()->with('error', 'All password fields are required.');
        }

        if ($newPassword !== $confirmPassword) {
            return redirect()->back()->with('error', 'New password and confirm password do not match.');
        }

        if (strlen($newPassword) < 6) {
            return redirect()->back()->with('error', 'New password must be at least 6 characters long.');
        }

        // Get current user
        $user = $userModel->find($userId);
        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        // Verify current password
        if (!password_verify($currentPassword, $user['password'])) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        // Update password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        if ($userModel->update($userId, ['password' => $hashedPassword])) {
            return redirect()->back()->with('success', 'Password changed successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to change password.');
        }
    }
}
