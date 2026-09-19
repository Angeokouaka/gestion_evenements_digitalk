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
            'evenements_populaires' => $this->repository->evenementsLesPlusPopulaires($organisateurId)->map(function ($evenement) {
                return [
                    'id' => $evenement->id,
                    'titre' => $evenement->titre,
                    'date_debut' => $evenement->date_debut,
                    'inscrits' => $evenement->inscriptions_count,
                    'note_moyenne' => $evenement->avis_evenements_avg_note ? round($evenement->avis_evenements_avg_note, 1) : null,
                ];
            }),
            'intervenants_sollicites' => $this->repository->intervenantsLesPlusSollicites()->map(function ($intervenant) {
                return [
                    'id' => $intervenant->id,
                    'nom' => $intervenant->nom,
                    'prenom' => $intervenant->prenom,
                    'specialite' => $intervenant->specialite,
                    'nombre_evenements' => $intervenant->evenements_count,
                ];
            }),
            'intervenants_mieux_notes' => $this->repository->intervenantsLesMieuxNotes()->map(function ($intervenant) {
                return [
                    'id' => $intervenant->id,
                    'nom' => $intervenant->nom,
                    'prenom' => $intervenant->prenom,
                    'specialite' => $intervenant->specialite,
                    'note_moyenne' => round($intervenant->avis_intervenants_avg_note, 1),
                    'nombre_avis' => $intervenant->avis_intervenants_count,
                ];
            }),
        ];
    }
}
