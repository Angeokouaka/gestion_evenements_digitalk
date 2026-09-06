<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\EvenementRepositoryInterface;
use App\Repositories\EvenementRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(EvenementRepositoryInterface::class, EvenementRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
