<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('offres', function (Blueprint $table) {
            $table->id();

            $table->foreignId('entreprise_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->foreignId('r_h_id')
                  ->constrained('r_h_s')
                  ->onDelete('cascade');

            $table->string('titre');

            $table->text('description');

            $table->string('type_stage'); // Ex: stage académique, stage de fin d'étude...

            $table->string('duree'); // Ex: 1 mois, 3 mois, 6 mois...

            $table->string('filiere_cible')->nullable(); // Ex: Informatique, Gestion...

            $table->string('competences_requises')->nullable();

            $table->string('lieu')->nullable();

            $table->date('date_debut')->nullable();

            $table->date('date_fin_candidature')->nullable();

            $table->string('statut')->default('active'); // active, fermée, pourvue

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offres');
    }
};