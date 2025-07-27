
<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class LocationController extends BaseController
{
    /**
     * Get all Indian states
     */
    public function getStates()
    {
        try {
            // Using Indian States API
            $states = [
                ['code' => 'AN', 'name' => 'Andaman and Nicobar Islands'],
                ['code' => 'AP', 'name' => 'Andhra Pradesh'],
                ['code' => 'AR', 'name' => 'Arunachal Pradesh'],
                ['code' => 'AS', 'name' => 'Assam'],
                ['code' => 'BR', 'name' => 'Bihar'],
                ['code' => 'CG', 'name' => 'Chhattisgarh'],
                ['code' => 'CH', 'name' => 'Chandigarh'],
                ['code' => 'DH', 'name' => 'Dadra and Nagar Haveli and Daman and Diu'],
                ['code' => 'DL', 'name' => 'Delhi'],
                ['code' => 'GA', 'name' => 'Goa'],
                ['code' => 'GJ', 'name' => 'Gujarat'],
                ['code' => 'HR', 'name' => 'Haryana'],
                ['code' => 'HP', 'name' => 'Himachal Pradesh'],
                ['code' => 'JK', 'name' => 'Jammu and Kashmir'],
                ['code' => 'JH', 'name' => 'Jharkhand'],
                ['code' => 'KA', 'name' => 'Karnataka'],
                ['code' => 'KL', 'name' => 'Kerala'],
                ['code' => 'LA', 'name' => 'Ladakh'],
                ['code' => 'LD', 'name' => 'Lakshadweep'],
                ['code' => 'MP', 'name' => 'Madhya Pradesh'],
                ['code' => 'MH', 'name' => 'Maharashtra'],
                ['code' => 'MN', 'name' => 'Manipur'],
                ['code' => 'ML', 'name' => 'Meghalaya'],
                ['code' => 'MZ', 'name' => 'Mizoram'],
                ['code' => 'NL', 'name' => 'Nagaland'],
                ['code' => 'OR', 'name' => 'Odisha'],
                ['code' => 'PB', 'name' => 'Punjab'],
                ['code' => 'PY', 'name' => 'Puducherry'],
                ['code' => 'RJ', 'name' => 'Rajasthan'],
                ['code' => 'SK', 'name' => 'Sikkim'],
                ['code' => 'TN', 'name' => 'Tamil Nadu'],
                ['code' => 'TS', 'name' => 'Telangana'],
                ['code' => 'TR', 'name' => 'Tripura'],
                ['code' => 'UP', 'name' => 'Uttar Pradesh'],
                ['code' => 'UK', 'name' => 'Uttarakhand'],
                ['code' => 'WB', 'name' => 'West Bengal']
            ];

            return $this->response->setJSON([
                'success' => true,
                'states' => $states
            ]);

        } catch (Exception $e) {
            log_message('error', 'States API Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Failed to load states'
            ]);
        }
    }

    /**
     * Get cities by state code
     */
    public function getCitiesByState($stateCode = null)
    {
        try {
            if (!$stateCode) {
                $stateCode = $this->request->getGet('state_code');
            }

            if (!$stateCode) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'State code is required'
                ]);
            }

            // Using a comprehensive cities database for India
            $citiesData = $this->getIndianCitiesData();
            
            $cities = isset($citiesData[$stateCode]) ? $citiesData[$stateCode] : [];

            return $this->response->setJSON([
                'success' => true,
                'cities' => $cities
            ]);

        } catch (Exception $e) {
            log_message('error', 'Cities API Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Failed to load cities'
            ]);
        }
    }

    /**
     * Get comprehensive Indian cities data
     */
    private function getIndianCitiesData()
    {
        return [
            'AP' => ['Visakhapatnam', 'Vijayawada', 'Guntur', 'Nellore', 'Kurnool', 'Rajahmundry', 'Kadapa', 'Kakinada', 'Anantapur', 'Tirupati'],
            'AR' => ['Itanagar', 'Naharlagun', 'Pasighat', 'Tawang', 'Ziro', 'Bomdila', 'Tezu', 'Seppa', 'Khonsa', 'Yingkiong'],
            'AS' => ['Guwahati', 'Silchar', 'Dibrugarh', 'Jorhat', 'Nagaon', 'Tinsukia', 'Tezpur', 'Bongaigaon', 'Dhubri', 'North Lakhimpur'],
            'BR' => ['Patna', 'Gaya', 'Bhagalpur', 'Muzaffarpur', 'Purnia', 'Darbhanga', 'Bihar Sharif', 'Arrah', 'Begusarai', 'Katihar'],
            'CG' => ['Raipur', 'Bhilai', 'Korba', 'Bilaspur', 'Durg', 'Rajnandgaon', 'Jagdalpur', 'Raigarh', 'Ambikapur', 'Mahasamund'],
            'DL' => ['New Delhi', 'Delhi', 'North Delhi', 'South Delhi', 'East Delhi', 'West Delhi', 'Central Delhi', 'North East Delhi', 'North West Delhi', 'South East Delhi'],
            'GA' => ['Panaji', 'Vasco da Gama', 'Margao', 'Mapusa', 'Ponda', 'Bicholim', 'Curchorem', 'Sanquelim', 'Cuncolim', 'Quepem'],
            'GJ' => ['Ahmedabad', 'Surat', 'Vadodara', 'Rajkot', 'Bhavnagar', 'Jamnagar', 'Junagadh', 'Gandhinagar', 'Anand', 'Nadiad'],
            'HR' => ['Faridabad', 'Gurgaon', 'Panipat', 'Ambala', 'Yamunanagar', 'Rohtak', 'Hisar', 'Karnal', 'Sonipat', 'Panchkula'],
            'HP' => ['Shimla', 'Dharamshala', 'Solan', 'Mandi', 'Palampur', 'Baddi', 'Nahan', 'Hamirpur', 'Una', 'Kullu'],
            'JH' => ['Ranchi', 'Jamshedpur', 'Dhanbad', 'Bokaro', 'Deoghar', 'Phusro', 'Hazaribagh', 'Giridih', 'Ramgarh', 'Medininagar'],
            'KA' => ['Bangalore', 'Mysore', 'Hubli', 'Mangalore', 'Belgaum', 'Gulbarga', 'Davanagere', 'Bellary', 'Bijapur', 'Shimoga'],
            'KL' => ['Thiruvananthapuram', 'Kochi', 'Kozhikode', 'Kollam', 'Thrissur', 'Alappuzha', 'Palakkad', 'Kannur', 'Kottayam', 'Malappuram'],
            'MP' => ['Bhopal', 'Indore', 'Gwalior', 'Jabalpur', 'Ujjain', 'Sagar', 'Dewas', 'Satna', 'Ratlam', 'Rewa'],
            'MH' => ['Mumbai', 'Pune', 'Nagpur', 'Thane', 'Nashik', 'Aurangabad', 'Solapur', 'Amravati', 'Kolhapur', 'Sangli'],
            'MN' => ['Imphal', 'Thoubal', 'Bishnupur', 'Churachandpur', 'Senapati', 'Ukhrul', 'Chandel', 'Tamenglong', 'Jiribam', 'Kangpokpi'],
            'ML' => ['Shillong', 'Tura', 'Nongstoin', 'Jowai', 'Baghmara', 'Williamnagar', 'Nongpoh', 'Resubelpara', 'Khliehriat', 'Ampati'],
            'MZ' => ['Aizawl', 'Lunglei', 'Saiha', 'Champhai', 'Kolasib', 'Serchhip', 'Lawngtlai', 'Mamit', 'Zawlnuam', 'Saitual'],
            'NL' => ['Kohima', 'Dimapur', 'Mokokchung', 'Tuensang', 'Wokha', 'Zunheboto', 'Phek', 'Kiphire', 'Longleng', 'Peren'],
            'OR' => ['Bhubaneswar', 'Cuttack', 'Rourkela', 'Brahmapur', 'Sambalpur', 'Puri', 'Balasore', 'Bhadrak', 'Baripada', 'Jharsuguda'],
            'PB' => ['Ludhiana', 'Amritsar', 'Jalandhar', 'Patiala', 'Bathinda', 'Mohali', 'Firozpur', 'Batala', 'Pathankot', 'Moga'],
            'RJ' => ['Jaipur', 'Jodhpur', 'Kota', 'Bikaner', 'Ajmer', 'Udaipur', 'Bhilwara', 'Alwar', 'Bharatpur', 'Sikar'],
            'TN' => ['Chennai', 'Coimbatore', 'Madurai', 'Tiruchirappalli', 'Salem', 'Tirunelveli', 'Tiruppur', 'Vellore', 'Erode', 'Thoothukudi'],
            'TS' => ['Hyderabad', 'Warangal', 'Nizamabad', 'Khammam', 'Karimnagar', 'Ramagundam', 'Mahabubnagar', 'Nalgonda', 'Adilabad', 'Suryapet'],
            'TR' => ['Agartala', 'Dharmanagar', 'Udaipur', 'Kailasahar', 'Belonia', 'Khowai', 'Teliamura', 'Sabroom', 'Ambassa', 'Ranir Bazar'],
            'UP' => ['Lucknow', 'Kanpur', 'Ghaziabad', 'Agra', 'Meerut', 'Varanasi', 'Allahabad', 'Bareilly', 'Aligarh', 'Moradabad'],
            'UK' => ['Dehradun', 'Haridwar', 'Roorkee', 'Haldwani', 'Rudrapur', 'Kashipur', 'Rishikesh', 'Kotdwar', 'Manglaur', 'Doiwala'],
            'WB' => ['Kolkata', 'Howrah', 'Durgapur', 'Asansol', 'Siliguri', 'Malda', 'Baharampur', 'Habra', 'Kharagpur', 'Shantipur'],
            'JK' => ['Srinagar', 'Jammu', 'Baramulla', 'Anantnag', 'Sopore', 'KathuaUdhampur', 'Punch', 'Rajouri', 'Kupwara'],
            'LA' => ['Leh', 'Kargil', 'Nubra', 'Zanskar', 'Drass', 'Turtuk', 'Diskit', 'Panamik', 'Khalsi', 'Sankoo'],
            'CH' => ['Chandigarh'],
            'AN' => ['Port Blair', 'Diglipur', 'Mayabunder', 'Rangat', 'Havelock Island', 'Neil Island', 'Car Nicobar', 'Katchal', 'Nancowry', 'Great Nicobar'],
            'DH' => ['Daman', 'Diu', 'Silvassa', 'Vapi', 'Dadra', 'Nagar Haveli'],
            'LD' => ['Kavaratti', 'Agatti', 'Minicoy', 'Amini', 'Andrott', 'Kalpeni', 'Kadmat', 'Kiltan', 'Chetlat', 'Bitra'],
            'PY' => ['Puducherry', 'Karaikal', 'Mahe', 'Yanam', 'Villianur', 'Ariyankuppam', 'Bahour', 'Nettapakkam', 'Mannadipet', 'Ozhukarai'],
            'SK' => ['Gangtok', 'Namchi', 'Geyzing', 'Mangan', 'Jorethang', 'Naya Bazar', 'Rangpo', 'Singtam', 'Pakyong', 'Ravangla']
        ];
    }
}
