<?php

namespace App\Http\Controllers;


use App\Imports\StatsImport;
use App\Models\Stats;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StatsRepository
{
    public function getDateNotes()
    {
        $dates = Stats::all()->groupBy(['Date_Heure_Note_Annee', 'Date_Heure_Note_Mois', 'Date_Heure_Note_Semaine', 'Date_Note']);
        $dates = $dates->map(function ($year, $index) {
            $_year = new \stdClass();
            $_year->id = $index; // year name
            $_year->text = $index; // year name
            $_year->children = []; // months
            $year->map(function ($month, $index) use (&$_year) {
                $_month = new \stdClass();
                $_month->id = $_year->text . '-' . $index; // month name
                $_month->text = $_year->text . '-' . $index; // month name
                $_month->children = []; // months
                $_year->children[] = $_month;
                $month->map(function ($week, $index) use (&$_year, &$_month) {
                    $_week = new \stdClass();
                    $_week->id = $index; // week name
                    $_week->text = $index; // week name
                    $_week->children = []; // days
                    $_month->children[] = $_week;
                    $week->map(function ($day, $index) use (&$_week) {
                        $_day = new \stdClass();
                        $_day->id = collect($index)->implode('-'); // day name
                        $_day->text = collect($index)->implode('-'); // day name
                        $_week->children[] = $_day; // collect($day)->implode('-');
                        return $_week;
                    });
                    return $_month;
                });
                return $_year;
            });
            return $_year;
        });

        return $dates->values();
    }

    public function GetDataRegions($callResult, $dates = null)
    {
        $regions = \DB::table('stats')
            ->select('Nom_Region', $callResult, \DB::raw('count(*) as total'));

        $columns = $regions->groupBy('Nom_Region', $callResult)->get();

        $regions = $regions->whereNotNull($callResult);
        if ($dates) {
            $dates = array_values($dates);
            $regions = $regions->whereIn('Date_Note', $dates);
        }
//        $regions = ($dates ? $regions->whereIn('Date_Note', $dates)->get() : $regions)->get();

        $regions = $regions->groupBy('Nom_Region', $callResult)->get();


        $totalCount = Stats::all()->count();
        $regions = $regions->map(function ($region) use ($totalCount) {
            $Region = $region->Nom_Region;
            $region->$Region = round($region->total * 100 / $totalCount, 2);
            return $region;
        });
        $columns = $columns->map(function ($region) use ($totalCount) {
            $Region = $region->Nom_Region;
            $region->$Region = round($region->total * 100 / $totalCount, 2);
            return $region;
        });

        $keys = $columns->groupBy(['Nom_Region'])->keys();

        $regions = $regions->groupBy([$callResult]);
//        $regions = $regions->groupBy(['Nom_Region']);

        $regions_names = [];
        $regions_names[0] = new \stdClass();
        $regions_names[0]->data = $callResult;
        $regions_names[0]->name = $callResult;
        $keys->map(function ($key, $index) use (&$regions_names) {
            $regions_names[$index + 1] = new \stdClass();
            $regions_names[$index + 1]->data = $key;
            $regions_names[$index + 1]->name = $key;
        });
        $regions_names[] = new \stdClass();
        $regions_names[count($regions_names) - 1]->data = 'total';
        $regions_names[count($regions_names) - 1]->name = 'total';

        $regions = $regions->map(function ($region) use (&$regions_names, $keys, $callResult) {
            $row = new \stdClass();
            $row->values = [];

            $col_arr = $keys->all();

            $item = $region->map(function ($call, $index) use (&$row, &$regions_names, &$col_arr, $callResult) {

                $row->$callResult = $call->$callResult;
                $nom_region = $call->Nom_Region;

                $col_arr = array_diff($col_arr, [$nom_region]);

                $row->values['value_' . $index] = $call->$nom_region . '%';
                $row->$nom_region = $call->$nom_region . '%';
                $row->total = round(array_sum($row->values) / count($row->values), 2) . '%';
                $row->_total = $call->total;
                $row->column = $callResult;
                return $row;
            });

            $_item = $item->last();
            $index = count($_item->values);
            foreach ($col_arr as $col) {
                $_item->values['value_' . $index++] = '0%';
                $_item->$col = '0%';
            }
            return $_item;
        });

        $regions = $regions->values();

        return ['columns' => $regions_names, 'data' => $regions];
    }

    public function GetDataRegionsCallState($column, $dates = null)
    {
        $regions = \DB::table('stats')
            ->select($column, 'Gpmt_Appel_Pre', \DB::raw('count(*) as total'));

        $columns = $regions->groupBy($column, 'Gpmt_Appel_Pre')->get();

        $regions = $regions->whereNotNull([$column, 'Gpmt_Appel_Pre']);
        if ($dates) {
            $dates = array_values($dates);
            $regions = $regions->whereIn('Date_Note', $dates);
        }
//        $regions = ($dates ? $regions->whereIn('Date_Note', $dates)->get() : $regions)->get();
        $regions = $regions->groupBy($column, 'Gpmt_Appel_Pre')->get();
        $totalCount = Stats::all()->count();
        $regions = $regions->map(function ($region) use ($column) {
            $Region = $region->$column;
            $region->$Region = $region->total; //round($region->total * 100 / $totalCount, 2) . '%';
            return $region;
        });
        $columns = $columns->map(function ($region) use ($column) {
            $Region = $region->$column;
            $region->$Region = $region->total; //round($region->total * 100 / $totalCount, 2) . '%';
            return $region;
        });

        $keys = $columns->groupBy([$column])->keys();
        $regions = $regions->groupBy(['Gpmt_Appel_Pre']);
//        $regions = $regions->groupBy(['Nom_Region']);

        $columns = [];
        $columns[0] = new \stdClass();
        $columns[0]->data = 'Gpmt_Appel_Pre';
        $columns[0]->name = 'Gpmt_Appel_Pre';
        $keys->map(function ($key, $index) use (&$columns) {
            $columns[$index + 1] = new \stdClass();
            $columns[$index + 1]->data = $key;
            $columns[$index + 1]->name = $key;
        });
        $columns[] = new \stdClass();
        $columns[count($columns) - 1]->data = 'total';
        $columns[count($columns) - 1]->name = 'total';
        $regions = $regions->map(function ($region, $index) use (&$columns, $keys, $column) {
//            dd($index, $region);
            $row = new \stdClass();
            $row->values = [];

            $col_arr = $keys->all();
            $total = 0;
            $item = $region->map(function ($call, $index) use (&$row, &$col_arr, &$total, $column) {
//                dd($index, $call);
                $row->Gpmt_Appel_Pre = $call->Gpmt_Appel_Pre;
                $column_name = $call->$column;
                $col_arr = array_diff($col_arr, [$column_name]);
                $row->values['value_' . $index] = $call->$column_name;
                $row->$column_name = $call->$column_name;
                $row->column = $call->Gpmt_Appel_Pre;
                $total += $call->total;
//                $row->_total = $call->total;
                $row->total = $total; //round(array_sum($row->regions) / count($row->regions), 2) . '%';
//                dd($row);
                return $row;
            });
            $_item = $item->last();
            $index = count($_item->values);
            foreach ($col_arr as $col) {
                $_item->values['value_' . $index++] = '0';
                $_item->$col = '0';
            }
            return $_item;
        });


        $regions = $regions->values();

        return ['columns' => $columns, 'data' => $regions];
    }

    public function getDataNonValidatedFolders($intervCol, $dates = null)
    {
        $regions = \DB::table('stats')
            ->select('Nom_Region', $intervCol, \DB::raw('count(*) as total'));

        $columns = $regions->groupBy('Nom_Region', $intervCol)->get();

        $regions = $regions->whereNotNull($intervCol);
        if ($dates) {
            $dates = array_values($dates);
            $regions = $regions->whereIn('Date_Note', $dates);
        }
        $regions = $regions->groupBy('Nom_Region', $intervCol)->get();

        $totalCount = Stats::all()->count();
        $regions = $regions->map(function ($region) use ($totalCount) {
            $Region = $region->Nom_Region;
            $region->$Region = round($region->total * 100 / $totalCount, 2);;
            return $region;
        });
        $columns = $columns->map(function ($region) use ($totalCount) {
            $Region = $region->Nom_Region;
            $region->$Region = round($region->total * 100 / $totalCount, 2);;
            return $region;
        });
        $keys = $columns->groupBy(['Nom_Region'])->keys();
        $regions = $regions->groupBy([$intervCol]);


        $regions_names = [];
        $regions_names[0] = new \stdClass();
        $regions_names[0]->data = $intervCol;
        $regions_names[0]->name = $intervCol;
        $keys->map(function ($key, $index) use (&$regions_names) {
            $regions_names[$index + 1] = new \stdClass();
            $regions_names[$index + 1]->data = $key;
            $regions_names[$index + 1]->name = $key;
        });
        $regions_names[] = new \stdClass();
        $regions_names[count($regions_names) - 1]->data = 'total';
        $regions_names[count($regions_names) - 1]->name = 'total';


        $regions = $regions->map(function ($region) use (&$regions_names, $keys, $intervCol) {
            $row = new \stdClass();
            $row->values = [];
            $col_arr = $keys->all();
            $item = $region->map(function ($call, $index) use (&$row, &$regions_names, &$col_arr, $intervCol) {

                $row->$intervCol = $call->$intervCol;

                $nom_region = $call->Nom_Region;

                $col_arr = array_diff($col_arr, [$nom_region]);

                $row->values['value_' . $index] = $call->$nom_region . '%';
                $row->$nom_region = $call->$nom_region . '%';
                $row->total = round(array_sum($row->values) / count($row->values), 2) . '%';
                return $row;
            });
            $_item = $item->last();
            $index = count($_item->values);
            foreach ($col_arr as $col) {
                $_item->values['value_' . $index++] = '0%';
                $_item->$col = '0%';
            }
            return $_item;
//            return $item->last();
        });
        $regions = $regions->values();
        $data = ['columns' => $regions_names, 'data' => $regions];
        return $data;
    }

    public function getDataClientsByCallState($callResult, $dates = null)
    {
        $codes = \DB::table('stats')
            ->select('Code_Intervention', 'Nom_Region', \DB::raw('count(*) as total'));
        $columns = $codes->groupBy('Code_Intervention', 'Nom_Region')->get();
        $codes = $codes->whereNotNull('Code_Intervention')
            ->where('Gpmt_Appel_Pre', $callResult);
//            ->groupBy('Code_Intervention', 'Nom_Region')
//            ->get();
        if ($dates) {
            $dates = array_values($dates);
            $codes = $codes->whereIn('Date_Note', $dates);
        }
        $codes = $codes->groupBy('Code_Intervention', 'Nom_Region')->get();
        $totalCount = Stats::all()->count();
        $codes = $codes->map(function ($code) use ($totalCount) {
            $Code = $code->Code_Intervention;
            $code->$Code = round($code->total * 100 / $totalCount, 2);;
            return $code;
        });
        $columns = $columns->map(function ($code) use ($totalCount) {
            $Code = $code->Code_Intervention;
            $code->$Code = round($code->total * 100 / $totalCount, 2);;
            return $code;
        });
        $keys = $columns->groupBy(['Code_Intervention'])->keys();
        $codes = $codes->groupBy(['Nom_Region']);

        $codes_names = [];
        $codes_names[0] = new \stdClass();
        $codes_names[0]->data = 'Nom_Region';
        $codes_names[0]->name = 'Nom_Region';
        $keys->map(function ($key, $index) use (&$codes_names) {
            $codes_names[$index + 1] = new \stdClass();
            $codes_names[$index + 1]->data = $key;
            $codes_names[$index + 1]->name = $key;
        });
        $codes_names[] = new \stdClass();
        $codes_names[count($codes_names) - 1]->data = 'total';
        $codes_names[count($codes_names) - 1]->name = 'total';

        $total = new \stdClass();
        $codes = $codes->map(function ($region) use (&$codes_names, &$total, $keys) {
            $row = new \stdClass(); //[];
            $row->values = [];
            $col_arr = $keys->all();
            $item = $region->map(function ($call, $index) use (&$row, &$codes_names, &$total, &$col_arr) {

//                $codes_names[] = $call->Code_Intervention;
                $row->Nom_Region = $call->Nom_Region;
                $code_intervention = $call->Code_Intervention;


                $col_arr = array_diff($col_arr, [$code_intervention]);

                $row->values['value_' . $index] = $call->$code_intervention . '%';
                $row->$code_intervention = $call->$code_intervention . '%';
//                $row->$code_intervention = $call->$code_intervention;
                $row->total = round(array_sum($row->values) / count($row->values), 2) . '%';
//                dump($code_intervention ? $total->{$code_intervention}[0] : 1);
//                if ($code_intervention)
//                    $total->$code_intervention =
//                        $total->$index == 0 ?
//                        $total->$index + $call->$code_intervention : 0;
//                    $total[$index] += $call->$code_intervention;

                return $row;
            });
            $_item = $item->last();
            $index = count($_item->values);
            foreach ($col_arr as $col) {
                $_item->values['value_' . $index++] = '0%';
                $_item->$col = '0%';
            }
            return $_item;
        });

        $codes_names = collect($codes_names)->unique()->values();
        $codes = $codes->values();

        $data = ['columns' => $codes_names, 'data' => $codes];
        return $data;
    }

    public function importStats($request)
    {
        try {
            Excel::import(new StatsImport, $request->file('file'));
            return [
                'success' => true,
                'message' => 'Le fichier a été importé avec succès'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Une erreur est survenue'
            ];
        }
    }

    #region OldCode ==================================

//    public function dashboard(Request $request)
//    {
//        $regions = Stats::getRegions();
//
//        $joignable = Stats::getClientsByCallState('Joignable');
//        $inJoignable = Stats::getClientsByCallState('Injoignable');
//
//        $foldersByIntervCode = Stats::getNonValidatedFolders('Code_Intervention');
//        $foldersByIntervType = Stats::getNonValidatedFolders('Code_Type_Intervention');
////        return [$folders, $regions];
////        return [$regions, $joignable, $inJoignable];
//        return [
//            'regions' => $regions,
//            'foldersByIntervType' => $foldersByIntervType,
//            'foldersByIntervCode' => $foldersByIntervCode,
//            'joignable' => $joignable,
//            'inJoignable' => $inJoignable
//        ];
//    }
//
//    public function getRegionsByDates(Request $request)
//    {
//        $data = array_filter($request->dates_0, function ($date) {
//            return $date != null;
//        });
//
//        $regions = Stats::getRegions($data);
//        $foldersByIntervCode = Stats::getNonValidatedFolders('Code_Intervention');
//        $foldersByIntervType = Stats::getNonValidatedFolders('Code_Type_Intervention');
//
//        $joignable = Stats::getClientsByCallState('Joignable');
//        $inJoignable = Stats::getClientsByCallState('Injoignable');
////        return [$regions, $joignable, $inJoignable];
//
//        return [
//            'regions' => $regions,
//            'foldersByIntervType' => $foldersByIntervType,
//            'foldersByIntervCode' => $foldersByIntervCode,
//            'joignable' => $joignable,
//            'inJoignable' => $inJoignable
//        ];
//
//    }
//
//    public function getNonValidatedFoldersByCodeByDates(Request $request)
//    {
//        $data = array_filter($request->dates_4, function ($date) {
//            return $date != null;
//        });
//
//        $regions = Stats::getRegions();
//        $foldersByIntervCode = Stats::getNonValidatedFolders('Code_Intervention', $data);
//        $foldersByIntervType = Stats::getNonValidatedFolders('Code_Type_Intervention');
////        return [$regions, $joignable, $inJoignable];
//        $joignable = Stats::getClientsByCallState('Joignable');
//        $inJoignable = Stats::getClientsByCallState('Injoignable');
//        return [
//            'regions' => $regions,
//            'foldersByIntervType' => $foldersByIntervType,
//            'foldersByIntervCode' => $foldersByIntervCode,
//            'joignable' => $joignable,
//            'inJoignable' => $inJoignable
//
//        ];
//    }
//
//    public function getNonValidatedFoldersByTypeByDates(Request $request)
//    {
//        $data = array_filter($request->dates_3, function ($date) {
//            return $date != null;
//        });
//
//        $regions = Stats::getRegions($data);
//        $foldersByIntervCode = Stats::getNonValidatedFolders('Code_Intervention');
//        $foldersByIntervType = Stats::getNonValidatedFolders('Code_Type_Intervention', $data);
////        return [$regions, $joignable, $inJoignable];
//        $joignable = Stats::getClientsByCallState('Joignable');
//        $inJoignable = Stats::getClientsByCallState('Injoignable');
//
//        return [
//            'regions' => $regions,
//            'foldersByIntervType' => $foldersByIntervType,
//            'foldersByIntervCode' => $foldersByIntervCode,
//            'joignable' => $joignable,
//            'inJoignable' => $inJoignable
//        ];
//    }
//
//    public function getClientsByCallStateJoiByDates(Request $request)
//    {
//        $data = array_filter($request->dates_1, function ($date) {
//            return $date != null;
//        });
//
//        $regions = Stats::getRegions($data);
//        $foldersByIntervCode = Stats::getNonValidatedFolders('Code_Intervention');
//        $foldersByIntervType = Stats::getNonValidatedFolders('Code_Type_Intervention');
////        return [$regions, $joignable, $inJoignable];
//        $joignable = Stats::getClientsByCallState('Joignable', $data);
//        $inJoignable = Stats::getClientsByCallState('Injoignable');
//
//        return [
//            'regions' => $regions,
//            'foldersByIntervType' => $foldersByIntervType,
//            'foldersByIntervCode' => $foldersByIntervCode,
//            'joignable' => $joignable,
//            'inJoignable' => $inJoignable
//        ];
//    }
//
//    public function getClientsByCallStateInjByDates(Request $request)
//    {
//        $data = array_filter($request->dates_2, function ($date) {
//            return $date != null;
//        });
//
//        $regions = Stats::getRegions($data);
//        $foldersByIntervCode = Stats::getNonValidatedFolders('Code_Intervention');
//        $foldersByIntervType = Stats::getNonValidatedFolders('Code_Type_Intervention');
////        return [$regions, $joignable, $inJoignable];
//        $joignable = Stats::getClientsByCallState('Joignable');
//        $inJoignable = Stats::getClientsByCallState('Injoignable', $data);
//
//        return [
//            'regions' => $regions,
//            'foldersByIntervType' => $foldersByIntervType,
//            'foldersByIntervCode' => $foldersByIntervCode,
//            'joignable' => $joignable,
//            'inJoignable' => $inJoignable
//        ];
//    }

    #endregion

}
