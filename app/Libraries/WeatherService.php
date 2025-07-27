<?php

namespace App\Libraries;

use GuzzleHttp\Client;
use Exception;

class WeatherService
{
    private $client;
    private $apiKey;
	private $baseUrl;

    public function __construct()
    {
        $this->client = new Client();
        
        // Load API settings from database
        $apiSettingModel = new \App\Models\ApiSettingModel();
        $settings = $apiSettingModel->getSettings();
        
        $this->apiKey = $settings['openweather_api_key'] ?? 'demo_key';
        $this->baseUrl = $settings['openweather_api_url'] ?? "https://api.openweathermap.org/data/2.5";

        // Log API key status for debugging
        if ($this->apiKey === 'demo_key' || empty($this->apiKey)) {
            log_message('warning', 'OpenWeather API key not configured properly');
        } else {
            log_message('info', 'OpenWeather API key loaded successfully');
        }
    }

    /**
     * Get weather forecast for a city and date
     */
    public function getWeatherForecast($cityName, $trialDate)
    {
        try {
            // Check if API is enabled and configured
            $apiSettingModel = new \App\Models\ApiSettingModel();
            $settings = $apiSettingModel->getSettings();
            
            if (!$settings['openweather_enabled'] || empty($this->apiKey) || $this->apiKey === 'demo_key') {
                log_message('info', 'OpenWeather API disabled or not configured, using default analysis');
                return $this->getDefaultWeatherAnalysis($cityName, $trialDate);
            }

            $daysFromNow = (strtotime($trialDate) - time()) / (24 * 60 * 60);

            // If trial date is more than 5 days away, use current weather as approximation
            if ($daysFromNow > 5) {
                $url = $this->baseUrl . "/weather";
            } else {
                $url = $this->baseUrl . "/forecast";
            }

            log_message('info', 'Making weather API request to: ' . $url . ' for city: ' . $cityName);

            $response = $this->client->get($url, [
                'query' => [
                    'q' => $cityName,
                    'appid' => $this->apiKey,
                    'units' => 'metric'
                ],
                'timeout' => 10
            ]);

            $data = json_decode($response->getBody(), true);
            
            if (!$data) {
                throw new Exception('Invalid API response');
            }

            if ($daysFromNow > 5) {
                return $this->processCurrentWeather($data, $trialDate);
            } else {
                return $this->processForecastWeather($data, $trialDate);
            }

        } catch (Exception $e) {
            log_message('error', 'Weather API Error: ' . $e->getMessage());
            log_message('error', 'API Key used: ' . substr($this->apiKey, 0, 8) . '...');
            // Return default analysis if API fails
            return $this->getDefaultWeatherAnalysis($cityName, $trialDate);
        }
    }

    /**
     * Process current weather data for future predictions
     */
    private function processCurrentWeather($data, $trialDate)
    {
        if (!isset($data['weather'])) {
            return $this->getDefaultWeatherAnalysis('Unknown', $trialDate);
        }

        $weather = $data['weather'][0];
        $main = $data['main'];

        return [
            'temperature' => $main['temp'] ?? 25,
            'humidity' => $main['humidity'] ?? 50,
            'description' => $weather['description'] ?? 'clear sky',
            'main' => $weather['main'] ?? 'Clear',
            'city' => $data['name'] ?? 'Unknown'
        ];
    }

    /**
     * Process forecast weather data
     */
    private function processForecastWeather($data, $trialDate)
    {
        if (!isset($data['list'])) {
            return $this->getDefaultWeatherAnalysis('Unknown', $trialDate);
        }

        $targetDate = date('Y-m-d', strtotime($trialDate));

        // Find forecast closest to trial date
        foreach ($data['list'] as $forecast) {
            $forecastDate = date('Y-m-d', $forecast['dt']);
            if ($forecastDate === $targetDate) {
                $weather = $forecast['weather'][0];
                $main = $forecast['main'];

                return [
                    'temperature' => $main['temp'] ?? 25,
                    'humidity' => $main['humidity'] ?? 50,
                    'description' => $weather['description'] ?? 'clear sky',
                    'main' => $weather['main'] ?? 'Clear',
                    'city' => $data['city']['name'] ?? 'Unknown'
                ];
            }
        }

        // If no exact match, use first forecast
        $forecast = $data['list'][0];
        $weather = $forecast['weather'][0];
        $main = $forecast['main'];

        return [
            'temperature' => $main['temp'] ?? 25,
            'humidity' => $main['humidity'] ?? 50,
            'description' => $weather['description'] ?? 'clear sky',
            'main' => $weather['main'] ?? 'Clear',
            'city' => $data['city']['name'] ?? 'Unknown'
        ];
    }

