<?php

namespace App\Repositories;

use App\Models\Transaction;

class TransactionRepository
{

    public function create(array $data)
    {
        $transaction = Transaction::create($data);

        if (isset($data['order_id'])) {
            $transaction->orders()->attach($data['order_id']);
        }

        return $transaction;
    }

    public function get(array $filters)
    {
        $query = Transaction::query();

        if (isset($filters['id'])) {
            $query->where('id', $filters['id']);
        }

        if (isset($filters['id'])) {
            return $query->with('orders')->find($filters['id']);
        }

        if (isset($filters['status'])) {
            $query->whereHas('orders', function ($query) use ($filters) {
                $query->where('status', $filters['status']);
            });
        }

        if (isset($filters['type'])) {
            $query->whereHas('orders', function ($query) use ($filters) {
                $query->where('type', $filters['type']);
            });
        }

        return $query->with('orders')->get();
    }

    public function getOrderByTransactionId($transactionId)
    {
        // Получаем транзакцию по ID
        $transaction = Transaction::with('orders')->find($transactionId);

        // Возвращаем первый ордер, если он существует
        return $transaction ? $transaction->orders->first() : null;
    }

}
