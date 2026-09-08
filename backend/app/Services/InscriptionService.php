<?php

namespace App\Services;

use App\Models\Inscription;
use App\Repositories\Interfaces\InscriptionRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Carbon\Carbon;

class InscriptionService
{
    public function __construct(
        protected InscriptionRepositoryInterface $repository,
        protected CertificatService $certificatService
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
        $data['qr_code'] = Str::uuid();
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

    public function confirmerPresenceArrivee(Inscription $inscription): Inscription
    {
        $evenement = $inscription->evenement;
        $debut = Carbon::parse($evenement->date_debut);
        $finFenetre = $debut->copy()->addMinutes($evenement->duree_fenetre_scan_debut);

        if (!now()->between($debut, $finFenetre)) {
            abort(422, 'Scan indisponible pour le moment.');
        }

        $inscription = $this->repository->update($inscription, [
            'presence_arrivee' => true,
            'date_presence_arrivee' => now(),
        ]);

        $this->certificatService->genererSiEligible($inscription);

        return $inscription;
    }

    public function confirmerPresenceDepart(Inscription $inscription): Inscription
    {
        $evenement = $inscription->evenement;
        $fin = Carbon::parse($evenement->date_fin);
        $finFenetre = $fin->copy()->addMinutes($evenement->duree_fenetre_scan_fin);

        if (!now()->between($fin, $finFenetre)) {
            abort(422, 'Scan indisponible pour le moment.');
        }

        $inscription = $this->repository->update($inscription, [
            'presence_depart' => true,
            'date_presence_depart' => now(),
        ]);

        $this->certificatService->genererSiEligible($inscription);

        return $inscription;
    }
}
