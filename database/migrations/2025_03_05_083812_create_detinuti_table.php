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
        Schema::create('detinuti', function (Blueprint $table) {
            $table->id();
            $table->date('data')->nullable();
            $table->foreignId('id_institution')->constrained('institutions')->onDelete('cascade');
            $table->bigInteger('total')->nullable();
            $table->bigInteger('real_inmates')->nullable();
            $table->bigInteger('in_search')->nullable();
            $table->bigInteger('pretrial_detention')->nullable();
            $table->bigInteger('initial_conditions')->nullable();
            $table->bigInteger('life')->nullable();
            $table->bigInteger('female')->nullable();
            $table->bigInteger('minors')->nullable();
            $table->bigInteger('open_sector')->nullable();
            $table->bigInteger('no_escort')->nullable();
            $table->bigInteger('monitoring_bracelets')->nullable();
            $table->bigInteger('hunger_strike')->nullable();
            $table->bigInteger('disciplinary_insulator')->nullable();
            $table->bigInteger('admitted_to_hospitals')->nullable();
            $table->bigInteger('employed_ip_in_hospitals')->nullable();
            $table->bigInteger('employed_dds_in_hospitals')->nullable();
            $table->bigInteger('work_outside')->nullable();
            $table->bigInteger('employed_ip_work_outside')->nullable();
            $table->timestamps();
            $table->index('id_institution', 'idx_detinuti_id_institution');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detinuti');
    }
};
