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
            $table->foreignId('offre_id')
                  ->nullable()
                  ->constrained()
                  ->onDelete('set null')
                  ->after('r_h_id');
        });
    }
    
    public function down(): void
    {
        Schema::table('candidatures', function (Blueprint $table) {
            $table->dropForeign(['offre_id']);
            $table->dropColumn('offre_id');
        });
    }
};
