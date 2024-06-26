<?php

namespace App\Imports;

use App\Models\Stats;
use App\Models\UserFlag;
use Exception;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;


class StatsImport implements ToModel, WithHeadingRow, WithChunkReading, WithBatchInserts //ToCollection
{
    use Importable;

    private $days = [];
    public $data = [];
    private $index = 0;
    private $user_flag;
    private $cuivreArray = array('MAINTENANCE_AMS', 'MAINTENANCE_BL', 'MAINTENANCE_NUM', 'MAINTENANCE_OPT', 'MAINTENANCE_PLU', 'MAINTENANCE_POTEAU', 'MAINTENANCE_RET', 'PRODUCTION_ENT', 'PRODUCTION_POI', 'PRODUCTION_R02', 'PRODUCTION_R03', 'PRODUCTION_R10');
    private $ftthArray = array('MAINTENANCE_FTH', 'PRODUCTION_FTH', 'PRODUCTION_POI_FO');
    private $cuivreFtthArray = array('MAINTENANCE', 'PRODUCTION', 'MAINTENANCE_ENT', 'AUTRE');
    private $produit;

    public function __construct($days)
    {
        if ($days) {
            $this->days = explode(',', $days);
//            \DB::table('stats')->whereIn('date_note', $this->days)->delete();
            // Prepare to be deleted
            \DB::table('stats')->whereIn('date_note', $this->days)->update(['isNotReady' => -1]);
        }
        $this->user_flag = getImportedData(false);
        $this->user_flag->flags = [
            'imported_data' => 0,
            'is_importing' => 1
        ];
        $this->user_flag->update();
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
        $this->produit = null;
        try {
            if (array_key_exists('dimension_notescode_type_intervention', $row)) {
                if (in_array($row['dimension_notescode_type_intervention'], $this->cuivreArray)) {
                    $this->produit = 'CUIVRE';
                }
                if (in_array($row['dimension_notescode_type_intervention'], $this->ftthArray)) {
                    $this->produit = 'FTTH';
                }
                if (in_array($row['dimension_notescode_type_intervention'], $this->cuivreFtthArray)) {
                    $this->produit = 'CUIVRE/FTTH';
                }
            }
        } catch (Exception $e) {
        }
        if (!$this->days || in_array($formatted_date, $this->days)) {
            if ($this->user_flag) {
                $imported_data = $this->user_flag->flags['imported_data'];
                $this->user_flag->flags = [
                    'imported_data' => $imported_data + 1,
                    'is_importing' => 1
                ];
                $this->user_flag->save();
            }
            return new Stats([
                'Type_Note' => $row['dimension_notestype_note'] ?? null,
                'Utilisateur' => $row['dimension_notesutilisateur'] ?? null,
                'Resultat_Appel' => $row['dimension_notesresultat_appel'] ?? null,
                'Date_Nveau_RDV' => $row['dimension_notesdate_nveau_rdv'] ?? null,
                'Heure_Nveau_RDV' => $row['dimension_notesheure_nveau_rdv'] ?? null,
                'Marge_Nveau_RDV' => $row['dimension_notesmarge_nveau_rdv'] ?? null,
                'Id_Externe' => $row['dimension_notesid_externe'] ?? null,
                'Date_Creation' => $row['dimension_notesdate_creation'] ?? null,
                'Code_Postal_Site' => $row['dimension_notescode_postal_site'] ?? null,
//                    'Departement' => $row['dimension_noteDepartement'] ?? null,
                'Drapeaux' => $row['dimension_notesdrapeaux'] ?? null,
                'Code_Type_Intervention' => $row['dimension_notescode_type_intervention'] ?? null,
                'Date_Rdv' => $row['dimension_notesdate_rdv'] ?? null,
                'Nom_Societe' => $row['dimension_notesnom_societe'] ?? null,
                'Nom_Region' => $row['dimension_notesnom_region'] ?? null,
                'Nom_Domaine' => $row['dimension_notesnom_domaine'] ?? null,
                'Nom_Agence' => $row['dimension_notesnom_agence'] ?? null,
                'Nom_Activite' => $row['dimension_notesnom_activite'] ?? null,
                'Date_Heure_Note' => $row['dimension_notesdate_heure_note'] ?? null,
                'Date_Heure_Note_Annee' => $row['dimension_notesdate_heure_note_annee'] ?? null,
                'Date_Heure_Note_Mois' => $row['dimension_notesdate_heure_note_mois'] ?? null,
                'Date_Heure_Note_Semaine' => $row['dimension_notesdate_heure_note_semaine'] ?? null,
                'Date_Note' => $formatted_date, // $row['dimension_notesdate_note'] ?? null,
                'Groupement' => $row['dimension_notesgroupement'] ?? null,
                'key_Groupement' => clean($row['dimension_notesgroupement']) ?? null,

                'Gpmt_Appel_Pre' => $row['dimension_notesgpmt_appel_pre'] ?? null,
                'Code_Intervention' => $row['dimension_notescode_intervention'] ?? null,
                'EXPORT_ALL_Nom_SITE' => $row['dimension_notesexport_all_nom_site'] ?? null,
                'EXPORT_ALL_Nom_TECHNICIEN' => $row['dimension_notesexport_all_nom_technicien'] ?? null,
                'EXPORT_ALL_PRENom_TECHNICIEN' => $row['dimension_notesexport_all_prenom_technicien'] ?? null,
//                    'EXPORT_ALL_Nom_CLIENT' => $row['dimension_note'EXPORT_ALL_Nom_CLIENT] ?? null,
                'EXPORT_ALL_Nom_EQUIPEMENT' => $row['dimension_notesexport_all_nom_equipement'] ?? null,
                'EXPORT_ALL_EXTRACT_CUI' => $row['dimension_notesexport_all_extract_cui'] ?? null,
                'EXPORT_ALL_Date_CHARGEMENT_PDA' => $row['dimension_notesexport_all_date_chargement_pda'] ?? null,
                'EXPORT_ALL_Date_SOLDE' => $row['dimension_notesexport_all_date_solde'] ?? null,
                'EXPORT_ALL_Date_VALIDATION' => $row['dimension_notesexport_all_date_validation'] ?? null,
                'isNotReady' => 1,
                'produit' => $this->produit
            ]);
        }
    }

    /**
     * Transform a date value into a Carbon object.
     *
     * @return string|null
     */
    public function transformDate($value, $format = 'Y - m - d')
    {
        try {
            return Carbon::instance(Date::excelToDateTimeObject($value))->format($format);
        } catch (Exception $e) {
            return Carbon::createFromFormat($format, $value)->toDateString();
        }
    }

    /**
     * @return int
     */
    public function batchSize(): int
    {
        return 1000;
    }

    /**
     * @return int
     */
    public function chunkSize(): int
    {
        return 1000;
    }
}
