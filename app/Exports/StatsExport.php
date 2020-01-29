<?php


namespace App\Exports;


use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StatsExport implements FromCollection,WithHeadings, WithMapping, ShouldAutoSize
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
        $row = $this->request->row;
        $rowValue = $this->request->rowValue;
        $col = $this->request->col;
        $colValue = $this->request->colValue;

        $agentName = $this->request->agent;
        $agenceCode = $this->request->agence;

        $dates = $this->request->dates;

        $resultat_appel = $this->request->Resultat_Appel;


        $allStats = DB::table('stats as st')->select([
            'Type_Note',
            'Utilisateur',
            'Resultat_Appel',
            'Date_Nveau_RDV',
            'Heure_Nveau_RDV',
            'Marge_Nveau_RDV',
            'st.Id_Externe',
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
        ]) ->join(\DB::raw('(SELECT Id_Externe, MAX(Date_Heure_Note) AS MaxDateTime FROM stats where 1=1 ' .
            ($agentName ? 'and Utilisateur like "' . $agentName . '"' : '') .
            ($agenceCode ? 'and Nom_Region like "%' . $agenceCode . '"' : '') .
            ' GROUP BY Id_Externe) groupedst'),
            function ($join) {
                $join->on('st.Id_Externe', '=', 'groupedst.Id_Externe');
                $join->on('st.Date_Heure_Note', '=', 'groupedst.MaxDateTime');
            });

        if ($row && $rowValue) {
            $allStats = $allStats->where($row, $rowValue);
        }
        if ($col && $colValue) {
            $allStats = $allStats->where($col, $colValue);
        }

        if ($agentName) {
            $allStats = $allStats->where('Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $allStats = $allStats->where('Nom_Region', 'like', "%$agenceCode");
        }

        if ($dates) {
            $dates = explode(',', $this->request->dates);
            $allStats = $allStats->whereIn('Date_Note', $dates);
        }


        if ($resultat_appel) {
            $allStats = $allStats->where('Resultat_Appel', $resultat_appel);
        }


        return $allStats->get();
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
        return[
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
