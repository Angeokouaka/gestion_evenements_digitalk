<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class IntervenantFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nom' => fake()->lastName(),
            'prenom' => fake()->firstName(),
            'email' => fake()->unique()->safeEmail(),
            'specialite' => fake()->randomElement(['Cybersécurité', 'Intelligence Artificielle', 'Développement Web', 'Réseaux', 'Data Science']),
            'bio' => fake()->paragraph(2),
        ];
    }
}
