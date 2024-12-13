<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Http;

class AiRepository
{
    /**
     *
     * @param string $text
     * @param string $model
     * @param string $type
     * @return array
     */
    public function sendRequest(string $prompt, string $type = "chatgpt", string $model = "gpt-4-turbo"): array
    {
        $url = "https://search.plur.online/api/ai/{$type}";
        $response = Http::get($url, [
            'q' => $prompt,
            'model' => $model,
        ]);

        if ($response->failed()) {
            throw new \Exception('Failed to fetch AI response.');
        }

        return $response->json();
    }
}
