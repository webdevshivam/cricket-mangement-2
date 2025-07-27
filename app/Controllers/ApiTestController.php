
<?php

namespace App\Controllers;

use App\Libraries\WeatherService;
use Exception;

class ApiTestController extends BaseController
{
    public function testWeatherApi()
    {
        try {
            $weatherService = new WeatherService();
            
            // Test with a known city
            $testCity = 'Mumbai';
            $testDate = date('Y-m-d', strtotime('+1 day'));
            
            $weather = $weatherService->getWeatherForecast($testCity, $testDate);
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Weather API is working',
                'test_data' => $weather
            ]);
            
        } catch (Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }
}
