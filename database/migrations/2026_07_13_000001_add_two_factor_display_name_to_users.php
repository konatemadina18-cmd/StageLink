<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('display_name')->nullable()->after('prenom');
            $table->string('two_factor_method')->nullable()->after('two_factor_enabled_at');
            $table->string('two_factor_code')->nullable()->after('two_factor_method');
            $table->timestamp('two_factor_expires_at')->nullable()->after('two_factor_code');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'display_name',
                'two_factor_method',
                'two_factor_code',
                'two_factor_expires_at',
            ]);
        });
    }
};
