<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('exp_huts_sleeper_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('exp_huts_usage_sessions')->onDelete('cascade');
            $table->foreignId('hut_id')->constrained('exp_huts_huts')->onDelete('cascade');
            $table->foreignId('sleeper_id')->constrained('exp_huts_sleepers')->onDelete('cascade');
            $table->date('assignment_date');
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
            $table->unique(['session_id','hut_id','assignment_date'], 'unique_session_hut_date');
        });
    }
    public function down(): void { Schema::dropIfExists('exp_huts_sleeper_assignments'); }
};
