<?php

namespace App\Services;

use App\Models\Inscription;
use App\Models\Evenement;
use App\Repositories\Interfaces\InscriptionRepositoryInterface;
use App\Repositories\Interfaces\EvenementRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Carbon\Carbon;

class InscriptionService
{
    public function __construct(
        protected InscriptionRepositoryInterface $repository,
        protected EvenementRepositoryInterface $evenementRepository,
        protected CertificatService $certificatService,
        protected ListeAttenteService $listeAttenteService
    ) {}

    public function lister(): Collection
    {
        return $this->repository->all();
    }

    public function afficher(Inscription $inscription): Inscription
    {
        return $this->repository->findWithRelations($inscription, ['participant', 'evenement']);
    }

    public function creer(array $data): Inscription|array
    {
        $evenement = Evenement::findOrFail($data['evenement_id']);

        if ($evenement->capacite_max) {
            $nombreInscrits = $this->repository->countByEvenement($data['evenement_id']);

            if ($nombreInscrits >= $evenement->capacite_max) {
                $listeAttente = $this->listeAttenteService->ajouter(
                    $data['evenement_id'],
                    $data['participant_id']
                );

                return [
                    'liste_attente' => true,
                    'position' => $listeAttente->position,
                ];
            }
        }

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

    public function confirmerParEmail(string $qrCodeEvenement, string $email): Inscription
    {
        $evenement = $this->evenementRepository->findByQrCode($qrCodeEvenement);

        if (!$evenement) {
            abort(404, 'Evenement introuvable.');
        }

        $inscription = $this->repository->findByEmailAndEvenement($email, $evenement->id);

        if (!$inscription) {
            abort(404, "Aucune inscription trouvee pour cet email sur cet evenement.");
        }

        $debut = Carbon::parse($evenement->date_debut);
        $finFenetreArrivee = $debut->copy()->addMinutes($evenement->duree_fenetre_scan_debut);

        $fin = Carbon::parse($evenement->date_fin);
        $finFenetreDepart = $fin->copy()->addMinutes($evenement->duree_fenetre_scan_fin);

        $maintenant = now();

        if ($maintenant->between($debut, $finFenetreArrivee)) {
            if ($inscription->presence_arrivee) {
                abort(422, "Presence d'arrivee deja confirmee.");
            }
            return $this->confirmerPresenceArrivee($inscription);
        }

        if ($maintenant->between($fin, $finFenetreDepart)) {
            if (!$inscription->presence_arrivee) {
                abort(422, "Vous devez d'abord confirmer votre arrivee.");
            }
            if ($inscription->presence_depart) {
                abort(422, 'Presence de depart deja confirmee.');
            }
            return $this->confirmerPresenceDepart($inscription);
        }

        abort(422, 'Scan indisponible pour le moment.');
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
