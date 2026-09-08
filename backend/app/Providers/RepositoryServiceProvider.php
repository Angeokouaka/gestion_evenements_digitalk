<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\EvenementRepositoryInterface;
use App\Repositories\EvenementRepository;
use App\Repositories\Interfaces\InscriptionRepositoryInterface;
use App\Repositories\InscriptionRepository;
use App\Repositories\Interfaces\CertificatRepositoryInterface;
use App\Repositories\CertificatRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(EvenementRepositoryInterface::class, EvenementRepository::class);
        $this->app->bind(InscriptionRepositoryInterface::class, InscriptionRepository::class);
        $this->app->bind(CertificatRepositoryInterface::class, CertificatRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
