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
        Schema::create('exp_huts_incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hut_id')->nullable()->constrained('exp_huts_huts')->onDelete('set null');
            $table->unsignedBigInteger('project_usage_id')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->string('title');
            $table->text('description');
            $table->date('incident_date');
            $table->enum('severity', ['low', 'medium', 'high', 'critical'])->default('low');
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->unsignedBigInteger('reported_by')->nullable();
            $table->unsignedBigInteger('resolved_by')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->text('resolution_notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exp_huts_incidents');
    }
};
