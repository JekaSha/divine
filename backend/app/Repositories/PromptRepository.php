<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Http;
use App\Models\Prompt;


class PromptRepository
{
    /**

     *
     * $this->promptRepository->get(['id' => 10])->first();
     * $this->promptRepository->get(['model_name' => 'gpt4o']);
     * $this->promptRepository->get(['provider' => 'openai', 'model_name' => 'gpt3']);
     *
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Collection
     */
    public function get(array $filters = [])
    {
        $query = Prompt::query();

        // Применяем фильтры при их наличии
        if (isset($filters['id'])) {
            $query->where('id', $filters['id']);
        }

        if (isset($filters['model_name'])) {
            $query->where('model_name', $filters['model_name']);
        }

        if (isset($filters['provider'])) {
            $query->where('provider', $filters['provider']);
        }

        $data = $query->get();
        return $data;
    }


/**
     *
     * @param string $text
     * @param string $model
     * @param string $type
     * @return array
     */
    public function render(string $template, string $request = "", array $data = []): string
    {
        $data['request'] = $request;

        foreach ($data as $key => $value) {
            $pattern = '/\{\s*' . preg_quote($key, '/') . '\s*\}/';
            $template = preg_replace($pattern, $value, $template);
        }
        return $template;
    }
}
