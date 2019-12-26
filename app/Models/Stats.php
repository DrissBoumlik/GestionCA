<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Stats extends Model
{
    use SoftDeletes;

//    protected $guarded = ['id'];

    protected $fillable = [
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
        'Gpmt_Appel_Pre',
        'Code_Intervention',
        'EXPORT_ALL_Nom_SITE',
        'EXPORT_ALL_Nom_TECHNICIEN',
        'EXPORT_ALL_PRENom_TECHNICIEN',
//        'EXPORT_ALL_Nom_CLIENT',
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
            $dates = array_values($dates);
            $regions = $regions->whereIn('Date_Note', $dates);
        }
//        $regions = ($dates ? $regions->whereIn('Date_Note', $dates)->get() : $regions)->get();

        $regions = $regions->groupBy('Nom_Region', 'Resultat_Appel')->get();

        $totalCount = Stats::count();
        $regions = $regions->map(function ($region) use ($totalCount) {
            $Region = $region->Nom_Region;
            $region->$Region = round($region->total * 100 / $totalCount, 2);;
            return $region;
        });
        $regions = $regions->groupBy(['Resultat_Appel']);

        $regions_names = [];

        $regions = $regions->map(function ($region) use (&$regions_names) {
            $row = new \stdClass();
            $row->regions = [];
            $item = $region->map(function ($call, $index) use (&$row, &$regions_names) {
                $regions_names[] = $call->Nom_Region;
                $row->Resultat_Appel = $call->Resultat_Appel;
                $nom_region = $call->Nom_Region;
                $row->regions['zone_' . $index] = $call->$nom_region;
                $row->$nom_region = $call->$nom_region;
                $row->total = round(array_sum($row->regions) / count($row->regions), 2);
                return $row;
            });
            return $item->last();
        });
        $regions_names = collect($regions_names)->unique()->values();
        $regions = $regions->values();
        $data = ['regions_names' => $regions_names, 'calls' => $regions];
//        dd($regions, $regions_names);
        return $data;
    }

    public static function getNonValidatedFolders($intervCol, $dates = null)
    {
        $regions = \DB::table('stats')
            ->select('Nom_Region', $intervCol, \DB::raw('count(*) as total'));
        if ($dates) {
            $dates = array_values($dates);
            $regions = $regions->whereIn('Date_Note', $dates);
        }
        $regions = $regions->groupBy('Nom_Region', $intervCol)->get();

        $totalCount = Stats::count();
        $regions = $regions->map(function ($region) use ($totalCount) {
            $Region = $region->Nom_Region;
            $region->$Region = round($region->total * 100 / $totalCount, 2);;
            return $region;
        });
        $regions = $regions->groupBy([$intervCol]);

        $regions_names = [];

        $regions = $regions->map(function ($region) use (&$regions_names) {
            $row = new \stdClass();
            $row->regions = [];
            $item = $region->map(function ($call, $index) use (&$row, &$regions_names) {
                $regions_names[] = $call->Nom_Region;

                if (property_exists($call, 'Code_Type_Intervention')) {
                    $row->Code_Type_Intervention = $call->Code_Type_Intervention;
                } elseif (property_exists($call, 'Code_Intervention')) {
                    $row->Code_Intervention = $call->Code_Intervention;
                }

                $nom_region = $call->Nom_Region;
                $row->regions['zone_' . $index] = $call->$nom_region;
                $row->$nom_region = $call->$nom_region;
                $row->total = round(array_sum($row->regions) / count($row->regions), 2);
                return $row;
            });
            return $item->last();
        });
        $regions_names = collect($regions_names)->unique()->values();
        $regions = $regions->values();
        $data = ['regions_names' => $regions_names, 'codes' => $regions];
        return $data;
    }

    public static function getClientsByCallState($state, $dates = null)
    {
        $codes = \DB::table('stats')
            ->select('Code_Intervention', 'Nom_Region', \DB::raw('count(*) as total'))
            ->where('Gpmt_Appel_Pre', $state);
//            ->groupBy('Code_Intervention', 'Nom_Region')
//            ->get();
        if ($dates) {
            $dates = array_values($dates);
            $codes = $codes->whereIn('Date_Note', $dates);
        }
        $codes = $codes->groupBy('Code_Intervention', 'Nom_Region')->get();
        $totalCount = Stats::count();
        $codes = $codes->map(function ($code) use ($totalCount) {
            $Code = $code->Code_Intervention;
            $code->$Code = round($code->total * 100 / $totalCount, 2);;
            return $code;
        });
        $codes = $codes->groupBy(['Nom_Region']);

        $codes_names = [];
        $total = new \stdClass();
        $codes = $codes->map(function ($region) use (&$codes_names, &$total) {
            $row = new \stdClass(); //[];
            $row->codes = [];
            $item = $region->map(function ($call, $index) use (&$row, &$codes_names, &$total) {
                $codes_names[] = $call->Code_Intervention;
                $row->Nom_Region = $call->Nom_Region;
                $code_intervention = $call->Code_Intervention;
                $row->codes['code_' . $index] = $call->$code_intervention;
//                $row->$code_intervention = $call->$code_intervention;
                $row->total = round(array_sum($row->codes) / count($row->codes), 2);
//                dump($code_intervention ? $total->{$code_intervention}[0] : 1);
//                if ($code_intervention)
//                    $total->$code_intervention =
//                        $total->$index == 0 ?
//                        $total->$index + $call->$code_intervention : 0;
//                    $total[$index] += $call->$code_intervention;

                return $row;
            });
            return $item->last();
        });
//        dd($total);
        $codes_names = collect($codes_names)->unique()->values();
        $codes = $codes->values();

        $data = ['codes_names' => $codes_names, 'regions' => $codes];
        return $data;
    }

    public static function getDateNotes2()
    {
        $dates = self::distinct()->orderBy('Date_Note', 'desc')->pluck('Date_Note');
        $dates = $dates->map(function ($date, $index) {
            $date = explode('-', $date);
            $item = new \stdClass();
            $item->year = $date[0];
            $item->month = $date[1];
            $item->day = $date[2];
            return $item;
        });
        $dates = collect($dates)->groupBy(['year', 'month']);
        $dates = $dates->map(function ($year, $index) {
            $_year = new \stdClass();
            $_year->id = $index; // year name
            $_year->text = $index; // year name
            $_year->children = []; // months
            $year->map(function ($month, $index) use (&$_year) {
                $_month = new \stdClass();
                $_month->id = $_year->text . '-' . $index; // month name
                $_month->text = $_year->text . '-' . $index; // month name
                $_month->children = []; // days
                $_year->children[] = $_month;
                $month->map(function ($day, $index) use (&$_month) {
                    $_day = new \stdClass();
                    $_day->id = collect($day)->implode('-'); // day name
                    $_day->text = collect($day)->implode('-'); // day name
                    $_month->children[] = $_day; // collect($day)->implode('-');
                    return $_month;
                });
                return $_year;
            });
            return $_year;
        });

        return $dates->values();
    }


}
