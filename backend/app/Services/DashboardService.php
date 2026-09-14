<?php

namespace App\Services;

use App\Repositories\Interfaces\DashboardRepositoryInterface;

class DashboardService
{
    public function __construct(
        protected DashboardRepositoryInterface $repository
    ) {}

    public function statistiques(int $organisateurId): array
    {
        return [
            'evenements_a_venir' => $this->repository->countEvenementsAVenir($organisateurId),
            'evenements_passes' => $this->repository->countEvenementsPasses($organisateurId),
            'inscriptions_total' => $this->repository->countInscriptionsTotal($organisateurId),
            'presences_confirmees' => $this->repository->countPresencesConfirmees($organisateurId),
            'repartition_heures_arrivee' => $this->repository->repartitionHeuresArrivee($organisateurId),
            'evenements_recents' => $this->repository->tauxRemplissageParEvenement($organisateurId)->map(function ($evenement) {
                return [
                    'id' => $evenement->id,
                    'titre' => $evenement->titre,
                    'date_debut' => $evenement->date_debut,
                    'capacite_max' => $evenement->capacite_max,
                    'inscrits' => $evenement->inscriptions_count,
                    'taux_remplissage' => $evenement->capacite_max
                        ? round(($evenement->inscriptions_count / $evenement->capacite_max) * 100)
                        : null,
                ];
            }),
        ];
    }
}
