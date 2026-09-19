<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inscriptions', function (Blueprint $table) {
            $table->boolean('avis_envoye')->default(false)->after('rappel_envoye');
            $table->timestamp('date_avis_envoye')->nullable()->after('avis_envoye');
        });
    }

    public function down(): void
    {
        Schema::table('inscriptions', function (Blueprint $table) {
            $table->dropColumn(['avis_envoye', 'date_avis_envoye']);
        });
    }
};
