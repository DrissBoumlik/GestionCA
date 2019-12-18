<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->date('date_reception_demande')->nullable();
            $table->string('operateur')->nullable();
            $table->string('code_projet_operateur')->nullable();
            $table->string('cdp_operateur')->nullable();
            $table->string('agence')->nullable();
            $table->string('cdp_circet')->nullable();
            $table->string('otc_uo')->nullable();
            $table->string('code_site')->nullable();
            $table->string('patrimoine')->nullable();
            $table->string('site_b')->nullable();
            $table->string('cle')->nullable();
            $table->string('type_op')->nullable();
            $table->string('type_support')->nullable();
            $table->string('conf')->nullable();
            $table->string('type_eb_tiers')->nullable();
            $table->string('acteur')->nullable();
            // $table->string('statut')->nullable();
            $table->enum('statut', ['affecter', 'encours', 'envoyee', 'swapiso']);
            $table->date('date_envoi_eb')->nullable();
            $table->date('date_validation_eb_par_tiers')->nullable();
            $table->string('etape_process_accueil_chez_tiers')->nullable();
            $table->string('commentaire')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tasks');
    }
}
