
<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\OtpSettingModel;

class OtpSettingController extends BaseController
{
    public function index()
    {
        $model = new OtpSettingModel();
        $data['settings'] = $model->getSettings();
        return view('admin/otp_settings/index', $data);
    }

    public function update()
    {
        $model = new OtpSettingModel();
        
        $data = [
            'trial_otp_enabled' => $this->request->getPost('trial_otp_enabled') ? 1 : 0,
            'league_otp_enabled' => $this->request->getPost('league_otp_enabled') ? 1 : 0,
            'otp_expiry_minutes' => $this->request->getPost('otp_expiry_minutes') ?: 10
        ];

        if ($model->updateSettings($data)) {
            return redirect()->to('/admin/otp-settings')->with('success', 'OTP settings updated successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to update OTP settings.');
        }
    }
}
