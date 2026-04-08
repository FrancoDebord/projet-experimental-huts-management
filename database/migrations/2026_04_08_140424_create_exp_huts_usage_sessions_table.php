<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('exp_huts_usage_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('phase_name')->nullable();
            $table->date('date_start');
            $table->date('date_end');
            $table->text('notes')->nullable();
            $table->enum('status', ['planned','active','completed','cancelled'])->default('planned');
            $table->boolean('notifications_sent_start')->default(false);
            $table->boolean('notifications_sent_end')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('exp_huts_usage_sessions'); }
};
