<?php

namespace Tests\Feature;

use App\Models\Evenement;
use App\Models\Inscription;
use App\Models\Participant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_participant_peut_s_inscrire_sans_compte(): void
    {
        $evenement = Evenement::factory()->create();

        $participantResponse = $this->postJson('/api/participants', [
            'nom' => 'Diallo',
            'prenom' => 'Fatou',
            'email' => 'fatou.diallo@example.com',
        ]);

        $participantResponse->assertStatus(201);

        $participantId = $participantResponse->json('data.id');

        $inscriptionResponse = $this->postJson('/api/inscriptions', [
            'participant_id' => $participantId,
            'evenement_id' => $evenement->id,
        ]);

        $inscriptionResponse->assertStatus(201)
            ->assertJsonPath('data.statut', 'en_attente');

        $this->assertDatabaseHas('inscriptions', [
            'participant_id' => $participantId,
            'evenement_id' => $evenement->id,
        ]);
    }

    public function test_l_inscription_participant_echoue_avec_un_email_deja_utilise(): void
    {
        Participant::factory()->create(['email' => 'existant@example.com']);

        $response = $this->postJson('/api/participants', [
            'nom' => 'Sow',
            'prenom' => 'Moussa',
            'email' => 'existant@example.com',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    public function test_l_inscription_participant_echoue_avec_un_matricule_deja_utilise(): void
    {
        Participant::factory()->create(['matricule' => 'L2GI1234']);

        $response = $this->postJson('/api/participants', [
            'nom' => 'Sow',
            'prenom' => 'Moussa',
            'email' => 'nouveau@example.com',
            'matricule' => 'L2GI1234',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors('matricule');
    }

    public function test_un_participant_ne_peut_pas_s_inscrire_deux_fois_au_meme_evenement(): void
    {
        $participant = Participant::factory()->create();
        $evenement = Evenement::factory()->create();

        Inscription::factory()->create([
            'participant_id' => $participant->id,
            'evenement_id' => $evenement->id,
        ]);

        $response = $this->postJson('/api/inscriptions', [
            'participant_id' => $participant->id,
            'evenement_id' => $evenement->id,
        ]);

        $response->assertStatus(422);
    }

    public function test_un_participant_recoit_son_certificat_apres_confirmation_de_presence(): void
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $evenement = Evenement::factory()->create([
            'date_debut' => now()->subMinutes(2),
            'date_fin' => now()->addHours(2),
        ]);

        $participant = Participant::factory()->create();

        $inscription = Inscription::factory()->create([
            'participant_id' => $participant->id,
            'evenement_id' => $evenement->id,
        ]);

        $this->postJson("/api/inscriptions/{$inscription->id}/scanner-arrivee")
            ->assertStatus(200)
            ->assertJsonPath('data.presence_arrivee', true);

        $this->assertDatabaseHas('certificats', [
            'inscription_id' => $inscription->id,
        ]);

        $certificat = \App\Models\Certificat::where('inscription_id', $inscription->id)->first();
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($certificat->url_fichier);
    }
}
