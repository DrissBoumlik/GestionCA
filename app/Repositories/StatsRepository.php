<?php

namespace App\Repositories;


use App\Exports\StatsExport;
use App\Imports\StatsImport;
use App\Models\Filter;
use App\Models\Stats;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;

class StatsRepository
{
    public function getStats(Request $request)
    {
        $row = $request->row;
        $rowValue = $request->rowValue;
        $col = $request->col;
        $colValue = $request->colValue;

        $agentName = $request->agent;
        $agenceCode = $request->agence;
        $queryJoin = $request->queryJoin;
        $IsWhereIn = $request->IsWhereIn;
        $dates = $request->dates;
        $resultat_appel = $request->Resultat_Appel;
        $element = $request->element;
        $query = explode(',', $request->queryValues);
        $sub_query = '(SELECT Id_Externe, MAX(Date_Heure_Note) AS MaxDateTime FROM stats 
        where Nom_Region is not null ' .
            ($agentName ? 'and Utilisateur like "' . $agentName . '" ' : ' ') .
            ($agenceCode ? 'and Nom_Region like "%' . $agenceCode . '" ' : ' ') .
            ($row && $rowValue ? ' and ' . $row . ' like "' . $rowValue . '"' : ' ') .
            ($col && $colValue ? ' and ' . $col . ' like "' . $colValue . '"' : ' ') .
            ($dates ? ' and Date_Note in ("' . str_replace(',', '","', $dates) . '")' : ' ') .

            ($queryJoin ? $queryJoin : '') . ' GROUP BY Id_Externe) groupedst';

        $allStats = DB::table('stats as st')
            ->select(['*'])
            ->join(\DB::raw($sub_query),
                function ($join) {
                    $join->on('st.Id_Externe', '=', 'groupedst.Id_Externe');
                    $join->on('st.Date_Heure_Note', '=', 'groupedst.MaxDateTime');
                })
            ->whereNotNull('Nom_Region');

        if ($row && $rowValue) {
            $allStats = $allStats->where($row, $rowValue);
        }
        if ($col && $colValue) {
            $allStats = $allStats->where($col, $colValue);
        }
        if ($dates) {
            $dates = explode(',', $request->dates);
            $allStats = $allStats->whereIn('Date_Note', $dates);
        }

        if ($agentName) {
            $allStats = $allStats->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $allStats = $allStats->where('st.Nom_Region', 'like', "%$agenceCode");
        }

        if ($resultat_appel) {
            $allStats = $allStats->where('Resultat_Appel', $resultat_appel);
        }

        if ($IsWhereIn) {
            $allStats = $allStats->whereIn($element, $query);
        } else {
            if ($element && $query) {
                foreach ($query as $q) {
                    $allStats = $allStats->where($element, explode('_', $q)[0], explode('_', $q)[1]);
                }
            }
        }
        return $allStats->get();
    }

