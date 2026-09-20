<?php

namespace App\Repositories;

use App\Models\Inscription;
use App\Repositories\Interfaces\InscriptionRepositoryInterface;
use Illuminate\Support\Collection;
use App\Models\Participant;

class InscriptionRepository implements InscriptionRepositoryInterface
{
    public function all(): Collection
    {
        return Inscription::with(['participant', 'evenement'])->get();
    }

    public function findWithRelations(Inscription $inscription, array $relations): Inscription
    {
        $inscription->load($relations);
        return $inscription;
    }

    public function create(array $data): Inscription
    {
        $inscription = Inscription::create($data);
        $inscription->load(['participant', 'evenement']);
        return $inscription;
    }

    public function update(Inscription $inscription, array $data): Inscription
    {
        $inscription->update($data);
        $inscription->load(['participant', 'evenement']);
        return $inscription;
    }

    public function delete(Inscription $inscription): bool
    {
        return $inscription->delete();
    }

    public function findByEmailAndEvenement(string $email, int $evenementId): ?Inscription
    {
        $participant = Participant::where('email', $email)->first();

        if (!$participant) {
            return null;
        }

        return Inscription::where('participant_id', $participant->id)
            ->where('evenement_id', $evenementId)
            ->first();
    }

    public function countByEvenement(int $evenementId): int
    {
        return Inscription::where('evenement_id', $evenementId)->count();
    }

    public function listeParEvenement(int $evenementId): Collection
    {
        return Inscription::where('evenement_id', $evenementId)
            ->with('participant')
            ->orderBy('date_presence_arrivee', 'desc')
            ->get();
    }

    public function findByQrCode(string $qrCode): ?Inscription
    {
        return Inscription::where('qr_code', $qrCode)
            ->with(['participant', 'evenement.intervenants'])
            ->first();
    }
}
