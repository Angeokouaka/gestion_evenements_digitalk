<?php

namespace App\Repositories;

use App\Models\Inscription;
use App\Repositories\Interfaces\InscriptionRepositoryInterface;
use Illuminate\Support\Collection;

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
}
