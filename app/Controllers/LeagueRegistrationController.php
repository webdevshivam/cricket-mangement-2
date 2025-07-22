<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\QrCodeSettingModel;
use App\Models\TrialcitiesModel;
use App\Models\LeaguePlayerModel;
use CodeIgniter\HTTP\ResponseInterface;
use Exception;

class LeagueRegistrationController extends BaseController
{
    public function index()
    {
        $model = new TrialcitiesModel();
        $qrCodeSetting = new QrCodeSettingModel();
        $data['qr_code_setting'] = $qrCodeSetting->first();
        $data['trial_cities'] = $model->where('status', 'enabled')->findAll();
        return view('frontend/league/registration', $data);
    }

    public function register()
    {
        $validation = \Config\Services::validation();

        $validation->setRules([
            'name' => 'required|min_length[3]',
            'email' => 'required|valid_email',
            'mobile' => 'required|min_length[10]|max_length[10]',
            'age' => 'required|integer|greater_than[7]',
            'cricketer_type' => 'required',
            'age_group' => 'required',
            'state' => 'required',
            'city' => 'required',
            
            'aadhar_document' => 'uploaded[aadhar_document]|max_size[aadhar_document,5120]|ext_in[aadhar_document,pdf,jpg,jpeg,png]',
            'marksheet_document' => 'uploaded[marksheet_document]|max_size[marksheet_document,5120]|ext_in[marksheet_document,pdf]',
            'dob_proof' => 'uploaded[dob_proof]|max_size[dob_proof,5120]|ext_in[dob_proof,pdf,jpg,jpeg,png]',
            'photo' => 'uploaded[photo]|max_size[photo,2048]|ext_in[photo,jpg,jpeg,png]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        // Handle file uploads
        $uploadPath = WRITEPATH . 'uploads/league_documents/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $uploadedFiles = [];
        $fileFields = ['aadhar_document', 'marksheet_document', 'dob_proof', 'photo'];

        foreach ($fileFields as $field) {
            $file = $this->request->getFile($field);
            if ($file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move($uploadPath, $newName);
                $uploadedFiles[$field] = $newName;
            }
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'mobile' => $this->request->getPost('mobile'),
            'age' => $this->request->getPost('age'),
            'cricketer_type' => $this->request->getPost('cricketer_type'),
            'age_group' => $this->request->getPost('age_group'),
            'state' => $this->request->getPost('state'),
            'city' => $this->request->getPost('city'),
            
            'aadhar_document' => $uploadedFiles['aadhar_document'] ?? null,
            'marksheet_document' => $uploadedFiles['marksheet_document'] ?? null,
            'dob_proof' => $uploadedFiles['dob_proof'] ?? null,
            'photo' => $uploadedFiles['photo'] ?? null,
            'payment_status' => 'no_payment'
        ];

        $model = new LeaguePlayerModel();
        if ($model->insert($data) === false) {
            return redirect()->back()->with('error', 'Registration failed. Please try again.');
        } else {
            return redirect()->to('/league-registration')->with('success', 'Registration successful!');
        }
    }

    public function adminIndex()
    {
        $model = new LeaguePlayerModel();
        $trialCitiesModel = new TrialcitiesModel();

        // Get search filters
        $phone = $this->request->getGet('phone');
        $paymentStatus = $this->request->getGet('payment_status');
        $trialCity = $this->request->getGet('trial_city');
        $ageGroup = $this->request->getGet('age_group');

        // Build query
        $builder = $model->select('league_players.*');

        if ($phone) {
            $builder->like('league_players.mobile', $phone);
        }

        if ($paymentStatus && $paymentStatus !== 'all') {
            $builder->where('league_players.payment_status', $paymentStatus);
        }

        

        if ($ageGroup) {
            $builder->where('league_players.age_group', $ageGroup);
        }

        $data['registrations'] = $builder->orderBy('league_players.id', 'DESC')->paginate(20);
        $data['pager'] = $model->pager;
        $data['trial_cities'] = $trialCitiesModel->where('status', 'enabled')->findAll();

        // Pass filter values to view
        $data['phone'] = $phone;
        $data['payment_status'] = $paymentStatus;
        $data['trial_city'] = $trialCity;
        $data['age_group'] = $ageGroup;

        return view('admin/league/registration', $data);
    }

    public function updatePaymentStatus()
    {
        $this->response->setHeader('Content-Type', 'application/json');

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }

        try {
            $data = $this->request->getJSON(true);

            if (empty($data['id']) || empty($data['payment_status'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Missing required data'
                ]);
            }

            $model = new LeaguePlayerModel();
            $player = $model->find($data['id']);

            if (!$player) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Player not found'
                ]);
            }

            $validStatuses = ['no_payment', 'partial', 'full'];
            if (!in_array($data['payment_status'], $validStatuses)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Invalid payment status'
                ]);
            }

            $updateData = [
                'payment_status' => $data['payment_status'],
                'verified_at' => date('Y-m-d H:i:s')
            ];

            $update = $model->update($data['id'], $updateData);

            if ($update) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Payment status updated successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to update payment status'
                ]);
            }
        } catch (Exception $e) {
            log_message('error', 'League payment status update error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while updating payment status'
            ]);
        }
    }

    public function viewDocument($playerId, $documentType)
    {
        $model = new LeaguePlayerModel();
        $player = $model->find($playerId);

        if (!$player || !isset($player[$documentType])) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Document not found');
        }

        $filePath = WRITEPATH . 'uploads/league_documents/' . $player[$documentType];

        if (!file_exists($filePath)) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('File not found');
        }

        $mimeType = mime_content_type($filePath);

        return $this->response
            ->setHeader('Content-Type', $mimeType)
            ->setHeader('Content-Disposition', 'inline; filename="' . $player[$documentType] . '"')
            ->setBody(file_get_contents($filePath));
    }

    public function deletePlayer()
    {
        $this->response->setHeader('Content-Type', 'application/json');

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
        }

        try {
            $data = $this->request->getJSON(true);

            if (empty($data['player_id'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Player ID is required'
                ]);
            }

            $model = new LeaguePlayerModel();
            $player = $model->find($data['player_id']);

            if (!$player) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Player not found'
                ]);
            }

            // Delete associated documents
            $uploadPath = WRITEPATH . 'uploads/league_documents/';
            $documentFields = ['aadhar_document', 'marksheet_document', 'dob_proof', 'photo'];

            foreach ($documentFields as $field) {
                if (!empty($player[$field]) && file_exists($uploadPath . $player[$field])) {
                    unlink($uploadPath . $player[$field]);
                }
            }

            if ($model->delete($data['player_id'])) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Player deleted successfully'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Failed to delete player'
                ]);
            }
        } catch (Exception $e) {
            log_message('error', 'Delete league player error: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'An error occurred while deleting player'
            ]);
        }
    }
}
