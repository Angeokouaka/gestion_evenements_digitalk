<?php

namespace App\Services;

use App\Models\Certificat;
use App\Models\Inscription;
use App\Repositories\Interfaces\CertificatRepositoryInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

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
            'code_verification' => (string) Str::uuid(),
            'url_fichier' => '',
        ]);

        $cheminFichier = $this->genererFichierPdf($inscription, $certificat);
        $certificat->update(['url_fichier' => $cheminFichier]);

        $this->envoyerParMail($inscription, $certificat);

        return $this->repository->marquerEnvoye($certificat);
    }

    protected function genererFichierPdf(Inscription $inscription, Certificat $certificat): string
    {
        $pdf = PDF::loadView('certificats.attestation', [
            'participant' => $inscription->participant,
            'evenement' => $inscription->evenement,
            'certificat' => $certificat,
        ]);

        $nomFichier = "certificat-{$inscription->id}-" . uniqid() . ".pdf";
        $cheminRelatif = "certificats/{$nomFichier}";

        Storage::disk('public')->put($cheminRelatif, $pdf->output());

        return $cheminRelatif;
    }

   protected function envoyerParMail(Inscription $inscription, Certificat $certificat): void
{
    \Illuminate\Support\Facades\Mail::to($inscription->participant->email)
        ->send(new \App\Mail\CertificatMail($certificat));
}
}
