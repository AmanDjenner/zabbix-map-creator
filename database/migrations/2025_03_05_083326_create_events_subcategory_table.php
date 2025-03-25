<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events_subcategory', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->foreignId('id_events_category')->constrained('events_category')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events_subcategory');
    }
};