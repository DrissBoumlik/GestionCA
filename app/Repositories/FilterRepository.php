<?php


namespace App\Repositories;


use App\Models\Filter;
use App\Models\Stats;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class FilterRepository
{
    public function GetDataRegionsByGrpCall(Request $request, $filter = null)
    {
        $resultatAppel = $request->get('resultatAppel');
        $groupement = $request->get('groupement');
        $dates = $request->get('dates');
        $agenceCode = $request->get('agence_code');
        $agentName = $request->get('agent_name');
        $radical_route = $filter ?? getRadicalRoute(Route::current());
        $key_groupement = $request->get('key_groupement');
//        $regions = \DB::table('stats')
//            ->select('Nom_Region', 'Groupement', 'Key_Groupement', 'Resultat_Appel', \DB::raw('count(Resultat_Appel) as total'))
//            ->where('Resultat_Appel', 'not like', '=%')
//            ->whereNotNull('Nom_Region');

        $regions = \DB::table('stats as st')
            ->select('Nom_Region', 'Groupement', 'Key_Groupement', 'Resultat_Appel', \DB::raw('count(Resultat_Appel) as total'))
            ->join(\DB::raw('(SELECT Id_Externe, MAX(Date_Heure_Note) AS MaxDateTime FROM stats
            where Resultat_Appel not like "=%"
            and Nom_Region is not null
            and key_Groupement like "' . $radical_route . '"
            GROUP BY Id_Externe) groupedst'),
                function ($join) {
                    $join->on('st.Id_Externe', '=', 'groupedst.Id_Externe');
                    $join->on('st.Date_Heure_Note', '=', 'groupedst.MaxDateTime');
                })
            ->where('Resultat_Appel', 'not like', '=%')
            ->where('key_Groupement', 'like', $radical_route)
            ->whereNotNull('Nom_Region');

        $columns = $regions->groupBy('Nom_Region', 'Groupement', 'Key_Groupement', 'Resultat_Appel')->get();
        $key_groupement = $key_groupement ? clean($key_groupement) : $filter;
        $regions = $regions->where('key_groupement', 'like', $key_groupement);
//        $columns = $regions->groupBy('Nom_Region', $callResult, 'Key_Groupement')->get();
        // BUILDING THE USER FILTER
        if ($agentName) {
            $regions = $regions->where('Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $regions = $regions->where('Nom_Region', 'like', "%$agenceCode");
        }

        $keys = ($regions->groupBy('Nom_Region', 'Groupement', 'Key_Groupement', 'Resultat_Appel')->get())->groupBy(['Nom_Region'])->keys();

        if ($resultatAppel) {
            $resultatAppel = array_values($resultatAppel);
            $regions = $regions->whereIn('Resultat_Appel', $resultatAppel);
        }
        if ($groupement) {
            $groupement = array_values($groupement);
            $regions = $regions->whereIn('Groupement', $groupement);
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

//        $columns = $regions->groupBy('Nom_Region', 'Groupement', 'Key_Groupement', 'Resultat_Appel')->get();

//        $regions = ($dates ? $regions->whereIn('Date_Note', $dates)->get() : $regions)->get();
        $regions = $regions->groupBy('Nom_Region', 'Groupement', 'Key_Groupement', 'Resultat_Appel')->get();
        if (!count($regions)) {
            $data = ['columns' => [], 'data' => []];
            return $data;
        } else {
            $totalCount = Stats::where('key_groupement', 'like', $key_groupement)->count();
            $temp = $regions->groupBy(['Nom_Region']);

            $temp = $temp->map(function ($calls, $index) {
                $totalZone = $calls->reduce(function ($carry, $call) {
                    return $carry + $call->total;
                }, 0);
                return $calls->map(function ($call) use ($index, $totalZone) {
                    $call->$index = round($call->total * 100 / $totalZone, 2);
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
            $columns = $columns->map(function ($region) use ($totalCount) {
                $Region = $region->Nom_Region;
                $region->$Region = round($region->total * 100 / $totalCount, 2);
                return $region;
            });

//            $keys = $columns->groupBy(['Nom_Region'])->keys();

//            $regions = $regions->groupBy(['Groupement']);
            $regions_names = [];
            $keys->map(function ($key, $index) use (&$regions_names) {
                $regions_names[$index + 1] = new \stdClass();
                $regions_names[$index + 1]->data = $key;
                $regions_names[$index + 1]->name = $key;
                $regions_names[$index + 1]->text = $key;
                $regions_names[$index + 1]->title = $key;
            });
            usort($regions_names, function ($item1, $item2) {
                return ($item1->data == $item2->data) ? 0 :
                    ($item1->data < $item2->data) ? -1 : 1;
            });
            $first = new \stdClass();
            $first->name = 'Resultat_Appel';
            $first->data = 'Resultat_Appel';
            $first->text = 'Résultats Appels Préalables';
            $first->title = 'Résultats Appels Préalables';
            $first->orderable = false;
            array_unshift($regions_names, $first);
//            $detailCol = new \stdClass();
//            $detailCol->name = 'Key_Groupement';
//            $detailCol->data = 'Key_Groupement';
//            array_unshift($regions_names, $detailCol);
//            $regions_names[] = new \stdClass();
//            $regions_names[count($regions_names) - 1]->data = 'total';
//            $regions_names[count($regions_names) - 1]->name = 'total';
//            dd($regions->groupBy('Nom_Region'));
            $regions = $regions->groupBy(['Resultat_Appel']);
            $regions = $regions->map(function ($region) use ($keys) {
                $row = new \stdClass();
                $row->values = [];

                $col_arr = $keys->all();
                $items = $region->map(function ($call, $index) use (&$row, &$col_arr) {
                    $row->Key_Groupement = $call->Key_Groupement;
                    $row->Groupement = $call->Resultat_Appel;
                    $row->Resultat_Appel = $call->Resultat_Appel;
                    $nom_region = $call->Nom_Region;

                    $col_arr = array_diff($col_arr, [$nom_region]);
                    $row->values[$nom_region] = $call->$nom_region;
                    $row->$nom_region = $call->$nom_region . '%';
//                    $row->total = round(array_sum($row->values) / count($row->values), 2) . '%';
//                    $row->_total = $call->total;
                    $row->column = 'Resultat_Appel';
                    return $row;
                });
                $_item = $items->last();

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
            return ['filter' => $filter, 'columns' => $regions_names, 'data' => $regions];
        }
    }

    public function GetDataRegionsCallState(Request $request, $column, $filter = null)
    {
        $gpmtAppelPre = $request->get('gpmtAppelPre');
        $dates = $request->get('dates');
        $agenceCode = $request->get('agence_code');
        $agentName = $request->get('agent_name');
        $route = getRoute(Route::current());
        $radical_route = $filter ?? getRadicalRoute(Route::current());
//        $regions = \DB::table('stats')
//            ->select($column, 'Gpmt_Appel_Pre', \DB::raw('count(*) as total'))
//            ->where('Groupement', 'not like', 'Non Renseigné')
//            ->whereNotNull('Nom_Region');
        if ($column == 'Date_Heure_Note_Semaine') {
            $regions = \DB::table('stats as st')
                ->select($column, 'Gpmt_Appel_Pre', 'Date_Heure_Note_Annee', \DB::raw('count(Nom_Region) as total'))
                ->join(\DB::raw('(SELECT Id_Externe, MAX(Date_Heure_Note) AS MaxDateTime FROM stats
            where Groupement not like "Non Renseigné"
            and Gpmt_Appel_Pre not like "Hors Périmètre"
            and Nom_Region is not null
            and Key_Groupement like "' . $radical_route . '"
            GROUP BY Id_Externe) groupedst'),
                    function ($join) {
                        $join->on('st.Id_Externe', '=', 'groupedst.Id_Externe');
                        $join->on('st.Date_Heure_Note', '=', 'groupedst.MaxDateTime');
                    })
                ->where('Groupement', 'not like', 'Non Renseigné')
                ->where('Gpmt_Appel_Pre', 'not like', 'Hors Périmètre')
                ->where('Key_Groupement', 'like', $radical_route)
                ->whereNotNull('Nom_Region');
        } else {
            $regions = \DB::table('stats as st')
                ->select($column, 'Gpmt_Appel_Pre', \DB::raw('count(Nom_Region) as total'))
                ->join(\DB::raw('(SELECT Id_Externe, MAX(Date_Heure_Note) AS MaxDateTime FROM stats
            where Groupement not like "Non Renseigné"
            and Gpmt_Appel_Pre not like "Hors Périmètre"
            and Nom_Region is not null
            and Key_Groupement like "' . $radical_route . '"
            GROUP BY Id_Externe) groupedst'),
                    function ($join) {
                        $join->on('st.Id_Externe', '=', 'groupedst.Id_Externe');
                        $join->on('st.Date_Heure_Note', '=', 'groupedst.MaxDateTime');
                    })
                ->where('Groupement', 'not like', 'Non Renseigné')
                ->where('Gpmt_Appel_Pre', 'not like', 'Hors Périmètre')
                ->where('Key_Groupement', 'like', $radical_route)
                ->whereNotNull('Nom_Region');
        }


        if ($agentName) {
            $regions = $regions->where('Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $regions = $regions->where('Nom_Region', 'like', "%$agenceCode");
        }

        if ($column == 'Date_Heure_Note_Semaine') {
            $keys = ($regions->groupBy($column, 'Gpmt_Appel_Pre', 'Date_Heure_Note_Annee')->get())
                ->map(function ($item) {
                    $item->key = $item->Date_Heure_Note_Semaine . '_' . $item->Date_Heure_Note_Annee;
                    return $item;
                })
                ->groupBy(['key'])->keys();
        } else {
            $keys = ($regions->groupBy($column, 'Gpmt_Appel_Pre')->get())->groupBy([$column])->keys();
        }

        if ($gpmtAppelPre) {
            $gpmtAppelPre = array_values($gpmtAppelPre);
            $regions = $regions->whereIn('Gpmt_Appel_Pre', $gpmtAppelPre);
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

        if ($column == 'Date_Heure_Note_Semaine') {
            $columns = $regions->groupBy($column, 'Gpmt_Appel_Pre', 'Date_Heure_Note_Annee')->get();
            $regions = $regions->groupBy($column, 'Gpmt_Appel_Pre', 'Date_Heure_Note_Annee')->get();
        } else {
            $columns = $regions->groupBy($column, 'Gpmt_Appel_Pre')->get();
            $regions = $regions->groupBy($column, 'Gpmt_Appel_Pre')->get();
        }

//        $regions = ($dates ? $regions->whereIn('Date_Note', $dates)->get() : $regions)->get();
        $regions = $columns = addRegionWithZero($request, $regions, $columns, $column);

        if (!count($regions)) {
            $data = ['columns' => [], 'data' => []];
            return $data;
        } else {
            $totalCount = Stats::count();

            // ====================
            $temp = $regions->groupBy([$column]);
//            dd($temp);
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
//            $regions = $regions->groupBy($column);
            // ======================


//            $regions = $regions->map(function ($region) use ($column) {
//                $Region = $region->$column;
//                $region->$Region = $region->total; //round($region->total * 100 / $totalCount, 2) . '%';
//                return $region;
//            });
//            $columns = $columns->map(function ($region) use ($column) {
//                $Region = $region->$column;
//                $region->$Region = $region->total; //round($region->total * 100 / $totalCount, 2) . '%';
//                return $region;
//            });
//            dd($regions);

//            $keys = $columns->groupBy([$column])->keys();
            $regions = $regions->groupBy(['Gpmt_Appel_Pre']);
//        $regions = $regions->groupBy(['Nom_Region']);

            $columns = [];
//            $columns[0] = new \stdClass();
//            $columns[0]->data = 'Gpmt_Appel_Pre';
//            $columns[0]->name = 'Gpmt_Appel_Pre';
            $keys->map(function ($key, $index) use (&$columns) {
                $columns[$index + 1] = new \stdClass();
                $columns[$index + 1]->data =
                $columns[$index + 1]->name =
                $columns[$index + 1]->text =
                $columns[$index + 1]->title = $key;
            });
//            $columns = $columns->all();
            usort($columns, function ($item1, $item2) {
                return ($item1->data == $item2->data) ? 0 :
                    ($item1->data < $item2->data) ? -1 : 1;
            });
            if ($column == 'Date_Heure_Note_Semaine') {
                usort($columns, function ($item1, $item2) {
                    $date1 = explode('_', $item1->data);
                    $date2 = explode('_', $item2->data);
                    $year1 = $date1[1];
                    $year2 = $date2[1];
                    if ($year1 != $year2) {
                        return ($year1 == $year2) ? 0 :
                            ($year1 < $year2) ? -1 : 1;
                    } else {
                        $week1 = $date1[0];
                        $week2 = $date2[0];
                        return ($week1 == $week2) ? 0 :
                            ($week1 < $week2) ? -1 : 1;
                    }
                });
            }
            $first = new \stdClass();
            $first->title = 'Résultats Appels Préalables';
            $first->text = 'Résultats Appels Préalables';
            $first->name = $first->data = 'Gpmt_Appel_Pre';
            $first->orderable = false;
            $last = new \stdClass();
            $last->data =
            $last->name =
            $last->text =
            $last->title = 'total';
            array_unshift($columns, $first);
            array_push($columns, $last);

//            $columns[] = new \stdClass();
//            $columns[count($columns) - 1]->data = 'total';
//            $columns[count($columns) - 1]->name = 'total';


            $total = new \stdClass();
            $total->values = [];
            $regions = $regions->map(function ($region, $index) use (&$columns, $keys, $column, &$total) {
//            dd($index, $region);
                $row = new \stdClass();
                $row->values = [];

                $col_arr = $keys->all();
                $item = $region->map(function ($call, $index) use (&$row, &$col_arr, $column) {
                    if ($column == 'Date_Heure_Note_Semaine') {
                        $column_week = $call->Date_Heure_Note_Semaine;
                        $column_name = $call->Date_Heure_Note_Semaine . '_' . $call->Date_Heure_Note_Annee;
                        $row->$column_name = $call->total . ' / ' . $call->$column_week . ' %';
                        $col_arr = array_diff($col_arr, [$column_name]);
                        $row->values[$column_name] = $call->total;
                    } else {
                        $column_name = $call->$column;
                        $col_arr = array_diff($col_arr, [$column_name]);
                        $row->values[$column_name] = $call->total;
                        $row->$column_name = $call->total . ' / ' . $call->$column_name . ' %';
                    }
                    $row->Gpmt_Appel_Pre = $call->Gpmt_Appel_Pre;
                    $row->column = $call->Gpmt_Appel_Pre;
                    $row->total = isset($row->total) ? $row->total + $call->total : $call->total;
//                $row->_total = $call->total;
//                $row->total = $total; //round(array_sum($row->regions) / count($row->regions), 2) . '%';
                    return $row;
                });
                $_item = $item->last();
                $index = count($_item->values);
                foreach ($col_arr as $col) {
                    $_item->values[$col] = 0; //'0%';
                    $_item->$col = 0; //'0%';
                }
                ksort($_item->values);
                if ($column == 'Date_Heure_Note_Semaine') {
                    $_item->values = sortWeeksDates($_item->values, true);
                }
//                dd($_item);
                collect($_item->values)->map(function ($value, $index) use (&$total, $_item) {
//                    dd($value, $index);
                    $total->values[$index] = round(!isset($total->values[$index]) ? $value : $value + $total->values[$index], 2);
                    $total->$index = $total->values[$index];
                });
//                dd($_item->values);
                if ($column == 'Date_Heure_Note_Semaine') {
                    sortWeeksDates($_item->values, true);
                }
                $_item->values = collect($_item->values)->values();
                return $_item;
            });
            $total->Gpmt_Appel_Pre = 'Total Général';
            $total->total = round(array_sum($total->values), 2);
            if ($column == 'Date_Heure_Note_Semaine') {
                sortWeeksDates($total->values, true);
            }
            $total->values = collect($total->values)->values();
            $regions->push($total);

            $regions = $regions->values();

            return ['filter' => $filter, 'columns' => $columns, 'data' => $regions];
        }
    }

    public function getDataClientsByCallState(Request $request, $callResult, $filter = null)
    {
        $route = getRoute(Route::current());
        $dates = $request->get('dates');
        $agentName = $request->get('agent_name');
        $agenceCode = $request->get('agence_code');
        $codeRdvInterventionConfirm = $request->get('codeRdvInterventionConfirm');
        $codeRdvIntervention = $request->get('codeRdvIntervention');
        $radical_route = $filter ?? getRadicalRoute(Route::current());

//        $codes = \DB::table('stats')
//            ->select('Code_Intervention', 'Nom_Region', \DB::raw('count(Nom_Region) as total'))
//            ->whereNotNull('Nom_Region');

        $codes = \DB::table('stats as st')
            ->select('Code_Intervention', 'Nom_Region', \DB::raw('count(Nom_Region) as total'))
            ->join(\DB::raw('(SELECT Id_Externe, MAX(Date_Heure_Note) AS MaxDateTime FROM stats
             where Nom_Region is not null
             and Key_Groupement like "' . $radical_route . '"
             GROUP BY Id_Externe) groupedst'),
                function ($join) {
                    $join->on('st.Id_Externe', '=', 'groupedst.Id_Externe');
                    $join->on('st.Date_Heure_Note', '=', 'groupedst.MaxDateTime');
                })
            ->where('Key_Groupement', 'like', $radical_route)
            ->whereNotNull('Nom_Region');

//        $keys = $columns->groupBy(['Code_Intervention'])->keys();
        if ($agentName) {
            $codes = $codes->where('Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $codes = $codes->where('Nom_Region', 'like', "%$agenceCode");
        }
        $keys = ($codes->groupBy('Code_Intervention', 'Nom_Region')->get())->groupBy(['Code_Intervention'])->keys();
        if ($dates) {
            $dates = array_values($dates);
            $codes = $codes->whereIn('Date_Note', $dates);
        }
        if ($codeRdvIntervention) {
            $codeRdvIntervention = array_values($codeRdvIntervention);
            $codes = $codes->whereIn('Nom_Region', $codeRdvIntervention);
        }
        if ($codeRdvInterventionConfirm) {
            $codeRdvInterventionConfirm = array_values($codeRdvInterventionConfirm);
            $codes = $codes->whereIn('Nom_Region', $codeRdvInterventionConfirm);
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
                    $codes = $codes->whereIn('Date_Note', $dates);
                } else {
                    $filter = Filter::where(['route' => $route, 'user_id' => $user->id])->first();
                    if ($filter) {
                        $filter->forceDelete();
                    }
                }
            } else {
                $filter = Filter::where(['route' => $route, 'user_id' => $user->id])->first();
                if ($filter) {
                    $codes = $codes->whereIn('Date_Note', $filter->date_filter);
                }
            }
        } else {
            $filter = Filter::where(['route' => $route, 'user_id' => $user->id])->first();
            if ($filter) {
                $codes = $codes->whereIn('Date_Note', $filter->date_filter);
            }
        }

        $codes = $codes->where('Gpmt_Appel_Pre', $callResult);
        $columns = $codes->groupBy('Code_Intervention', 'Nom_Region')->get();

        $codes = $codes->groupBy('Code_Intervention', 'Nom_Region')->get();
        $codes = $columns = addRegionWithZero($request, $codes, $columns, null, 'Gpmt_Appel_Pre', $callResult);
        if (!count($codes)) {
            $data = ['columns' => [], 'data' => []];
            return $data;
        } else {

            $temp = $codes->groupBy(['Nom_Region']);

//            dd($temp);
            $temp = $temp->map(function ($calls, $index) {
                $totalZone = $calls->reduce(function ($carry, $call) {
                    return $carry + $call->total;
                }, 0);
                return $calls->map(function ($call, $index2) use ($index, $totalZone) {
                    $code_intervention = $call->Code_Intervention;
                    $call->$code_intervention = $totalZone == 0 ? 0.00 : round($call->total * 100 / $totalZone, 2);
                    return $call;
                });
            });
//            dd($temp);
            $codes = $temp->flatten();
//            dd(count($codes));
//            $totalCount = Stats::count();
//            $codes = $codes->map(function ($code) use ($totalCount) {
//                $Code = $code->Code_Intervention;
//                $code->$Code = round($code->total * 100 / $totalCount, 2);
//                return $code;
//            });
//            dd($codes);
//            $columns = $columns->map(function ($code) use ($totalCount) {
////            if ($code->Code_Intervention) {
//                $Code = $code->Code_Intervention;
//                $code->$Code = round($code->total * 100 / $totalCount, 2);
//                return $code;
////            }
////            return;
//            });
//            $keys = $columns->groupBy(['Code_Intervention'])->keys(); ==== DRISS
            $codes = $codes->groupBy(['Nom_Region']);
            $codes_names = [];
//        $codes_names[0] = new \stdClass();
//        $codes_names[0]->data = 'Nom_Region';
//        $codes_names[0]->name = 'Nom_Region';
            $keys->map(function ($key, $index) use (&$codes_names) {
                $codes_names[$index + 1] = new \stdClass();
                $codes_names[$index + 1]->title =
                $codes_names[$index + 1]->text =
                $codes_names[$index + 1]->data =
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
            $first->title = 'Résultats Appels Préalables';
            $first->text = 'Résultats Appels Préalables';
            $first->name = $first->data = 'Nom_Region';
            $first->orderable = false;
            $last = new \stdClass();
            $last->title =
            $last->text =
            $last->data =
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
//                    dd($call);
//                    dd($code_intervention, $call->Code_Intervention);
                    $row->values[$code_intervention ?? ''] = $call->$code_intervention;
                    $row->$code_intervention = $call->$code_intervention . '%';
//                $row->$code_intervention = $call->$code_intervention;
                    $row->total = ceil(round(array_sum($row->values), 2)) . '%'; // round(array_sum($row->values) / count($row->values), 2) . '%';
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
                    $_item->values[$col] = 0;
                    $_item->$col = '0%';
                }
//            dump($_item->values);
                ksort($_item->values);

                collect($_item->values)->map(function ($value, $index) use (&$total) {
                    $value = str_replace('%', '', $value);
                    $total->values[$index] = (round(!isset($total->values[$index]) ? $value : $value + $total->values[$index], 2));
                    $total->$index = $total->values[$index];
                });
                $_item->values = collect($_item->values)->values();
                return $_item;
            });

            $dataCount = $codes->count();
            collect($total->values)->map(function ($value, $index) use (&$total, $dataCount) {
                $total->values[$index] = ceil(round($total->values[$index], 2)); // round($total->values[$index] / $dataCount, 2);
                $total->$index = $total->values[$index] . '%';
            });

            $total->Nom_Region = 'Total Général';
            $total->total = ceil(round(array_sum($total->values), 2)); //round(array_sum($total->values) / count($total->values), 2) . '%';

//            $codes->push($total);
            $codes = $codes->values();
            $data = ['filter' => $filter, 'columns' => $codes_names, 'data' => $codes];
            return $data;
        }
    }

    public function GetDataFolders(Request $request, $callResult, $filter = null)
    {
        $resultatAppel = $request->get('resultatAppel');
        $groupement = $request->get('groupement');
        $dates = $request->get('dates');
        $agenceCode = $request->get('agence_code');
        $agentName = $request->get('agent_name');
        $route = getRoute(Route::current());
        $radical_route = $filter ?? getRadicalRoute(Route::current());
//        $regions = \DB::table('stats')
//            ->select('Nom_Region', $callResult, \DB::raw('count(Nom_Region) as total'))
//            ->where($callResult, 'not like', '=%')
//            ->where('Groupement', 'not like', 'Non Renseigné')
//            ->where('Groupement', 'not like', 'Appels post')
//            ->whereNotNull('Nom_Region');

        $regions = \DB::table('stats as st')
            ->select('Nom_Region', $callResult, \DB::raw('count(Nom_Region) as total'))
            ->join(\DB::raw('(SELECT Id_Externe, MAX(Date_Heure_Note) AS MaxDateTime FROM stats

             where Groupement not like "=%"
             and  Groupement not like "Non Renseigné"
             and  Groupement not like "Appels post"
             and key_Groupement like "' . $radical_route . '"
             and  Nom_Region is not null

            GROUP BY Id_Externe) groupedst'),
                function ($join) {
                    $join->on('st.Id_Externe', '=', 'groupedst.Id_Externe');
                    $join->on('st.Date_Heure_Note', '=', 'groupedst.MaxDateTime');
                })
            ->where($callResult, 'not like', '=%')
            ->where('Groupement', 'not like', 'Non Renseigné')
            ->where('Groupement', 'not like', 'Appels post')
            ->where('key_Groupement', 'like', $radical_route)
            ->whereNotNull('Nom_Region');

        if ($agentName) {
            $regions = $regions->where('Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $regions = $regions->where('Nom_Region', 'like', "%$agenceCode");
        }
        $keys = ($regions->groupBy('Nom_Region', $callResult)->get())->groupBy(['Nom_Region'])->keys();
        if ($dates) {
            $dates = array_values($dates);
            $regions = $regions->whereIn('Date_Note', $dates);
        }
        $columns = $regions->groupBy('Nom_Region', $callResult)->get();
        if ($resultatAppel) {
            $resultatAppel = array_values($resultatAppel);
            $regions = $regions->whereIn('Resultat_Appel', $resultatAppel);
        }


        if ($dates) {
            $dates = array_values($dates);
            $regions = $regions->whereIn('Date_Note', $dates);
        }
        if ($groupement) {
            $groupement = array_values($groupement);
            $regions = $regions->whereIn('Groupement', $groupement);
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


//        $regions = ($dates ? $regions->whereIn('Date_Note', $dates)->get() : $regions)->get();
        $columns = $regions->groupBy('Nom_Region', $callResult)->get();

        $regions = $regions->groupBy('Nom_Region', $callResult)->get();
        $regions = $columns = addRegionWithZero($request, $regions, $columns);
        if (!count($regions)) {
            $data = ['columns' => [], 'data' => []];
            return $data;
        } else {
            $totalCount = Stats::count();
            $regions = $regions->map(function ($region) use ($totalCount) {
                $Region = $region->Nom_Region;
                $region->$Region = $region->total; //round($region->total * 100 / $totalCount, 2);
                return $region;
            });
            $columns = $columns->map(function ($region) use ($totalCount) {
                $Region = $region->Nom_Region;
                $region->$Region = $region->total; //round($region->total * 100 / $totalCount, 2);
                return $region;
            });

//            $keys = $columns->groupBy(['Nom_Region'])->keys();

            $regions = $regions->groupBy([$callResult]);
//        $regions = $regions->groupBy(['Nom_Region']);

            $regions_names = [];
//            $regions_names[0] = new \stdClass();
//            $regions_names[0]->data = $callResult;
//            $regions_names[0]->name = $callResult;
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
            $first->name = $callResult;
            $first->data = $callResult;
            $first->text = 'Type Traitement';
            $first->title = 'Type Traitement';
            $first->orderable = false;
            array_unshift($regions_names, $first);
            $last = new \stdClass();
            $last->data =
            $last->name =
            $last->text =
            $last->title = 'total';
            array_push($regions_names, $last);

//            $regions_names[] = new \stdClass();
//            $regions_names[count($regions_names) - 1]->data = 'total';
//            $regions_names[count($regions_names) - 1]->name = 'total';

            $total = new \stdClass();
            $total->values = [];
            $regions = $regions->map(function ($region) use (&$regions_names, $keys, $callResult, &$total) {
                $row = new \stdClass();
                $row->values = [];

                $col_arr = $keys->all();

                $item = $region->map(function ($call, $index) use (&$row, &$regions_names, &$col_arr, $callResult) {

                    $row->$callResult = $call->$callResult;
                    $nom_region = $call->Nom_Region;

                    $col_arr = array_diff($col_arr, [$nom_region]);

                    $row->values[$nom_region] = $call->$nom_region;
                    $row->$nom_region = $call->$nom_region;
                    $row->total = array_sum($row->values); //$call->$nom_region; //round(array_sum($row->values) / count($row->values), 2) . '%';
                    $row->_total = $call->total;
                    $row->column = $callResult;
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

                ksort($_item->values);

                $_item->values = collect($_item->values)->values();

                return $_item;
            });

            $total->$callResult = 'Total Général';
            $total->total = round(array_sum($total->values), 2);
            $total->values = collect($total->values)->values();

            $regions->push($total);

            $regions = $regions->values();

            return ['filter' => $filter, 'columns' => $regions_names, 'data' => $regions];
        }
    }

    public function getDataNonValidatedFolders(Request $request, $intervCol, $filter = null)
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
            ->select('Nom_Region', $intervCol, \DB::raw('count(Nom_Region) as total'))
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
                    $row->$nom_region = $call->$nom_region . '%';
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
            $regions->push($total);

            $regions = $regions->values();
            $data = ['filter' => $filter, 'columns' => $regions_names, 'data' => $regions];
            return $data;
        }
    }

    public function getCloturetechCall(Request $request, $filter = null)
    {
        $dates = $request->get('dates');
        $agentName = $request->get('agent_name');
        $codeTypeIntervention = $request->get('codeTypeIntervention');
        $codeIntervention = $request->get('codeIntervention');
        $agenceCode = $request->get('agence_code');
        $radical_route = $filter ?? getRadicalRoute(Route::current());

        $regions = \DB::table('stats as st')
            ->select('Nom_Region', \DB::raw('count(1) as count'), \DB::raw('CASE
                    WHEN TIMESTAMPDIFF(MINUTE,EXPORT_ALL_Date_VALIDATION,EXPORT_ALL_Date_SOLDE) > 1440 THEN "superieur d\'un jour"
                    WHEN TIMESTAMPDIFF(MINUTE,EXPORT_ALL_Date_VALIDATION,EXPORT_ALL_Date_SOLDE) between 60 and 360   then "ENTTRE 6h and 1J "
                    WHEN TIMESTAMPDIFF(MINUTE,EXPORT_ALL_Date_VALIDATION,EXPORT_ALL_Date_SOLDE) BETWEEN 30 and 60 then "ENTTRE 30 min and 1H "
                    ELSE "ENTRE 0 and 30 min "
                    END as Title')
            )->whereNotNull('EXPORT_ALL_Date_VALIDATION')
            ->whereNotNull('EXPORT_ALL_Date_SOLDE');
        $keys = ($regions->groupBy('Nom_Region', 'Title')->get())->groupBy(['Nom_Region'])->keys();
        $regions = $regions->groupBy(['Title']);


        $regions_names = [];

        $keys->map(function ($key, $index) use (&$regions_names) {
            $regions_names[$index + 1] = new \stdClass();
            $regions_names[$index + 1]->data = $key;
            $regions_names[$index + 1]->name = $key;
            $regions_names[$index + 1]->text = $key;
            $regions_names[$index + 1]->title = $key;
        });
        usort($regions_names, function ($item1, $item2) {
            return ($item1->data == $item2->data) ? 0 :
                ($item1->data < $item2->data) ? -1 : 1;
        });
        $first = new \stdClass();
        $first->title = 'Nom_region';
        $first->name = 'Nom_region';
        $first->data = 'Nom_region';
        $first->orderable = false;
        array_unshift($regions_names, $first);

        $regions = $regions->groupBy('Nom_Region', 'title')->get()->groupBy('title');

        $regions = $regions->map(function ($region) use ($keys) {
            $row = new \stdClass();
            $row->values = [];

            $col_arr = $keys->all();
            $items = $region->map(function ($call, $index) use (&$row, &$col_arr) {
                $row->Nom_region = $call->Title;
                $region_name = $call->Nom_Region;
                $row->$region_name = $call->count;
                $row->values[$region_name] = $call->count;
                $col_arr = array_diff($col_arr, [$region_name]);
                return $row;
            });

            $_item = $items->last();

            $index = count($_item->values);
            foreach ($col_arr as $col) {
                $_item->values[$col] = 0;
                $_item->$col = '0';
            }

            ksort($_item->values);

            $_item->values = collect($_item->values)->values();

            return $_item;
        });
        $regions = $regions->values();

        return ['filter' => $filter, 'columns' => $regions_names, 'data' => $regions];
    }


}
