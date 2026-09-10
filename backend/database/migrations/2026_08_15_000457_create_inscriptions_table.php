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
        Schema::create('inscriptions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('participant_id')->constrained('participants')->onDelete('cascade');
    $table->foreignId('evenement_id')->constrained('evenements')->onDelete('cascade');
    $table->dateTime('date_inscription')->useCurrent();
    $table->enum('statut', ['en_attente', 'confirmee', 'annulee'])->default('en_attente');
    $table->unique(['participant_id', 'evenement_id']);
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inscriptions');
    }
};
