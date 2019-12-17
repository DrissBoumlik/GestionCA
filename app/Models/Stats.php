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

    public static function getRegions($dates = null)
    {
        $regions = \DB::table('stats')
            ->select('Nom_Region', 'Resultat_Appel', \DB::raw('count(*) as total'));
        if ($dates) {
            $regions = $regions->whereIn('Date_Note', $dates);
        }
//        $regions = ($dates ? $regions->whereIn('Date_Note', $dates)->get() : $regions)->get();

        $regions = $regions->groupBy('Nom_Region', 'Resultat_Appel')->get();
        dd($regions);

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
            $row['regions'] = [];
            $item = $region->map(function ($call, $index) use (&$row, &$regions_names) {
                $regions_names[] = $call->Nom_Region;
                $row['Resultat_Appel'] = $call->Resultat_Appel;
                $nom_region = $call->Nom_Region;
                $row['regions']['zone_' . $index] = $call->$nom_region;
                $row[$nom_region] = $call->$nom_region;
                $row['total'] = round(array_sum($row['regions']) / count($row['regions']), 2);
                return $row;
            });
            return $item->last();
        });
        $regions_names = collect($regions_names)->unique()->values();
        $regions = $regions->values();
        $data = ['regions_names' => $regions_names, 'calls' => $regions];
        return $data;
    }

    public static function getClientsByCallState($state)
    {
        $codes = \DB::table('stats')
            ->select('Code_Intervention', 'Nom_Region', \DB::raw('count(*) as total'))
            ->groupBy('Code_Intervention', 'Nom_Region')
            ->where('Gpmt_Appel_Pré', $state)
            ->get();
        $totalCount = Stats::all()->count();
        $codes = $codes->map(function ($code) use ($totalCount) {
            $Code = $code->Code_Intervention;
            $code->$Code = round($code->total * 100 / $totalCount, 2);;
            return $code;
        });
        $codes = $codes->groupBy(['Nom_Region']);

        $codes_names = [];
        $total[] = 'Total';
//        $total = collect($total);
        $codes = $codes->map(function ($region) use (&$codes_names, &$total) {
            $row = [];
            $row['codes'] = [];
            $item = $region->map(function ($call, $index) use (&$row, &$codes_names, &$total) {
                $codes_names[] = $call->Code_Intervention;
                $row['Nom_Region'] = $call->Nom_Region;
                $code_intervention = $call->Code_Intervention;
                $row['codes']['code_' . $index] = $call->$code_intervention;
                $row[$code_intervention] = $call->$code_intervention;
                $row['total'] = round(array_sum($row['codes']) / count($row['codes']), 2);

                try {
                    $total[$index] += $call->$code_intervention;
                } catch (\Exception $e) {
                    $total[] = 0;
                }

                return $row;
            });
            return $item->last();
        });
//        dd($total, $codes);
        $codes_names = collect($codes_names)->unique()->values();
        $codes = $codes->values();

        $data = ['codes_names' => $codes_names, 'regions' => $codes];
        return $data;
    }
}
