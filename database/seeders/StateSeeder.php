<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\State;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    public function run(): void
    {
        $india = Country::where('code', 'IN')->first();

        if (!$india) {
            return;
        }

        $states = [
            ['name' => 'Andhra Pradesh', 'code' => 'AP','country_id'=>1],
            ['name' => 'Arunachal Pradesh', 'code' => 'AR','country_id'=>1],
            ['name' => 'Assam', 'code' => 'AS','country_id'=>1],
            ['name' => 'Bihar', 'code' => 'BR','country_id'=>1],
            ['name' => 'Chhattisgarh', 'code' => 'CG','country_id'=>1],
            ['name' => 'Goa', 'code' => 'GA','country_id'=>1],
            ['name' => 'Gujarat', 'code' => 'GJ','country_id'=>1],
            ['name' => 'Haryana', 'code' => 'HR','country_id'=>1],
            ['name' => 'Himachal Pradesh', 'code' => 'HP','country_id'=>1],
            ['name' => 'Jharkhand', 'code' => 'JH','country_id'=>1],
            ['name' => 'Karnataka', 'code' => 'KA','country_id'=>1],
            ['name' => 'Kerala', 'code' => 'KL','country_id'=>1],
            ['name' => 'Madhya Pradesh', 'code' => 'MP','country_id'=>1],
            ['name' => 'Maharashtra', 'code' => 'MH','country_id'=>1],
            ['name' => 'Manipur', 'code' => 'MN','country_id'=>1],
            ['name' => 'Meghalaya', 'code' => 'ML','country_id'=>1],
            ['name' => 'Mizoram', 'code' => 'MZ','country_id'=>1],
            ['name' => 'Nagaland', 'code' => 'NL','country_id'=>1],
            ['name' => 'Odisha', 'code' => 'OD','country_id'=>1],
            ['name' => 'Punjab', 'code' => 'PB','country_id'=>1],
            ['name' => 'Rajasthan', 'code' => 'RJ','country_id'=>1],
            ['name' => 'Sikkim', 'code' => 'SK','country_id'=>1],
            ['name' => 'Tamil Nadu', 'code' => 'TN','country_id'=>1],
            ['name' => 'Telangana', 'code' => 'TS','country_id'=>1],
            ['name' => 'Tripura', 'code' => 'TR','country_id'=>1],
            ['name' => 'Uttar Pradesh', 'code' => 'UP','country_id'=>1],
            ['name' => 'Uttarakhand', 'code' => 'UK','country_id'=>1],
            ['name' => 'West Bengal', 'code' => 'WB','country_id'=>1],

            // Union Territories
            ['name' => 'Andaman and Nicobar Islands', 'code' => 'AN','country_id'=>1],
            ['name' => 'Chandigarh', 'code' => 'CH','country_id'=>1],
            ['name' => 'Dadra and Nagar Haveli and Daman and Diu', 'code' => 'DN','country_id'=>1],
            ['name' => 'Delhi', 'code' => 'DL','country_id'=>1],
            ['name' => 'Jammu and Kashmir', 'code' => 'JK','country_id'=>1],
            ['name' => 'Ladakh', 'code' => 'LA','country_id'=>1],
            ['name' => 'Lakshadweep', 'code' => 'LD','country_id'=>1],
            ['name' => 'Puducherry', 'code' => 'PY','country_id'=>1],
        ];

        foreach ($states as $state) {
            State::updateOrCreate(
                ['country_id' => $india->id, 'name' => $state['name']],
                ['code' => $state['code']]
            );
        }
    }
}