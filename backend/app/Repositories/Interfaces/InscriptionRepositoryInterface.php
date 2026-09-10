<?php

namespace App\Repositories\Interfaces;

use App\Models\Inscription;
use Illuminate\Support\Collection;

interface InscriptionRepositoryInterface
{
    public function all(): Collection;

    public function findWithRelations(Inscription $inscription, array $relations): Inscription;

    public function create(array $data): Inscription;

    public function update(Inscription $inscription, array $data): Inscription;

    public function delete(Inscription $inscription): bool;

    public function findByEmailAndEvenement(string $email, int $evenementId): ?Inscription;
    public function countByEvenement(int $evenementId): int;

}
