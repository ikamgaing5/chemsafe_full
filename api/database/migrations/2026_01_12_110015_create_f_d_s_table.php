<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('infofds', function (Blueprint $table) {
            $table->id();
            $table->uuid(column: 'code')->nullable()->unique();
            $table->string('produit_id');
            $table->string('physique');
            $table->string('sante');
            $table->string('ppt');
            $table->string('stabilite');
            $table->string('eviter');
            $table->string('incompatible');
            $table->string('reactivite');
            $table->string('stockage');
            $table->string('secours');
            $table->string('epi');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('infofds');
    }
};
