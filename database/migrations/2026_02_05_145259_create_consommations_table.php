<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consommation', function (Blueprint $table) {
            $table->id('conso_id');
            $table->foreignId('app_id')->constrained('application', 'app_id');
            $table->foreignId('res_id')->constrained('ressource', 'res_id');
            $table->date('mois');
            $table->decimal('volume', 10, 2);
            $table->index('mois');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consommation');
    }
};
