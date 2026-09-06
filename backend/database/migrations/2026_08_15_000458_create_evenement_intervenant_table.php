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
        Schema::create('evenement_intervenant', function (Blueprint $table) {
    $table->id();
    $table->foreignId('evenement_id')->constrained('evenements')->onDelete('cascade');
    $table->foreignId('intervenant_id')->constrained('intervenants')->onDelete('cascade');
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evenement_intervenant');
    }
};
