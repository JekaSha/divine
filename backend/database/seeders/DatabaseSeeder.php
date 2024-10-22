<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            UsersTableSeeder::class,
            AccountsTableSeeder::class,
            CurrencyProtocolsTableSeeder::class,
            ExchangesTableSeeder::class,
            CurrenciesTableSeeder::class,
            WalletsTableSeeder::class,
            CurrencyExchangeTableSeeder::class,
            AccountStrategyTableSeeder::class,
            StrategiesTableSeeder::class,
        ]);
        
    }
}
