<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PlayersModel;
use App\Models\TrialcitiesModel;
use CodeIgniter\HTTP\ResponseInterface;

class AdminPlayerController extends BaseController
{
    public function index()
    {
        $model = new \App\Models\PlayersModel();

        // Get filters
        $city           = $this->request->getGet('city');
        $cricketer_type = $this->request->getGet('cricketer_type');
        $payment_status = $this->request->getGet('payment_status');
        $date           = $this->request->getGet('date');


        if ($city)           $model->where('city', $city);
        if ($cricketer_type) $model->where('cricketer_type', $cricketer_type);
        if ($payment_status) $model->where('payment_status', $payment_status);
        if ($date)           $model->where('date', $date);

        $model->orderBy('id', 'DESC');
        $players = $model->paginate(10);
        $pager   = $model->pager;
        $grades = (new \App\Models\GradeModel())->where('status', 'active')->findAll();

        return view('admin/players/index', [
            'players'        => $players,
            'pager'          => $pager,
            'city'           => $city,
            'cricketer_type' => $cricketer_type,
            'payment_status' => $payment_status,
            'date'           => $date,
            'grades'         => $grades
        ]);
    }


    public function updatePaymentStatus()
    {
        helper('email');
        if ($this->request->isAJAX()) {
            $data = $this->request->getJSON(true); // associative array

            $playerModel = new \App\Models\PlayersModel();
            $player = $playerModel->find($data['id']);

            if (!$player) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Player not found'
                ]);
            }

            $update = $playerModel->update($data['id'], [
                'payment_status' => $data['payment_status']
            ]);

            if ($update) {
                // ✅ Load helper if not already loaded
                helper('custom_mail');

                // ✅ Build email content
                $statusText = ucfirst($data['payment_status']);
                $subject = "Payment Status Updated";
                $message = "
                <p>Hello <strong>{$player['name']}</strong>,</p>
                <p>Your payment status has been updated to: <strong style='color:green;'>$statusText</strong>.</p>
                <p>Thank you for being part of MegaStar League!</p>
                <br><p>Regards,<br>MegaStar League Team</p>
            ";

                // ✅ Send the email
                sendCustomMail($player['email'], $subject, $message);
            }

            return $this->response->setJSON([
                'success' => $update
            ]);
        }
    }


    public function view($id)
    {
        $model = new \App\Models\PlayersModel();
        $player = $model->find($id);

        if (!$player) {
            echo "No player found with ID: $id";
            return;
        }

        return view('admin/players/view', ['player' => $player]);
    }

    public function create()
    {
        $model = new TrialcitiesModel();
        $data['trialCities'] = $model->findAll();
        // Logic to show the form for adding a new player
        return view('admin/players/add', $data);
    }

    public function save()
    {
        $model = new PlayersModel();


        // Validate the input data
        //check email exist or phont exist or note

        $validation = \Config\Services::validation();
        $validation->setRules([
            'name'           => 'required|min_length[3]|max_length[50]',
            'age'            => 'required|integer|greater_than[0]',
            'mobile_number'  => 'required|regex_match[/^[0-9]{10}$/]',
            'email'          => 'required|valid_email|is_unique[players.email]',
            'cricketer_type' => 'required',
            'state'          => 'required|min_length[2]|max_length[50]',
            'city'           => 'required|min_length[2]|max_length[50]',
            'trial_city'     => 'required',
        ]);

        if (!$this->validate($validation->getRules())) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'name'           => $this->request->getPost('name'),
            'age'            => $this->request->getPost('age'),
            'mobile_number'  => $this->request->getPost('mobile_number'),
            'email'          => $this->request->getPost('email'),
            'cricketer_type' => $this->request->getPost('cricketer_type'),
            'state'          => $this->request->getPost('state'),
            'city'           => $this->request->getPost('city'),
            'trial_city'     => $this->request->getPost('trial_city'),
            'payment_status' => 'pending', // Default status
        ];


        if ($model->save($data)) {
            return redirect()->to(base_url('admin/players'))->with('success', 'Player added successfully.');
        } else {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }
    }

    public function deleteMultiple()
    {
        $ids = $this->request->getPost('selected');

        if (!empty($ids)) {
            $model = new \App\Models\PlayersModel();
            $model->whereIn('id', $ids)->delete();

            return redirect()->back()->with('success', 'Selected players deleted successfully.');
        }

        return redirect()->back()->with('error', 'No players selected for deletion.');
    }

    public function delete($id)
    {

        $model = new \App\Models\PlayersModel();
        $player = $model->find($id);

        if (!$player) {
            return redirect()->back()->with('error', 'Player not found.');
        }

        if ($model->delete($id)) {
            return redirect()->to(base_url('admin/players'))->with('success', 'Player deleted successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to delete player.');
        }
    }

    public function edit($id)
    {
        $model = new \App\Models\PlayersModel();
        $player = $model->find($id);

        if (!$player) {
            return redirect()->back()->with('error', 'Player not found.');
        }

        $trialModel = new \App\Models\TrialcitiesModel();
        $data['trialCities'] = $trialModel->findAll();

        return view('admin/players/edit', [
            'player' => $player,
            'trialCities' => $data['trialCities']
        ]);
    }

    public function update($id)
    {

        $model = new PlayersModel();

        // Validate the input data
        $validation = \Config\Services::validation();
        $validation->setRules([
            'name'           => 'required|min_length[3]|max_length[50]',
            'age'            => 'required|integer|greater_than[0]',
            'mobile_number'  => 'required|regex_match[/^[0-9]{10}$/]',
            'email'          => 'required|valid_email',
            'cricketer_type' => 'required',
            'state'          => 'required|min_length[2]|max_length[50]',
            'city'           => 'required|min_length[2]|max_length[50]',
            'trial_city'     => 'required',
        ]);

        if (!$this->validate($validation->getRules())) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }

        $data = [
            'name'           => $this->request->getPost('name'),
            'age'            => $this->request->getPost('age'),
            'mobile_number'  => $this->request->getPost('mobile_number'),
            'email'          => $this->request->getPost('email'),
            'cricketer_type' => $this->request->getPost('cricketer_type'),
            'state'          => $this->request->getPost('state'),
            'city'           => $this->request->getPost('city'),
            'trial_city'     => $this->request->getPost('trial_city'),
        ];

        if ($model->update($id, $data)) {
            return redirect()->to(base_url('admin/players'))->with('success', 'Player updated successfully.');
        } else {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }
    }
}
