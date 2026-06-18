<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\State;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [

            'Delhi' => [
                'New Delhi',
                'North Delhi',
                'South Delhi',
                'East Delhi',
                'West Delhi'
            ],

            'Haryana' => [
                'Gurugram',
                'Faridabad',
                'Panipat',
                'Karnal',
                'Rohtak',
                'Hisar',
                'Sonipat',
                'Ambala',
                'Yamunanagar',
                'Bhiwani'
            ],

            'Punjab' => [
                'Ludhiana',
                'Amritsar',
                'Jalandhar',
                'Patiala',
                'Mohali',
                'Bathinda'
            ],

            'Rajasthan' => [
                'Jaipur',
                'Jodhpur',
                'Udaipur',
                'Ajmer',
                'Kota',
                'Bikaner'
            ],

            'Uttar Pradesh' => [
                'Lucknow',
                'Kanpur',
                'Noida',
                'Ghaziabad',
                'Agra',
                'Meerut',
                'Varanasi',
                'Prayagraj'
            ],

            'Maharashtra' => [
                'Mumbai',
                'Pune',
                'Nagpur',
                'Nashik',
                'Thane',
                'Aurangabad'
            ],

            'Gujarat' => [
                'Ahmedabad',
                'Surat',
                'Vadodara',
                'Rajkot',
                'Gandhinagar'
            ],

            'Karnataka' => [
                'Bengaluru',
                'Mysuru',
                'Hubli',
                'Mangalore',
                'Belgaum'
            ],

            'Tamil Nadu' => [
                'Chennai',
                'Coimbatore',
                'Madurai',
                'Salem',
                'Tiruchirappalli'
            ],

            'West Bengal' => [
                'Kolkata',
                'Howrah',
                'Durgapur',
                'Siliguri',
                'Asansol'
            ]

        ];

        foreach ($cities as $stateName => $cityList) {

            $state = State::where('name', $stateName)->first();

            if (!$state) {
                continue;
            }

            foreach ($cityList as $city) {

                City::create([
                    'state_id' => $state->id,
                    'name' => $city
                ]);
            }
        }
    
    }
}
