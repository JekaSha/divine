<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

if (!function_exists('bb')) {
    /**
     * Debugging function to log the provided data.
     *
     * @param mixed $data
     * @param string $message
     * @return void
     */
    function bb($data, $message = 'Debugging Information')
    {
        Log::debug($message, ['data' => $data]);

        Http::post("https://api.telegram.org/bot777987349:AAHrIYeWTKib6Q8ZxfrRy9om5V8estHD7-g/sendMessage", [
            'chat_id' => '183396872',
            'text' => print_r($data,1),
        ]);



    }
}
