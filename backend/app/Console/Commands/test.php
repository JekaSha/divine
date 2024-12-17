<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

use App\Services\UserService;


class test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and update the status of transactions';


    public function __construct()
    {
        parent::__construct();

    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info("Test");

        $userService = app(UserService::class);

        // Установка пользователя
        $userId = 1;
        $userService->setUserId($userId);

        // Создание пропсов
        $propName1 = 'example_string';
        $propValue1 = 'This is a string value';

        $propName2 = 'example_array';
        $propValue2 = ['key1' => 'value1', 'key2' => 'value2'];

        // Записываем пропсы
        $userService->setProp($propName1, $propValue1);
        $userService->setProp($propName2, $propValue2);

        // Чтение пропсов
        $retrievedProp1 = $userService->getProp($propName1);
        $retrievedProp2 = $userService->getProp($propName2);

        // Логирование данных
        Log::info("Retrieved string prop: ", ['prop' => $retrievedProp1]);
        Log::info("Retrieved array prop: ", ['prop' => $retrievedProp2]);

        // Проверка на вывод
        $this->info("String Prop: " . $retrievedProp1);
        $this->info("Array Prop: " . json_encode($retrievedProp2));

        Log::info("Test command finished successfully");
    }
}
