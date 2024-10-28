<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

trait TelegramNotifier
{
    protected $account;
    /**
     * Отправка сообщения в Telegram.
     *
     * @param string $message
     * @return void
     */
    protected function sendTelegramMessage(string $message)
    {
        if (isset($this->account) && isset($this->account->stream['telegram_notification'])) {
            $botToken = $this->account->stream['telegram_notification']['token'];
            $chatId = $this->account->stream['telegram_notification']['chat_id'];

            try {
                $response = Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $message,
                ]);

                if ($response->failed()) {
                    Log::error('Failed to send Telegram message.', ['response' => $response->body()]);
                } else {
                    Log::info('Telegram message sent successfully.', ['message' => $message]);
                }
            } catch (\Exception $e) {
                Log::error('Exception while sending Telegram message.', ['error' => $e->getMessage()]);
            }
        } else {
            Log::error('Account isnt setted.');
        }
    }
}
