<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\ChallengeRepositoryInterface;
use App\Repositories\ChallengeRepository;

class AppServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->bind(ChallengeRepositoryInterface::class, ChallengeRepository::class);
    }


    public function boot()
    {
        //
    }
}
