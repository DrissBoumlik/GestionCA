<?php

namespace App\Imports;

use App\Models\Stats;
use Exception;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;


class StatsImport implements ToModel, WithHeadingRow //ToCollection
{
    private $days = [];
    public $data = [];
    private $index = 0;

    public function __construct($days)
    {
        if ($days) {
            $this->days = explode(',', $days);
            \DB::table('stats')->whereIn('date_note', $this->days)->delete();
        }
    }

    public function collection(Collection $rows)
    {
        $rows->shift();
        $data = $rows->map(function ($row, $index) {
//            $formatted_date = $this->transformDate($row[22]);
            if (!$this->days || in_array($row[22], $this->days)) {
                $_index = 0;
                $item = [
                    'Type_Note' => $row[$_index++],
                    'Utilisateur' => $row[$_index++],
                    'Resultat_Appel' => $row[$_index++],
                    'Date_Nveau_RDV' => $row[$_index++],
                    'Heure_Nveau_RDV' => $row[$_index++],
                    'Marge_Nveau_RDV' => $row[$_index++],
                    'Id_Externe' => $row[$_index++],
                    'Date_Creation' => $row[$_index++],
                    'Code_Postal_Site' => $row[$_index++],
//                    'Departement' => $row[],
                    'Drapeaux' => $row[$_index++],
                    'Code_Type_Intervention' => $row[$_index++],
                    'Date_Rdv' => $row[$_index++],
                    'Nom_Societe' => $row[$_index++],
                    'Nom_Region' => $row[$_index++],
                    'Nom_Domaine' => $row[$_index++],
                    'Nom_Agence' => $row[$_index++],
                    'Nom_Activite' => $row[$_index++],
                    'Date_Heure_Note' => $row[$_index++],
                    'Date_Heure_Note_Annee' => $row[$_index++],
                    'Date_Heure_Note_Mois' => $row[$_index++],
                    'Date_Heure_Note_Semaine' => $row[$_index++],
                    'Date_Note' => $row[$_index++], // $row[],
                    'Groupement' => $row[$_index++],
                    'key_Groupement' => clean($row[$_index++]),

                    'Gpmt_Appel_Pre' => $row[$_index++],
                    'Code_Intervention' => $row[$_index++],
                    'EXPORT_ALL_Nom_SITE' => $row[$_index++],
                    'EXPORT_ALL_Nom_TECHNICIEN' => $row[$_index++],
                    'EXPORT_ALL_PRENom_TECHNICIEN' => $row[$_index++],
//                    'EXPORT_ALL_Nom_CLIENT' => $row['dimension_note'EXPORT_ALL_Nom_CLIENT],
                    'EXPORT_ALL_Nom_EQUIPEMENT' => $row[$_index++],
                    'EXPORT_ALL_EXTRACT_CUI' => $row[$_index++],
                    'EXPORT_ALL_Date_CHARGEMENT_PDA' => $row[$_index++],
                    'EXPORT_ALL_Date_SOLDE' => $row[$_index++],
                    'EXPORT_ALL_Date_VALIDATION' => $row[$_index++],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                return $item;
            }
        });
        Stats::insert($data->all());
    }

    /**
     * @param array $row
     *
     * @return Stats
     */
    public function model($row)
    {
        $formatted_date = $this->transformDate($row['dimension_notesdate_note']);
        if (!$this->days || in_array($formatted_date, $this->days)) {
//            return new Stats([
//                'Type_Note' => $row['dimension_notestype_note'],
//                'Utilisateur' => $row['dimension_notesutilisateur'],
//                'Resultat_Appel' => $row['dimension_notesresultat_appel'],
//                'Date_Nveau_RDV' => $row['dimension_notesdate_nveau_rdv'],
//                'Heure_Nveau_RDV' => $row['dimension_notesheure_nveau_rdv'],
//                'Marge_Nveau_RDV' => $row['dimension_notesmarge_nveau_rdv'],
//                'Id_Externe' => $row['dimension_notesid_externe'],
//                'Date_Creation' => $row['dimension_notesdate_creation'],
//                'Code_Postal_Site' => $row['dimension_notescode_postal_site'],
////                    'Departement' => $row['dimension_noteDepartement'],
//                'Drapeaux' => $row['dimension_notesdrapeaux'],
//                'Code_Type_Intervention' => $row['dimension_notescode_type_intervention'],
//                'Date_Rdv' => $row['dimension_notesdate_rdv'],
//                'Nom_Societe' => $row['dimension_notesnom_societe'],
//                'Nom_Region' => $row['dimension_notesnom_region'],
//                'Nom_Domaine' => $row['dimension_notesnom_domaine'],
//                'Nom_Agence' => $row['dimension_notesnom_agence'],
//                'Nom_Activite' => $row['dimension_notesnom_activite'],
//                'Date_Heure_Note' => $row['dimension_notesdate_heure_note'],
//                'Date_Heure_Note_Annee' => $row['dimension_notesdate_heure_note_annee'],
//                'Date_Heure_Note_Mois' => $row['dimension_notesdate_heure_note_mois'],
//                'Date_Heure_Note_Semaine' => $row['dimension_notesdate_heure_note_semaine'],
//                'Date_Note' => $formatted_date, // $row['dimension_notesdate_note'],
//                'Groupement' => $row['dimension_notesgroupement'],
//                'key_Groupement' => clean($row['dimension_notesgroupement']),
//
//                'Gpmt_Appel_Pre' => $row['dimension_notesgpmt_appel_pre'],
//                'Code_Intervention' => $row['dimension_notescode_intervention'],
//                'EXPORT_ALL_Nom_SITE' => $row['dimension_notesexport_all_nom_site'],
//                'EXPORT_ALL_Nom_TECHNICIEN' => $row['dimension_notesexport_all_nom_technicien'],
//                'EXPORT_ALL_PRENom_TECHNICIEN' => $row['dimension_notesexport_all_prenom_technicien'],
////                    'EXPORT_ALL_Nom_CLIENT' => $row['dimension_note'EXPORT_ALL_Nom_CLIENT],
//                'EXPORT_ALL_Nom_EQUIPEMENT' => $row['dimension_notesexport_all_nom_equipement'],
//                'EXPORT_ALL_EXTRACT_CUI' => $row['dimension_notesexport_all_extract_cui'],
//                'EXPORT_ALL_Date_CHARGEMENT_PDA' => $row['dimension_notesexport_all_date_chargement_pda'],
//                'EXPORT_ALL_Date_SOLDE' => $row['dimension_notesexport_all_date_solde'],
//                'EXPORT_ALL_Date_VALIDATION' => $row['dimension_notesexport_all_date_validation'],
//            ]);
            $this->data[] = [
                'Type_Note' => $row['dimension_notestype_note'],
                'Utilisateur' => $row['dimension_notesutilisateur'],
                'Resultat_Appel' => $row['dimension_notesresultat_appel'],
                'Date_Nveau_RDV' => $row['dimension_notesdate_nveau_rdv'],
                'Heure_Nveau_RDV' => $row['dimension_notesheure_nveau_rdv'],
                'Marge_Nveau_RDV' => $row['dimension_notesmarge_nveau_rdv'],
                'Id_Externe' => $row['dimension_notesid_externe'],
                'Date_Creation' => $row['dimension_notesdate_creation'],
                'Code_Postal_Site' => $row['dimension_notescode_postal_site'],
//                    'Departement' => $row['dimension_noteDepartement'],
                'Drapeaux' => $row['dimension_notesdrapeaux'],
                'Code_Type_Intervention' => $row['dimension_notescode_type_intervention'],
                'Date_Rdv' => $row['dimension_notesdate_rdv'],
                'Nom_Societe' => $row['dimension_notesnom_societe'],
                'Nom_Region' => $row['dimension_notesnom_region'],
                'Nom_Domaine' => $row['dimension_notesnom_domaine'],
                'Nom_Agence' => $row['dimension_notesnom_agence'],
                'Nom_Activite' => $row['dimension_notesnom_activite'],
                'Date_Heure_Note' => $row['dimension_notesdate_heure_note'],
                'Date_Heure_Note_Annee' => $row['dimension_notesdate_heure_note_annee'],
                'Date_Heure_Note_Mois' => $row['dimension_notesdate_heure_note_mois'],
                'Date_Heure_Note_Semaine' => $row['dimension_notesdate_heure_note_semaine'],
                'Date_Note' => $formatted_date, // $row['dimension_notesdate_note'],
                'Groupement' => $row['dimension_notesgroupement'],
                'key_Groupement' => clean($row['dimension_notesgroupement']),

                'Gpmt_Appel_Pre' => $row['dimension_notesgpmt_appel_pre'],
                'Code_Intervention' => $row['dimension_notescode_intervention'],
                'EXPORT_ALL_Nom_SITE' => $row['dimension_notesexport_all_nom_site'],
                'EXPORT_ALL_Nom_TECHNICIEN' => $row['dimension_notesexport_all_nom_technicien'],
                'EXPORT_ALL_PRENom_TECHNICIEN' => $row['dimension_notesexport_all_prenom_technicien'],
//                    'EXPORT_ALL_Nom_CLIENT' => $row['dimension_note'EXPORT_ALL_Nom_CLIENT],
                'EXPORT_ALL_Nom_EQUIPEMENT' => $row['dimension_notesexport_all_nom_equipement'],
                'EXPORT_ALL_EXTRACT_CUI' => $row['dimension_notesexport_all_extract_cui'],
                'EXPORT_ALL_Date_CHARGEMENT_PDA' => $row['dimension_notesexport_all_date_chargement_pda'],
                'EXPORT_ALL_Date_SOLDE' => $row['dimension_notesexport_all_date_solde'],
                'EXPORT_ALL_Date_VALIDATION' => $row['dimension_notesexport_all_date_validation'],
            ];
            if ($this->index % 500) {
                Stats::insert($this->data);
                $this->data = [];
            }
            $this->index++;
        }
    }

    /**
     * Transform a date value into a Carbon object.
     *
     * @return string|null
     */
    public function transformDate($value, $format = 'Y-m-d')
    {
        try {
            return Carbon::instance(Date::excelToDateTimeObject($value))->format($format);
        } catch (Exception $e) {
            return Carbon::createFromFormat($format, $value)->toDateString();
        }
    }

}
