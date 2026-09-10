<?php

namespace App\Repositories\Interfaces;

use App\Models\ListeAttente;
use Illuminate\Support\Collection;

interface ListeAttenteRepositoryInterface
{
    public function create(array $data): ListeAttente;

    public function countByEvenement(int $evenementId): int;

    public function findByParticipantAndEvenement(int $participantId, int $evenementId): ?ListeAttente;

    public function listeByEvenement(int $evenementId): Collection;
}
