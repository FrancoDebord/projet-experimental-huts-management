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
        Schema::create('exp_huts_huts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('site_id')->constrained('exp_huts_sites')->onDelete('cascade');
            $table->unsignedInteger('number');
            $table->string('name');
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->enum('status', ['available', 'in_use', 'damaged', 'abandoned'])->default('available');
            $table->string('image_path')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->unique(['site_id', 'number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exp_huts_huts');
    }
};
