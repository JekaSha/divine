<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckDataMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Получаем данные из запроса
        $data = $request->input('data');

        // Проверяем, не пустой ли массив data
        if (!empty($data)) {
            // Если data не пустая, возвращаем статус success
            return response()->json(['status' => 'success', 'data' => $data]);
        }

        // Если data пустая, продолжаем выполнение запроса
        return $next($request);
    }
}
