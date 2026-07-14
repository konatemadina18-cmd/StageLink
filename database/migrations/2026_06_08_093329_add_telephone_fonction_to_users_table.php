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
            if (!Schema::hasColumn('users', 'telephone')) {
                $table->string('telephone', 20)->nullable()->after('role');
            }

            if (!Schema::hasColumn('users', 'fonction')) {
                $table->string('fonction')->nullable()->after('telephone');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columnsToDrops = [];

            if (Schema::hasColumn('users', 'telephone')) {
                $columnsToDrops[] = 'telephone';
            }

            if (Schema::hasColumn('users', 'fonction')) {
                $columnsToDrops[] = 'fonction';
            }

            if (!empty($columnsToDrops)) {
                $table->dropColumn($columnsToDrops);
            }
        });
    }
};