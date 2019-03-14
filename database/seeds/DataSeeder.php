<?php

use Illuminate\Database\Seeder;

class DataSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        for($i=1; $i <= 17; $i++) {
            $temp = -5 + (mt_rand() / mt_getrandmax()) * 35;
            $pressure = 1024 - (mt_rand() / mt_getrandmax()) * 24;
            \Illuminate\Support\Facades\DB::table('data')->insert([
                'station_id' => $i,
                'temperature' => $temp,
                'pressure' => $pressure,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
}
