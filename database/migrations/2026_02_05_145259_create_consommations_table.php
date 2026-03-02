<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('consommations', function (Blueprint $table) {
            // Clé primaire 'conso_id'
            $table->id('conso_id');

            // Clés étrangères vers applications et ressources
            // On précise bien les noms de colonnes cibles ('app_id' et 'res_id')
            $table->foreignId('app_id')->constrained('applications', 'app_id');
            $table->foreignId('res_id')->constrained('ressources', 'res_id');

            $table->date('mois');
            $table->decimal('volume', 10, 2);

            // Index de performance
            $table->index('mois');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consommations');
    }
};
