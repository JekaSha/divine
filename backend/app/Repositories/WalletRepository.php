<?php

namespace App\Repositories;

use App\Models\Wallet;

class WalletRepository
{
    /**
     *
     * @param int $accountId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableCurrencies($userId)
    {
        $currencies = Wallet::with(['currency', 'protocol', 'account'])
            ->where('status', 'active')
            ->whereHas('account', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->get()
            ->groupBy('currency.id')
            ->map(function ($group) {
                return [
                    'currency_id' => $group->first()->currency->id,
                    'currency_name' => $group->first()->currency->name,
                    'protocols' => $group->map(function ($item) {
                        return [
                            'protocol_id' => $item->protocol->id,
                            'protocol_name' => $item->protocol->name,
                        ];
                    })->unique('protocol_id'),
                ];
            })
            ->values();

        return $currencies;
    }
    public function get($currencyId, $protocolId, $userId) {

        $wallets = Wallet::where('currency_id', $currencyId)
            ->where('status', 'active')
            ->whereHas('account.user', function ($query) use ($userId) {
                $query->where('id', $userId);
            });


        if ($protocolId !== false) {
            $wallets->where('protocol_id', $protocolId);
        }


        return $wallets->get();
    }

    public function getFreeWallet(int $userId, int $currencyId, int $protocolId, $type = "rand") {

        if ($type == 'rand') {
            $wallet = Wallet::where('status', 'active')
                ->where('currency_id', $currencyId)
                ->where('protocol_id', $protocolId)
                ->whereHas('account.user', function ($query) use ($userId) {
                    $query->where('id', $userId);
                })
                ->inRandomOrder()
                ->first();
        }

        return $wallet;
    }

    public function create(array $data) : Wallet
    {
        return Wallet::create([
            'wallet_token' => $data['wallet_token'],
            'currency_id' => $data['currency_id'],
            'protocol_id' => $data['protocol_id'],
            'status' => $data['status'],
        ]);
    }

}
