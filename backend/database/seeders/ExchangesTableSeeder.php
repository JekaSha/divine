<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Exchange;

class ExchangesTableSeeder extends Seeder
{
    public function run()
    {
        Exchange::create(['id' => 1, 'name' => 'OKX', 'status' => 'active']);
    }
}
