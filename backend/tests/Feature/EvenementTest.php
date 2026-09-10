<?php

namespace Tests\Feature;

use App\Models\Categorie;
use App\Models\Evenement;
use App\Models\Organisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EvenementTest extends TestCase
{
    use RefreshDatabase;

    public function test_n_importe_qui_peut_lister_les_evenements(): void
    {
        Evenement::factory()->count(3)->create();

        $response = $this->getJson('/api/evenements');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_n_importe_qui_peut_voir_le_detail_d_un_evenement(): void
    {
        $evenement = Evenement::factory()->create(['titre' => 'Hackathon Test']);

        $response = $this->getJson("/api/evenements/{$evenement->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.titre', 'Hackathon Test');
    }

    public function test_creer_un_evenement_sans_authentification_est_refuse(): void
    {
        $categorie = Categorie::factory()->create();
        $organisateur = Organisateur::factory()->create();

        $response = $this->postJson('/api/evenements', [
            'titre' => 'Test Sans Auth',
            'date_debut' => '2026-10-01 09:00:00',
            'date_fin' => '2026-10-01 17:00:00',
            'categorie_id' => $categorie->id,
            'organisateur_id' => $organisateur->id,
        ]);

        $response->assertStatus(401);
    }

    public function test_un_organisateur_authentifie_peut_creer_un_evenement(): void
    {
        $organisateur = Organisateur::factory()->create();
        $categorie = Categorie::factory()->create();

        $response = $this->actingAs($organisateur, 'sanctum')->postJson('/api/evenements', [
            'titre' => 'Hackathon Robotique',
            'date_debut' => '2026-10-01 09:00:00',
            'date_fin' => '2026-10-01 17:00:00',
            'categorie_id' => $categorie->id,
            'organisateur_id' => $organisateur->id,
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.titre', 'Hackathon Robotique')
            ->assertJsonPath('data.statut', 'planifie');

        $this->assertDatabaseHas('evenements', ['titre' => 'Hackathon Robotique']);
    }

    public function test_la_creation_echoue_si_date_fin_avant_date_debut(): void
    {
        $organisateur = Organisateur::factory()->create();
        $categorie = Categorie::factory()->create();

        $response = $this->actingAs($organisateur, 'sanctum')->postJson('/api/evenements', [
            'titre' => 'Evenement Invalide',
            'date_debut' => '2026-10-01 17:00:00',
            'date_fin' => '2026-10-01 09:00:00',
            'categorie_id' => $categorie->id,
            'organisateur_id' => $organisateur->id,
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('date_fin');
    }

    public function test_un_organisateur_ne_peut_pas_modifier_l_evenement_d_un_autre(): void
    {
        $proprietaire = Organisateur::factory()->create();
        $autreOrganisateur = Organisateur::factory()->create();
        $evenement = Evenement::factory()->create([
            'organisateur_id' => $proprietaire->id,
            'titre' => 'Titre original',
        ]);

        $response = $this->actingAs($autreOrganisateur, 'sanctum')->putJson("/api/evenements/{$evenement->id}", [
            'titre' => 'Titre modifie par un intrus',
        ]);

        $response->assertStatus(403);

        $this->assertDatabaseHas('evenements', [
            'id' => $evenement->id,
            'titre' => 'Titre original',
        ]);
    }

    public function test_un_organisateur_ne_peut_pas_supprimer_l_evenement_d_un_autre(): void
    {
        $proprietaire = Organisateur::factory()->create();
        $autreOrganisateur = Organisateur::factory()->create();
        $evenement = Evenement::factory()->create(['organisateur_id' => $proprietaire->id]);

        $response = $this->actingAs($autreOrganisateur, 'sanctum')->deleteJson("/api/evenements/{$evenement->id}");

        $response->assertStatus(403);

        $this->assertDatabaseHas('evenements', ['id' => $evenement->id]);
    }

    public function test_un_proprietaire_peut_modifier_son_propre_evenement(): void
    {
        $organisateur = Organisateur::factory()->create();
        $evenement = Evenement::factory()->create(['organisateur_id' => $organisateur->id]);

        $response = $this->actingAs($organisateur, 'sanctum')->putJson("/api/evenements/{$evenement->id}", [
            'titre' => 'Titre mis a jour par le proprietaire',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.titre', 'Titre mis a jour par le proprietaire');
    }
}