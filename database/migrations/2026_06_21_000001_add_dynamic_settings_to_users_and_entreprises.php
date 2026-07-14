<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('photo')->nullable()->after('fonction');
            $table->json('settings')->nullable()->after('photo');
            $table->timestamp('two_factor_enabled_at')->nullable()->after('settings');
        });

        Schema::table('entreprises', function (Blueprint $table) {
            $table->string('taille')->nullable()->after('secteur_activite');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['photo', 'settings', 'two_factor_enabled_at']);
        });

        Schema::table('entreprises', function (Blueprint $table) {
            $table->dropColumn('taille');
        });
    }
};
