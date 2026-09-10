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
    Schema::create('certificats', function (Blueprint $table) {
        $table->id();
        $table->timestamp('date_generation')->nullable();
        $table->string('code_verification')->unique();
        $table->string('url_fichier');
        $table->timestamp('date_envoi')->nullable();
        $table->foreignId('inscription_id')->unique()->constrained('inscriptions')->cascadeOnDelete();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('certificats');
}
};
