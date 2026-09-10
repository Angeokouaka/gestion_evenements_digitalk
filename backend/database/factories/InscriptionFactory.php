<?php

namespace Database\Factories;

use App\Models\Evenement;
use App\Models\Participant;
use Illuminate\Database\Eloquent\Factories\Factory;

class InscriptionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'participant_id' => Participant::factory(),
            'evenement_id' => Evenement::factory(),
            'date_inscription' => now(),
            'statut' => fake()->randomElement(['en_attente', 'confirmee', 'annulee']),
        ];
    }
}