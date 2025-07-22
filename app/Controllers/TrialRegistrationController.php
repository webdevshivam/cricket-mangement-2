<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\QrCodeSettingModel;
use App\Models\TrialcitiesModel;
use App\Models\TrialPlayerModel;
use CodeIgniter\HTTP\ResponseInterface;

class TrialRegistrationController extends BaseController
{
    public function index()
    {
        // Load the view for trial registration
        //all trial city_name

        $model = new TrialcitiesModel();
        $qrCodeSetting = new QrCodeSettingModel();
        $data['qr_code_setting'] = $qrCodeSetting->first();
        $data['trial_cities'] = $model->where('status', 'enabled')->findAll();
        return view('frontend/trial/registration', $data);
    }
    public function register()
    {
        //get form data
        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'mobile' => $this->request->getPost('phone'),
            'age' => $this->request->getPost('age'),
            'state_id' => $this->request->getPost('state'),
            'city' => $this->request->getPost('city'),
            'trial_city_id' => $this->request->getPost('trialCity'),
            'cricket_type' => $this->request->getPost('cricket_type'),
        ];
        $model = new TrialPlayerModel();
        if ($model->insert($data) === false) {
            echo "Error: " . $model->errors();
        } else {
            return redirect()->to('/trial-registration')->with('success', 'Registration successful!');
        }
    }

    public function adminIndex()
    {

        $model = new \App\Models\TrialPlayerModel();


        $data['registrations'] = $model->orderBy('id', 'DESC')->paginate(10);
        $data['pager'] = $model->pager;

        return view('admin/trial/registration', $data);
    }
}
