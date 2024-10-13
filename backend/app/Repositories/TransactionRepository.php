<?php

namespace App\Repositories;

use App\Models\Transaction;

class TransactionRepository
{

    public function create(array $data)
    {
        return Transaction::create($data);
    }

}
