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
        schema::create('produit', function (Blueprint $table) {
            $table->id();
            $table->uuid('code');
            $table->string('nomprod');
            $table->string('type_emballage');
            $table->string('poids');
            $table->string('nature');
            $table->string('utilisation');
            $table->string('fabricant');
            $table->string('photo');
            $table->string('fds');
            $table->string('risque');
        });


        Schema::create('contenir', function (Blueprint $table) {
            $table->foreignId('atelier_id')->constrained()->onDelete('cascade');
            $table->foreignId('produit_id')->constrained()->onDelete('cascade');
        });

        Schema::create('possede', function (Blueprint $table) {
            $table->foreignId('danger_id')->constrained()->onDelete('cascade');
            $table->foreignId('produit_id')->constrained()->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produit');
        Schema::dropIfExists('contenir');
        Schema::dropIfExists('possede');
    }
};
