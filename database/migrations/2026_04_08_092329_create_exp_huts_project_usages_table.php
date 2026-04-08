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
        Schema::create('exp_huts_project_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hut_id')->constrained('exp_huts_huts')->onDelete('cascade');
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('study_activity_id')->nullable();
            $table->string('phase_name')->nullable();
            $table->date('date_start');
            $table->date('date_end');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exp_huts_project_usages');
    }
};
