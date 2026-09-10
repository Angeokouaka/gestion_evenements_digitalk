<?php

namespace App\Repositories\Interfaces;

use App\Models\Certificat;
use App\Models\Inscription;

interface CertificatRepositoryInterface
{
    public function findByInscription(Inscription $inscription): ?Certificat;

    public function create(array $data): Certificat;

    public function marquerEnvoye(Certificat $certificat): Certificat;
}
