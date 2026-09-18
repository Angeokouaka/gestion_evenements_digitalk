<?php

namespace App\Services;

use App\Models\AvisEvenement;
use App\Models\AvisIntervenant;
use App\Models\Inscription;
use App\Repositories\Interfaces\AvisRepositoryInterface;

class AvisService
{
    public function __construct(
        protected AvisRepositoryInterface $repository
    ) {}

    public function noterEvenement(int $evenementId, string $email, int $note, ?string $commentaire): AvisEvenement
    {
        $inscription = $this->trouverInscriptionValidee($email, $evenementId);

        return $this->repository->creerOuMettreAJourAvisEvenement([
            'evenement_id' => $evenementId,
            'participant_id' => $inscription->participant_id,
            'note' => $note,
            'commentaire' => $commentaire,
        ]);
    }

    public function noterIntervenant(int $intervenantId, int $evenementId, string $email, int $note, ?string $commentaire): AvisIntervenant
    {
        $inscription = $this->trouverInscriptionValidee($email, $evenementId);

        return $this->repository->creerOuMettreAJourAvisIntervenant([
            'intervenant_id' => $intervenantId,
            'evenement_id' => $evenementId,
            'participant_id' => $inscription->participant_id,
            'note' => $note,
            'commentaire' => $commentaire,
        ]);
    }

    protected function trouverInscriptionValidee(string $email, int $evenementId): Inscription
    {
        $inscription = Inscription::whereHas('participant', function ($query) use ($email) {
            $query->where('email', $email);
        })
            ->where('evenement_id', $evenementId)
            ->first();

        if (!$inscription) {
            abort(404, "Aucune inscription trouvee pour cet email sur cet evenement.");
        }

        if (!$inscription->presence_arrivee) {
            abort(403, "Seuls les participants ayant confirme leur presence peuvent noter.");
        }

        return $inscription;
    }

    public function moyenneEvenement(int $evenementId): ?float
    {
        return $this->repository->moyenneEvenement($evenementId);
    }

    public function avisEvenement(int $evenementId)
    {
        return $this->repository->avisEvenement($evenementId);
    }

    public function moyenneIntervenant(int $intervenantId): ?float
    {
        return $this->repository->moyenneIntervenant($intervenantId);
    }

    public function avisIntervenant(int $intervenantId)
    {
        return $this->repository->avisIntervenant($intervenantId);
    }
}
