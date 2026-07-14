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
    Schema::table('candidats', function (Blueprint $table) {
        $table->string('photo')->nullable()->after('user_id');
        $table->string('cv')->nullable()->after('photo');
        $table->string('adresse')->nullable()->after('cv');
        $table->string('github')->nullable()->after('linkedin');
        $table->string('portfolio')->nullable()->after('github');
        $table->text('competences')->nullable()->after('portfolio');
        $table->text('experiences')->nullable()->after('competences');
        $table->text('langues')->nullable()->after('experiences');
        $table->text('certifications')->nullable()->after('langues');
    });
}

public function down(): void
{
    Schema::table('candidats', function (Blueprint $table) {
        $table->dropColumn(['photo','cv','adresse','github','portfolio','competences','experiences','langues','certifications']);
    });
}
};
