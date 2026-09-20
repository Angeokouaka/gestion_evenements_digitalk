<?php

namespace App\Console\Commands;

use App\Models\Inscription;
use App\Mail\DemandeAvisMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class EnvoyerDemandesAvis extends Command
{
    protected $signature = 'digitalk:envoyer-demandes-avis';

    protected $description = "Envoie un email de demande d'avis aux participants presents, une fois l'evenement termine";

    public function handle(): int
    {
        $inscriptions = Inscription::with(['participant', 'evenement'])
            ->where('avis_envoye', false)
            ->where('presence_arrivee', true)
            ->whereHas('evenement', function ($query) {
                $query->where('date_fin', '<=', now());
            })
            ->get();

        $count = 0;

        foreach ($inscriptions as $inscription) {
            Mail::to($inscription->participant->email)
                ->send(new DemandeAvisMail($inscription));

            $inscription->update([
                'avis_envoye' => true,
                'date_avis_envoye' => now(),
            ]);

            $count++;
        }

        $this->info("{$count} demande(s) d'avis envoyee(s).");

        return self::SUCCESS;
    }
}
