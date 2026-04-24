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
        Schema::create('atelier', function (Blueprint $table) {
            $table->id();
            $table->uuid(column: 'code')->nullable()->unique();
            $table->foreignIdFor(App\Models\Usine::class)->constrained('usine')->onDelete('cascade');
            $table->string('nomatelier');
            $table->string('active')->default("true");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ateliers');
    }
};
