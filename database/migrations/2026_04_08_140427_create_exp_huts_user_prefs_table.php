<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('exp_huts_user_prefs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->unique();
            $table->boolean('push_enabled')->default(false);
            $table->boolean('notify_incidents')->default(true);
            $table->boolean('notify_activity_start')->default(true);
            $table->boolean('notify_activity_end')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('exp_huts_user_prefs'); }
};
