<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Currency;

class CurrenciesTableSeeder extends Seeder
{
    public function run()
    {
        Currency::create(['id' => 1, 'name' => 'BTC', 'status' => 'active']);
        Currency::create(['id' => 2, 'name' => 'TRX', 'status' => 'active']);
        Currency::create(['id' => 3, 'name' => 'USDT', 'status' => 'active']);
    }
}
