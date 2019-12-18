<?php

use App\Models\Stats;
use Illuminate\Database\Seeder;
use PhpOffice\PhpSpreadsheet\IOFactory;

class StatsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        try {
            $reader = IOFactory::createReaderForFile('public/data/data.xlsx');
            $reader->setReadDataOnly(true);
            $sheet = $reader->load('public/data/data.xlsx');
            $data = $sheet->getActiveSheet()->toArray(null, true, true, true);
            $removed = array_shift($data);

            $data = collect($data);
            $db_data = [];
            $data->map(function ($rowItems, $index) use (&$db_data) {
                $rowItems = collect($rowItems)->values()->all();
                $_index = 0;
//                if ($_index == 0)
//                    dump($rowItems);
                $db_data[] = [
                    'Type_Note' => $rowItems[$_index++],
                    'Utilisateur' => $rowItems[$_index++],
                    'Resultat_Appel' => $rowItems[$_index++],
                    'Date_Nveau_RDV' => $rowItems[$_index++],
                    'Heure_Nveau_RDV' => $rowItems[$_index++],
                    'Marge_Nveau_RDV' => $rowItems[$_index++],
                    'Id_Externe' => $rowItems[$_index++],
                    'Date_Création' => $rowItems[$_index++],
                    'Code_Postal_Site' => $rowItems[$_index++],
//                    'Departement' => $rowItems[$index++],
                    'Drapeaux' => $rowItems[$_index++],
                    'Code_Type_Intervention' => $rowItems[$_index++],
                    'Date_Rdv' => $rowItems[$_index++],
                    'Nom_Societe' => $rowItems[$_index++],
                    'Nom_Region' => $rowItems[$_index++],
                    'Nom_Domaine' => $rowItems[$_index++],
                    'Nom_Agence' => $rowItems[$_index++],
                    'Nom_Activite' => $rowItems[$_index++],
                    'Date_Heure_Note' => $rowItems[$_index++],
                    'Date_Heure_Note_Annee' => $rowItems[$_index++],
                    'Date_Heure_Note_Mois' => $rowItems[$_index++],
                    'Date_Heure_Note_Semaine' => $rowItems[$_index++],
                    'Date_Note' => $rowItems[$_index++],
                    'Groupement' => $rowItems[$_index++],
                    'Gpmt_Appel_Pré' => $rowItems[$_index++],
                    'Code_Intervention' => $rowItems[$_index++],
                    'EXPORT_ALL_Nom_SITE' => $rowItems[$_index++],
                    'EXPORT_ALL_Nom_TECHNICIEN' => $rowItems[$_index++],
                    'EXPORT_ALL_PRENom_TECHNICIEN' => $rowItems[$_index++],
//                    'EXPORT_ALL_Nom_CLIENT' => $rowItems[$index++],
                    'EXPORT_ALL_Nom_EQUIPEMENT' => $rowItems[$_index++],
                    'EXPORT_ALL_EXTRACT_CUI' => $rowItems[$_index++],
                    'EXPORT_ALL_Date_CHARGEMENT_PDA' => $rowItems[$_index++],
                    'EXPORT_ALL_Date_SOLDE' => $rowItems[$_index++],
                    'EXPORT_ALL_Date_VALIDATION' => $rowItems[$_index],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
            });
            Stats::truncate();
            Stats::insert($db_data);
            return ['status' => 'OK'];
        } catch (\Exception $e) {
            return ['status' => 'Not OK'];
        }
    }
}