    public function getAgencies(Request $request)
    {
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

    public function getAgenciesAll()
    {
        $stats = Stats::select(['Nom_Region'])->distinct('Nom_Region')
            ->whereNotNull('Nom_Region')
            ->orderBy('Nom_Region')->get()->map(function ($s) {
                $sn = explode(' - ', $s->Nom_Region);
                return [
                    'name' => trim($s->Nom_Region),
                    'code' => trim($s->Nom_Region)
                ];
            });
        return $stats->toArray();
    }

    public function getAgents(Request $request)
    {
        $stats = Stats::select(['Utilisateur']);
        if ($request->has('name')) {
            $term = trim(strtolower($request->get('name')));
            $stats = $stats->whereRaw('LOWER(Utilisateur) LIKE ?', ["%$term%"]);
        }
        $stats = $stats->distinct('Utilisateur')->get()->map(function ($s) {
            return [
                'name' => trim($s->Utilisateur),
                'code' => trim($s->Utilisateur)
            ];
        });
        return $stats;
    }

    public function getAgentsAll()
    {
        $stats = Stats::select(['Utilisateur'])->distinct('Utilisateur')->get()->map(function ($s) {
            return [
                'name' => trim($s->Utilisateur),
                'code' => trim($s->Utilisateur)
            ];
        });
        return $stats->toArray();
    }

    public function filterList($column, $request)
    {
        $agenceCode = $request->get('agence_code');
        $agentName = $request->get('agent_name');
        $groupement = $request->get('groupement');
        $key_groupement = $request->get('key_groupement');
        $stats = Stats::select([$column])
            ->distinct($column)
            ->where($column, 'not like', '=%')
            ->whereNotNull($column)
            ->where('Groupement', 'not like', 'Non Renseigné');
//        if ($column === 'Groupement') {
//            $stats = $stats->where('Groupement', 'not like', 'Non Renseigné');
//        }
        if ($agentName) {
            $stats = $stats->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $stats = $stats->where('st.Nom_Region', 'like', "%$agenceCode");
        }
        if ($key_groupement) {
            $stats = $stats->where('st.key_groupement', 'like', $key_groupement);
        }
        $stats = $stats->orderBy($column)->get();
        return $stats->map(function ($s) use ($column) {
            return $s[$column];
        });
    }

    public function getDateNotes(Request $request)
    {
        $agenceCode = $request->get('agence_code');
        $agentName = $request->get('agent_name');

        $dates = Stats::select('Date_Heure_Note_Annee', 'Date_Heure_Note_Mois', 'Date_Heure_Note_Semaine', 'Date_Note')
            ->orderBy('Date_Heure_Note_Annee')
            ->orderBy('Date_Heure_Note_Mois')
            ->orderBy('Date_Heure_Note_Semaine')
            ->orderBy('Date_Note');
//        if ($agentName) {
//            $dates = $dates->where('Utilisateur', $agentName);
//        }
//        if ($agenceCode) {
//            $dates = $dates->where('Nom_Region', 'like', "%$agenceCode");
//        }
        $dates = $dates->get()->groupBy(['Date_Heure_Note_Annee', 'Date_Heure_Note_Mois', 'Date_Heure_Note_Semaine', 'Date_Note']);

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
                    $_week->id = $_month->text . '-' . $index; // week name
                    $_week->text = $_month->text . '-' . $index; // week name
                    $_week->children = []; // days
                    $_month->children[] = $_week;
                    $week->map(function ($day, $index) use (&$_week) {
                        $_day = new \stdClass();
                        $_day->id = $index; //collect($index)->implode('-'); // day name
                        $_day->text = $index; //collect($index)->implode('-'); // day name
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

    public function GetDataRegions(Request $request, $callResult)
    {
//        $groupement = $request->get('rowFilter');
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

        list($filter, $queryFilters) = makeFilterSubQuery($request, $route, 'Groupement');

        $regions = \DB::table('stats as st')
            ->select('Nom_Region', $callResult, 'Key_Groupement', \DB::raw('count(distinct st.Id_Externe) as total'))
            ->join(\DB::raw('(SELECT Id_Externe, MAX(Date_Heure_Note) AS MaxDateTime FROM stats
            where Nom_Region is not null ' .
                ($agentName ? 'and Utilisateur like "' . $agentName . '"' : '') .
                ($agenceCode ? 'and Nom_Region like "%' . $agenceCode . '"' : '') .
                ' and ' . $queryFilters .
                ' and Resultat_Appel not like "=%"
            and Groupement not like "Non Renseigné"
            and Groupement not like "Appels post"
            GROUP BY Id_Externe, Nom_Region, ' . $callResult . ', Key_Groupement) groupedst'),
                function ($join) {
                    $join->on('st.Id_Externe', '=', 'groupedst.Id_Externe');
                    $join->on('st.Date_Heure_Note', '=', 'groupedst.MaxDateTime');
                })
            ->whereNotNull('Nom_Region')
            ->where('Resultat_Appel', 'not like', '=%')
            ->where('Groupement', 'not like', 'Non Renseigné')
            ->where('Groupement', 'not like', 'Appels post');
        $regions = applyFilter($regions, $filter, 'Groupement');

        if ($agentName) {
            $regions = $regions->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $regions = $regions->where('st.Nom_Region', 'like', "%$agenceCode");
        }
        $rowsKeys = \DB::table('stats as st')
            ->select($callResult)
            ->whereNotNull('Nom_Region')
            ->where('Resultat_Appel', 'not like', '=%')
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

    public function GetDataRegionsByGrpCall(Request $request)
    {
//        $resultatAppel = $request->get('resultatAppel');
//        $groupement = $request->get('groupement');
        $agenceCode = $request->get('agence_code');
        $agentName = $request->get('agent_name');

        $key_groupement = $request->get('key_groupement');
//        $regions = \DB::table('stats')
//            ->select('Nom_Region', 'Groupement', 'Key_Groupement', 'Resultat_Appel', \DB::raw('count(Resultat_Appel) as total'))
//            ->where('Resultat_Appel', 'not like', '=%')
//            ->whereNotNull('Nom_Region');
        $_route = $request->get('route') ?? getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);
        list($filter, $queryFilters) = makeFilterSubQuery($request, $route, 'Resultat_Appel');

        $regions = \DB::table('stats as st')
            ->select('Nom_Region', 'Groupement', 'Key_Groupement', 'Resultat_Appel', \DB::raw('count(distinct st.Id_Externe) as total'))
            ->join(\DB::raw('(SELECT Id_Externe, MAX(Date_Heure_Note) AS MaxDateTime FROM stats
            where Resultat_Appel not like "=%"
            and Nom_Region is not null
            and Groupement not like "Non Renseigné"
            and Groupement not like "Appels post" ' .
                ' and ' . $queryFilters .
                ($agentName ? 'and Utilisateur like "' . $agentName . '"' : '') .
                ($agenceCode ? 'and Nom_Region like "%' . $agenceCode . '"' : '') .
                ' GROUP BY Id_Externe, Nom_Region, Groupement, Key_Groupement, Resultat_Appel) groupedst'),
                function ($join) {
                    $join->on('st.Id_Externe', '=', 'groupedst.Id_Externe');
                    $join->on('st.Date_Heure_Note', '=', 'groupedst.MaxDateTime');
                })
            ->where('Resultat_Appel', 'not like', '=%')
            ->whereNotNull('Nom_Region')
            ->where('Groupement', 'not like', 'Non Renseigné')
            ->where('Groupement', 'not like', 'Appels post');

        $regions = applyFilter($regions, $filter, 'Resultat_Appel');

        $columns = $regions->groupBy('Nom_Region', 'Groupement', 'Key_Groupement', 'Resultat_Appel')->get();
        $key_groupement = clean($key_groupement);
        $regions = $regions->where('key_groupement', 'like', $key_groupement);
//        $columns = $regions->groupBy('Nom_Region', $callResult, 'Key_Groupement')->get();
        // BUILDING THE USER FILTER
        if ($agentName) {
            $regions = $regions->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $regions = $regions->where('st.Nom_Region', 'like', "%$agenceCode");
        }

        $rowsKeys = \DB::table('stats as st')
            ->select('Resultat_Appel')
            ->where('Resultat_Appel', 'not like', '=%')
            ->whereNotNull('Nom_Region')
            ->where('Groupement', 'not like', 'Non Renseigné')
            ->where('Groupement', 'not like', 'Appels post')
            ->where('key_groupement', 'like', $key_groupement)
            ->groupBy('Resultat_Appel')->pluck('Resultat_Appel');
//        if ($resultatAppel) {
//            $resultatAppel = array_values($resultatAppel);
//            $regions = $regions->whereIn('Resultat_Appel', $resultatAppel);
//        }
//        if ($groupement) {
//            $groupement = array_values($groupement);
//            $regions = $regions->whereIn('Groupement', $groupement);
//        }


//        $columns = $regions->groupBy('Nom_Region', 'Groupement', 'Key_Groupement', 'Resultat_Appel')->get();

//        $regions = ($dates ? $regions->whereIn('Date_Note', $dates)->get() : $regions)->get();
        $regions = $regions->groupBy('Nom_Region', 'Groupement', 'Key_Groupement', 'Resultat_Appel')->get();
        $keys = $regions->groupBy(['Nom_Region'])->keys();

        if (!count($regions)) {
            $data = ['filter' => $filter, 'columns' => [], 'data' => [], 'rows' => $rowsKeys, 'rowsFilterHeader' => 'Résultat Appel'];
            return $data;
        } else {
            $totalCount = Stats::where('key_groupement', 'like', $key_groupement)->count();
            $temp = $regions->groupBy(['Nom_Region']);

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
                    $row->$nom_region = $call->total . ' / ' . $call->$nom_region . '%';
//                    $row->$nom_region = $call->$nom_region . '%';
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

            return ['filter' => $filter, 'columns' => $regions_names, 'rows' => $rowsKeys, 'rowsFilterHeader' => 'Résultat Appel', 'data' => $regions];
        }
    }

    public function GetDataFolders(Request $request, $callResult)
    {
        $resultatAppel = $request->get('rowFilter');
//        $resultatAppel = $request->get('resultatAppel');
//        $groupement = $request->get('groupement');
        $dates = $request->get('dates');
        $agenceCode = $request->get('agence_code');
        $agentName = $request->get('agent_name');
//        $regions = \DB::table('stats')
//            ->select('Nom_Region', $callResult, \DB::raw('count(distinct st.Id_Externe) as total'))
//            ->where($callResult, 'not like', '=%')
//            ->where('Groupement', 'not like', 'Non Renseigné')
//            ->where('Groupement', 'not like', 'Appels post')
//            ->whereNotNull('Nom_Region');

        $regions = \DB::table('stats as st')
            ->select('Nom_Region', $callResult, \DB::raw('count(distinct st.Id_Externe) as total'))
            ->join(\DB::raw('(SELECT Id_Externe, MAX(Date_Heure_Note) AS MaxDateTime FROM stats

             where Resultat_Appel not like "=%"
             and  Groupement not like "Non Renseigné"
             and  Groupement not like "Appels post"
             and  Nom_Region is not null ' .
                ($agentName ? 'and Utilisateur like "' . $agentName . '"' : '') .
                ($agenceCode ? 'and Nom_Region like "%' . $agenceCode . '"' : '') .
                ' GROUP BY Id_Externe) groupedst'),
                function ($join) {
                    $join->on('st.Id_Externe', '=', 'groupedst.Id_Externe');
                    $join->on('st.Date_Heure_Note', '=', 'groupedst.MaxDateTime');
                })
            ->where('Resultat_Appel', 'not like', '=%')
            ->where('Groupement', 'not like', 'Non Renseigné')
            ->where('Groupement', 'not like', 'Appels post')
            ->whereNotNull('Nom_Region');
        if ($agentName) {
            $regions = $regions->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $regions = $regions->where('st.Nom_Region', 'like', "%$agenceCode");
        }

//        dd($regions->groupBy('Nom_Region', $callResult)->toSql());
        $rowsKeys = ($regions->groupBy('Nom_Region', $callResult)->get())->groupBy([$callResult])->keys();
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
//        if ($groupement) {
//            $groupement = array_values($groupement);
//            $regions = $regions->whereIn('Groupement', $groupement);
//        }

        $user = getAuthUser();
        $_route = getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);
        $filter = Filter::where(['route' => $route, 'user_id' => $user->id])->first();

        list($filter, $regions) = applyFilter($request, $route, $regions, 'Resultat_Appel', $resultatAppel);


//        $regions = ($dates ? $regions->whereIn('Date_Note', $dates)->get() : $regions)->get();
        $columns = $regions->groupBy('Nom_Region', $callResult)->get();

        $regions = $regions->groupBy('Nom_Region', $callResult)->get();

        $keys = $regions->groupBy(['Nom_Region'])->keys();


        $regions = $columns = addRegionWithZero($request, $regions, $columns);
        if (!count($regions)) {
            $data = ['filter' => $filter, 'columns' => [], 'data' => [], 'rows' => $rowsKeys, 'rowsFilterHeader' => 'Groupement'];
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

            $total->isTotal = true;
            $regions->push($total);

            $regions = $regions->values();

            return ['filter' => $filter, 'columns' => $regions_names, 'rows' => $rowsKeys, 'rowsFilterHeader' => 'Groupement', 'data' => $regions];
        }
    }

    public function GetDataRegionsCallState(Request $request, $column)
    {
//        $gpmtAppelPre = $request->get('gpmtAppelPre');
//        $gpmtAppelPre = $request->get('rowFilter');
//        $dates = $request->get('dates');
        $agenceCode = $request->get('agence_code');
        $agentName = $request->get('agent_name');

//        $regions = \DB::table('stats')
//            ->select($column, 'Gpmt_Appel_Pre', \DB::raw('count(*) as total'))
//            ->where('Groupement', 'not like', 'Non Renseigné')
//            ->whereNotNull('Nom_Region');
        $_route = getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);

        list($filter, $queryFilters) = makeFilterSubQuery($request, $route, 'Gpmt_Appel_Pre');

        $regions = \DB::table('stats as st');
        if ($column == 'Date_Heure_Note_Semaine') {
            $regions = $regions->select($column, 'Gpmt_Appel_Pre', 'Date_Heure_Note_Annee', \DB::raw('count(distinct st.Id_Externe) as total'))
                ->join(\DB::raw('(SELECT Id_Externe, MAX(Date_Heure_Note) AS MaxDateTime FROM stats
            where Groupement not like "Non Renseigné"
            and Groupement like "Appels préalables"
            and Gpmt_Appel_Pre not like "Hors Périmètre"
            and Nom_Region is not null ' .
                    ($agentName ? 'and Utilisateur like "' . $agentName . '"' : '') .
                    ($agenceCode ? 'and Nom_Region like "%' . $agenceCode . '"' : '') .
                    ' and ' . $queryFilters .
                    ' GROUP BY Id_Externe, ' . $column . ', Gpmt_Appel_Pre, Date_Heure_Note_Annee) groupedst'),
                    function ($join) {
                        $join->on('st.Id_Externe', '=', 'groupedst.Id_Externe');
                        $join->on('st.Date_Heure_Note', '=', 'groupedst.MaxDateTime');
                    });
        } else {
            $regions = $regions->select($column, 'Gpmt_Appel_Pre', \DB::raw('count(distinct st.Id_Externe) as total'))
                ->join(\DB::raw('(SELECT Id_Externe, MAX(Date_Heure_Note) AS MaxDateTime FROM stats
            where Groupement not like "Non Renseigné"
            and Groupement like "Appels préalables"
            and Gpmt_Appel_Pre not like "Hors Périmètre"
            and Nom_Region is not null ' .
                    ($agentName ? 'and Utilisateur like "' . $agentName . '"' : '') .
                    ($agenceCode ? 'and Nom_Region like "%' . $agenceCode . '"' : '') .
                    ' and ' . $queryFilters .
                    ' GROUP BY Id_Externe, ' . $column . ', Gpmt_Appel_Pre) groupedst'),
                    function ($join) {
                        $join->on('st.Id_Externe', '=', 'groupedst.Id_Externe');
                        $join->on('st.Date_Heure_Note', '=', 'groupedst.MaxDateTime');
                    });
        }
        $regions = $regions->where('Groupement', 'not like', 'Non Renseigné')
            ->where('Groupement', 'like', 'Appels préalables')
            ->where('Gpmt_Appel_Pre', 'not like', 'Hors Périmètre')
            ->whereNotNull('Nom_Region');

        $regions = applyFilter($regions, $filter, 'Gpmt_Appel_Pre');

        if ($agentName) {
            $regions = $regions->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $regions = $regions->where('st.Nom_Region', 'like', "%$agenceCode");
        }

//        dd($regions->groupBy($column, 'Gpmt_Appel_Pre', 'Date_Heure_Note_Annee')->toSql());

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
        //$rowsKeys = ($regions->groupBy($column, 'Gpmt_Appel_Pre')->get())->groupBy(['Gpmt_Appel_Pre'])->keys();
        $rowsKeys = \DB::table('stats as st')
            ->select('Gpmt_Appel_Pre')
            ->where('Gpmt_Appel_Pre', 'not like', 'Non renseigné')
            ->where('Gpmt_Appel_Pre', 'not like', 'Hors Périmètre')
            ->whereNotNull('Nom_Region')
            ->groupBy('Gpmt_Appel_Pre')->pluck('Gpmt_Appel_Pre');
//        if ($gpmtAppelPre) {
//            $gpmtAppelPre = array_values($gpmtAppelPre);
//            $regions = $regions->whereIn('Gpmt_Appel_Pre', $gpmtAppelPre);
//        }
//        if ($dates) {
//            $dates = array_values($dates);
//            $regions = $regions->whereIn('Date_Note', $dates);
//        }


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
            $data = ['filter' => $filter, 'columns' => [], 'data' => [], 'rows' => $rowsKeys, 'rowsFilterHeader' => ($column == 'Date_Heure_Note_Semaine' ? 'Résultats Appels Préalables par semaine' : 'Résultats Appels Préalables par agence')];
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
            $total->isTotal = true;
            $regions->push($total);

            $regions = $regions->values();

            return ['filter' => $filter, 'columns' => $columns, 'rows' => $rowsKeys, 'rowsFilterHeader' => ($column == 'Date_Heure_Note_Semaine' ? 'Résultats Appels Préalables par semaine' : 'Résultats Appels Préalables par agence'), 'data' => $regions];
        }
    }

    public function getDataClientsByCallState(Request $request, $callResult)
    {
        $agentName = $request->get('agent_name');
        $agenceCode = $request->get('agence_code');
//        $codeRdvInterventionConfirm = $request->get('codeRdvInterventionConfirm');
//        $codeRdvIntervention = $request->get('codeRdvIntervention');
//        $rowFilter = $request->get('rowFilter');

//        $codes = \DB::table('stats')
//            ->select('Code_Intervention', 'Nom_Region', \DB::raw('count(distinct st.Id_Externe) as total'))
//            ->whereNotNull('Nom_Region');

        $_route = getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);
        list($filter, $queryFilters) = makeFilterSubQuery($request, $route, 'Nom_Region');

        $codes = \DB::table('stats as st')
            ->select('Code_Intervention', 'Nom_Region', \DB::raw('count(distinct st.Id_Externe) as total'))
            ->join(\DB::raw('(SELECT Id_Externe, MAX(Date_Heure_Note) AS MaxDateTime FROM stats
             where Nom_Region is not null ' .
                ' and ' . $queryFilters .
                ($agentName ? 'and Utilisateur like "' . $agentName . '"' : '') .
                ($agenceCode ? 'and Nom_Region like "%' . $agenceCode . '"' : '') .

                ($callResult == 'Joignable' ? ' and Resultat_Appel in ("Appels préalables - RDV confirmé",
                                                    "Appels préalables - RDV confirmé Client non informé",
                                                    "Appels préalables - RDV repris et confirmé")'
                    : 'and Resultat_Appel in ("Appels préalables - Annulation RDV client non informé",
                                                "Appels préalables - Client sauvé",
                                                "Appels préalables - Client Souhaite être rappelé plus tard",
                                                "Appels préalables - Injoignable / Absence de répondeur",
                                                "Appels préalables - Injoignable 2ème Tentative",
                                                "Appels préalables - Injoignable 3ème Tentative",
                                                "Appels préalables - Injoignable avec Répondeur",
                                                "Appels préalables - Numéro erroné",
                                                "Appels préalables - Numéro Inaccessible",
                                                "Appels préalables - Numéro non attribué",
                                                "Appels préalables - Numéro non Renseigné",
                                                "Appels préalables - RDV annulé le client ne souhaite plus d’intervention",
                                                "Appels préalables - RDV annulé Rétractation/Résiliation",
                                                "Appels préalables - RDV planifié mais non confirmé",
                                                "Appels préalables - RDV repris Mais non confirmé")') .

                ' GROUP BY Id_Externe, Code_Intervention, Nom_Region) groupedst'),
                function ($join) {
                    $join->on('st.Id_Externe', '=', 'groupedst.Id_Externe');
                    $join->on('st.Date_Heure_Note', '=', 'groupedst.MaxDateTime');
                })
            ->whereNotNull('Nom_Region');

