<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('injuries', function (Blueprint $table) {
            $table->id();
            $table->date('data')->nullable();
            $table->foreignId('id_institution')->constrained('institutions')->onDelete('cascade');
            $table->foreignId('id_injuries_category')->constrained('injuries_category')->onDelete('cascade');
            $table->integer('persons_involved')->nullable();
            $table->text('injuries_text')->nullable();
            $table->timestamps();
            $table->index('id_institution', 'idx_injuries_id_institution');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('injuries');
    }
};
