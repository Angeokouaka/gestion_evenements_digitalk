<?php

namespace App\Repositories;

use App\Models\ListeAttente;
use App\Repositories\Interfaces\ListeAttenteRepositoryInterface;
use Illuminate\Support\Collection;

class ListeAttenteRepository implements ListeAttenteRepositoryInterface
{
    public function create(array $data): ListeAttente
    {
        return ListeAttente::create($data);
    }

    public function countByEvenement(int $evenementId): int
    {
        return ListeAttente::where('evenement_id', $evenementId)->count();
    }

    public function findByParticipantAndEvenement(int $participantId, int $evenementId): ?ListeAttente
    {
        return ListeAttente::where('participant_id', $participantId)
            ->where('evenement_id', $evenementId)
            ->first();
    }

    public function listeByEvenement(int $evenementId): Collection
    {
        return ListeAttente::where('evenement_id', $evenementId)
            ->orderBy('position')
            ->with('participant')
            ->get();
    }
}
