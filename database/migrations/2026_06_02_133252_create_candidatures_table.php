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
        Schema::create('candidatures', function (Blueprint $table) {
            $table->id();
    
            $table->foreignId('candidat_id')
                  ->constrained()
                  ->onDelete('cascade');
    
            $table->foreignId('entreprise_id')
                  ->constrained()
                  ->onDelete('cascade');
    
            $table->foreignId('r_h_id')
                  ->constrained('r_h_s')
                  ->onDelete('cascade');
    
            $table->date('date_candidature');
    
            $table->string('type_stage');
    
            $table->string('duree');
    
            $table->string('statut')->default('En attente');
    
            $table->decimal('score', 5, 2)->nullable();
    
            $table->text('commentaire_rh')->nullable();
    
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidatures');
    }
};
