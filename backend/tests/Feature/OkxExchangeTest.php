<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Account;
use App\Services\ExchangeApiService;
//use Illuminate\Foundation\Testing\RefreshDatabase;

class OkxExchangeTest extends TestCase
{

    protected $exchangeApiService;

    protected function setUp(): void
    {
        parent::setUp();

        // Use the account with ID = 1 for testing
        $account = Account::find(1); // Fetch the account directly

        // Check if the account exists
        if (!$account) {
            $this->fail("Account with ID 1 does not exist.");
        }

        // Instantiate ExchangeApiService with the account credentials
        $this->exchangeApiService = new ExchangeApiService($account);
    }

    /** @test */
    public function it_can_get_the_exchange_rate()
    {
        // Mock the ExchangeApiService
        $this->mock(ExchangeApiService::class, function ($mock) {
            $mock->shouldReceive('getExchangeRate')
                ->once() // Expect the method to be called once
                ->with('BTC', 'USDT')
                ->andReturn([
                    'status' => 'success',
                    'data' => [
                        'symbols' => [
                            'BTCUSDT' => [
                                'symbol' => 'BTCUSDT',
                                'rate' => '40000', // Example rate as a string
                            ],
                        ],
                    ],
                ]);
        });

        // Now call the method you want to test that uses ExchangeApiService
        // For example, you might need to call a method in your controller or service
        $response = $this->exchangeApiService->getExchangeRate('BTC', 'USDT');

        // Assert that the returned rate is a float
        $this->assertIsFloat((float)$response['data']['symbols']['BTCUSDT']['rate']);
    }

    /** @test */
    public function it_can_place_a_market_order()
    {
        // Mock the market method in ExchangeApiService
        $this->mock(ExchangeApiService::class, function ($mock) {
            $mock->shouldReceive('market')
                ->once()
                ->with('sell', 'TRX-USDT', 10) // Example parameters
                ->andReturn([
                    'status' => 'success',
                    'data' => [
                        'order_id' => '123456', // Example order ID
                    ],
                ]);
        });

        // Call the market method
        $response = $this->exchangeApiService->market('sell', 'TRX','USDT', 10);

        // Assert that the response is as expected
        $this->assertArrayHasKey('0', $response['code']);
    }

    /** @test */
    public function it_can_return_multiple_transactions()
    {
        // Assuming you have a method in your service to get transactions
        $transactions = [
            [
                'id' => 1,
                'amount' => 100,
                'currency' => 'BTC',
                'status' => 'completed',
            ],
            [
                'id' => 2,
                'amount' => 50,
                'currency' => 'ETH',
                'status' => 'pending',
            ],
        ];

        // Mock the method to return the array of transactions
        $this->mock(ExchangeApiService::class, function ($mock) use ($transactions) {
            $mock->shouldReceive('getTransactionHistory')
                ->once()
                ->andReturn([
                    'status' => 'success',
                    'data' => $transactions,
                ]);
        });

        // Call the method to retrieve transactions
        $response = $this->exchangeApiService->getTransactionHistory();

        // Assert that the response contains the transactions
        $this->assertEquals('success', $response['status']);
        $this->assertCount(2, $response['data']);
        $this->assertEquals(100, $response['data'][0]['amount']);
        $this->assertEquals('BTC', $response['data'][0]['currency']);
    }
}
