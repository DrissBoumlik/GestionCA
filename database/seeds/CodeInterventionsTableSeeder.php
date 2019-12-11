<?php

use Illuminate\Database\Seeder;

class CodeInterventionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $db_data = [
            ['code' => 'ABS', 'label' => 'client absent', 'description' => ''],
            ['code' => 'DEF', 'label' => 'Relève en définitif', 'description' => ''],
            ['code' => 'DIP', 'label' => 'installation client KO', 'description' => ''],
            ['code' => 'DOP', 'label' => 'egpt opérateur KO', 'description' => ''],
            ['code' => 'INL', 'label' => 'inéligibilité adsl', 'description' => ''],
            ['code' => 'PAC', 'label' => 'pas d\'accées internet', 'description' => ''],
            ['code' => 'PDC', 'label' => 'pas de défault constaté', 'description' => ''],
            ['code' => 'PRO', 'label' => 'relève en provisoire', 'description' => ''],
            ['code' => 'REO', 'label' => 'réorientation', 'description' => ''],
            ['code' => 'RMC', 'label' => 'RDV manqué client', 'description' => ''],
            ['code' => 'RMF', 'label' => 'RDV manqué FT', 'description' => '']
        ];
        \App\Models\CodeIntervention::insert($db_data);
    }
}
