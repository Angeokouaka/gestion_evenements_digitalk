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
    Schema::table('evenement_intervenant', function (Blueprint $table) {
        $table->string('role')->default('Intervenant')->after('intervenant_id');
    });
}

public function down(): void
{
    Schema::table('evenement_intervenant', function (Blueprint $table) {
        $table->dropColumn('role');
    });
}
};
