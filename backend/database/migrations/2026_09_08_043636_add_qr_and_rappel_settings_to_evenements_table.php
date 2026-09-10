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
    Schema::table('evenements', function (Blueprint $table) {
        $table->string('qr_code')->unique()->nullable()->after('statut');
        $table->unsignedInteger('duree_fenetre_scan_debut')->default(60)->after('qr_code');
        $table->unsignedInteger('duree_fenetre_scan_fin')->default(30)->after('duree_fenetre_scan_debut');
        $table->boolean('rappel_actif')->default(true)->after('duree_fenetre_scan_fin');
        $table->unsignedInteger('delai_rappel_heures')->default(24)->after('rappel_actif');
    });
}

public function down(): void
{
    Schema::table('evenements', function (Blueprint $table) {
        $table->dropColumn([
            'qr_code', 'duree_fenetre_scan_debut', 'duree_fenetre_scan_fin',
            'rappel_actif', 'delai_rappel_heures',
        ]);
    });
}
};
