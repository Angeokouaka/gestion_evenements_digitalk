<?php

namespace App\Console\Commands;

use App\Models\Inscription;
use App\Mail\RappelEvenementMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class EnvoyerRappelsEvenements extends Command
{
    protected $signature = 'digitalk:envoyer-rappels';

    protected $description = "Envoie un email de rappel aux participants dont l'evenement approche";

    public function handle(): int
    {
        $inscriptions = Inscription::with(['participant', 'evenement'])
            ->where('rappel_envoye', false)
            ->where('statut', '!=', 'annulee')
            ->whereHas('evenement', function ($query) {
                $query->where('rappel_actif', true);
            })
            ->get()
            ->filter(function (Inscription $inscription) {
                $evenement = $inscription->evenement;
                $seuil = \Carbon\Carbon::parse($evenement->date_debut)
                    ->subHours($evenement->delai_rappel_heures);

                return now()->greaterThanOrEqualTo($seuil)
                    && now()->lessThan($evenement->date_debut);
            });

        $count = 0;

        foreach ($inscriptions as $inscription) {
            Mail::to($inscription->participant->email)
                ->send(new RappelEvenementMail($inscription));

            $inscription->update([
                'rappel_envoye' => true,
                'date_rappel_envoye' => now(),
            ]);

            $count++;
        }

        $this->info("{$count} rappel(s) envoye(s).");

        return self::SUCCESS;
    }
}
