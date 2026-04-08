<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('exp_huts_daily_observations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('exp_huts_usage_sessions')->onDelete('cascade');
            $table->unsignedBigInteger('hut_id')->nullable();
            $table->date('observation_date');
            $table->text('observation');
            $table->unsignedBigInteger('observed_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('exp_huts_daily_observations'); }
};
