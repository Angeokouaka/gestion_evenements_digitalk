<?php

namespace App\Repositories;

use App\Models\Evenement;
use App\Models\Inscription;
use App\Repositories\Interfaces\DashboardRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardRepository implements DashboardRepositoryInterface
{
    public function countEvenementsAVenir(int $organisateurId): int
    {
        return Evenement::where('organisateur_id', $organisateurId)
            ->where('date_debut', '>=', now())
            ->count();
    }

    public function countEvenementsPasses(int $organisateurId): int
    {
        return Evenement::where('organisateur_id', $organisateurId)
            ->where('date_fin', '<', now())
            ->count();
    }

    public function countInscriptionsTotal(int $organisateurId): int
    {
        return Inscription::whereHas('evenement', function ($query) use ($organisateurId) {
            $query->where('organisateur_id', $organisateurId);
        })->count();
    }

    public function countPresencesConfirmees(int $organisateurId): int
    {
        return Inscription::whereHas('evenement', function ($query) use ($organisateurId) {
            $query->where('organisateur_id', $organisateurId);
        })->where('presence_arrivee', true)->count();
    }

    public function repartitionHeuresArrivee(int $organisateurId): Collection
    {
        return Inscription::whereHas('evenement', function ($query) use ($organisateurId) {
                $query->where('organisateur_id', $organisateurId);
            })
            ->whereNotNull('date_presence_arrivee')
            ->select(DB::raw('HOUR(date_presence_arrivee) as heure'), DB::raw('COUNT(*) as total'))
            ->groupBy('heure')
            ->orderBy('heure')
            ->get();
    }

    public function tauxRemplissageParEvenement(int $organisateurId): Collection
    {
        return Evenement::where('organisateur_id', $organisateurId)
            ->withCount('inscriptions')
            ->orderBy('date_debut', 'desc')
            ->limit(10)
            ->get(['id', 'titre', 'date_debut', 'capacite_max']);
    }
}
