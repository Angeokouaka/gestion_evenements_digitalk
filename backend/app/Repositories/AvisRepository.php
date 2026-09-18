<?php

namespace App\Repositories;

use App\Models\AvisEvenement;
use App\Models\AvisIntervenant;
use App\Repositories\Interfaces\AvisRepositoryInterface;
use Illuminate\Support\Collection;

class AvisRepository implements AvisRepositoryInterface
{
    public function trouverAvisEvenement(int $evenementId, int $participantId): ?AvisEvenement
    {
        return AvisEvenement::where('evenement_id', $evenementId)
            ->where('participant_id', $participantId)
            ->first();
    }

    public function creerOuMettreAJourAvisEvenement(array $data): AvisEvenement
    {
        return AvisEvenement::updateOrCreate(
            ['evenement_id' => $data['evenement_id'], 'participant_id' => $data['participant_id']],
            ['note' => $data['note'], 'commentaire' => $data['commentaire'] ?? null]
        );
    }

    public function moyenneEvenement(int $evenementId): ?float
    {
        return AvisEvenement::where('evenement_id', $evenementId)->avg('note');
    }

    public function avisEvenement(int $evenementId): Collection
    {
        return AvisEvenement::where('evenement_id', $evenementId)
            ->with('participant')
            ->latest()
            ->get();
    }

    public function trouverAvisIntervenant(int $intervenantId, int $evenementId, int $participantId): ?AvisIntervenant
    {
        return AvisIntervenant::where('intervenant_id', $intervenantId)
            ->where('evenement_id', $evenementId)
            ->where('participant_id', $participantId)
            ->first();
    }

    public function creerOuMettreAJourAvisIntervenant(array $data): AvisIntervenant
    {
        return AvisIntervenant::updateOrCreate(
            [
                'intervenant_id' => $data['intervenant_id'],
                'evenement_id' => $data['evenement_id'],
                'participant_id' => $data['participant_id'],
            ],
            ['note' => $data['note'], 'commentaire' => $data['commentaire'] ?? null]
        );
    }

    public function moyenneIntervenant(int $intervenantId): ?float
    {
        return AvisIntervenant::where('intervenant_id', $intervenantId)->avg('note');
    }

    public function avisIntervenant(int $intervenantId): Collection
    {
        return AvisIntervenant::where('intervenant_id', $intervenantId)
            ->with('participant')
            ->latest()
            ->get();
    }
}
