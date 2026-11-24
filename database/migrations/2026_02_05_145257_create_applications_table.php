<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Même structure que database/sql/schema.sql (sujet du concours).
    public function up(): void
    {
        Schema::create('application', function (Blueprint $table) {
            $table->id('app_id');
            $table->string('nom', 80)->unique();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application');
    }
};
