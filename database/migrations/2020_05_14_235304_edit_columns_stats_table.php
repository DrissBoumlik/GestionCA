<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EditColumnsStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stats',function (Blueprint $table){
            $table->string('Type_Note', 100)->nullable()->change();
            $table->string('Utilisateur', 100)->nullable()->change();
            $table->string('Date_Nveau_RDV', 100)->nullable()->change();
            $table->string('Heure_Nveau_RDV', 100)->nullable()->change();
            $table->string('Marge_Nveau_RDV', 100)->nullable()->change();
            $table->string('Id_Externe', 100)->nullable()->change();
            $table->string('Date_Creation', 100)->nullable()->change();
            $table->string('Code_Postal_Site', 100)->nullable()->change();
//            $table->string('Departement', 100)->nullable()->change();
            $table->string('Drapeaux', 100)->nullable()->change();
            $table->string('Code_Type_Intervention', 100)->nullable()->change();
            $table->string('Date_Rdv', 100)->nullable()->change();
            $table->string('Nom_Societe', 100)->nullable()->change();
            $table->string('Nom_Region', 100)->nullable()->change();
            $table->string('Nom_Domaine', 100)->nullable()->change();
            $table->string('Nom_Agence', 100)->nullable()->change();
            $table->string('Nom_Activite', 100)->nullable()->change();
            $table->string('Date_Heure_Note', 100)->nullable()->change();
            $table->string('Date_Heure_Note_Annee', 100)->nullable()->change();
            $table->string('Date_Heure_Note_Mois', 100)->nullable()->change();
            $table->string('Date_Heure_Note_Semaine', 100)->nullable()->change();
            $table->string('Groupement', 100)->nullable()->change();
            $table->string('key_Groupement', 100)->nullable()->change();

            $table->string('Gpmt_Appel_Pre', 100)->nullable()->change();
            $table->string('Code_Intervention', 100)->nullable()->change();
            $table->string('EXPORT_ALL_Nom_SITE', 100)->nullable()->change();
            $table->string('EXPORT_ALL_Nom_TECHNICIEN', 100)->nullable()->change();
            $table->string('EXPORT_ALL_PRENom_TECHNICIEN', 100)->nullable()->change();
//            $table->string('EXPORT_ALL_Nom_CLIENT', 100)->nullable()->change();
            $table->string('EXPORT_ALL_Nom_EQUIPEMENT', 100)->nullable()->change();
            $table->string('EXPORT_ALL_EXTRACT_CUI', 100)->nullable()->change();
            $table->string('EXPORT_ALL_Date_CHARGEMENT_PDA', 100)->nullable()->change();
            $table->string('EXPORT_ALL_Date_SOLDE', 100)->nullable()->change();
            $table->string('EXPORT_ALL_Date_VALIDATION', 100)->nullable()->change();

            $table->String('produit', 100)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
