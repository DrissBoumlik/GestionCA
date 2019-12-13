<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stats extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'Type_Note',
        'Utilisateur',
        'Resultat_Appel',
        'Date_Nveau_RDV',
        'Heure_Nveau_RDV',
        'Marge_Nveau_RDV',
        'Id_Externe',
        'Date_Création',
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
        'Gpmt_Appel_Pré',
        'Code_Intervention',
        'EXPORT_ALL_Nom_SITE',
        'EXPORT_ALL_Nom_TECHNICIEN',
        'EXPORT_ALL_PRENom_TECHNICIEN',
        'EXPORT_ALL_Nom_CLIENT',
        'EXPORT_ALL_Nom_EQUIPEMENT',
        'EXPORT_ALL_EXTRACT_CUI',
        'EXPORT_ALL_Date_CHARGEMENT_PDA',
        'EXPORT_ALL_Date_SOLDE',
        'EXPORT_ALL_Date_VALIDATION',
    ];

    public static function getRegions()
    {
        $regions = \DB::table('stats')
            ->select('Nom_Region', 'Resultat_Appel', \DB::raw('count(*) as total'))
            ->groupBy('Nom_Region', 'Resultat_Appel')
            ->get();
        $totalCount = Stats::all()->count();
        $regions = $regions->map(function ($region) use ($totalCount) {
            $Region = $region->Nom_Region;
            $region->$Region = round($region->total * 100 / $totalCount, 2);;
            return $region;
        });
        $regions = $regions->groupBy(['Resultat_Appel']);

        $regions_names = [];

        $regions = $regions->map(function ($region) use (&$regions_names) {
            $row = [];
            $row['zones'] = [];
            $item = $region->map(function ($call, $index) use (&$row, &$regions_names) {
                $regions_names[] = $call->Nom_Region;
                $row['Resultat_Appel'] = $call->Resultat_Appel;
                $nom_region = $call->Nom_Region;
                $row['zones']['zone_' . $index] = $call->$nom_region;
                $row[$nom_region] = $call->$nom_region;
                $row['total'] = round(array_sum($row['zones']) / count($row['zones']), 2);
                return $row;
            });
            return $item->last();
        });
        $regions_names = collect($regions_names)->unique()->values();
        $regions = $regions->values();
        $data = ['regions' => $regions_names, 'calls' => $regions];
        return $data;
    }

    public static function getCodes()
    {
//        $codes = \DB::table('stats')
//            ->select('Code_Intervention', 'Nom_Region', \DB::raw('count(*) as total'))
//            ->groupBy('Code_Intervention', 'Nom_Region')
//            ->where('')
//            ->get();
//        $totalCount = Stats::all()->count();

        // ========================================

        $codes = \DB::table('stats')
            ->select('Code_Intervention]', 'Nom_Region', \DB::raw('count(*) as total'))
            ->groupBy('Code_Intervention]', 'Nom_Region')
            ->get();
        $totalCount = Stats::all()->count();
        $codes = $codes->map(function ($region) use ($totalCount) {
            $Region = $region->Nom_Region;
            $region->$Region = round($region->total * 100 / $totalCount, 2);;
            return $region;
        });
        $codes = $codes->groupBy(['Resultat_Appel']);

        $regions_names = [];

        $codes = $codes->map(function ($region) use (&$regions_names) {
            $row = [];
            $row['zones'] = [];
            $item = $region->map(function ($call, $index) use (&$row, &$regions_names) {
                $regions_names[] = $call->Nom_Region;
                $row['Resultat_Appel'] = $call->Resultat_Appel;
                $nom_region = $call->Nom_Region;
                $row['zones']['zone_' . $index] = $call->$nom_region;
                $row[$nom_region] = $call->$nom_region;
                $row['total'] = round(array_sum($row['zones']) / count($row['zones']), 2);
                return $row;
            });
            return $item->last();
        });
        $regions_names = collect($regions_names)->unique()->values();
        $codes = $codes->values();
        $data = ['regions' => $regions_names, 'calls' => $codes];
        return $data;
        // ========================================

        $data = ['codes' => $codes, 'regions' => $codes];
        return $data;
    }
}
