<?php

namespace App\Repositories;

use App\Models\Order;

class OrderRepository
{

    public function create(array $data)
    {
        return Order::create($data);
    }

    public function get($identifier)
    {

        if (is_numeric($identifier)) {
            return Order::with(['transaction', 'transaction.wallet'])
                ->find($identifier);
        } else {
            return Order::with(['transaction', 'transaction.wallet'])
                ->where('hash', $identifier)
                ->first();
        }
    }


}
