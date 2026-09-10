<?php

namespace App\Services;

use App\Models\ListeAttente;
use App\Repositories\Interfaces\ListeAttenteRepositoryInterface;
use Illuminate\Support\Collection;

class ListeAttenteService
{
    public function __construct(
        protected ListeAttenteRepositoryInterface $repository
    ) {}

    public function ajouter(int $evenementId, int $participantId): ListeAttente
    {
        $existant = $this->repository->findByParticipantAndEvenement($participantId, $evenementId);

        if ($existant) {
            return $existant;
        }

        $position = $this->repository->countByEvenement($evenementId) + 1;

        return $this->repository->create([
            'evenement_id' => $evenementId,
            'participant_id' => $participantId,
            'position' => $position,
            'date_ajout' => now(),
        ]);
    }

    public function positionDe(int $participantId, int $evenementId): ?int
    {
        $entree = $this->repository->findByParticipantAndEvenement($participantId, $evenementId);
        return $entree?->position;
    }

    public function listerParEvenement(int $evenementId): Collection
    {
        return $this->repository->listeByEvenement($evenementId);
    }
}
