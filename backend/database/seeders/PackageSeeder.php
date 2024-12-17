<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageSeeder extends Seeder
{
    public function run()
    {
        DB::table('packages')->insert([
            [
                'id' => 1,
                'name' => 'curious',
                'type' => 'requests_per_month',
                'days' => 30,
                'requests' => 100,
                'price' => 8.76,
                'currency' => 'USD',
                'stream' => json_encode(['dialog' => false]),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'name' => 'researcher',
                'type' => 'requests_per_month',
                'days' => 30,
                'requests' => 250,
                'price' => 18.54,
                'currency' => 'USD',
                'stream' => json_encode(['dialog' => true]),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'name' => 'investigator',
                'type' => 'requests_per_month',
                'days' => 30,
                'requests' => 1000,
                'price' => 28.78,
                'currency' => 'USD',
                'stream' => json_encode(['dialog' => true]),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
