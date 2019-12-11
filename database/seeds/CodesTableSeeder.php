<?php

use Illuminate\Database\Seeder;

class CodesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $db_data = [
            ['code' => 'PAD', 'description' => 'Problème adresse erronée'],
            ['code' => 'DMS', 'description' => 'Demande de mise en service effectuée'],
            ['code' => 'RF', 'description' => 'Report fournisseur'],
            ['code' => 'MC', 'description' => 'Manque moyen matériel ou compétence']
        ];
        \App\Models\Code::insert($db_data);
    }
}
