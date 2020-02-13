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
        $allStats = getStats($this->request);
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
