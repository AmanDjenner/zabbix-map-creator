<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('object_prison_objects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('object_prison_id')->constrained('object_prisons')->onDelete('cascade');
            $table->foreignId('object_list_id')->constrained('object_list')->onDelete('cascade');
            $table->integer('quantity')->default(0);
            $table->timestamps();
            $table->unique(['object_prison_id', 'object_list_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('object_prison_objects');
    }
};