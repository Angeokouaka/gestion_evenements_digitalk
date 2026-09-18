<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('avis_intervenants', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('note');
            $table->text('commentaire')->nullable();
            $table->foreignId('intervenant_id')->constrained('intervenants')->cascadeOnDelete();
            $table->foreignId('evenement_id')->constrained('evenements')->cascadeOnDelete();
            $table->foreignId('participant_id')->constrained('participants')->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['intervenant_id', 'evenement_id', 'participant_id'], 'avis_intervenants_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('avis_intervenants');
    }
};
