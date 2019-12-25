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

    #region IMPROVEMENT IN PROGRESS

    // TODO: MAKE IT REUSABLE
    public function getData($row, $column, $dates = null)
    {
        $rows = \DB::table('stats')
            ->select($column, $row, \DB::raw('count(*) as total'));
        $columns = $rows->groupBy($column, $row)->get();

        // DEMO PLACEHOLDER (1)

        #region DEMO (1) ==============
//        $regions = $regions->whereNotNull($callResult);
        #endregion DEMO


        if ($dates) {
            $dates = array_values($dates);
            $rows = $rows->whereIn('Date_Note', $dates);
        }
//        $regions = ($dates ? $regions->whereIn('Date_Note', $dates)->get() : $regions)->get();

        $rows = $rows->groupBy($column, $row)->get();


        $totalCount = Stats::all()->count();
        $rows = $rows->map(function ($_row) use ($totalCount, $column) {
            $dataCol = $_row->$column;
            $_row->$dataCol = round($_row->total * 100 / $totalCount, 2);
            return $_row;
        });
        $columns = $columns->map(function ($_column) use ($totalCount, $row) {
            $dataRow = $_column->$row;
            $_column->$dataRow = round($_column->total * 100 / $totalCount, 2);
            return $_column;
        });
        $keys = $columns->groupBy([$column])->keys();

        $rows = $rows->groupBy([$row]);
//        $regions = $regions->groupBy(['Nom_Region']);

        $_columns = [];
        $_columns[0] = new \stdClass();
        $_columns[0]->data = $row;
        $_columns[0]->name = $row;
        $keys->map(function ($key, $index) use (&$_columns) {
            $_columns[$index + 1] = new \stdClass();
            $_columns[$index + 1]->data = $key;
            $_columns[$index + 1]->name = $key;
        });
        $_columns[] = new \stdClass();
        $_columns[count($_columns) - 1]->data = 'total';
        $_columns[count($_columns) - 1]->name = 'total';


        $rows = $rows->map(function ($rowItem) use (&$_columns, $keys, $row, $column) {
            $_row = new \stdClass();
            $_row->values = [];

            $col_arr = $keys->all();
            $item = $rowItem->map(function ($rowData, $index) use (&$_row, &$regions_names, &$col_arr, $row, $column) {
                $_row->$row = $rowData->$row;
                $dataCol = $rowData->$column;

                $col_arr = array_diff($col_arr, [$dataCol]);

                $_row->values['value_' . $index] = $rowData->$dataCol . '%';
                $_row->$dataCol = $rowData->$dataCol . '%';
                $_row->total = round(array_sum($_row->values) / count($_row->values), 2) . '%';
                $_row->_total = $rowData->total;
                $_row->column = $row;
                return $_row;
            });

            $_item = $item->last();
            $index = count($_item->values);
            foreach ($col_arr as $col) {
                $_item->values['value_' . $index++] = '0%';
                $_item->$col = '0%';
            }
            return $_item;
        });

        $rows = $rows->values();

        return ['columns' => $_columns, 'data' => $rows];
    }

    #endregion

    public function GetDataRegions($callResult, $dates = null)
    {
        $regions = \DB::table('stats')
            ->select('Nom_Region', $callResult, \DB::raw('count(*) as total'))
            ->where($callResult, 'not like', '=%');
        $columns = $regions->groupBy('Nom_Region', $callResult)->get();

        // DEMO PLACEHOLDER (1)

        #region DEMO (1) ==============
//        $regions = $regions->whereNotNull($callResult);
        #endregion DEMO


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

        // DEMO PLACEHOLDER (1)

        #region DEMO (1) ==============
//        $regions = $regions->whereNotNull([$column, 'Gpmt_Appel_Pre']);
        #endregion DEMO


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


        $total = new \stdClass();
        $total->values = [];
        $regions = $regions->map(function ($region, $index) use (&$columns, $keys, $column, &$total) {
//            dd($index, $region);
            $row = new \stdClass();
            $row->values = [];

            $col_arr = $keys->all();
            $item = $region->map(function ($call, $index) use (&$row, &$col_arr, $column) {
//                dd($index, $call);
                $row->Gpmt_Appel_Pre = $call->Gpmt_Appel_Pre;
                $column_name = $call->$column;
                $col_arr = array_diff($col_arr, [$column_name]);
                $row->values[$column_name] = $call->$column_name;
                $row->$column_name = $call->$column_name;
                $row->column = $call->Gpmt_Appel_Pre;
                $row->total = isset($row->total) ? $row->total + $call->total : 0;
//                $row->_total = $call->total;
//                $row->total = $total; //round(array_sum($row->regions) / count($row->regions), 2) . '%';
//                dd($row);
                return $row;
            });
            $_item = $item->last();
            $index = count($_item->values);
            foreach ($col_arr as $col) {
                $_item->values[$col] = 0; //'0%';
                $_item->$col = 0; //'0%';
            }
            collect($_item->values)->map(function ($value, $index) use (&$total) {
                $total->values[$index] = round(!isset($total->values[$index]) ? $value : $value + $total->values[$index], 2);
                $total->$index = $total->values[$index];
            });
            return $_item;
        });
        $total->Gpmt_Appel_Pre = 'Total Général';
        $total->total = round(array_sum($total->values), 2);
        $regions->push($total);

        $regions = $regions->values();

        return ['columns' => $columns, 'data' => $regions];
    }

    public function getDataNonValidatedFolders($intervCol, $dates = null)
    {
        $regions = \DB::table('stats')
            ->select('Nom_Region', $intervCol, \DB::raw('count(*) as total'));

        $columns = $regions->groupBy('Nom_Region', $intervCol)->get();

        // DEMO PLACEHOLDER (1)

        #region DEMO (1) ==============
//        $regions = $regions->whereNotNull($intervCol);
        #endregion DEMO

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


        $total = new \stdClass();
        $total->values = [];
        $regions = $regions->map(function ($region) use (&$regions_names, $keys, $intervCol, &$total) {
            $row = new \stdClass();
            $row->values = [];
            $col_arr = $keys->all();
            $item = $region->map(function ($call, $index) use (&$row, &$regions_names, &$col_arr, $intervCol) {

                $row->$intervCol = $call->$intervCol;

                $nom_region = $call->Nom_Region;

                $col_arr = array_diff($col_arr, [$nom_region]);

                $row->values[$nom_region] = $call->$nom_region . '%';
                $row->$nom_region = $call->$nom_region . '%';
                $row->total = round(array_sum($row->values) / count($row->values), 2) . '%';
                return $row;
            });
            $_item = $item->last();
            $index = count($_item->values);
            foreach ($col_arr as $col) {
                $_item->values[$col] = '0%';
                $_item->$col = '0%';
            }
            collect($_item->values)->map(function ($value, $index) use (&$total) {
                $value = str_replace('%', '', $value);
                $total->values[$index] = round(!isset($total->values[$index]) ? $value : $value + $total->values[$index], 2);
                $total->$index = $total->values[$index] . '%';
            });
            return $_item;
//            return $item->last();
        });


        $total->Nom_Region = 'Total Général';
        $total->total = round(array_sum($total->values) / count($total->values), 2) . '%';

        $regions->push($total);

        $regions = $regions->values();
        $data = ['columns' => $regions_names, 'data' => $regions];
        return $data;
    }

    public function getDataClientsByCallState($callResult, $dates = null)
    {
        $codes = \DB::table('stats')
            ->select('Code_Intervention', 'Nom_Region', \DB::raw('count(*) as total'));
        $columns = $codes->groupBy('Code_Intervention', 'Nom_Region')->get();
        // DEMO PLACEHOLDER (1)

        #region DEMO (1) ==============
//        $codes = $codes->whereNotNull('Code_Intervention')
//            ->where('Gpmt_Appel_Pre', $callResult);
        $codes = $codes->where('Gpmt_Appel_Pre', $callResult);
        #endregion DEMO

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
            $code->$Code = round($code->total * 100 / $totalCount, 2);
            return $code;
        });
        $columns = $columns->filter(function ($code) use ($totalCount) {
//            if ($code->Code_Intervention) {
            $Code = $code->Code_Intervention;
            $code->$Code = round($code->total * 100 / $totalCount, 2);
            return $code;
//            }
//            return;
        });
        $keys = $columns->groupBy(['Code_Intervention'])->keys();
        $codes = $codes->groupBy(['Nom_Region']);
        $codes_names = [];
