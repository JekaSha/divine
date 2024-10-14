<?php

namespace App\Services\Strategies;

use App\Models\Transaction;

interface StrategyInterface
{
    public function execute(Transaction $transaction);
}
