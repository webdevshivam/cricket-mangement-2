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
        
        // Get search filters
        $phone = $this->request->getGet('phone');
        $paymentStatus = $this->request->getGet('payment_status');
        
        if ($phone) {
            $model->like('mobile', $phone);
        }
        
        if ($paymentStatus && $paymentStatus !== 'all') {
            $model->where('payment_status', $paymentStatus);
        }

        $data['registrations'] = $model->orderBy('id', 'DESC')->paginate(20);
        $data['pager'] = $model->pager;

        return view('admin/trial/registration', $data);
    }
    
    public function updatePaymentStatus()
    {
        helper('email');
        if ($this->request->isAJAX()) {
            $data = $this->request->getJSON(true);

            $model = new \App\Models\TrialPlayerModel();
            $player = $model->find($data['id']);

            if (!$player) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Player not found'
                ]);
            }

            $update = $model->update($data['id'], [
                'payment_status' => $data['payment_status'],
                'verified_at' => date('Y-m-d H:i:s')
            ]);

            if ($update) {
                // Load email helper if available
                if (function_exists('sendCustomMail')) {
                    helper('custom_mail');

                    // Build email content based on payment status
                    $statusMessages = [
                        'not_verified' => 'Your payment is pending verification. Please bring payment proof to the trial.',
                        'partial_paid' => 'Your partial payment (₹199) has been verified. You will receive a T-shirt. Please complete full payment for trial participation.',
                        'full_paid' => 'Your full payment has been verified! You are all set for the trial and will receive a T-shirt.'
                    ];

                    $subject = "Payment Status Update - MPCL Trial";
                    $message = "
                        <p>Hello <strong>{$player['name']}</strong>,</p>
                        <p>{$statusMessages[$data['payment_status']]}</p>
                        <p>Phone: {$player['mobile']}</p>
                        <p>Status: " . ucfirst(str_replace('_', ' ', $data['payment_status'])) . "</p>
                        <br><p>Regards,<br>MegaStar Premier Cricket League Team</p>
                    ";

                    sendCustomMail($player['email'], $subject, $message);
                }
            }

            return $this->response->setJSON([
                'success' => $update,
                'message' => $update ? 'Payment status updated successfully' : 'Failed to update payment status'
            ]);
        }
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Invalid request'
        ]);
    }
}
