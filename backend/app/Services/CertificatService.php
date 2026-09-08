<?php

namespace App\Services;

use App\Models\Certificat;
use App\Models\Inscription;
use App\Repositories\Interfaces\CertificatRepositoryInterface;
use Illuminate\Support\Str;

class CertificatService
{
    public function __construct(
        protected CertificatRepositoryInterface $repository
    ) {}

    public function genererSiEligible(Inscription $inscription): ?Certificat
    {
        if (!$inscription->presence_arrivee || !$inscription->presence_depart) {
            return null;
        }

        $existant = $this->repository->findByInscription($inscription);
        if ($existant) {
            return $existant;
        }

        $certificat = $this->repository->create([
            'inscription_id' => $inscription->id,
            'date_generation' => now(),
            'code_verification' => Str::uuid(),
            'url_fichier' => $this->genererFichierPdf($inscription),
        ]);

        $this->envoyerParMail($inscription, $certificat);

        return $this->repository->marquerEnvoye($certificat);
    }

    protected function genererFichierPdf(Inscription $inscription): string
    {
        // À implémenter : génération réelle du PDF (ex. avec dompdf ou barryvdh/laravel-dompdf)
        return "certificats/certificat-{$inscription->id}.pdf";
    }

    protected function envoyerParMail(Inscription $inscription, Certificat $certificat): void
    {
        // À implémenter : Mail::to($inscription->participant->email)->send(new CertificatMail($certificat));
    }
}
