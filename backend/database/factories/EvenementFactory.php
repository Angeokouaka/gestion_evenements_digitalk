<?php

namespace Database\Factories;

use App\Models\Categorie;
use App\Models\Organisateur;
use Illuminate\Database\Eloquent\Factories\Factory;

class EvenementFactory extends Factory
{
    public function definition(): array
    {
        $debut = fake()->dateTimeBetween('now', '+3 months');
        $fin = (clone $debut)->modify('+' . fake()->numberBetween(2, 8) . ' hours');

        return [
            'titre' => fake()->sentence(4),
            'description' => fake()->paragraph(3),
            'date_debut' => $debut,
            'date_fin' => $fin,
            'lieu' => fake()->randomElement(['Campus SUPDECO', 'Amphithéâtre A', 'Salle 204', 'Auditorium']),
            'capacite_max' => fake()->numberBetween(20, 200),
            'statut' => 'planifie',
            'categorie_id' => Categorie::factory(),
            'organisateur_id' => Organisateur::factory(),
        ];
    }
}
