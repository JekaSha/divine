<?php

namespace App\Repositories;

use App\Models\Order;

class OrderRepository
{

    public function create(array $data)
    {
        return Order::create($data);
    }

}
