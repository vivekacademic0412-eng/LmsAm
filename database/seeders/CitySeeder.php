<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\State;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $cities = [

            'Andhra Pradesh' => [
                'Visakhapatnam', 'Vijayawada', 'Guntur', 'Nellore', 'Kurnool',
                'Rajahmundry', 'Tirupati', 'Kadapa', 'Kakinada', 'Anantapur',
            ],

            'Arunachal Pradesh' => [
                'Itanagar', 'Naharlagun', 'Pasighat', 'Tawang', 'Ziro',
            ],

            'Assam' => [
                'Guwahati', 'Silchar', 'Dibrugarh', 'Jorhat', 'Nagaon',
                'Tinsukia', 'Tezpur', 'Bongaigaon',
            ],

            'Bihar' => [
                'Patna', 'Gaya', 'Bhagalpur', 'Muzaffarpur', 'Darbhanga',
                'Purnia', 'Ara', 'Begusarai', 'Katihar', 'Munger',
            ],

            'Chhattisgarh' => [
                'Raipur', 'Bhilai', 'Bilaspur', 'Korba', 'Durg',
                'Rajnandgaon', 'Jagdalpur', 'Ambikapur',
            ],

            'Goa' => [
                'Panaji', 'Margao', 'Vasco da Gama', 'Mapusa', 'Ponda',
            ],

            'Gujarat' => [
                'Ahmedabad', 'Surat', 'Vadodara', 'Rajkot', 'Gandhinagar',
                'Bhavnagar', 'Jamnagar', 'Junagadh', 'Anand', 'Nadiad',
            ],

            'Haryana' => [
                'Gurugram', 'Faridabad', 'Panipat', 'Karnal', 'Rohtak',
                'Hisar', 'Sonipat', 'Ambala', 'Yamunanagar', 'Bhiwani',
                'Panchkula', 'Kurukshetra',
            ],

            'Himachal Pradesh' => [
                'Shimla', 'Manali', 'Dharamshala', 'Solan', 'Mandi',
                'Kullu', 'Una', 'Bilaspur',
            ],

            'Jharkhand' => [
                'Ranchi', 'Jamshedpur', 'Dhanbad', 'Bokaro', 'Deoghar',
                'Hazaribagh', 'Giridih',
            ],

            'Karnataka' => [
                'Bengaluru', 'Mysuru', 'Hubli', 'Mangalore', 'Belgaum',
                'Davangere', 'Ballari', 'Shivamogga', 'Tumakuru', 'Udupi',
            ],

            'Kerala' => [
                'Thiruvananthapuram', 'Kochi', 'Kozhikode', 'Thrissur', 'Kollam',
                'Kannur', 'Alappuzha', 'Palakkad', 'Malappuram',
            ],

            'Madhya Pradesh' => [
                'Bhopal', 'Indore', 'Jabalpur', 'Gwalior', 'Ujjain',
                'Sagar', 'Dewas', 'Satna', 'Ratlam', 'Rewa',
            ],

            'Maharashtra' => [
                'Mumbai', 'Pune', 'Nagpur', 'Nashik', 'Thane',
                'Aurangabad', 'Solapur', 'Kolhapur', 'Amravati', 'Navi Mumbai',
            ],

            'Manipur' => [
                'Imphal', 'Thoubal', 'Bishnupur', 'Churachandpur',
            ],

            'Meghalaya' => [
                'Shillong', 'Tura', 'Jowai', 'Nongstoin',
            ],

            'Mizoram' => [
                'Aizawl', 'Lunglei', 'Champhai', 'Serchhip',
            ],

            'Nagaland' => [
                'Kohima', 'Dimapur', 'Mokokchung', 'Tuensang',
            ],

            'Odisha' => [
                'Bhubaneswar', 'Cuttack', 'Rourkela', 'Berhampur', 'Sambalpur',
                'Puri', 'Balasore',
            ],

            'Punjab' => [
                'Ludhiana', 'Amritsar', 'Jalandhar', 'Patiala', 'Mohali',
                'Bathinda', 'Hoshiarpur', 'Pathankot', 'Moga',
            ],

            'Rajasthan' => [
                'Jaipur', 'Jodhpur', 'Udaipur', 'Ajmer', 'Kota',
                'Bikaner', 'Bharatpur', 'Alwar', 'Sikar',
            ],

            'Sikkim' => [
                'Gangtok', 'Namchi', 'Gyalshing', 'Mangan',
            ],

            'Tamil Nadu' => [
                'Chennai', 'Coimbatore', 'Madurai', 'Salem', 'Tiruchirappalli',
                'Tirunelveli', 'Erode', 'Vellore', 'Thoothukudi', 'Dindigul',
            ],

            'Telangana' => [
                'Hyderabad', 'Warangal', 'Nizamabad', 'Karimnagar', 'Khammam',
                'Ramagundam', 'Secunderabad',
            ],

            'Tripura' => [
                'Agartala', 'Udaipur', 'Dharmanagar', 'Kailashahar',
            ],

            'Uttar Pradesh' => [
                'Lucknow', 'Kanpur', 'Noida', 'Ghaziabad', 'Agra',
                'Meerut', 'Varanasi', 'Prayagraj', 'Bareilly', 'Aligarh',
                'Moradabad', 'Gorakhpur',
            ],

            'Uttarakhand' => [
                'Dehradun', 'Haridwar', 'Roorkee', 'Haldwani', 'Rudrapur',
                'Nainital', 'Rishikesh',
            ],

            'West Bengal' => [
                'Kolkata', 'Howrah', 'Durgapur', 'Siliguri', 'Asansol',
                'Bardhaman', 'Malda', 'Kharagpur',
            ],

            // Union Territories
            'Andaman and Nicobar Islands' => [
                'Port Blair',
            ],

            'Chandigarh' => [
                'Chandigarh',
            ],

            'Dadra and Nagar Haveli and Daman and Diu' => [
                'Daman', 'Diu', 'Silvassa',
            ],

            'Delhi' => [
                'New Delhi', 'North Delhi', 'South Delhi', 'East Delhi',
                'West Delhi', 'Central Delhi', 'North West Delhi', 'South West Delhi',
            ],

            'Jammu and Kashmir' => [
                'Srinagar', 'Jammu', 'Anantnag', 'Baramulla', 'Sopore',
            ],

            'Ladakh' => [
                'Leh', 'Kargil',
            ],

            'Lakshadweep' => [
                'Kavaratti',
            ],

            'Puducherry' => [
                'Puducherry', 'Karaikal', 'Mahe', 'Yanam',
            ],

        ];

        foreach ($cities as $stateName => $cityList) {

            $state = State::where('name', $stateName)->first();

            if (!$state) {
                continue;
            }

            foreach ($cityList as $city) {
                City::updateOrCreate(
                    ['state_id' => $state->id, 'name' => $city],
                    []
                );
            }
        }
    }
}