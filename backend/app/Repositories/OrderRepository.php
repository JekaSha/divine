<?php

namespace App\Repositories;

use App\Models\Order;

class OrderRepository
{

    public function create(array $data)
    {

        $transactionId = $data['transaction_id'];
        unset($data['transaction_id']);

        $order = Order::create($data);

        if (isset($transactionId)) {
            $order->transactions()->attach($transactionId);
        }

        return $order;
    }

    public function get($filters)
    {

        $query = Order::query();

        if (isset($filters['id'])) {
            $query->where('id', $filters['id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['hash'])) {
            $query->where('hash', $filters['hash']);
        }

        if (isset($filters['transaction_type'])) {
            $query->whereHas('transactions', function ($subQuery) use ($filters) {
                $subQuery->where('type', $filters['transaction_type']);
            });
        }

        if (isset($filters['transaction_status'])) {
            $query->whereHas('transactions', function ($subQuery) use ($filters) {
                $subQuery->where('status', $filters['transaction_status']);
            });
        }

        if (isset($filters['transaction_id'])) {
            if (!is_array($filters['transaction_id'])) $filters['transaction_id'] = [$filters['transaction_id']];
            $query->whereHas('transactions', function ($subQuery) use ($filters) {
                $subQuery->whereIn('transactions.id', $filters['transaction_id']);
            });
        }

        return $query->with(['transactions', 'transactions.wallet'])->get();

    }

    public function getTransactionsByOrderId($orderId, $status = null)
    {
        $query = Order::find($orderId)->transactions();

        if ($status) {
            $query->where('status', $status);
        }

        return $query->get();
    }

    public function getOrderWithTransactions($orderId)
    {
        return Order::with(['transactions', 'transactions.wallet'])
            ->find($orderId);
    }


}
