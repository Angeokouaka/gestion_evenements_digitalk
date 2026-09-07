<?php

namespace App\Services;

use App\Models\Inscription;
use App\Repositories\Interfaces\InscriptionRepositoryInterface;
use Illuminate\Support\Collection;

class InscriptionService
{
    public function __construct(
        protected InscriptionRepositoryInterface $repository
    ) {}

    public function lister(): Collection
    {
        return $this->repository->all();
    }

    public function afficher(Inscription $inscription): Inscription
    {
        return $this->repository->findWithRelations($inscription, ['participant', 'evenement']);
    }

    public function creer(array $data): Inscription
    {
        $data['statut'] = $data['statut'] ?? 'en_attente';
        $data['date_inscription'] = now();
        return $this->repository->create($data);
    }

    public function modifier(Inscription $inscription, array $data): Inscription
    {
        return $this->repository->update($inscription, $data);
    }

    public function supprimer(Inscription $inscription): bool
    {
        return $this->repository->delete($inscription);
    }
}