//        $codes_names[0] = new \stdClass();
//        $codes_names[0]->data = 'Nom_Region';
//        $codes_names[0]->name = 'Nom_Region';
        $keys->map(function ($key, $index) use (&$codes_names) {
            $codes_names[$index + 1] = new \stdClass();
            $codes_names[$index + 1]->data = $key;
            $codes_names[$index + 1]->name = $key;
//            $_value = new \stdClass();
//            $_value->data = $key;
//            $_value->name = $key;
//            return $_value;
        });
//        $codes_names = $codes_names->all();
        usort($codes_names, function ($item1, $item2) {
            return ($item1->data == $item2->data) ? 0 :
                ($item1->data < $item2->data) ? -1 : 1;
        });

        $first = new \stdClass();
        $first->name = 'Nom_Region';
        $first->data = 'Nom_Region';
        $last = new \stdClass();
        $last->data = 'total';
        $last->name = 'total';
        array_unshift($codes_names, $first);
        array_push($codes_names, $last);

        $total = new \stdClass();
        $total->values = [];
        $codes = $codes->map(function ($region) use (&$codes_names, &$total, $keys) {
            $row = new \stdClass(); //[];
            $row->values = [];
            $col_arr = $keys->all();
            $item = $region->map(function ($call, $index) use (&$row, &$codes_names, &$col_arr) {

//                $codes_names[] = $call->Code_Intervention;
                $row->Nom_Region = $call->Nom_Region;
                $code_intervention = $call->Code_Intervention;


                $col_arr = array_diff($col_arr, [$code_intervention]);
//                dd($call->Code_Intervention);
                $row->values[$code_intervention] = $call->$code_intervention . '%';
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
                $_item->values[$col] = '0%';
                $_item->$col = '0%';
            }

//            dump($_item->values);
            ksort($_item->values);

            collect($_item->values)->map(function ($value, $index) use (&$total) {
                $value = str_replace('%', '', $value);
                $total->values[$index] = round(!isset($total->values[$index]) ? $value : $value + $total->values[$index], 2);
                $total->$index = $total->values[$index] . '%';
            });
            return $_item;
        });

        $total->Nom_Region = 'Total Général';
        $total->total = round(array_sum($total->values) / count($total->values), 2) . '%';

        $codes->push($total);
        $codes = $codes->values();
        $data = ['columns' => $codes_names, 'data' => $codes];
        return $data;
    }

    public function getDataClientsByPerimeter($dates = null)
    {
        $results = \DB::table('stats')
            ->select('Groupement', 'Nom_Region', \DB::raw('count(*) as total'));
        $columns = $results->groupBy('Groupement', 'Nom_Region')->get();
        // DEMO PLACEHOLDER (1)

        #region DEMO (1) ==============
//        $codes = $codes->whereNotNull('Code_Intervention')
//            ->where('Gpmt_Appel_Pre', $callResult);
//        $codes = $codes->where('Gpmt_Appel_Pre', $callResult);
        #endregion DEMO

//            ->groupBy('Code_Intervention', 'Nom_Region')
//            ->get();
        if ($dates) {
            $dates = array_values($dates);
            $results = $results->whereIn('Date_Note', $dates);
        }
        $results = $results->groupBy('Groupement', 'Nom_Region')->get();
        $totalCount = Stats::all()->count();
        $results = $results->map(function ($resultItem) use ($totalCount) {
            $Code = $resultItem->Groupement;
            $resultItem->$Code = round($resultItem->total * 100 / $totalCount, 2);;
            return $resultItem;
        });
        $columns = $columns->filter(function ($code) use ($totalCount) {
//            if ($code->Code_Intervention) {
            $Code = $code->Groupement;
            $code->$Code = round($code->total * 100 / $totalCount, 2);;
            return $code;
//            }
//            return;
        });

        $keys = $columns->groupBy(['Groupement'])->keys();
        $results = $results->groupBy(['Nom_Region']);
//         TODO : SORT/SYNC COLUMNS & VALUES
        $column_names = [];
//        $codes_names[0] = new \stdClass();
//        $codes_names[0]->data = 'Nom_Region';
//        $codes_names[0]->name = 'Nom_Region';
        $keys->map(function ($key, $index) use (&$column_names) {
            $column_names[$index + 1] = new \stdClass();
            $column_names[$index + 1]->data = $key;
            $column_names[$index + 1]->name = $key;
//            $_value = new \stdClass();
//            $_value->data = $key;
//            $_value->name = $key;
//            return $_value;
        });
//        $codes_names = $codes_names->all();
        usort($column_names, function ($item1, $item2) {
            return ($item1->data == $item2->data) ? 0 :
                ($item1->data < $item2->data) ? -1 : 1;
        });

        $first = new \stdClass();
        $first->name = 'Nom_Region';
        $first->data = 'Nom_Region';
        $last = new \stdClass();
        $last->data = 'total';
        $last->name = 'total';
        array_unshift($column_names, $first);
        array_push($column_names, $last);


        $total = new \stdClass();
        $results = $results->map(function ($region) use (&$column_names, &$total, $keys) {
            $row = new \stdClass(); //[];
            $row->values = [];
            $col_arr = $keys->all();
            $item = $region->map(function ($call, $index) use (&$row, &$codes_names, &$total, &$col_arr) {

//                $codes_names[] = $call->Code_Intervention;
                $row->Nom_Region = $call->Nom_Region;
                $groupment = $call->Groupement;


                $col_arr = array_diff($col_arr, [$groupment]);
//                dd($call->Code_Intervention);
                $row->values['value_' . $index] = $call->$groupment . '%';
                $row->$groupment = $call->$groupment . '%';
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
                $_item->values['value_' . $index] = '0%';
                $_item->$col = '0%';
            }
//            dump($_item->values);
            ksort($_item->values);
//            dd($_item->values);
            return $_item;
        });

        $results = $results->values();

        $data = ['columns' => $column_names, 'data' => $results];
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
}
