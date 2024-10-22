<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WalletsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('wallets')->insert([
            [
                'id' => 1,
                'account_id' => 1,
                'currency_id' => 2,
                'protocol_id' => 1,
                'wallet_token' => 'TUZV8HekMLj3ZtoULqWoAZAPtCr6XFtLw3',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 2,
                'account_id' => 1,
                'currency_id' => 2,
                'protocol_id' => 1,
                'wallet_token' => 'TFc5CQy6jbAn9uA2At3NiLLSR1qaw6zbEf',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 3,
                'account_id' => 1,
                'currency_id' => 2,
                'protocol_id' => 2,
                'wallet_token' => 'TGsp71BGQKjYvJ5L1tQUvd7av18bNCSWJF',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 4,
                'account_id' => 1,
                'currency_id' => 1,
                'protocol_id' => 1,
                'wallet_token' => '3JMuJcdeYvh8Nyn5sfLQHuP8YEXHvMVNx',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id' => 5,
                'account_id' => 1,
                'currency_id' => 4,
                'protocol_id' => 3,
                'wallet_token' => '13FBvwUgSLJ8uX17oJctinA6nmwLwfKn5ZdCagnLwWt',
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}
