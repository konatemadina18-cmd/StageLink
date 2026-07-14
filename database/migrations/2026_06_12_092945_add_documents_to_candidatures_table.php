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
    Schema::table('candidatures', function (Blueprint $table) {
        $table->string('lettre_motivation')->nullable()->after('offre_id');
        $table->string('cv')->nullable()->after('lettre_motivation');
        $table->string('linkedin')->nullable()->after('cv');
        $table->string('portfolio')->nullable()->after('linkedin');
    });
}

public function down(): void
{
    Schema::table('candidatures', function (Blueprint $table) {
        $table->dropColumn(['lettre_motivation', 'cv', 'linkedin', 'portfolio']);
    });
}
};
