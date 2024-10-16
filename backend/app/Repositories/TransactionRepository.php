<?php

namespace App\Repositories;

use App\Models\Transaction;
use Carbon\Carbon;

class TransactionRepository
{

    public function create(array $data)
    {
        if (isset($data['order_id'])) {
            $orderId = $data['order_id'];
            unset($data['order_id']);
        }

        $transaction = Transaction::create($data);

        if (isset($orderId)) {
            $transaction->orders()->attach($orderId);
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
            $query->whereIn('status', $filters['status']);
        }

        if (isset($filters['!status'])) {
            $query->whereNotIn('status', $filters['!status']);
        }

        if (isset($filters['!expired']) && $filters['!expired'] === true) {
            $query->where('expiry_time', '>', Carbon::now());
        }

        if (isset($filters['order_status'])) {
            $query->whereHas('orders', function ($query) use ($filters) {
                $query->where('status', $filters['order_status']);
            });
        }

        if (isset($filters['type'])) {
            $query->whereIn('type', $filters['type']);
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