        $codes = applyFilter($codes, $filter, 'Nom_Region');

        if ($callResult == 'Joignable') {
            $codes = $codes->whereIn('Resultat_Appel', [
                'Appels préalables - RDV confirmé',
                'Appels préalables - RDV confirmé Client non informé',
                'Appels préalables - RDV repris et confirmé'
            ]);
        } else {
            $codes = $codes->whereIn('Resultat_Appel', [
                'Appels préalables - Annulation RDV client non informé',
                'Appels préalables - Client sauvé',
                'Appels préalables - Client Souhaite être rappelé plus tard',
                'Appels préalables - Injoignable / Absence de répondeur',
                'Appels préalables - Injoignable 2ème Tentative',
                'Appels préalables - Injoignable 3ème Tentative',
                'Appels préalables - Injoignable avec Répondeur',
                'Appels préalables - Numéro erroné',
                'Appels préalables - Numéro Inaccessible',
                'Appels préalables - Numéro non attribué',
                'Appels préalables - Numéro non Renseigné',
                'Appels préalables - RDV annulé le client ne souhaite plus d’intervention',
                'Appels préalables - RDV annulé Rétractation/Résiliation',
                'Appels préalables - RDV planifié mais non confirmé',
                'Appels préalables - RDV repris Mais non confirmé',
            ]);
        }

//        $keys = $columns->groupBy(['Code_Intervention'])->keys();
        if ($agentName) {
            $codes = $codes->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $codes = $codes->where('st.Nom_Region', 'like', "%$agenceCode");
        }
        $rowsKeys = \DB::table('stats as st')
            ->select('Nom_Region')
            ->whereNotNull('Nom_Region')
            ->groupBy('Nom_Region')->pluck('Nom_Region');

//        if ($dates) {
//            $dates = array_values($dates);
//            $codes = $codes->whereIn('Date_Note', $dates);
//        }
//        if ($codeRdvIntervention) {
//            $codeRdvIntervention = array_values($codeRdvIntervention);
//            $codes = $codes->whereIn('Nom_Region', $codeRdvIntervention);
//        }
//        if ($codeRdvInterventionConfirm) {
//            $codeRdvInterventionConfirm = array_values($codeRdvInterventionConfirm);
//            $codes = $codes->whereIn('Nom_Region', $codeRdvInterventionConfirm);
//        }
//        if ($rowFilter) {
//            $rowFilter = array_values($rowFilter);
//            $codes = $codes->whereIn('Nom_Region', $rowFilter);
//        }


