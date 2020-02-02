<?php

namespace App\Http\Controllers;


use App\Imports\StatsImport;
use App\Models\Stats;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use function Matrix\trace;

class AgenceRepository
{
    public function getAgencies (Request $request) {
        $stats = Stats::select(['Nom_Region']);
        if ($request->has('name')) {
            $term = trim(strtolower($request->get('name')));
            $stats = $stats->whereRaw('LOWER(Nom_Region) LIKE ?', ["%$term%"]);
        }
        $stats = $stats->distinct('Nom_Region')->limit(10)->get()->map(function ($s) {
            $sn = explode(' - ', $s->Nom_Region);
            return [
                'name' => trim($s->Nom_Region),
                'code' => trim($sn[1])
            ];
        });
        return $stats;
    }
    public function getAgenciesAll () {
        $stats = Stats::select(['Nom_Region'])->distinct('Nom_Region')->limit(10)->get()->map(function ($s) {
            $sn = explode(' - ', $s->Nom_Region);
            return [
                'name' => trim($s->Nom_Region),
                'code' => trim($sn[1])
            ];
        });
        return $stats->toArray();
    }

    public function filterList ($column, $request) {
        $agenceCode = $request->get('agence_code');
        $stats = Stats::select([$column])
            ->distinct($column)
            ->where($column, 'not like', '=%')
            ->where('Nom_Region', 'like', "%$agenceCode")
            ->whereNotNull($column)->get();
        return $stats->map(function ($s) use ($column) { return $s[$column]; });
    }

