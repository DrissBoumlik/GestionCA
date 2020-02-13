<?php


namespace App\Exports;


use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use function Maatwebsite\Excel\Facades\Excel;

class StatsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @inheritDoc
     */
    public function collection()
    {
        // CASE : Appel préalable
        $callType = $this->request->callType;
        $row = $this->request->row;
        $rowValue = $this->request->rowValue;
        $col = $this->request->col;
        $colValue = $this->request->colValue;
        $agentName = $this->request->agent;
        $agenceCode = $this->request->agence;
        $queryJoin = $this->request->queryJoin;
        $dates = $this->request->dates;
        $resultat_appel = $this->request->Resultat_Appel;
        $subGroupBy = $this->request->subGroupBy;
        $queryGroupBy = $this->request->queryGroupBy;
        $appCltquery = $this->request->appCltquery;
        $allStats = null;

        $columns = 'SELECT st.Type_Note,
            st.Utilisateur,
            st.Resultat_Appel,
            st.Date_Nveau_RDV,
            st.Heure_Nveau_RDV,
            st.Marge_Nveau_RDV,
            st.Id_Externe,
            st.Date_Creation,
            st.Code_Postal_Site,
            st.Drapeaux,
            st.Code_Type_Intervention,
            st.Date_Rdv,
            st.Nom_Societe,
            st.Nom_Region,
            st.Nom_Domaine,
            st.Nom_Agence,
            st.Nom_Activite,
            st.Date_Heure_Note,
            st.Date_Heure_Note_Annee,
            st.Date_Heure_Note_Mois,
            st.Date_Heure_Note_Semaine,
            st.Date_Note,
            st.Groupement,
            st.key_Groupement,
            st.Gpmt_Appel_Pre,
            st.Code_Intervention,
            st.EXPORT_ALL_Nom_SITE,
            st.EXPORT_ALL_Nom_TECHNICIEN,
            st.EXPORT_ALL_PRENom_TECHNICIEN,
            st.EXPORT_ALL_Nom_EQUIPEMENT,
            st.EXPORT_ALL_EXTRACT_CUI,
            st.EXPORT_ALL_Date_CHARGEMENT_PDA,
            st.EXPORT_ALL_Date_SOLDE,
            st.EXPORT_ALL_Date_VALIDATION';

        if ($appCltquery) {
            $allStats = DB::select($columns
                . ' FROM stats AS st WHERE Nom_Region is not null and EXPORT_ALL_Date_VALIDATION is not null and EXPORT_ALL_Date_SOLDE is not null ' .
                ($agentName ? 'and Utilisateur like "' . $agentName . '" ' : ' ') .
                ($agenceCode ? 'and Nom_Region like "%' . $agenceCode . '" ' : ' ') .
                ($rowValue ?? '') .
                ($col && $colValue ? ' and ' . $col . ' like "' . $colValue . '"' : ' ') .
                ($dates ? ' and Date_Note in ("' . str_replace(',', '","', $dates) . '")' : ' ') .
                ' and Resultat_Appel not like "=%" group by Id_Externe'

            );
        } else {
            $allStats = DB::select($columns
                . ' FROM stats AS st INNER JOIN (SELECT Id_Externe, MAX(Date_Heure_Note) AS MaxDateTime FROM stats  where Nom_Region is not null ' .
                ($agentName ? 'and Utilisateur like "' . $agentName . '" ' : ' ') .
                ($agenceCode ? 'and Nom_Region like "%' . $agenceCode . '" ' : ' ') .
                ($row && $rowValue ? ' and ' . $row . ' like "' . $rowValue . '"' : ' ') .
                ($col && $colValue ? ' and ' . $col . ' like "' . $colValue . '"' : ' ') .
                ($dates ? ' and Date_Note in ("' . str_replace(',', '","', $dates) . '")' : ' ') .
                ($queryJoin ?? '') . ' ' .
                ($callType ? 'and Groupement like "' . $callType . '"' : ' ') .
                ' and Resultat_Appel not like "=%" '
                . ($subGroupBy ?? ' GROUP BY Id_Externe ) groupedst')
                . ' on st.Id_Externe = groupedst.Id_Externe and st.Date_Heure_Note = groupedst.MaxDateTime where Nom_Region is not null ' .
                ($agentName ? 'and Utilisateur like "' . $agentName . '" ' : ' ') .
                ($agenceCode ? 'and Nom_Region like "%' . $agenceCode . '" ' : ' ') .
                ($row && $rowValue ? ' and ' . $row . ' like "' . $rowValue . '"' : ' ') .
                ($col && $colValue ? ' and ' . $col . ' like "' . $colValue . '"' : ' ') .
                ($dates ? ' and Date_Note in ("' . str_replace(',', '","', $dates) . '")' : ' ') .
                ($queryJoin ?? '') . ' ' .
                ($callType ? 'and Groupement like "' . $callType . '"' : ' ') .
                ' and Resultat_Appel not like "=%" '
                . ($queryGroupBy ?? ' ')
            );
        }
        return collect($allStats);
    }

    public function headings(): array
    {
        return [
            'Type_Note',
            'Utilisateur',
            'Resultat_Appel',
            'Date_Nveau_RDV',
            'Heure_Nveau_RDV',
            'Marge_Nveau_RDV',
            'Id_Externe',
            'Date_Creation',
            'Code_Postal_Site',
            'Drapeaux',
            'Code_Type_Intervention',
            'Date_Rdv',
            'Nom_Societe',
            'Nom_Region',
            'Nom_Domaine',
            'Nom_Agence',
            'Nom_Activite',
            'Date_Heure_Note',
            'Date_Heure_Note_Annee',
            'Date_Heure_Note_Mois',
            'Date_Heure_Note_Semaine',
            'Date_Note',
            'Groupement',
            'key_Groupement',
            'Gpmt_Appel_Pre',
            'Code_Intervention',
            'EXPORT_ALL_Nom_SITE',
            'EXPORT_ALL_Nom_TECHNICIEN',
            'EXPORT_ALL_PRENom_TECHNICIEN',
            'EXPORT_ALL_Nom_EQUIPEMENT',
            'EXPORT_ALL_EXTRACT_CUI',
            'EXPORT_ALL_Date_CHARGEMENT_PDA',
            'EXPORT_ALL_Date_SOLDE',
            'EXPORT_ALL_Date_VALIDATION'
        ];
    }

    /**
     * @inheritDoc
     */
    public function map($row): array
    {
        return [
            $row->Type_Note,
            $row->Utilisateur,
            $row->Resultat_Appel,
            $row->Date_Nveau_RDV,
            $row->Heure_Nveau_RDV,
            $row->Marge_Nveau_RDV,
            $row->Id_Externe,
            $row->Date_Creation,
            $row->Code_Postal_Site,
            $row->Drapeaux,
            $row->Code_Type_Intervention,
            $row->Date_Rdv,
            $row->Nom_Societe,
            $row->Nom_Region,
            $row->Nom_Domaine,
            $row->Nom_Agence,
            $row->Nom_Activite,
            $row->Date_Heure_Note,
            $row->Date_Heure_Note_Annee,
            $row->Date_Heure_Note_Mois,
            $row->Date_Heure_Note_Semaine,
            $row->Date_Note,
            $row->Groupement,
            $row->key_Groupement,
            $row->Gpmt_Appel_Pre,
            $row->Code_Intervention,
            $row->EXPORT_ALL_Nom_SITE,
            $row->EXPORT_ALL_Nom_TECHNICIEN,
            $row->EXPORT_ALL_PRENom_TECHNICIEN,
            $row->EXPORT_ALL_Nom_EQUIPEMENT,
            $row->EXPORT_ALL_EXTRACT_CUI,
            $row->EXPORT_ALL_Date_CHARGEMENT_PDA,
            $row->EXPORT_ALL_Date_SOLDE,
            $row->EXPORT_ALL_Date_VALIDATION
        ];
    }
}
