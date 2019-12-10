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
            $data->map(function ($rowItems) use (&$db_data) {
                $rowItems = collect($rowItems)->values()->all();
                $index = 0;
                $db_data[] = [
                    'Type_Note' => $rowItems[$index++],
                    'Utilisateur' => $rowItems[$index++],
                    'Resultat_Appel' => $rowItems[$index++],
                    'Date_Nveau_RDV' => $rowItems[$index++],
                    'Heure_Nveau_RDV' => $rowItems[$index++],
                    'Marge_Nveau_RDV' => $rowItems[$index++],
                    'Id_Externe' => $rowItems[$index++],
                    'Date_Création' => $rowItems[$index++],
                    'Code_Postal_Site' => $rowItems[$index++],
                    'Drapeaux' => $rowItems[$index++],
                    'Code_Type_Intervention' => $rowItems[$index++],
                    'Date_Rdv' => $rowItems[$index++],
                    'Nom_Societe' => $rowItems[$index++],
                    'Nom_Region' => $rowItems[$index++],
                    'Nom_Domaine' => $rowItems[$index++],
                    'Nom_Agence' => $rowItems[$index++],
                    'Nom_Activite' => $rowItems[$index++],
                    'Date_Heure_Note' => $rowItems[$index++],
                    'Date_Heure_Note_Annee' => $rowItems[$index++],
                    'Date_Heure_Note_Mois' => $rowItems[$index++],
                    'Date_Heure_Note_Semaine' => $rowItems[$index++],
                    'Date_Note' => $rowItems[$index++],
                    'Groupement' => $rowItems[$index++],
                    'Gpmt_Appel_Pré' => $rowItems[$index++],
                    'Code_Intervention' => $rowItems[$index++],
                    'EXPORT_ALL_Nom_SITE' => $rowItems[$index++],
                    'EXPORT_ALL_Nom_TECHNICIEN' => $rowItems[$index++],
                    'EXPORT_ALL_PRENom_TECHNICIEN' => $rowItems[$index++],
                    'EXPORT_ALL_Nom_CLIENT' => $rowItems[$index++],
                    'EXPORT_ALL_Nom_EQUIPEMENT' => $rowItems[$index++],
                    'EXPORT_ALL_EXTRACT_CUI' => $rowItems[$index++],
                    'EXPORT_ALL_Date_CHARGEMENT_PDA' => $rowItems[$index++],
                    'EXPORT_ALL_Date_SOLDE' => $rowItems[$index++],
                    'EXPORT_ALL_Date_VALIDATION' => $rowItems[$index++],
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