    /**
     * Get default weather analysis when API is unavailable
     */
    private function getDefaultWeatherAnalysis($cityName, $trialDate)
    {
        $month = date('n', strtotime($trialDate));

        // Basic seasonal analysis for India
        if ($month >= 6 && $month <= 9) {
            // Monsoon season
            return [
                'temperature' => 28,
                'humidity' => 80,
                'description' => 'monsoon season',
                'main' => 'Rain',
                'city' => $cityName
            ];
        } elseif ($month >= 10 && $month <= 2) {
            // Winter season
            return [
                'temperature' => 22,
                'humidity' => 60,
                'description' => 'clear sky',
                'main' => 'Clear',
                'city' => $cityName
            ];
        } else {
            // Summer season
            return [
                'temperature' => 35,
                'humidity' => 40,
                'description' => 'hot weather',
                'main' => 'Clear',
                'city' => $cityName
            ];
        }
    }

    /**
     * AI-powered weather analysis and recommendations
     */
    public function analyzeWeatherForTrial($weatherData, $trialDate)
    {
        $recommendations = [];
        $riskLevel = 'low';
        $shouldDelay = false;

        // Analyze temperature
        if ($weatherData['temperature'] > 40) {
            $recommendations[] = "⚠️ Extreme heat expected ({$weatherData['temperature']}°C). Consider morning slots or indoor venues.";
            $riskLevel = 'high';
        } elseif ($weatherData['temperature'] > 35) {
            $recommendations[] = "🌡️ Hot weather expected ({$weatherData['temperature']}°C). Ensure adequate hydration arrangements.";
            $riskLevel = ($riskLevel === 'low') ? 'medium' : $riskLevel;
        } elseif ($weatherData['temperature'] < 10) {
            $recommendations[] = "🥶 Cold weather expected ({$weatherData['temperature']}°C). Consider afternoon slots for better temperature.";
            $riskLevel = ($riskLevel === 'low') ? 'medium' : $riskLevel;
        }

        // Analyze precipitation and humidity
        $rainKeywords = ['rain', 'drizzle', 'shower', 'thunderstorm', 'storm'];
        $isRainy = false;

        foreach ($rainKeywords as $keyword) {
            if (stripos($weatherData['description'], $keyword) !== false || 
                stripos($weatherData['main'], $keyword) !== false) {
                $isRainy = true;
                break;
            }
        }

        if ($isRainy || $weatherData['humidity'] > 85) {
            $recommendations[] = "🌧️ High chance of rain/storms. Strong recommendation to delay or arrange covered venue.";
            $riskLevel = 'high';
            $shouldDelay = true;
        } elseif ($weatherData['humidity'] > 70) {
            $recommendations[] = "☁️ High humidity expected. Consider backup indoor arrangements.";
            $riskLevel = ($riskLevel === 'low') ? 'medium' : $riskLevel;
        }

        // Seasonal analysis
        $month = date('n', strtotime($trialDate));
        if ($month >= 6 && $month <= 9) {
            $recommendations[] = "🌦️ Monsoon season - historically high rainfall probability in this period.";
            $riskLevel = ($riskLevel === 'low') ? 'medium' : $riskLevel;
        }

        // Day of week analysis
        $dayOfWeek = date('N', strtotime($trialDate));
        if ($dayOfWeek >= 6) {
            $recommendations[] = "📅 Weekend date - expect higher participant turnout despite weather conditions.";
        }

        // Generate overall recommendation
        if ($shouldDelay) {
            $overallAdvice = "🚨 STRONGLY RECOMMEND RESCHEDULING: Weather conditions pose significant risk to trial operations.";
        } elseif ($riskLevel === 'high') {
            $overallAdvice = "⚠️ PROCEED WITH CAUTION: Consider backup plans and safety measures.";
        } elseif ($riskLevel === 'medium') {
            $overallAdvice = "✅ GOOD TO PROCEED: Minor weather considerations noted.";
        } else {
            $overallAdvice = "✅ EXCELLENT CONDITIONS: Ideal weather for outdoor trials.";
        }

        return [
            'risk_level' => $riskLevel,
            'should_delay' => $shouldDelay,
            'overall_advice' => $overallAdvice,
            'recommendations' => $recommendations,
            'weather_summary' => "Expected: {$weatherData['description']}, {$weatherData['temperature']}°C, {$weatherData['humidity']}% humidity"
        ];
    }
}