<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ressource', function (Blueprint $table) {
            $table->id('res_id');
            $table->string('nom', 30)->unique();
            $table->string('unite', 10);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ressource');
    }
};
