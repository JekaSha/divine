<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;

class AccountsTableSeeder extends Seeder
{
    public function run()
    {
        Account::create([
            'id' => 1,
            'user_id' => 1,
            'exchange_id' => 1,
            'name' => 'OKX',
            'description' => 'First',
            'api_key' => 'c139de93-9e40-4c76-a9c9-36ad5607d058',
            'api_secret' => 'C5146E61ADB600E4F0A5A8A07F54F69D7',
            'stream' => ['passphrase' => '!MaxObmin123'],
            'status' => 'active',
        ]);
    }
}
