<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CurrencyExchangeTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('currency_exchange')->insert([
            [
                'id' => 1,
                'from_currency_id' => 2,
                'to_currency_id' => 1,
                'exchange_id' => 1,
                'current_rate' => 0.00000231,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'from_currency_id' => 2,
                'to_currency_id' => 4,
                'exchange_id' => 1,
                'current_rate' => 0.03494791,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // Добавьте другие записи, если необходимо
        ]);
    }
}