        $codes = $codes->where('Gpmt_Appel_Pre', $callResult);
        $columns = $codes->groupBy('Code_Intervention', 'Nom_Region')->get();

        $codes = $codes->groupBy('Code_Intervention', 'Nom_Region')->get();
        $keys = $codes->groupBy(['Code_Intervention'])->keys();
        $codes = $columns = addRegionWithZero($request, $codes, $columns, null, 'Gpmt_Appel_Pre', $callResult);
        if (!count($codes)) {
            $data = ['filter' => $filter, 'columns' => [], 'data' => [], 'rows' => $rowsKeys, 'rowsFilterHeader' => 'Region'];
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
                $codes_names[$index + 1]->text =
                $codes_names[$index + 1]->data =
                $codes_names[$index + 1]->name =
                $codes_names[$index + 1]->title = $key;
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
            $last->text =
            $last->data =
            $last->name =
            $last->title = 'total';
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
                    $row->$code_intervention = $call->total . ' / ' . $call->$code_intervention . '%';
//                $row->$code_intervention = $call->$code_intervention;
//                    $row->total = ceil(round(array_sum($row->values), 2)) . '%'; // round(array_sum($row->values) / count($row->values), 2) . '%';
                    $row->total = isset($row->total) ? $row->total + $call->total : $call->total;

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
            $data = ['filter' => $filter, 'columns' => $codes_names, 'rows' => $rowsKeys, 'rowsFilterHeader' => 'Region', 'data' => $codes];
            return $data;
        }
    }

    public function getDataNonValidatedFolders(Request $request, $intervCol)
    {
        $agentName = $request->get('agent_name');
//        $codeTypeIntervention = $request->get('codeTypeIntervention');
//        $codeIntervention = $request->get('codeIntervention');
//        $rowFilter = $request->get('rowFilter');
        $agenceCode = $request->get('agence_code');

//        $regions = \DB::table('stats')
//            ->select('Nom_Region', $intervCol, \DB::raw('count(*) as total'))
//            ->whereNotNull('Nom_Region');

        $_route = getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);
        list($filter, $queryFilters) = makeFilterSubQuery($request, $route, $intervCol);


        $regions = \DB::table('stats as st')
            ->select('Nom_Region', $intervCol, 'Resultat_Appel', \DB::raw('count(distinct st.Id_Externe) as total'))
            ->join(\DB::raw('(SELECT Id_Externe, MAX(Date_Heure_Note) AS MaxDateTime FROM stats
            where Nom_Region is not null
            and Groupement like "Appels clôture" ' .
                ' and ' . $queryFilters .
                ($agentName ? 'and Utilisateur like "' . $agentName . '"' : '') .
                ($agenceCode ? 'and Nom_Region like "%' . $agenceCode . '"' : '') .
                ' GROUP BY Id_Externe, Nom_Region, ' . $intervCol . ', Resultat_Appel) groupedst'),
                function ($join) {
                    $join->on('st.Id_Externe', '=', 'groupedst.Id_Externe');
                    $join->on('st.Date_Heure_Note', '=', 'groupedst.MaxDateTime');
                })
            ->whereNotNull('Nom_Region')
            ->where('Groupement', 'Appels clôture');
        $regions = applyFilter($regions, $filter, $intervCol);
        if ($agentName) {
            $regions = $regions->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $regions = $regions->where('st.Nom_Region', 'like', "%$agenceCode");
        }

        $rowsKeys = \DB::table('stats as st')
            ->select($intervCol)
            ->whereNotNull('Nom_Region')
            ->where('Groupement', 'Appels clôture')
            ->groupBy($intervCol)->pluck($intervCol);


