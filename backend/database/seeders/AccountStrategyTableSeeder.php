<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountStrategyTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('account_strategy')->insert([
            [
                'id' => 1,
                'account_id' => 1,
                'strategy_id' => 1,
                'event_type' => 'FundsCredited',
            ],
            // Добавьте другие записи, если необходимо
        ]);
    }
}
