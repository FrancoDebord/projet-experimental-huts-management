<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        foreach (['exp_huts_sites','exp_huts_huts','exp_huts_project_usages','exp_huts_state_changes','exp_huts_incidents'] as $table) {
            Schema::table($table, fn(Blueprint $t) => $t->softDeletes());
        }
    }
    public function down(): void {
        foreach (['exp_huts_sites','exp_huts_huts','exp_huts_project_usages','exp_huts_state_changes','exp_huts_incidents'] as $table) {
            Schema::table($table, fn(Blueprint $t) => $t->dropSoftDeletes());
        }
    }
};
