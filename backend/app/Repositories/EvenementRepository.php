<?php

namespace App\Repositories;

use App\Models\Evenement;
use App\Repositories\Interfaces\EvenementRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class EvenementRepository implements EvenementRepositoryInterface
{
    public function paginate(int $perPage = 9, ?string $filiere = null): LengthAwarePaginator
    {
        $query = Evenement::with(['categorie', 'organisateur']);

        if ($filiere) {
            $query->where('filiere', $filiere);
        }

        return $query->paginate($perPage);
    }

    public function findWithRelations(Evenement $evenement, array $relations): Evenement
    {
        $evenement->load($relations);
        return $evenement;
    }

    public function create(array $data): Evenement
    {
        $evenement = Evenement::create($data);
        $evenement->load(['categorie', 'organisateur']);
        return $evenement;
    }

    public function update(Evenement $evenement, array $data): Evenement
    {
        $evenement->update($data);
        $evenement->load(['categorie', 'organisateur']);
        return $evenement;
    }

    public function delete(Evenement $evenement): bool
    {
        return $evenement->delete();
    }

    public function attachIntervenant(Evenement $evenement, int $intervenantId, string $role = 'Intervenant'): Evenement
    {
        $evenement->intervenants()->syncWithoutDetaching([
            $intervenantId => ['role' => $role],
        ]);
        return $evenement->load('intervenants');
    }

    public function detachIntervenant(Evenement $evenement, int $intervenantId): Evenement
    {
        $evenement->intervenants()->detach($intervenantId);
        return $evenement->load('intervenants');
    }

    public function findByQrCode(string $qrCode): ?Evenement
    {
        return Evenement::where('qr_code', $qrCode)->first();
    }
}
