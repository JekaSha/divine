<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CurrencyProtocol;

class CurrencyProtocolsTableSeeder extends Seeder
{
    public function run()
    {
        CurrencyProtocol::create(['id' => 1, 'name' => 'TRC20', 'description' => 'TRC20 Protocol', 'status' => 'active']);
    }
}
