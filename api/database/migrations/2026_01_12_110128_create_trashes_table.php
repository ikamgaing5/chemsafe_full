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
        Schema::create('trash', function (Blueprint $table) {
            $table->id();
            $table->uuid(column: 'code')->nullable()->unique();
            $table->string('user_id')->nullable();
            $table->string('produit_id')->nullable();
            $table->string('atelier_id')->nullable();
            $table->string('usine_id')->nullable();
            $table->timestamp('created_at');
            $table->index('created_at');
            $table->string('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trash');
    }
};
