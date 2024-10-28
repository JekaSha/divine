<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ExchangeService;
use App\Traits\TelegramNotifier;
use App\Models\Account;
use Illuminate\Support\Facades\Log;


class TransactionsCheck extends Command
{
    use TelegramNotifier;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:transactions-check';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and update the status of transactions';
    protected $exchangeService;

    public function __construct(ExchangeService $exchangeService)
    {
        parent::__construct();
        $this->exchangeService = $exchangeService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info("cron works done");

        $this->info('Checked Incoming transactions and updated statuses if necessary.');
        $this->exchangeService->IncomingTransactionsCheck();


        $this->info('Checked Outgoing pending transactions and updated statuses if necessary.');
        $this->exchangeService->OutgoingTransactionsCheck();

    }
}
