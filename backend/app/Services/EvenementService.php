<?php

namespace App\Services;

use App\Models\Evenement;
use App\Repositories\Interfaces\EvenementRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class EvenementService
{
    public function __construct(
        protected EvenementRepositoryInterface $repository
    ) {}

    public function lister(?string $filiere = null): LengthAwarePaginator
    {
        return $this->repository->paginate(9, $filiere);
    }

    public function afficher(Evenement $evenement): Evenement
    {
        return $this->repository->findWithRelations(
            $evenement,
            ['categorie', 'organisateur', 'intervenants', 'participants']
        );
    }

    public function creer(array $data): Evenement
    {
        $data['statut'] = $data['statut'] ?? 'planifie';
        return $this->repository->create($data);
    }

    public function modifier(int $userId, Evenement $evenement, array $data): Evenement
    {
        $this->verifierProprietaire($userId, $evenement, 'modifier');
        return $this->repository->update($evenement, $data);
    }

    public function supprimer(int $userId, Evenement $evenement): bool
    {
        $this->verifierProprietaire($userId, $evenement, 'supprimer');
        return $this->repository->delete($evenement);
    }

    public function ajouterIntervenant(Evenement $evenement, int $intervenantId): Evenement
    {
        return $this->repository->attachIntervenant($evenement, $intervenantId);
    }

    public function retirerIntervenant(Evenement $evenement, int $intervenantId): Evenement
    {
        return $this->repository->detachIntervenant($evenement, $intervenantId);
    }

    protected function verifierProprietaire(int $userId, Evenement $evenement, string $action): void
    {
        if ($userId !== $evenement->organisateur_id) {
            abort(403, "Non autorise a {$action} cet evenement.");
        }
    }
}
