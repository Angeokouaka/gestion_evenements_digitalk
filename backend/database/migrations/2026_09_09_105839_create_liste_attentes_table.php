<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('liste_attentes', function (Blueprint $table) {
        $table->id();
        $table->timestamp('date_ajout')->useCurrent();
        $table->unsignedInteger('position');
        $table->foreignId('evenement_id')->constrained('evenements')->cascadeOnDelete();
        $table->foreignId('participant_id')->constrained('participants')->cascadeOnDelete();
        $table->timestamps();
        $table->unique(['evenement_id', 'participant_id']);
    });
}

public function down(): void
{
    Schema::dropIfExists('liste_attentes');
}
};
