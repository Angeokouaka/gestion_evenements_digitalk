<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

class OrganisateurFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nom' => fake()->company(),
            'email' => fake()->unique()->companyEmail(),
            'password' => Hash::make('password'),
            'telephone' => fake()->phoneNumber(),
            'structure' => fake()->randomElement(['Service Developpement Numerique', 'Club Info', 'Filiere GI', 'Vie Etudiante']),
        ];
    }
}