<?php

namespace App\Repositories;

use App\Models\Filter;
use App\Models\Stats;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class DemoRepository
{
    public function GetDataRegions(Request $request, $callResult)
    {
        $groupement = $request->get('rowFilter');
//        $resultatAppel = $request->get('resultatAppel');
//        $groupement = $request->get('groupement');
        $agenceCode = $request->get('agence_code');
        $agentName = $request->get('agent_name');


//        $regions = \DB::table('stats')
//            ->select('Nom_Region', $callResult, 'Key_Groupement', \DB::raw('count(distinct st.Id_Externe) as total'))
//            ->whereNotNull('Nom_Region')
//            ->where($callResult, 'not like', '=%')
//            ->where('Groupement', 'not like', 'Non Renseigné')
//            ->where('Groupement', 'not like', 'Appels post');

        $_route = getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);

        list($filter, $queryFilters) = makeFilterSubQuery($request, $route, 'Groupement', $groupement);

        $regions = \DB::table('stats as st')
            ->select('Nom_Region', $callResult, 'Key_Groupement', \DB::raw('count(distinct st.Id_Externe) as total'))
            ->join(\DB::raw('(SELECT Id_Externe, MAX(Date_Heure_Note) AS MaxDateTime FROM stats
            where Nom_Region is not null ' .
                ($agentName ? 'and Utilisateur like "' . $agentName . '"' : '') .
                ($agenceCode ? 'and Nom_Region like "%' . $agenceCode . '"' : '') .
                ' and ' . $queryFilters .
                ' and Groupement not like "=%"
            and Groupement not like "Non Renseigné"
            and Groupement not like "Appels post"
            GROUP BY Id_Externe) groupedst'),
                function ($join) {
                    $join->on('st.Id_Externe', '=', 'groupedst.Id_Externe');
                    $join->on('st.Date_Heure_Note', '=', 'groupedst.MaxDateTime');
                })
            ->whereNotNull('Nom_Region')
            ->where('Groupement', 'not like', '=%')
            ->where('Groupement', 'not like', 'Non Renseigné')
            ->where('Groupement', 'not like', 'Appels post');
        $regions = applyFilter($regions, $filter, 'Groupement');

        if ($agentName) {
            $regions = $regions->where('Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $regions = $regions->where('Nom_Region', 'like', "%$agenceCode");
        }
        $rowsKeys = \DB::table('stats as st')
            ->select($callResult)
            ->whereNotNull('Nom_Region')
            ->where('Groupement', 'not like', '=%')
            ->where('Groupement', 'not like', 'Non Renseigné')
            ->where('Groupement', 'not like', 'Appels post')
            ->groupBy($callResult)->pluck($callResult);

//        if ($resultatAppel) {
//            $resultatAppel = array_values($resultatAppel);
//            $regions = $regions->whereIn('Resultat_Appel', $resultatAppel);
//        }
//        if ($groupement) {
//            $groupement = array_values($groupement);
//            $regions = $regions->whereIn('Groupement', $groupement);
//        }
//        $route = getRoute(Route::current());
        dd($regions->groupBy('Nom_Region', $callResult, 'Key_Groupement')->toSql());
        $columns = $regions->groupBy('Nom_Region', $callResult, 'Key_Groupement')->get();
        $regions = $regions->groupBy('Nom_Region', $callResult, 'Key_Groupement')->get();

        $keys = $regions->groupBy(['Nom_Region'])->keys();

        $regions = $columns = addRegionWithZero($request, $regions, $columns);
        // logger($regions);
        if (!count($regions)) {
            $data = ['filter' => $filter, 'columns' => [], 'data' => [], 'rows' => $rowsKeys, 'rowsFilterHeader' => 'Groupement'];
            return $data;
        } else {
            $totalCount = Stats::count();
            $temp = $regions->groupBy(['Nom_Region']);

//            dd($temp);
            $temp = $temp->map(function ($calls, $index) {
                $totalZone = $calls->reduce(function ($carry, $call) {
                    return $carry + $call->total;
                }, 0);
                return $calls->map(function ($call) use ($index, $totalZone) {
                    $call->$index = $totalZone == 0 ? 0.00 : round($call->total * 100 / $totalZone, 2);
                    return $call;
                });
            });

            $regions = $temp->flatten();
//            dd($temp);

//            $regions = $regions->map(function ($region) use ($totalCount) {
//                dump($region);
//                $Region = $region->Nom_Region;
//                $region->$Region = round($region->total * 100 / $totalCount, 2);
//                dd($region);
//                return $region;
//            });
//            $columns = $columns->map(function ($region) use ($totalCount) {
//                $Region = $region->Nom_Region;
//                $region->$Region = round($region->total * 100 / $totalCount, 2);
//                return $region;
//            });
//            $keys = $columns->groupBy(['Nom_Region'])->keys();
//            dd($keys);

            $regions = $regions->groupBy([$callResult]);
//        $regions = $regions->groupBy(['Nom_Region']);
            $regions_names = [];
            $keys->map(function ($key, $index) use (&$regions_names) {
                $regions_names[$index + 1] = new \stdClass();
                $regions_names[$index + 1]->data =
                $regions_names[$index + 1]->name =
                $regions_names[$index + 1]->text =
                $regions_names[$index + 1]->title = $key;
            });
            usort($regions_names, function ($item1, $item2) {
                return ($item1->data == $item2->data) ? 0 :
                    ($item1->data < $item2->data) ? -1 : 1;
            });
            $first = new \stdClass();
            $first->title = 'Résultats Appels Préalables';
            $first->text = 'Résultats Appels Préalables';
            $first->name = $first->data = $callResult;
            array_unshift($regions_names, $first);
//            $detailCol = new \stdClass();
//            $detailCol->name = 'Key_Groupement';
//            $detailCol->data = 'Key_Groupement';
//            array_unshift($regions_names, $detailCol);
//            $regions_names[] = new \stdClass();
//            $regions_names[count($regions_names) - 1]->data = 'total';
//            $regions_names[count($regions_names) - 1]->name = 'total';

            $regions = $regions->map(function ($region, $index) use (&$regions_names, $keys, $callResult) {
                $row = new \stdClass();
                $row->values = [];
                $row->line = $index;

                $col_arr = $keys->all();

                $item = $region->map(function ($call, $index) use (&$row, &$regions_names, &$col_arr, $callResult) {
                    $row->Key_Groupement = $call->Key_Groupement;
                    $row->$callResult = $call->$callResult;
                    $nom_region = $call->Nom_Region;

                    $col_arr = array_diff($col_arr, [$nom_region]);

                    $row->values[$nom_region] = $call->$nom_region;
                    $row->$nom_region = $call->total . ' / ' . $call->$nom_region . '%';
//                    $row->$nom_region = $call->$nom_region . '%';
//                    $row->total = round(array_sum($row->values) / count($row->values), 2) . '%';
//                    $row->_total = $call->total;
                    $row->column = $callResult;
//                    dump($row);
                    return $row;
                });

                $_item = $item->last();
//                dd($_item);
                $index = count($_item->values);
                foreach ($col_arr as $col) {
                    $_item->values[$col] = 0;
                    $_item->$col = '0%';
                }

                ksort($_item->values);

                $_item->values = collect($_item->values)->values();

                return $_item;
            });

            $regions = $regions->values();

            return ['filter' => $filter, 'columns' => $regions_names, 'rows' => $rowsKeys, 'rowsFilterHeader' => 'Groupement', 'data' => $regions];
        }
    }


    #region Trash code

    public function stats_getDataNonValidatedFolders(Request $request, $intervCol)
    {
        $dates = $request->get('dates');
        $agentName = $request->get('agent_name');
        $codeTypeIntervention = $request->get('codeTypeIntervention');
        $codeIntervention = $request->get('codeIntervention');
        $agenceCode = $request->get('agence_code');

//        $regions = \DB::table('stats')
//            ->select('Nom_Region', $intervCol, \DB::raw('count(*) as total'))
//            ->whereNotNull('Nom_Region');

        $regions = \DB::table('stats as st')
            ->select('Nom_Region', $intervCol, \DB::raw('count(distinct st.Id_Externe) as total'))
            ->join(\DB::raw('(SELECT Id_Externe, MAX(Date_Heure_Note) AS MaxDateTime FROM stats
            where Nom_Region is not null
            and Groupement like "Appels clôture" ' .
                ($agentName ? 'and Utilisateur like "' . $agentName . '"' : '') .
                ($agenceCode ? 'and Nom_Region like "%' . $agenceCode . '"' : '') .
                ' GROUP BY Id_Externe) groupedst'),
                function ($join) {
                    $join->on('st.Id_Externe', '=', 'groupedst.Id_Externe');
                    $join->on('st.Date_Heure_Note', '=', 'groupedst.MaxDateTime');
                })
            ->whereNotNull('Nom_Region')
            ->where('Groupement', 'Appels clôture');

        if ($agentName) {
            $regions = $regions->where('Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $regions = $regions->where('Nom_Region', 'like', "%$agenceCode");
        }
        $keys = ($regions->groupBy('Nom_Region', $intervCol)->get())->groupBy(['Nom_Region'])->keys();
        $rowsKeys = ($regions->groupBy('Nom_Region', $intervCol)->get())->groupBy([$intervCol])->keys();

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

        $user = auth()->user() ?? User::find(1);
        $_route = getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);
        $filter = Filter::where(['route' => $route, 'user_id' => $user->id])->first();

        list($filter, $regions) = applyFilter($request, $route, $regions);

        $columns = $regions->groupBy('Nom_Region', $intervCol)->get();
//        $regions = $regions->groupBy('Nom_Region', $intervCol)->get();


        $regions = $regions->groupBy('Nom_Region', $intervCol)->get();
        $regions = $columns = addRegionWithZero($request, $regions, $columns);

        if (!count($regions)) {
            $data = ['filter' => $filter, 'columns' => [], 'data' => [], 'rows' => $rowsKeys];
            return $data;
        } else {
            $totalCount = Stats::count();
            $temp = $regions->groupBy(['Nom_Region']);

//            dd($temp);
            $temp = $temp->map(function ($calls, $index) {
                $totalZone = $calls->reduce(function ($carry, $call) {
                    return $carry + $call->total;
                }, 0);
                return $calls->map(function ($call) use ($index, $totalZone) {
                    $call->$index = $totalZone == 0 ? 0.00 : round($call->total * 100 / $totalZone, 2);
                    return $call;
                });
            });
//            dd($temp);

            $regions = $temp->flatten();

//            $regions = $regions->map(function ($region) use ($totalCount) {
//                $Region = $region->Nom_Region;
//                $region->$Region = round($region->total * 100 / $totalCount, 2);;
//                return $region;
//            });
//            $columns = $columns->map(function ($region) use ($totalCount) {
//                $Region = $region->Nom_Region;
//                $region->$Region = round($region->total * 100 / $totalCount, 2);;
//                return $region;
//            });
//            $keys = $columns->groupBy(['Nom_Region'])->keys();
            $regions = $regions->groupBy($intervCol);
//            $regions = $regions->groupBy('Nom_Region');


            $regions_names = [];
//            $regions_names[0] = new \stdClass();
//            $regions_names[0]->data = $intervCol;
//            $regions_names[0]->name = $intervCol;
            $keys->map(function ($key, $index) use (&$regions_names) {
                $regions_names[$index + 1] = new \stdClass();
                $regions_names[$index + 1]->text =
                $regions_names[$index + 1]->data =
                $regions_names[$index + 1]->name =
                $regions_names[$index + 1]->title = $key;
            });
            usort($regions_names, function ($item1, $item2) {
                return ($item1->data == $item2->data) ? 0 :
                    ($item1->data < $item2->data) ? -1 : 1;
            });
            $first = new \stdClass();
            $first->title = $intervCol == 'Code_Intervention' ? 'Code Intervention' : 'Type Intervention';
            $first->text = $intervCol == 'Code_Intervention' ? 'Code Intervention' : 'Type Intervention';
            $first->name = $first->data = $intervCol;
            $first->orderable = false;
            array_unshift($regions_names, $first);
//            $last = new \stdClass();
//            $last->data = 'total';
//            $last->name = 'total';
//            array_push($regions_names, $last);
//            $regions_names[] = new \stdClass();
//            $regions_names[count($regions_names) - 1]->data = 'total';
//            $regions_names[count($regions_names) - 1]->name = 'total';


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

                    $row->values[$nom_region] = $call->$nom_region;
                    $row->$nom_region = $call->total . ' / ' . $call->$nom_region . '%';
                    $row->total = isset($row->total) ? $row->total + $call->total : $call->total;
//                    $row->total = round(array_sum($row->values), 2); //round(array_sum($row->values) / count($row->values), 2) . '%';
                    return $row;
                });
                $_item = $item->last();
                $index = count($_item->values);
                foreach ($col_arr as $col) {
                    $_item->values[$col] = 0; //'0%';
                    $_item->$col = '0%';
                }

                ksort($_item->values);
                collect($_item->values)->map(function ($value, $index) use (&$total) {
                    $value = str_replace('%', '', $value);
                    $total->values[$index] = round(!isset($total->values[$index]) ? $value : $value + $total->values[$index], 2);
                    $total->$index = $total->values[$index];
                });
                $_item->values = collect($_item->values)->values();
                return $_item;
//            return $item->last();
            });

            $dataCount = $regions->count();
            collect($total->values)->map(function ($value, $index) use (&$total, $dataCount) {
                $total->values[$index] = ceil(round($total->values[$index], 2)); // round($total->values[$index] / $dataCount, 2);
                $total->$index = $total->values[$index] . '%';
            });

            $total->$intervCol = 'Total Général';
            $total->total = round(array_sum($total->values)); //round(array_sum($total->values) / count($total->values), 2) . '%';
            $total->values = collect($total->values)->values();
            $total->isTotal = true;
            $regions->push($total);

            $regions = $regions->values();
            $data = ['filter' => $filter, 'columns' => $regions_names, 'rows' => $rowsKeys, 'data' => $regions];
            return $data;
        }
    }

    public function filter_getDataNonValidatedFolders(Request $request, $intervCol, $filter = null)
    {
        $route = getRoute(Route::current());

        $dates = $request->get('dates');
        $agentName = $request->get('agent_name');
        $codeTypeIntervention = $request->get('codeTypeIntervention');
        $codeIntervention = $request->get('codeIntervention');
        $agenceCode = $request->get('agence_code');
        $radical_route = $filter ?? getRadicalRoute(Route::current());
//        $regions = \DB::table('stats')
//            ->select('Nom_Region', $intervCol, \DB::raw('count(*) as total'))
//            ->whereNotNull('Nom_Region');

        $regions = \DB::table('stats as st')
            ->select('Nom_Region', $intervCol, \DB::raw('count(distinct st.Id_Externe) as total'))
            ->join(\DB::raw('(SELECT Id_Externe, MAX(Date_Heure_Note) AS MaxDateTime FROM stats
            where Nom_Region is not null
            and key_Groupement like "' . $radical_route . '"
            GROUP BY Id_Externe) groupedst'),
                function ($join) {
                    $join->on('st.Id_Externe', '=', 'groupedst.Id_Externe');
                    $join->on('st.Date_Heure_Note', '=', 'groupedst.MaxDateTime');
                })
            ->where('key_Groupement', 'like', $radical_route)
            ->whereNotNull('Nom_Region');

        if ($agentName) {
            $regions = $regions->where('Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $regions = $regions->where('Nom_Region', 'like', "%$agenceCode");
        }
        $keys = ($regions->groupBy('Nom_Region', $intervCol)->get())->groupBy(['Nom_Region'])->keys();
        $rowsKeys = ($regions->groupBy('Nom_Region', $intervCol)->get())->groupBy([$intervCol])->keys();

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

        $user = auth()->user() ?? User::find(1);
        $_route = getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);
        $filter = Filter::where(['route' => $route, 'user_id' => $user->id])->first();

        if ($request && count($request->all())) {
            if ($request->exists('refreshMode')) {
                if ($dates) {
                    $dates = array_values($dates);
                    $filter = Filter::firstOrNew(['route' => $route, 'user_id' => $user->id]);
                    $filter->date_filter = $dates;
                    $filter->save();
                    $regions = $regions->whereIn('Date_Note', $dates);
                } else {
                    $filter = Filter::where(['route' => $route, 'user_id' => $user->id])->first();
                    if ($filter) {
                        $filter->forceDelete();
                    }
                }
            } else {
                $filter = Filter::where(['route' => $route, 'user_id' => $user->id])->first();
                if ($filter) {
                    $regions = $regions->whereIn('Date_Note', $filter->date_filter);
                }
            }
        } else {
            $filter = Filter::where(['route' => $route, 'user_id' => $user->id])->first();
            if ($filter) {
                $regions = $regions->whereIn('Date_Note', $filter->date_filter);
            }
        }

        $columns = $regions->groupBy('Nom_Region', $intervCol)->get();
//        $regions = $regions->groupBy('Nom_Region', $intervCol)->get();


        $regions = $regions->groupBy('Nom_Region', $intervCol)->get();
        $regions = $columns = addRegionWithZero($request, $regions, $columns);

        if (!count($regions)) {
            $data = ['columns' => [], 'data' => []];
            return $data;
        } else {
            $totalCount = Stats::count();
            $temp = $regions->groupBy(['Nom_Region']);

//            dd($temp);
            $temp = $temp->map(function ($calls, $index) {
                $totalZone = $calls->reduce(function ($carry, $call) {
                    return $carry + $call->total;
                }, 0);
                return $calls->map(function ($call) use ($index, $totalZone) {
                    $call->$index = $totalZone == 0 ? 0.00 : round($call->total * 100 / $totalZone, 2);
                    return $call;
                });
            });
//            dd($temp);

            $regions = $temp->flatten();

//            $regions = $regions->map(function ($region) use ($totalCount) {
//                $Region = $region->Nom_Region;
//                $region->$Region = round($region->total * 100 / $totalCount, 2);;
//                return $region;
//            });
//            $columns = $columns->map(function ($region) use ($totalCount) {
//                $Region = $region->Nom_Region;
//                $region->$Region = round($region->total * 100 / $totalCount, 2);;
//                return $region;
//            });
//            $keys = $columns->groupBy(['Nom_Region'])->keys();
            $regions = $regions->groupBy($intervCol);
//            $regions = $regions->groupBy('Nom_Region');


            $regions_names = [];
//            $regions_names[0] = new \stdClass();
//            $regions_names[0]->data = $intervCol;
//            $regions_names[0]->name = $intervCol;
            $keys->map(function ($key, $index) use (&$regions_names) {
                $regions_names[$index + 1] = new \stdClass();
                $regions_names[$index + 1]->text =
                $regions_names[$index + 1]->data =
                $regions_names[$index + 1]->name =
                $regions_names[$index + 1]->title = $key;
            });
            usort($regions_names, function ($item1, $item2) {
                return ($item1->data == $item2->data) ? 0 :
                    ($item1->data < $item2->data) ? -1 : 1;
            });
            $first = new \stdClass();
            $first->title = $intervCol == 'Code_Intervention' ? 'Code Intervention' : 'Type Intervention';
            $first->text = $intervCol == 'Code_Intervention' ? 'Code Intervention' : 'Type Intervention';
            $first->name = $first->data = $intervCol;
            $first->orderable = false;
            array_unshift($regions_names, $first);
//            $last = new \stdClass();
//            $last->data = 'total';
//            $last->name = 'total';
//            array_push($regions_names, $last);
//            $regions_names[] = new \stdClass();
//            $regions_names[count($regions_names) - 1]->data = 'total';
//            $regions_names[count($regions_names) - 1]->name = 'total';


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

                    $row->values[$nom_region] = $call->$nom_region;
                    $row->$nom_region = $call->total . ' / ' . $call->$nom_region . '%';
                    $row->total = isset($row->total) ? $row->total + $call->total : $call->total;
//                    $row->total = round(array_sum($row->values), 2); //round(array_sum($row->values) / count($row->values), 2) . '%';
                    return $row;
                });
                $_item = $item->last();
                $index = count($_item->values);
                foreach ($col_arr as $col) {
                    $_item->values[$col] = 0; //'0%';
                    $_item->$col = '0%';
                }

                ksort($_item->values);
                collect($_item->values)->map(function ($value, $index) use (&$total) {
                    $value = str_replace('%', '', $value);
                    $total->values[$index] = round(!isset($total->values[$index]) ? $value : $value + $total->values[$index], 2);
                    $total->$index = $total->values[$index];
                });
                $_item->values = collect($_item->values)->values();
                return $_item;
//            return $item->last();
            });

            $dataCount = $regions->count();
            collect($total->values)->map(function ($value, $index) use (&$total, $dataCount) {
                $total->values[$index] = ceil(round($total->values[$index], 2)); // round($total->values[$index] / $dataCount, 2);
                $total->$index = $total->values[$index] . '%';
            });

            $total->$intervCol = 'Total Général';
            $total->total = round(array_sum($total->values)); //round(array_sum($total->values) / count($total->values), 2) . '%';
            $total->values = collect($total->values)->values();
            $total->isTotal = true;
            $regions->push($total);

            $regions = $regions->values();
            $data = ['filter' => $filter, 'columns' => $regions_names, 'data' => $regions, 'rows' => $rowsKeys];
            return $data;
        }
    }

    #endregion
}
