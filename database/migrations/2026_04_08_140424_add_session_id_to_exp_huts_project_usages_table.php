<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('exp_huts_project_usages', function (Blueprint $table) {
            $table->unsignedBigInteger('session_id')->nullable()->after('id');
        });
    }
    public function down(): void {
        Schema::table('exp_huts_project_usages', fn(Blueprint $t) => $t->dropColumn('session_id'));
    }
};
