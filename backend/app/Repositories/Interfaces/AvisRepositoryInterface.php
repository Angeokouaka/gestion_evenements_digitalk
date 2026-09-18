<?php

namespace App\Repositories\Interfaces;

use App\Models\AvisEvenement;
use App\Models\AvisIntervenant;
use Illuminate\Support\Collection;

interface AvisRepositoryInterface
{
    public function trouverAvisEvenement(int $evenementId, int $participantId): ?AvisEvenement;

    public function creerOuMettreAJourAvisEvenement(array $data): AvisEvenement;

    public function moyenneEvenement(int $evenementId): ?float;

    public function avisEvenement(int $evenementId): Collection;

    public function trouverAvisIntervenant(int $intervenantId, int $evenementId, int $participantId): ?AvisIntervenant;

    public function creerOuMettreAJourAvisIntervenant(array $data): AvisIntervenant;

    public function moyenneIntervenant(int $intervenantId): ?float;

    public function avisIntervenant(int $intervenantId): Collection;
}
