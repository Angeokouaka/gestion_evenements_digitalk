<?php

namespace App\Repositories;

use App\Models\Certificat;
use App\Models\Inscription;
use App\Repositories\Interfaces\CertificatRepositoryInterface;

class CertificatRepository implements CertificatRepositoryInterface
{
    public function findByInscription(Inscription $inscription): ?Certificat
    {
        return Certificat::where('inscription_id', $inscription->id)->first();
    }

    public function create(array $data): Certificat
    {
        return Certificat::create($data);
    }

    public function marquerEnvoye(Certificat $certificat): Certificat
    {
        $certificat->update(['date_envoi' => now()]);
        return $certificat;
    }
}
