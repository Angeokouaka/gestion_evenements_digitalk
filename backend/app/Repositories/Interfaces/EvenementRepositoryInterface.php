<?php

namespace App\Repositories\Interfaces;

use App\Models\Evenement;
use Illuminate\Pagination\LengthAwarePaginator;

interface EvenementRepositoryInterface
{
    public function paginate(int $perPage = 9, ?string $filiere = null): LengthAwarePaginator;

    public function findWithRelations(Evenement $evenement, array $relations): Evenement;

    public function create(array $data): Evenement;

    public function update(Evenement $evenement, array $data): Evenement;

    public function delete(Evenement $evenement): bool;

    public function attachIntervenant(Evenement $evenement, int $intervenantId): Evenement;

    public function detachIntervenant(Evenement $evenement, int $intervenantId): Evenement;
    
    public function findByQrCode(string $qrCode): ?Evenement;
}
