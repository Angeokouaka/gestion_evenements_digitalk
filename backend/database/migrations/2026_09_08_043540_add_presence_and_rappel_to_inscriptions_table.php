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
    Schema::table('inscriptions', function (Blueprint $table) {
        $table->string('qr_code')->unique()->nullable()->after('statut');
        $table->boolean('presence_arrivee')->default(false)->after('qr_code');
        $table->timestamp('date_presence_arrivee')->nullable()->after('presence_arrivee');
        $table->boolean('presence_depart')->default(false)->after('date_presence_arrivee');
        $table->timestamp('date_presence_depart')->nullable()->after('presence_depart');
        $table->boolean('rappel_envoye')->default(false)->after('date_presence_depart');
        $table->timestamp('date_rappel_envoye')->nullable()->after('rappel_envoye');
    });
}

public function down(): void
{
    Schema::table('inscriptions', function (Blueprint $table) {
        $table->dropColumn([
            'qr_code', 'presence_arrivee', 'date_presence_arrivee',
            'presence_depart', 'date_presence_depart',
            'rappel_envoye', 'date_rappel_envoye',
        ]);
    });
}
};
