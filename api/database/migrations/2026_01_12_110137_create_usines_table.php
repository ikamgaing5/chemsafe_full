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
        schema::create('usine', function (Blueprint $table) {
            $table->id();
            $table->uuid(column: 'code')->nullable()->unique();
            $table->string('nomusine');
            $table->string('active')->default("true");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usine');
    }
};
