<?php

namespace App\Repositories\Interfaces;

use Illuminate\Support\Collection;

interface DashboardRepositoryInterface
{
    public function countEvenementsAVenir(int $organisateurId): int;

    public function countEvenementsPasses(int $organisateurId): int;

    public function countInscriptionsTotal(int $organisateurId): int;

    public function countPresencesConfirmees(int $organisateurId): int;

    public function repartitionHeuresArrivee(int $organisateurId): Collection;

    public function tauxRemplissageParEvenement(int $organisateurId): Collection;
}
