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
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'native_language')) {
                $table->string('native_language', 10)->default('vi')->after('password');
            }
            if (! Schema::hasColumn('users', 'target_language')) {
                $table->string('target_language', 10)->default('en')->after('native_language');
            }
            if (! Schema::hasColumn('users', 'target_level')) {
                $table->enum('target_level', ['A1', 'A2', 'B1', 'B2'])->default('A2')->after('target_language');
            }
            if (! Schema::hasColumn('users', 'default_level')) {
                $table->string('default_level', 10)->default('A2')->after('target_level');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columnsToDrop = [];
            foreach (['native_language', 'target_language', 'target_level', 'default_level'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $columnsToDrop[] = $column;
                }
            }
            if (! empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
