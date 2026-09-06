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
    {Schema::create('evenements', function (Blueprint $table) {
    $table->id();
    $table->string('titre');
    $table->text('description')->nullable();
    $table->dateTime('date_debut');
    $table->dateTime('date_fin');
    $table->string('lieu')->nullable();
    $table->integer('capacite_max')->nullable();
    $table->enum('statut', ['planifie', 'en_cours', 'termine', 'annule'])->default('planifie');
    $table->foreignId('categorie_id')->constrained('categories')->onDelete('cascade');
    $table->foreignId('organisateur_id')->constrained('organisateurs')->onDelete('cascade');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evenements');
    }
};
