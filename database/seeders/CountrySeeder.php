<?php

    namespace Database\Seeders;

    use App\Models\Country;
    use Illuminate\Database\Seeder;

    class CountrySeeder extends Seeder
    {
        /**
         * Run the database seeds.
         */

        public function run(): void
        {
            Country::create([
                'name' => 'India',
                'code' => 'IN',
                'dial_code' => '+91',
            ]);

            // add more countries here if needed
        }
    }
