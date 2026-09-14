<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\EvenementRepositoryInterface;
use App\Repositories\EvenementRepository;
use App\Repositories\Interfaces\InscriptionRepositoryInterface;
use App\Repositories\InscriptionRepository;
use App\Repositories\Interfaces\CertificatRepositoryInterface;
use App\Repositories\CertificatRepository;
use App\Repositories\Interfaces\ListeAttenteRepositoryInterface;
use App\Repositories\ListeAttenteRepository;
use App\Repositories\Interfaces\DashboardRepositoryInterface;
use App\Repositories\DashboardRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(EvenementRepositoryInterface::class, EvenementRepository::class);
        $this->app->bind(InscriptionRepositoryInterface::class, InscriptionRepository::class);
        $this->app->bind(CertificatRepositoryInterface::class, CertificatRepository::class);
        $this->app->bind(ListeAttenteRepositoryInterface::class, ListeAttenteRepository::class);
        $this->app->bind(DashboardRepositoryInterface::class, DashboardRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
