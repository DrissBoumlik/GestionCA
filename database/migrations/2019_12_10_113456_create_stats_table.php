<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stats', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('Type_Note')->nullable();
            $table->string('Utilisateur')->nullable();
            $table->string('Resultat_Appel')->nullable();
            $table->string('Date_Nveau_RDV')->nullable();
            $table->string('Heure_Nveau_RDV')->nullable();
            $table->string('Marge_Nveau_RDV')->nullable();
            $table->string('Id_Externe')->nullable();
            $table->string('Date_Creation')->nullable();
            $table->string('Code_Postal_Site')->nullable();
//            $table->string('Departement')->nullable();
            $table->string('Drapeaux')->nullable();
            $table->string('Code_Type_Intervention')->nullable();
            $table->string('Date_Rdv')->nullable();
            $table->string('Nom_Societe')->nullable();
            $table->string('Nom_Region')->nullable();
            $table->string('Nom_Domaine')->nullable();
            $table->string('Nom_Agence')->nullable();
            $table->string('Nom_Activite')->nullable();
            $table->string('Date_Heure_Note')->nullable();
            $table->string('Date_Heure_Note_Annee')->nullable();
            $table->string('Date_Heure_Note_Mois')->nullable();
            $table->string('Date_Heure_Note_Semaine')->nullable();
            $table->date('Date_Note')->nullable();
            $table->string('Groupement')->nullable();
            $table->string('Gpmt_Appel_Pre')->nullable();
            $table->string('Code_Intervention')->nullable();
            $table->string('EXPORT_ALL_Nom_SITE')->nullable();
            $table->string('EXPORT_ALL_Nom_TECHNICIEN')->nullable();
            $table->string('EXPORT_ALL_PRENom_TECHNICIEN')->nullable();
//            $table->string('EXPORT_ALL_Nom_CLIENT')->nullable();
            $table->string('EXPORT_ALL_Nom_EQUIPEMENT')->nullable();
            $table->string('EXPORT_ALL_EXTRACT_CUI')->nullable();
            $table->string('EXPORT_ALL_Date_CHARGEMENT_PDA')->nullable();
            $table->string('EXPORT_ALL_Date_SOLDE')->nullable();
            $table->string('EXPORT_ALL_Date_VALIDATION')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stats');
    }
}
