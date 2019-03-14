<?php

use Illuminate\Database\Seeder;

class StationsSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        for($i=1; $i <= 17; $i++) {
            $x = 54 - (mt_rand() / mt_getrandmax()) * 5;
            $y = 24 - (mt_rand() / mt_getrandmax()) * 8;
            \Illuminate\Support\Facades\DB::table('stations')->insert([
                'name' => 'stacja '.$i,
                'jwt' => 'jwt',
                'position_x' => $x,
                'position_y' => $y
            ]);
        }
    }
}
