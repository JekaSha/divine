<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ExchangeService;


class TransactionsCheck extends Command
{
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


        $this->exchangeService->IncomingTransactionsCheck();
        $this->info('Checked Outgoing transactions and updated statuses if necessary.');
        //$this->exchangeService->IncomingTransactionsCheck();
        //$this->info('Checked pending transactions and updated statuses if necessary.');
    }
}
