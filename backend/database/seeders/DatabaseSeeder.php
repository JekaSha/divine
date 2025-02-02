<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PromptsTableSeeder::class,
            MerchantsTableSeeder::class,
            PackagesTableSeeder::class,
        ]);

    }
}

class PromptsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('prompts')->insert([
            [
                'id' => 1,
                'ai_model' => 'chatgpt',
                'ai_type' => 'gpt-4-turbo',
                'template' => 'Lets big information about: {request}',
                'stream' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

class MerchantsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('merchants')->insert([
            [
                'name' => 'stripe',
                'key' => 'sk_test_51LmGgmHOcioBkeK5Q7Vsp5kpUeT8dX5yzoUdTRInc1kbUch0QNVmuSsQIowjjhUsiWBmaDsI7LR80G8N7crqe2GZ00KF58OQoi',
                'secret' => " ",
                'stream' => json_encode(['webhook_sign' => 'whsec_ZVWxPulP3B4anpbhr8naArZQHZkN62jg']),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

class PackagesTableSeeder extends Seeder
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
                'stream' => json_encode(['chat' => false, 'email_chat' => false]),
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
                'stream' => json_encode(['chat' => true, 'email_chat' => false]),
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
                'stream' => json_encode(['chat' => true, 'email_chat' => true]),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}