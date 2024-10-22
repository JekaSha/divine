<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StrategiesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('strategies')->insert([
            [
                'id' => 1,
                'name' => 'QuickSell',
                'description' => 'A quick selling strategy.',
                'status' => 'active',
                'stream' => null,
                'className' => 'App\Services\Strategies\QuickSellStrategy',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Добавьте другие записи, если необходимо
        ]);
    }
}
