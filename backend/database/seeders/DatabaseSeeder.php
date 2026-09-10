<?php

namespace Database\Seeders;

use App\Models\Categorie;
use App\Models\Evenement;
use App\Models\Inscription;
use App\Models\Intervenant;
use App\Models\Organisateur;
use App\Models\Participant;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Categories fixes (pas aleatoires)
        $categories = collect(['Conference', 'Atelier', 'Hackathon', 'Seminaire', 'Webinaire', 'Forum'])
            ->map(fn ($nom) => Categorie::create([
                'nom' => $nom,
                'description' => fake()->sentence(10),
            ]));

        // 2. Organisateurs
        $organisateurs = Organisateur::factory()->count(5)->create();

        // 3. Intervenants
        $intervenants = Intervenant::factory()->count(8)->create();

        // 4. Participants
        $participants = Participant::factory()->count(20)->create();

        // 5. Evenements (reutilisent les categories/organisateurs existants)
        $evenements = collect(range(1, 10))->map(function () use ($categories, $organisateurs) {
            return Evenement::factory()->create([
                'categorie_id' => $categories->random()->id,
                'organisateur_id' => $organisateurs->random()->id,
            ]);
        });

        // 6. Attacher 2 a 4 intervenants aleatoires par evenement
        $evenements->each(function ($evenement) use ($intervenants) {
            $evenement->intervenants()->attach(
                $intervenants->random(rand(2, 4))->pluck('id')
            );
        });

        // 7. Inscriptions aleatoires (chaque participant s'inscrit a 1-3 evenements)
        $participants->each(function ($participant) use ($evenements) {
            $evenementsChoisis = $evenements->random(rand(1, 3));
            foreach ($evenementsChoisis as $evenement) {
                Inscription::firstOrCreate(
                    [
                        'participant_id' => $participant->id,
                        'evenement_id' => $evenement->id,
                    ],
                    [
                        'date_inscription' => now(),
                        'statut' => fake()->randomElement(['en_attente', 'confirmee', 'annulee']),
                    ]
                );
            }
        });
    }
}