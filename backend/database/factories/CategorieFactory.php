<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategorieFactory extends Factory
{
    public function definition(): array
    {
        $categories = ['Conférence', 'Atelier', 'Hackathon', 'Séminaire', 'Webinaire', 'Forum'];

        return [
            'nom' => fake()->randomElement($categories),
            'description' => fake()->sentence(10),
        ];
    }
}
