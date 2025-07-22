<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TrialcitiesModel;
use CodeIgniter\HTTP\ResponseInterface;

class TrialCityController extends BaseController
{
    public function index()
    {


        $model = new TrialcitiesModel();
        $data['cities'] = $model->paginate(10);
        $data['pager']  = $model->pager;

        return view('admin/trial_cities/index', $data);
    }

    public function create()
    {
        return view('admin/trial_cities/create');
    }
    public function save()
    {
        $model =  new TrialcitiesModel();

        $data = [
            'city_name'   => $this->request->getPost('city_name'),
            'state'       => $this->request->getPost('state'),
            'trial_date'  => $this->request->getPost('trial_date'),
            'trial_venue' => $this->request->getPost('trial_venue'),
            'map_link'    => $this->request->getPost('map_link'),
            'status'      => $this->request->getPost('status'),
            'notes'       => $this->request->getPost('notes'),
            'created_at'  => date('Y-m-d H:i:s'),
        ];

        $model->insert($data);

        return redirect()->to('admin/manage-trial-cities/add')->with('success', 'Trial city added successfully.');
    }

    public function edit($id)
    {
        $model = new TrialcitiesModel();
        $data['city'] = $model->find($id);

        if (!$data['city']) {
            return redirect()->to('admin/manage-trial-cities')->with('error', 'City not found.');
        }

        return view('admin/trial_cities/edit', $data);
    }

    public function update($id)
    {
        $model = new TrialcitiesModel();

        $data = [
            'city_name'   => $this->request->getPost('city_name'),
            'state'       => $this->request->getPost('state'),
            'trial_date'  => $this->request->getPost('trial_date'),
            'trial_venue' => $this->request->getPost('trial_venue'),
            'map_link'    => $this->request->getPost('map_link'),
            'status'      => $this->request->getPost('status'),
            'notes'       => $this->request->getPost('notes'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        $model->update($id, $data);

        return redirect()->to('admin/manage-trial-cities')->with('success', 'Trial city updated successfully.');
    }

    public function delete($id)
    {
        $model = new TrialcitiesModel();

        $city = $model->find($id);
        if ($city) {
            $model->delete($id);
            return redirect()->to('admin/manage-trial-cities')->with('success', 'City deleted.');
        } else {
            return redirect()->to('admin/manage-trial-cities')->with('error', 'City not found.');
        }
    }
}
