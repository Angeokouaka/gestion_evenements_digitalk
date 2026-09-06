<?php

namespace Tests\Feature;

use App\Models\Organisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_organisateur_peut_s_inscrire(): void
    {
        $response = $this->postJson('/api/register', [
            'nom' => 'Club Robotique',
            'email' => 'club.robotique@supdeco.sn',
            'password' => 'motdepasse123',
            'password_confirmation' => 'motdepasse123',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['organisateur', 'token']);

        $this->assertDatabaseHas('organisateurs', [
            'email' => 'club.robotique@supdeco.sn',
        ]);
    }

    public function test_l_inscription_echoue_avec_un_email_deja_utilise(): void
    {
        Organisateur::factory()->create(['email' => 'existant@supdeco.sn']);

        $response = $this->postJson('/api/register', [
            'nom' => 'Autre Club',
            'email' => 'existant@supdeco.sn',
            'password' => 'motdepasse123',
            'password_confirmation' => 'motdepasse123',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    public function test_un_organisateur_peut_se_connecter(): void
    {
        Organisateur::factory()->create([
            'email' => 'club.robotique@supdeco.sn',
            'password' => bcrypt('motdepasse123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'club.robotique@supdeco.sn',
            'password' => 'motdepasse123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['organisateur', 'token']);
    }

    public function test_la_connexion_echoue_avec_un_mauvais_mot_de_passe(): void
    {
        Organisateur::factory()->create([
            'email' => 'club.robotique@supdeco.sn',
            'password' => bcrypt('motdepasse123'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'club.robotique@supdeco.sn',
            'password' => 'mauvais-mot-de-passe',
        ]);

        $response->assertStatus(422);
    }

    public function test_un_organisateur_connecte_peut_se_deconnecter(): void
    {
        $organisateur = Organisateur::factory()->create();
        $token = $organisateur->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson('/api/logout');

        $response->assertStatus(200);
    }
}