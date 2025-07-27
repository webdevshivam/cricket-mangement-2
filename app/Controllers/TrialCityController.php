<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TrialcitiesModel;
use App\Libraries\WeatherService;
use CodeIgniter\HTTP\ResponseInterface;

class TrialCityController extends BaseController
{
    public function index()
    {
        $model = new TrialcitiesModel();
        $data['cities'] = $model->findAll(); // Get all cities for calendar
        $data['paginatedCities'] = $model->paginate(10); // Paginated cities for table
        $data['pager']  = $model->pager;

        return view('admin/trial_cities/index', $data);
    }

    public function create()
    {
        return view('admin/trial_cities/create');
    }

    /**
     * Get weather analysis for trial city via AJAX
     */
    public function getWeatherAnalysis()
    {
        try {
            log_message('info', 'Starting weather analysis for city: ' . $cityName . ', date: ' . $trialDate);

            $cityName = $this->request->getPost('city_name');
            $trialDate = $this->request->getPost('trial_date');

            if (!$cityName || !$trialDate) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'City name and trial date are required'
                ]);
            }

            $weatherService = new WeatherService();
            $weather = $weatherService->getWeatherForecast($cityName, $trialDate);
            $analysis = $weatherService->analyzeWeatherForTrial($weather, $trialDate);

            log_message('info', 'Weather analysis completed successfully');

            return $this->response->setJSON([
                'success' => true,
                'weather' => $weather,
                'analysis' => $analysis
            ]);

        } catch (Exception $e) {
            log_message('error', 'Weather Analysis Error: ' . $e->getMessage());
            log_message('error', 'Request data - City: ' . $cityName . ', Date: ' . $trialDate);

            return $this->response->setJSON([
                'success' => false,
                'error' => 'Weather service error: ' . $e->getMessage(),
                'weather' => $this->getDefaultWeatherData($cityName ?? 'Unknown'),
                'analysis' => $this->getDefaultAnalysis()
            ]);
        }
    }

    private function getDefaultWeatherData($cityName)
    {
        return [
            'temperature' => 25,
            'humidity' => 60,
            'description' => 'moderate weather',
            'main' => 'Clear',
            'city' => $cityName
        ];
    }

    private function getDefaultAnalysis()
    {
        return [
            'risk_level' => 'medium',
            'should_delay' => false,
            'overall_advice' => '⚠️ Weather data unavailable. Please check conditions manually.',
            'recommendations' => ['Weather service unavailable. Please verify local conditions before proceeding.']
        ];
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