    public function getDateNotes($agenceCode)
    {
        $dates = Stats::where('Nom_Region', 'like', "%$agenceCode")->get()
            ->groupBy(['Date_Heure_Note_Annee', 'Date_Heure_Note_Mois', 'Date_Heure_Note_Semaine', 'Date_Note']);
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

    public function GetDataRegions($callResult, $request)
    {
        $resultatAppel = $request->get('resultatAppel');
        $dates = $request->get('dates');
        $agenceCode = $request->get('agence_code');
        $regions = \DB::table('stats')
            ->select('Nom_Region', $callResult, \DB::raw("count($callResult) as total"))
            ->where($callResult, 'not like', '=%')
            ->whereNotNull($callResult);
        if ($agenceCode) {
            $regions = $regions->where('Nom_Region', 'like', "%$agenceCode");
        }
        if ($resultatAppel) {
            $resultatAppel = array_values($resultatAppel);
            $regions = $regions->whereIn('Resultat_Appel', $resultatAppel);
        }
        if ($dates) {
            $dates = array_values($dates);
            $regions = $regions->whereIn('Date_Note', $dates);
        }
//        $regions = ($dates ? $regions->whereIn('Date_Note', $dates)->get() : $regions)->get();

        $regions = $regions->groupBy('Nom_Region', $callResult)->get();

        $totalCount = Stats::count();
        $regions = $regions->map(function ($region) use ($totalCount) {
            $Region = $region->Nom_Region;
            $region->$Region = round($region->total * 100 / $totalCount, 2);
            return $region;
        });

        $keys_regions = $regions->groupBy(['Nom_Region'])->keys();

        $regions = $regions->groupBy([$callResult]);
//        $regions = $regions->groupBy(['Nom_Region']);

        $regions_names = [];
        $regions_names[0] = new \stdClass();
        $regions_names[0]->data = $callResult;
        $regions_names[0]->name = $callResult;

        $regions = $regions->map(function ($region) use (&$regions_names, $keys_regions, $callResult) {
            $row = new \stdClass();
            $row->values = [];

            $col_arr = $keys_regions->all();

            $item = $region->map(function ($call, $index) use (&$row, &$regions_names, &$col_arr, $callResult) {
                $_index = $index + 1;
                $regions_names[$_index] = new \stdClass();
                $regions_names[$_index]->data = $call->Nom_Region;
                $regions_names[$_index]->name = $call->Nom_Region;
                $row->$callResult = $call->$callResult;
                $nom_region = $call->Nom_Region;

                $col_arr = array_diff($col_arr, [$nom_region]);

                $row->values['zone_' . $index] = $call->$nom_region . '%';
                $row->$nom_region = $call->$nom_region . '%';
                $row->total = round(array_sum($row->values) / count($row->values), 2) . '%';
                $row->_total = $call->total;
                return $row;
            });

            $_item = $item->last();
            $index = count($_item->values);
            foreach ($col_arr as $col) {
                $_item->values['zone_' . $index++] = '0%';
                $_item->$col = '0%';
            }
            return $_item;
        });
        $regions_names[] = new \stdClass();
        $regions_names[count($regions_names) - 1]->data = 'total';
        $regions_names[count($regions_names) - 1]->name = 'total';

        $regions_names = collect($regions_names)->unique()->filter()->values();
        $regions = $regions->values();

        return ['columns' => $regions_names, 'data' => $regions];
    }

    public function GetDataRegionsCallState($column, $request)
    {
        $gpmtAppelPre = $request->get('gpmtAppelPre');
        $dates = $request->get('dates');
        $agenceCode = $request->get('agence_code');
        $regions = \DB::table('stats')
            ->select($column, 'Gpmt_Appel_Pre', \DB::raw('count(Gpmt_Appel_Pre) as total'))
            ->whereNotNull('Gpmt_Appel_Pre')
            ->whereNotNull($column);
        if ($agenceCode) {
            $regions->where('Nom_Region', 'like', "%$agenceCode");
        }
        if ($gpmtAppelPre) {
            $gpmtAppelPre = array_values($gpmtAppelPre);
            $regions = $regions->whereIn('Gpmt_Appel_Pre', $gpmtAppelPre);
        }
        if ($dates) {
            $dates = array_values($dates);
            $regions = $regions->whereIn('Date_Note', $dates);
        }
//        $regions = ($dates ? $regions->whereIn('Date_Note', $dates)->get() : $regions)->get();

        $regions = $regions->groupBy($column, 'Gpmt_Appel_Pre')->get();
        $totalCount = Stats::count();
        $regions = $regions->map(function ($region) use ($column) {
            $Region = $region->$column;
            $region->$Region = $region->total; //round($region->total * 100 / $totalCount, 2) . '%';
            return $region;
        });

        $keys = $regions->groupBy([$column])->keys();
        $regions = $regions->groupBy(['Gpmt_Appel_Pre']);
//        $regions = $regions->groupBy(['Nom_Region']);

        $columns = [];
        $columns[0] = new \stdClass();
        $columns[0]->data = 'Gpmt_Appel_Pre';
        $columns[0]->name = 'Gpmt_Appel_Pre';

        $regions = $regions->map(function ($region, $index) use (&$columns, $keys, $column) {
//            dd($index, $region);
            $row = new \stdClass();
            $row->values = [];

            $col_arr = $keys->all();
            $total = 0;
            $item = $region->map(function ($call, $index) use (&$row, &$columns, &$col_arr, &$total, $column) {
//                dd($index, $call);
                $_index = $index + 1;
                $columns[$_index] = new \stdClass();
                $columns[$_index]->data = $call->$column;
                $columns[$_index]->name = $call->$column;
                $row->Gpmt_Appel_Pre = $call->Gpmt_Appel_Pre;
                $column_name = $call->$column;
                $col_arr = array_diff($col_arr, [$column_name]);
                $row->values['value_' . $index] = $call->$column_name;
                $row->$column_name = $call->$column_name;
                $total += $call->total;
//                $row->_total = $call->total;
                $row->total = $total; //round(array_sum($row->values) / count($row->values), 2) . '%';
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


        $columns[] = new \stdClass();
        $columns[count($columns) - 1]->data = 'total';
        $columns[count($columns) - 1]->name = 'total';


        $columns = collect($columns)->unique()->filter()->values();
        $regions = $regions->values();

        return ['columns' => $columns, 'data' => $regions];
    }

    public function getDataNonValidatedFolders($intervCol, $request)
    {
        $dates = $request->get('dates');
        $agenceCode = $request->get('agence_code');
        $codeTypeIntervention = $request->get('codeTypeIntervention');
        $codeIntervention = $request->get('codeIntervention');
        $regions = \DB::table('stats')
            ->select('Nom_Region', $intervCol, \DB::raw('count(distinct st.Id_Externe) as total'))
            ->whereNotNull($intervCol);
        if ($agenceCode) {
            $regions->where('Nom_Region', 'like', "%$agenceCode");
        }
        if ($codeTypeIntervention) {
            $codeTypeIntervention = array_values($codeTypeIntervention);
            $regions = $regions->whereIn('Code_Type_Intervention', $codeTypeIntervention);
        }
        if ($codeIntervention) {
            $codeIntervention = array_values($codeIntervention);
            $regions = $regions->whereIn('Code_Intervention', $codeIntervention);
        }
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
        $keys_regions = $regions->groupBy(['Nom_Region'])->keys();
        $regions = $regions->groupBy([$intervCol]);

        $regions_names = [];
        $regions_names[0] = new \stdClass();
        $regions_names[0]->data = $intervCol;
        $regions_names[0]->name = $intervCol;

        $regions = $regions->map(function ($region) use (&$regions_names, $keys_regions, $intervCol) {
            $row = new \stdClass();
            $row->values = [];
            $col_arr = $keys_regions->all();
            $item = $region->map(function ($call, $index) use (&$row, &$regions_names, &$col_arr, $intervCol) {
                $_index = $index + 1;
                $regions_names[$_index] = new \stdClass();
                $regions_names[$_index]->data = $call->Nom_Region;
                $regions_names[$_index]->name = $call->Nom_Region;

//                if (property_exists($call, 'Code_Type_Intervention')) {
//                    $row->Code_Type_Intervention = $call->Code_Type_Intervention;
//                } elseif (property_exists($call, 'Code_Intervention')) {
//                    $row->Code_Intervention = $call->Code_Intervention;
//                }

                $row->$intervCol = $call->$intervCol;

                $nom_region = $call->Nom_Region;

                $col_arr = array_diff($col_arr, [$nom_region]);

                $row->values['zone_' . $index] = $call->$nom_region . '%';
                $row->$nom_region = $call->$nom_region . '%';
                $row->total = round(array_sum($row->values) / count($row->values), 2) . '%';
                return $row;
            });
            $_item = $item->last();
            $index = count($_item->values);
            foreach ($col_arr as $col) {
                $_item->values['zone_' . $index++] = '0%';
                $_item->$col = '0%';
            }
            return $_item;
//            return $item->last();
        });
        $regions_names[] = new \stdClass();
        $regions_names[count($regions_names) - 1]->data = 'total';
        $regions_names[count($regions_names) - 1]->name = 'total';

        $regions_names = collect($regions_names)->unique()->values();
        $regions = $regions->values();
        $data = ['columns' => $regions_names, 'data' => $regions];
        return $data;
    }

    public function getDataClientsByCallState($callResult, $dates = null, $agenceCode)
    {
        $codes = \DB::table('stats')
            ->select('Code_Intervention', 'Nom_Region', \DB::raw('count(Code_Intervention) as total'))
            ->whereNotNull('Code_Intervention')
            ->where('Nom_Region', 'like', "%$agenceCode")
            ->where('Gpmt_Appel_Pre', $callResult);
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

        $keys_codes = $codes->groupBy(['Code_Intervention'])->keys();
        $codes = $codes->groupBy(['Nom_Region']);

        $codes_names = [];
        $codes_names[0] = new \stdClass();
        $codes_names[0]->data = 'Nom_Region';
        $codes_names[0]->name = 'Nom_Region';


        $total = new \stdClass();
        $codes = $codes->map(function ($region) use (&$codes_names, &$total, $keys_codes) {
            $row = new \stdClass(); //[];
            $row->values = [];
            $col_arr = $keys_codes->all();
            $item = $region->map(function ($call, $index) use (&$row, &$codes_names, &$col_arr) {
                $_index = $index + 1;
                $codes_names[$_index] = new \stdClass();
                $codes_names[$_index]->data = $call->Code_Intervention;
                $codes_names[$_index]->name = $call->Code_Intervention;

//                $codes_names[] = $call->Code_Intervention;
                $row->Nom_Region = $call->Nom_Region;
                $code_intervention = $call->Code_Intervention;


                $col_arr = array_diff($col_arr, [$code_intervention]);

                $row->values['code_' . $index] = $call->$code_intervention . '%';
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
//            dump($col_arr);
            $_item = $item->last();
            $index = count($_item->values);
            foreach ($col_arr as $col) {
                $_item->values['code_' . $index++] = '0%';
                $_item->$col = '0%';
            }
            return $_item;
//            return $item->last();
        });
//        dd($total);
        $codes_names[] = new \stdClass();
        $codes_names[count($codes_names) - 1]->data = 'total';
        $codes_names[count($codes_names) - 1]->name = 'total';

        $codes_names = collect($codes_names)->unique()->values();
        $codes = $codes->values();

        $data = ['columns' => $codes_names, 'data' => $codes];
        return $data;
    }

    #region OldCode ==================================

    public function dashboard(Request $request)
    {
        $regions = Stats::getRegions();

        $joignable = Stats::getClientsByCallState('Joignable');
        $inJoignable = Stats::getClientsByCallState('Injoignable');

        $foldersByIntervCode = Stats::getNonValidatedFolders('Code_Intervention');
        $foldersByIntervType = Stats::getNonValidatedFolders('Code_Type_Intervention');
//        return [$folders, $regions];
//        return [$regions, $joignable, $inJoignable];
        return [
            'regions' => $regions,
            'foldersByIntervType' => $foldersByIntervType,
            'foldersByIntervCode' => $foldersByIntervCode,
            'joignable' => $joignable,
            'inJoignable' => $inJoignable
        ];
    }

    public function getRegionsByDates(Request $request)
    {
        $data = array_filter($request->dates_0, function ($date) {
            return $date != null;
        });

        $regions = Stats::getRegions($data);
        $foldersByIntervCode = Stats::getNonValidatedFolders('Code_Intervention');
        $foldersByIntervType = Stats::getNonValidatedFolders('Code_Type_Intervention');

        $joignable = Stats::getClientsByCallState('Joignable');
        $inJoignable = Stats::getClientsByCallState('Injoignable');
//        return [$regions, $joignable, $inJoignable];

        return [
            'regions' => $regions,
            'foldersByIntervType' => $foldersByIntervType,
            'foldersByIntervCode' => $foldersByIntervCode,
            'joignable' => $joignable,
            'inJoignable' => $inJoignable
        ];

    }

    public function getNonValidatedFoldersByCodeByDates(Request $request)
    {
        $data = array_filter($request->dates_4, function ($date) {
            return $date != null;
        });

        $regions = Stats::getRegions();
        $foldersByIntervCode = Stats::getNonValidatedFolders('Code_Intervention', $data);
        $foldersByIntervType = Stats::getNonValidatedFolders('Code_Type_Intervention');
//        return [$regions, $joignable, $inJoignable];
        $joignable = Stats::getClientsByCallState('Joignable');
        $inJoignable = Stats::getClientsByCallState('Injoignable');
        return [
            'regions' => $regions,
            'foldersByIntervType' => $foldersByIntervType,
            'foldersByIntervCode' => $foldersByIntervCode,
            'joignable' => $joignable,
            'inJoignable' => $inJoignable

        ];
    }

    public function getNonValidatedFoldersByTypeByDates(Request $request)
    {
        $data = array_filter($request->dates_3, function ($date) {
            return $date != null;
        });

        $regions = Stats::getRegions($data);
        $foldersByIntervCode = Stats::getNonValidatedFolders('Code_Intervention');
        $foldersByIntervType = Stats::getNonValidatedFolders('Code_Type_Intervention', $data);
//        return [$regions, $joignable, $inJoignable];
        $joignable = Stats::getClientsByCallState('Joignable');
        $inJoignable = Stats::getClientsByCallState('Injoignable');

        return [
            'regions' => $regions,
            'foldersByIntervType' => $foldersByIntervType,
            'foldersByIntervCode' => $foldersByIntervCode,
            'joignable' => $joignable,
            'inJoignable' => $inJoignable
        ];
    }

    public function getClientsByCallStateJoiByDates(Request $request)
    {
        $data = array_filter($request->dates_1, function ($date) {
            return $date != null;
        });

        $regions = Stats::getRegions($data);
        $foldersByIntervCode = Stats::getNonValidatedFolders('Code_Intervention');
        $foldersByIntervType = Stats::getNonValidatedFolders('Code_Type_Intervention');
//        return [$regions, $joignable, $inJoignable];
        $joignable = Stats::getClientsByCallState('Joignable', $data);
        $inJoignable = Stats::getClientsByCallState('Injoignable');

        return [
            'regions' => $regions,
            'foldersByIntervType' => $foldersByIntervType,
            'foldersByIntervCode' => $foldersByIntervCode,
            'joignable' => $joignable,
            'inJoignable' => $inJoignable
        ];
    }

    public function getClientsByCallStateInjByDates(Request $request)
    {
        $data = array_filter($request->dates_2, function ($date) {
            return $date != null;
        });

        $regions = Stats::getRegions($data);
        $foldersByIntervCode = Stats::getNonValidatedFolders('Code_Intervention');
        $foldersByIntervType = Stats::getNonValidatedFolders('Code_Type_Intervention');
//        return [$regions, $joignable, $inJoignable];
        $joignable = Stats::getClientsByCallState('Joignable');
        $inJoignable = Stats::getClientsByCallState('Injoignable', $data);

        return [
            'regions' => $regions,
            'foldersByIntervType' => $foldersByIntervType,
            'foldersByIntervCode' => $foldersByIntervCode,
            'joignable' => $joignable,
            'inJoignable' => $inJoignable
        ];
    }

    #endregion

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