//        if ($codeTypeIntervention) {
//            $codeTypeIntervention = array_values($codeTypeIntervention);
//            $regions = $regions->whereIn('Code_Type_Intervention', $codeTypeIntervention);
//        }
//        if ($codeIntervention) {
//            $codeIntervention = array_values($codeIntervention);
//            $regions = $regions->whereIn('Code_Intervention', $codeIntervention);
//        }
//        if ($rowFilter) {
//            $rowFilter = array_values($rowFilter);
//            $regions = $regions->whereIn($intervCol, $rowFilter);
//        }
//        if ($dates) {
//            $dates = array_values($dates);
//            $regions = $regions->whereIn('Date_Note', $dates);
//        }


        $columns = $regions->groupBy('Nom_Region', $intervCol, 'Resultat_Appel')->get();
//        $regions = $regions->groupBy('Nom_Region', $intervCol)->get();

        $regions = $regions->groupBy('Nom_Region', $intervCol, 'Resultat_Appel')->get();

        $keys = $regions->groupBy(['Nom_Region'])->keys();

        $regions = $columns = addRegionWithZero($request, $regions, $columns);

        if (!count($regions)) {
            $data = ['filter' => $filter, 'columns' => [], 'data' => [], 'rows' => $rowsKeys, 'rowsFilterHeader' => ($intervCol == 'Code_Intervention' ? 'Code Intervention' : 'Type Intervention')];
            return $data;
        } else {
            $totalCount = Stats::count();
            $temp = ($regions->groupBy(['Nom_Region', $intervCol]))->map(function ($region, $index) {
                $totalZone = 0;
                $results = $region->map(function ($codeType, $index2) use (&$totalZone, $index) {
                    $totalCodeType = $codeType->reduce(function ($carry, $call) use (&$totalZone) {
                        $totalZone += $call->total;
                        return $carry + $call->total;
                    }, 0);
                    $percentResults = $codeType->filter(function ($call) use ($index2, $totalCodeType, $index, $totalZone) {
                        if ($call->Resultat_Appel == 'Appels clôture - CRI non conforme') {
                            $call->$index2 = $totalCodeType == 0 ? 0.00 : round($call->total * 100 / $totalCodeType, 2);
                            return $call;
                        }
//                        $call->$index2 = $totalCodeType == 0 ? 0.00 : round($call->total * 100 / $totalCodeType, 2);
//                        return $call;
                    });
                    return $percentResults;
                });
//                dd($index, $region->flatten());
                $percentRegions = $region->map(function ($codeType) use ($index, $totalZone) {
                    $mergedObject = null;
                    $percentRegion = $codeType->reduce(function ($carry, $call) use (&$mergedObject) {
                        if ($call->Resultat_Appel == 'Appels clôture - CRI non conforme') {
                            $call->itemTotal = $call->total;
                        }
                        $mergedObject = ($mergedObject == null) ? collect($call) : collect($mergedObject)->merge(collect($call));
                        return $carry + $call->total;
                    }, 0);
                    $item = new \stdClass();
                    $mergedObject->each(function ($value, $key) use (&$item) {
                        $item->$key = $value;
                    });
                    $item->Resultat_Appel = 'Appels clôture - CRI non conforme';
                    $item->total = $percentRegion;
                    $item->$index = $totalZone == 0 ? 0.00 : round($percentRegion * 100 / $totalZone, 2);
//                    dd($item);
                    return $item;
                });
                return $percentRegions;
            });
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
            $regions = $regions->map(function ($region, $index) use (&$regions_names, $keys, $intervCol, &$total) {
                $row = new \stdClass();
                $row->values = [];
                $col_arr = $keys->all();
                $item = $region->map(function ($call) use ($index, &$row, &$regions_names, &$col_arr, $intervCol) {
                    $row->$intervCol = $call->$intervCol;

                    $nom_region = $call->Nom_Region;

                    $col_arr = array_diff($col_arr, [$nom_region]);

                    $row->values[$nom_region] = $call->$nom_region;
                    $row->$nom_region = $call->total . ' / ' . (isset($call->itemTotal) ? $call->itemTotal : 0) . ' / ' . (isset($call->$index) ? $call->$index : 0) . '%';
//                    $row->$nom_region = $call->total . ' / ' . $call->$nom_region . '%';
                    $row->total = isset($row->total) ? $row->total + $call->total : $call->total;
//                    $row->total = round(array_sum($row->values), 2); //round(array_sum($row->values) / count($row->values), 2) . '%';
                    return $row;
                });
                $_item = $item->last();
                $index = count($_item->values);
                foreach ($col_arr as $col) {
                    $_item->values[$col] = 0; //'0%';
                    $_item->$col = '0';
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
            $data = ['filter' => $filter, 'columns' => $regions_names, 'rows' => $rowsKeys, 'rowsFilterHeader' => ($intervCol == 'Code_Intervention' ? 'Code Intervention' : 'Type Intervention'), 'data' => $regions];
            return $data;
        }
    }

    public function getDataClientsByPerimeter(Request $request)
    {

//        $dates = $request->get('dates');
//        $nomRegion = $request->get('nomRegion');
//        $rowFilter = $request->get('rowFilter');

        $agenceCode = $request->get('agence_code');
        $agentName = $request->get('agent_name');
//        $results = \DB::table('stats')
//            ->select('Groupement', 'Nom_Region', \DB::raw('count(*) as total'))
//            ->whereNotNull('Groupement')
//            ->where('Groupement', 'not like', 'Non Renseigné')
//            ->where('Groupement', 'not like', 'Appels post')
//            ->whereNotNull('Nom_Region');
        $_route = getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);
        list($filter, $queryFilters) = makeFilterSubQuery($request, $route, 'Nom_Region');


        $results = \DB::table('stats as st')
            ->select('st.Groupement', 'st.Nom_Region', \DB::raw('count(distinct st.Id_Externe) as total'))
            ->join(\DB::raw('(SELECT Id_Externe, MAX(Date_Heure_Note) AS MaxDateTime,
                groupement , Resultat_Appel , Nom_Region
            FROM stats
             where Groupement IS NOT NULL
             AND Nom_Region IS NOT NULL
             AND Groupement not LIKE "Non renseigné"
             AND Groupement not LIKE "Appels post"
             
             AND Type_Note LIKE "CAM"' .
                ' and ' . $queryFilters .
                ($agentName ? 'and Utilisateur like "' . $agentName . '"' : '') .
                ($agenceCode ? 'and Nom_Region like "%' . $agenceCode . '"' : '') .
                ' GROUP BY Id_Externe, Groupement, Resultat_Appel, Nom_Region ) groupedst'),
                function ($join) {
                    $join->on('st.Id_Externe', '=', 'groupedst.Id_Externe');
                    $join->on('st.Date_Heure_Note', '=', 'groupedst.MaxDateTime');
                    $join->on('st.Groupement', '=', 'groupedst.Groupement');
                    $join->on('st.Resultat_Appel', '=', 'groupedst.Resultat_Appel');
                    $join->on('st.Nom_Region', '=', 'groupedst.Nom_Region');
                })
            ->whereNotNull('st.Groupement')
            ->whereNotNull('st.Nom_Region')
            ->where('st.Groupement', 'not like', 'Non renseigné')
            ->where('st.Groupement', 'not like', 'Appels post')
            ->where('Type_Note', 'like', 'CAM');
        $results = applyFilter($results, $filter, 'Nom_Region');

        if ($agentName) {
            $results = $results->where('st.Utilisateur', $agentName);
        }
//        if ($nomRegion) {
//            $nomRegion = array_values($nomRegion);
//            $results = $results->whereIn('Nom_Region', $nomRegion);
//        }
//        if ($rowFilter) {
//            $rowFilter = array_values($rowFilter);
//            $results = $results->whereIn('Nom_Region', $rowFilter);
//        }
        if ($agenceCode) {
            $results = $results->where('st.Nom_Region', 'like', "%$agenceCode");
        }
//        if ($dates) {
//            $dates = array_values($dates);
//            $results = $results->whereIn('Date_Note', $dates);
//        }

        $rowsKeys = \DB::table('stats as st')
            ->select('st.Nom_Region')
            ->whereNotNull('st.Groupement')
            ->whereNotNull('st.Nom_Region')
            ->where('st.Groupement', 'not like', 'Non renseigné')
            ->where('st.Groupement', 'not like', 'Appels post')
            ->where('Type_Note', 'like', 'CAM')
            ->groupBy('Nom_Region')->pluck('Nom_Region');

//            ($results->groupBy('Groupement', 'Nom_Region')->get())->groupBy(['Nom_Region'])->keys();

        $columns = $results->groupBy('Groupement', 'Nom_Region')->get();


        $results = $results->groupBy('Groupement', 'Nom_Region')->get();

        $keys = $results->groupBy(['Groupement'])->keys();

        $results = $columns = addRegionWithZero($request, $results, $columns);

        if (!count($results)) {
            $data = ['filter' => $filter, 'columns' => [], 'data' => [], 'rows' => $rowsKeys, 'rowsFilterHeader' => 'Region'];
            return $data;
        } else {

            $totalCount = Stats::count();

            // ====================
//            dd($results);
            $temp = $results->groupBy(['Nom_Region']);
//            dd($temp);
//            dd($temp);
            $temp = $temp->map(function ($calls, $index) {
                $totalZone = $calls->reduce(function ($carry, $call) {
                    return $carry + $call->total;
                }, 0);
                return $calls->map(function ($call) use ($index, $totalZone) {
                    $Code = $call->Groupement;
                    $call->$index = $call->$Code = $totalZone == 0 ? 0.00 : round($call->total * 100 / $totalZone, 2);
                    return $call;
                });
            });
//            dd($temp);

            $results = $temp->flatten();
//            dd($results);
//            $regions = $regions->groupBy($column);
            // ======================

//            $results = $results->map(function ($resultItem) use ($totalCount) {
//                $Code = $resultItem->Groupement;
//                $resultItem->$Code = $resultItem->total; //round($resultItem->total * 100 / $totalCount, 2);;
//                return $resultItem;
//            });
//            $columns = $columns->filter(function ($code) use ($totalCount) {
////            if ($code->Code_Intervention) {
//                $Code = $code->Groupement;
//                $code->$Code = $code->total; //round($code->total * 100 / $totalCount, 2);;
//                return $code;
////            }
////            return;
//            });

//            dd($results);
//            $keys = $columns->groupBy(['Groupement'])->keys();
            $results = $results->groupBy(['Nom_Region']);
            $column_names = [];
//        $codes_names[0] = new \stdClass();
//        $codes_names[0]->data = 'Nom_Region';
//        $codes_names[0]->name = 'Nom_Region';
            $keys->map(function ($key, $index) use (&$column_names) {
                $column_names[$index + 1] = new \stdClass();
                $column_names[$index + 1]->text =
                $column_names[$index + 1]->data =
                $column_names[$index + 1]->name =
                $column_names[$index + 1]->title = $key;
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
            $first->title = 'Région';
            $first->text = 'Région';
            $first->name = $first->data = 'Nom_Region';
            $first->orderable = false;
            $last = new \stdClass();
            $last->data =
            $last->name =
            $last->text =
            $last->title = 'total';
            array_unshift($column_names, $first);
            array_push($column_names, $last);


            $total = new \stdClass();
            $total->values = [];
            $results = $results->map(function ($region) use (&$column_names, &$total, $keys) {
                $row = new \stdClass(); //[];
                $row->values = [];
                $col_arr = $keys->all();
                $item = $region->map(function ($call, $index) use (&$row, &$codes_names, &$total, &$col_arr) {
//                    dd($call, $index);
//                $codes_names[] = $call->Code_Intervention;
                    $row->Nom_Region = $call->Nom_Region;
                    $column_name = $call->Groupement;
                    $col_arr = array_diff($col_arr, [$column_name]);
//                dd($call->Code_Intervention);
                    $row->values[$column_name] = $call->total;
                    $row->$column_name = $call->total . ' / ' . $call->$column_name . '%';
//                $row->$code_intervention = $call->$code_intervention;
                    $row->total = isset($row->total) ? $row->total + $call->total : $call->total;
//                round(array_sum($row->values) / count($row->values), 2) . '%';
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
//            dd($_item->values);
                return $_item;
            });
            $total->Nom_Region = 'Total Général';
            $total->total = round(array_sum($total->values), 2);
            $total->values = collect($total->values)->values();
            $total->isTotal = true;
            $results->push($total);
            $results = $results->values();

            $data = ['filter' => $filter, 'columns' => $column_names, 'rows' => $rowsKeys, 'rowsFilterHeader' => 'Region', 'data' => $results];
            return $data;
        }
    }

    public function importStats($request)
    {
        /*try {
            $fileName = $request->file('file')->getClientOriginalName();
            $request->file('file')->storeAs('data_source', $fileName);
            $statImport = new StatsImport($request->days);
            Excel::queueImport($statImport, $request->file('file'));
//            Stats::insert($statImport->data);
            return [
                'success' => true,
                'message' => 'Le fichier a été importé avec succès'
            ];
        } catch (Exception $e) {
            throw $e;
//            return [
//                'success' => false,
//                'message' => 'Une erreur est survenue'
//            ];
        }*/

        $fileName = $request->file('file')->getClientOriginalName();
        $request->file('file')->storeAs('data_source', $fileName);
        $file_path = $request->file('file')->getPathName();

        $statImport = new StatsImport($request->days);
        Excel::import($statImport, $request->file('file'));
       // (new StatsImport)->import($request->file('file'));
        return [
            'success' => true,
            'message' => 'Le fichier a été importé avec succès'
        ];
    }

    public function exportXlsCall(Request $request)
    {
        return Excel::download(new StatsExport($request), 'stats.xlsx');
    }
}
