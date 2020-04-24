<?php

namespace App\Repositories;


use App\Exports\StatsExport;
use App\Imports\StatsImport;
use App\Models\Filter;
use App\Models\Stats;
use App\Models\User;
use App\Models\UserFlag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Carbon\Carbon;
use Box\Spout\Reader\Common\Creator\ReaderEntityFactory;

class StatsRepository
{
    public function getStats(Request $request)
    {
        $allStats = getStats($request);
        return $allStats;

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
        $dates = \DB::table('stats as st')
            ->select('Date_Heure_Note_Annee', 'Date_Heure_Note_Mois', 'Date_Heure_Note_Semaine', 'Date_Note')
            ->distinct()
            ->whereNull('isNotReady')
            ->orderBy('Date_Heure_Note_Annee')
            ->orderBy('Date_Heure_Note_Mois')
            ->orderBy('Date_Heure_Note_Semaine')
            ->orderBy('Date_Note')
            ->get()
            ->groupBy(['Date_Heure_Note_Annee', 'Date_Heure_Note_Mois', 'Date_Heure_Note_Semaine', 'Date_Note'])
            ->map(function ($year, $index) {
                $_year = new \stdClass();
                $_year->id = $index; // year name
                $_year->text = $index; // year name
                $_year->children = []; // months
                $year->map(function ($month, $index) use (&$_year) {
                    $_month = new \stdClass();
                    $_month->id = $_year->text . '-' . $index; // month name
                    $_month->text = getMonthName((int)$index); // month name
                    $_month->children = []; // months
                    $_year->children[] = $_month;
                    $month->map(function ($week, $index) use (&$_year, &$_month) {
                        $_week = new \stdClass();
                        $_week->id = $_year->id . '-' . $_month->id . '-' . $index; // week name
                        $_week->text = $index; // week name
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


    public function GetColumnsRegions(Request $request, $callResult)
    {
        $agenceCode = $request->get('agence_code');
        $agentName = $request->get('agent_name');
        $key_groupement = $request->get('key_groupement');
        $key_groupement = $key_groupement ? clean($key_groupement) : null;

        $_route = getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);

        list($filter, $queryFilters) = makeFilterSubQuery($request, $route, 'Groupement');

        $results = \DB::table('stats as st')
            ->select('Nom_Region')
            ->distinct()
            ->whereNotNull('Nom_Region')
            ->where('Resultat_Appel', 'not like', '=%')
            ->where('Groupement', 'not like', 'Non Renseigné')
            ->where('Groupement', 'not like', 'Appels post')
            ->whereNull('isNotReady');
        $results = applyFilter($results, $filter, 'Groupement');

        if ($key_groupement) {
            $results = $results->where('key_groupement', 'like', $key_groupement);
        }
        if ($agentName) {
            $results = $results->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $results = $results->where('st.Nom_Region', 'like', "%$agenceCode");
        }
        $rowsKeys = \DB::table('stats as st')
            ->select($callResult)
            ->distinct()
            ->whereNotNull('Nom_Region')
            ->where('Resultat_Appel', 'not like', '=%')
            ->where('Groupement', 'not like', 'Non Renseigné')
            ->where('Groupement', 'not like', 'Appels post')
            ->whereNull('isNotReady');
        if ($key_groupement) {
            $rowsKeys = $rowsKeys->where('key_groupement', 'like', $key_groupement);
        }
        if ($agentName) {
            $rowsKeys = $rowsKeys->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $rowsKeys = $rowsKeys->where('st.Nom_Region', 'like', "%$agenceCode");
        }
        $rowsKeys = $rowsKeys->pluck($callResult);

        $keys = $results->pluck('Nom_Region');

        if (!count($keys)) {
            $data = ['filter' => $filter, 'columns' => [], 'rows' => $rowsKeys, 'rowsFilterHeader' => 'Groupement'];
            return $data;
        } else {
            $columns = [];
            $keys->map(function ($key, $index) use (&$columns) {
                $columns[$index + 1] = new \stdClass();
                $columns[$index + 1]->data =
                $columns[$index + 1]->name =
                $columns[$index + 1]->text =
                $columns[$index + 1]->title = $key;
            });
            usort($columns, function ($item1, $item2) {
                return ($item1->data == $item2->data) ? 0 :
                    ($item1->data < $item2->data) ? -1 : 1;
            });
            $first = new \stdClass();
            $first->title = 'Résultats Appels Préalables';
            $first->text = 'Résultats Appels Préalables';
            $first->name = $first->data = $callResult;
            array_unshift($columns, $first);

            return ['filter' => $filter, 'columns' => $columns, 'rows' => $rowsKeys, 'rowsFilterHeader' => 'Groupement'];
        }
    }

    public function GetDataRegions(Request $request, $callResult)
    {
        $agenceCode = $request->get('agence_code');
        $agentName = $request->get('agent_name');
        $key_groupement = $request->get('key_groupement');
        $key_groupement = $key_groupement ? clean($key_groupement) : null;

        $_route = getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);

        list($filter, $queryFilters) = makeFilterSubQuery($request, $route, 'Groupement');

        $regions = \DB::table('stats as st')
            ->select('Nom_Region', $callResult, 'Key_Groupement', \DB::raw('count(distinct st.Id_Externe) as total'))
            ->join(\DB::raw('(SELECT Id_Externe, MAX(Date_Heure_Note) AS MaxDateTime FROM stats
            where Nom_Region is not null ' .
                ($agentName ? 'and Utilisateur like "' . $agentName . '"' : '') .
                ($agenceCode ? 'and Nom_Region like "%' . $agenceCode . '"' : '') .
                ($key_groupement ? 'and key_groupement like "' . $key_groupement . '"' : '') .
                ' and ' . $queryFilters .
                ' and Resultat_Appel not like "=%"
            and Groupement not like "Non Renseigné"
            and Groupement not like "Appels post"
            and isNotReady is null
            GROUP BY Id_Externe, Nom_Region, ' . $callResult . ', Key_Groupement) groupedst'),
                function ($join) {
                    $join->on('st.Id_Externe', '=', 'groupedst.Id_Externe');
                    $join->on('st.Date_Heure_Note', '=', 'groupedst.MaxDateTime');
                })
            ->whereNotNull('Nom_Region')
            ->where('Resultat_Appel', 'not like', '=%')
            ->where('Groupement', 'not like', 'Non Renseigné')
            ->where('Groupement', 'not like', 'Appels post')
            ->whereNull('isNotReady');
        $regions = applyFilter($regions, $filter, 'Groupement');

        if ($key_groupement) {
            $regions = $regions->where('key_groupement', 'like', $key_groupement);
        }
        if ($agentName) {
            $regions = $regions->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $regions = $regions->where('st.Nom_Region', 'like', "%$agenceCode");
        }

        $regions = $regions->groupBy('Nom_Region', $callResult, 'Key_Groupement')->get();

        $keys = $regions->groupBy(['Nom_Region'])->keys();

        if (!count($regions)) {
            $data = ['data' => []];
            return $data;
        } else {
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

            $regions = $regions->groupBy([$callResult]);

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

                    $row->values[$nom_region] = $call->total;
                    $row->$nom_region = $call->total . '|' . $call->$nom_region . '%';
//                    $row->$nom_region = $call->$nom_region . '%';
//                    $row->total = round(array_sum($row->values) / count($row->values), 2) . '%';
//                    $row->_total = $call->total;
                    $row->column = $callResult;
//                    dump($row);
                    return $row;
                });

                $_item = $item->last();
//                dd($_item);
                foreach ($col_arr as $col) {
                    $_item->values[$col] = 0;
                    $_item->$col = '0%';
                }

                ksort($_item->values);

                $_item->values = collect($_item->values)->values();

                return $_item;
            });

            $regions = $regions->values();

            return ['data' => $regions];
        }
    }

    public function GetColumnsRegionsByGrpCall(Request $request)
    {
        $agenceCode = $request->get('agence_code');
        $agentName = $request->get('agent_name');
        $key_groupement = $request->get('key_groupement');
//        $key_groupement = $key_groupement ? clean($key_groupement) : null;

        $route_request = $request->get('route');
        $_route = $route_request ?? getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);
        list($filter, $queryFilters) = makeFilterSubQuery($request, $route, $route_request ? null : 'Resultat_Appel');

        $results = \DB::table('stats as st')
            ->select('Nom_Region')
            ->distinct()
            ->where('Resultat_Appel', 'not like', '=%')
            ->whereNotNull('Nom_Region')
            ->where('Groupement', 'not like', 'Non Renseigné')
            ->where('Groupement', 'not like', 'Appels post')
            ->whereNull('isNotReady');

        $results = applyFilter($results, $filter, $route_request ? null : 'Resultat_Appel');

        if ($key_groupement) {
            $results = $results->where('Groupement', 'like', $key_groupement);
        }

        if ($agentName) {
            $results = $results->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $results = $results->where('st.Nom_Region', 'like', "%$agenceCode");
        }

        $rowsKeys = \DB::table('stats as st')
            ->select('Resultat_Appel')
            ->distinct()
            ->where('Resultat_Appel', 'not like', '=%')
            ->whereNotNull('Nom_Region')
            ->where('Groupement', 'not like', 'Non Renseigné')
            ->where('Groupement', 'not like', 'Appels post')
            ->whereNull('isNotReady');
        if ($key_groupement) {
            $rowsKeys = $rowsKeys->where('Groupement', 'like', $key_groupement);
        }
        if ($agentName) {
            $rowsKeys = $rowsKeys->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $rowsKeys = $rowsKeys->where('st.Nom_Region', 'like', "%$agenceCode");
        }
        $rowsKeys = $rowsKeys->pluck('Resultat_Appel');

        $keys = $results->pluck('Nom_Region');

        if (!count($keys)) {
            $data = ['filter' => $filter, 'columns' => [], 'rows' => $rowsKeys, 'rowsFilterHeader' => 'Résultat Appel'];
            return $data;
        } else {

            $columns = [];
            $keys->map(function ($key, $index) use (&$columns) {
                $columns[$index + 1] = new \stdClass();
                $columns[$index + 1]->data = $key;
                $columns[$index + 1]->name = $key;
                $columns[$index + 1]->text = $key;
                $columns[$index + 1]->title = $key;
            });
            usort($columns, function ($item1, $item2) {
                return ($item1->data == $item2->data) ? 0 :
                    ($item1->data < $item2->data) ? -1 : 1;
            });
            $first = new \stdClass();
            $first->name = 'Resultat_Appel';
            $first->data = 'Resultat_Appel';
            $first->text = $key_groupement;
            $first->title = $key_groupement;
            $first->orderable = false;
            array_unshift($columns, $first);

            return ['filter' => $filter, 'columns' => $columns, 'rows' => $rowsKeys, 'rowsFilterHeader' => 'Résultat Appel'];
        }
    }

    public function GetDataRegionsByGrpCall(Request $request)
    {
        $agenceCode = $request->get('agence_code');
        $agentName = $request->get('agent_name');
        $key_groupement = $request->get('key_groupement');
        $key_groupement = $key_groupement ? clean($key_groupement) : null;

        $route_request = $request->get('route');
        $_route = $route_request ?? getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);
        list($filter, $queryFilters) = makeFilterSubQuery($request, $route, $route_request ? null : 'Resultat_Appel');

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
                ($key_groupement ? 'and key_groupement like "' . $key_groupement . '"' : '') .
                ' and isNotReady is null ' .
                ' GROUP BY Id_Externe, Nom_Region, Groupement, Key_Groupement, Resultat_Appel) groupedst'),
                function ($join) {
                    $join->on('st.Id_Externe', '=', 'groupedst.Id_Externe');
                    $join->on('st.Date_Heure_Note', '=', 'groupedst.MaxDateTime');
                })
            ->where('Resultat_Appel', 'not like', '=%')
            ->whereNotNull('Nom_Region')
            ->where('Groupement', 'not like', 'Non Renseigné')
            ->where('Groupement', 'not like', 'Appels post')
            ->whereNull('isNotReady');

        $regions = applyFilter($regions, $filter, $route_request ? null : 'Resultat_Appel');

        if ($key_groupement) {
            $regions = $regions->where('key_groupement', 'like', $key_groupement);
        }
        if ($agentName) {
            $regions = $regions->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $regions = $regions->where('st.Nom_Region', 'like', "%$agenceCode");
        }

        $regions = $regions->groupBy('Nom_Region', 'Groupement', 'Key_Groupement', 'Resultat_Appel')->get();
        $keys = $regions->groupBy(['Nom_Region'])->keys();

        if (!count($regions)) {
            $data = ['data' => []];
            return $data;
        } else {
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
                    $row->values[$nom_region] = $call->total;
                    $row->$nom_region = $call->total . '|' . $call->$nom_region . '%';
//                    $row->$nom_region = $call->$nom_region . '%';
//                    $row->total = round(array_sum($row->values) / count($row->values), 2) . '%';
//                    $row->_total = $call->total;
                    $row->column = 'Resultat_Appel';
                    return $row;
                });
                $_item = $items->last();

                foreach ($col_arr as $col) {
                    $_item->values[$col] = 0;
                    $_item->$col = '0%';
                }

                ksort($_item->values);

                $_item->values = collect($_item->values)->values();
                return $_item;
            });
            $regions = $regions->values();

            return ['data' => $regions];
        }
    }

    public function GetColumnsRegionsCallState(Request $request, $column)
    {
        $agenceCode = $request->get('agence_code');
        $agentName = $request->get('agent_name');
        $key_groupement = $request->get('key_groupement');
        $key_groupement = $key_groupement ? clean($key_groupement) : null;

        $_route = getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);

        list($filter, $queryFilters) = makeFilterSubQuery($request, $route, 'Gpmt_Appel_Pre');

        $columns = \DB::table('stats as st');
        if ($column == 'Date_Heure_Note_Semaine') {
            $columns = $columns->select($column, 'Date_Heure_Note_Annee');
        } else {
            $columns = $columns->select($column);
        }
        $columns = $columns->distinct()
            ->where('Groupement', 'not like', 'Non Renseigné')
            ->where('Groupement', 'like', 'Appels préalables')
            ->where('Gpmt_Appel_Pre', 'not like', 'Hors Périmètre')
            ->whereNotNull('Nom_Region')
            ->whereNull('isNotReady');

        $columns = applyFilter($columns, $filter, 'Gpmt_Appel_Pre');

        if ($key_groupement) {
            $columns = $columns->where('key_groupement', 'like', $key_groupement);
        }
        if ($agentName) {
            $columns = $columns->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $columns = $columns->where('st.Nom_Region', 'like', "%$agenceCode");
        }
        if ($column == 'Date_Heure_Note_Semaine') {
            $keys = $columns->get()
                ->map(function ($item) {
                    $item->key = $item->Date_Heure_Note_Semaine . '_' . $item->Date_Heure_Note_Annee;
                    return $item;
                })->pluck('key');
        } else {
            $keys = $columns->pluck($column);
        }

        $rowsKeys = \DB::table('stats as st')
            ->select('Gpmt_Appel_Pre')
            ->distinct()
            ->where('Gpmt_Appel_Pre', 'not like', 'Non renseigné')
            ->where('Gpmt_Appel_Pre', 'not like', 'Hors Périmètre')
            ->whereNotNull('Nom_Region')
            ->whereNull('isNotReady');
        if ($key_groupement) {
            $rowsKeys = $rowsKeys->where('key_groupement', 'like', $key_groupement);
        }
        if ($agentName) {
            $rowsKeys = $rowsKeys->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $rowsKeys = $rowsKeys->where('st.Nom_Region', 'like', "%$agenceCode");
        }
        $rowsKeys = $rowsKeys->pluck('Gpmt_Appel_Pre');


        if (!count($keys)) {
            $data = ['filter' => $filter, 'columns' => [], 'rows' => $rowsKeys, 'rowsFilterHeader' => ($column == 'Date_Heure_Note_Semaine' ? 'Résultats Appels Préalables par semaine' : 'Résultats Appels Préalables par agence')];
            return $data;
        } else {
            $columns = [];
            $keys->map(function ($key, $index) use (&$columns) {
                $columns[$index + 1] = new \stdClass();
                $columns[$index + 1]->data =
                $columns[$index + 1]->name =
                $columns[$index + 1]->text =
                $columns[$index + 1]->title = $key;
            });
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

            return ['filter' => $filter, 'columns' => $columns, 'rows' => $rowsKeys, 'rowsFilterHeader' => ($column == 'Date_Heure_Note_Semaine' ? 'Résultats Appels Préalables par semaine' : 'Résultats Appels Préalables par agence')];
        }
    }

    public function GetDataRegionsCallState(Request $request, $column)
    {
        $agenceCode = $request->get('agence_code');
        $agentName = $request->get('agent_name');
        $key_groupement = $request->get('key_groupement');
        $key_groupement = $key_groupement ? clean($key_groupement) : null;

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
                    ($key_groupement ? 'and key_groupement like "' . $key_groupement . '"' : '') .
                    ($agentName ? 'and Utilisateur like "' . $agentName . '"' : '') .
                    ($agenceCode ? 'and Nom_Region like "%' . $agenceCode . '"' : '') .
                    ' and ' . $queryFilters .
                    ' and isNotReady is null ' .
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
                    ($key_groupement ? 'and key_groupement like "' . $key_groupement . '"' : '') .
                    ($agentName ? 'and Utilisateur like "' . $agentName . '"' : '') .
                    ($agenceCode ? 'and Nom_Region like "%' . $agenceCode . '"' : '') .
                    ' and ' . $queryFilters .
                    ' and isNotReady is null ' .
                    ' GROUP BY Id_Externe, ' . $column . ', Gpmt_Appel_Pre) groupedst'),
                    function ($join) {
                        $join->on('st.Id_Externe', '=', 'groupedst.Id_Externe');
                        $join->on('st.Date_Heure_Note', '=', 'groupedst.MaxDateTime');
                    });
        }
        $regions = $regions->where('Groupement', 'not like', 'Non Renseigné')
            ->where('Groupement', 'like', 'Appels préalables')
            ->where('Gpmt_Appel_Pre', 'not like', 'Hors Périmètre')
            ->whereNotNull('Nom_Region')
            ->whereNull('isNotReady');

        $regions = applyFilter($regions, $filter, 'Gpmt_Appel_Pre');


        if ($key_groupement) {
            $regions = $regions->where('key_groupement', 'like', $key_groupement);
        }
        if ($agentName) {
            $regions = $regions->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $regions = $regions->where('st.Nom_Region', 'like', "%$agenceCode");
        }

        if ($column == 'Date_Heure_Note_Semaine') {
            $regions = $regions->groupBy($column, 'Gpmt_Appel_Pre', 'Date_Heure_Note_Annee')->get();
            $keys = $regions->map(function ($item) {
                $item->key = $item->Date_Heure_Note_Semaine . '_' . $item->Date_Heure_Note_Annee;
                return $item;
            })
                ->groupBy(['key'])->keys();
        } else {
            $regions = $regions->groupBy($column, 'Gpmt_Appel_Pre')->get();
            $keys = $regions->groupBy([$column])->keys();
        }

        if (!count($regions)) {
            $data = ['data' => []];
            return $data;
        } else {
            $temp = $regions->groupBy([$column]);
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
            $regions = $regions->groupBy(['Gpmt_Appel_Pre']);

            $total = new \stdClass();
            $total->values = [];
            $regions = $regions->map(function ($region, $index) use (&$columns, $keys, $column, &$total) {
                $row = new \stdClass();
                $row->values = [];

                $col_arr = $keys->all();
                $item = $region->map(function ($call, $index) use (&$row, &$col_arr, $column) {
                    if ($column == 'Date_Heure_Note_Semaine') {
                        $column_week = $call->Date_Heure_Note_Semaine;
                        $column_name = $call->Date_Heure_Note_Semaine . '_' . $call->Date_Heure_Note_Annee;
                        $row->$column_name = $call->total . '|' . $call->$column_week . ' %';
                        $col_arr = array_diff($col_arr, [$column_name]);
                        $row->values[$column_name] = $call->total;
                    } else {
                        $column_name = $call->$column;
                        $col_arr = array_diff($col_arr, [$column_name]);
                        $row->values[$column_name] = $call->total;
                        $row->$column_name = $call->total . '|' . $call->$column_name . ' %';
                    }
                    $row->Gpmt_Appel_Pre = $call->Gpmt_Appel_Pre;
                    $row->column = $call->Gpmt_Appel_Pre;
                    $row->total = isset($row->total) ? $row->total + $call->total : $call->total;
                    return $row;
                });
                $_item = $item->last();
                foreach ($col_arr as $col) {
                    $_item->values[$col] = 0; //'0%';
                    $_item->$col = 0; //'0%';
                }
                ksort($_item->values);
                if ($column == 'Date_Heure_Note_Semaine') {
                    $_item->values = sortWeeksDates($_item->values, true);
                }
                collect($_item->values)->map(function ($value, $index) use (&$total, $_item) {
                    $total->values[$index] = round(!isset($total->values[$index]) ? $value : $value + $total->values[$index], 2);
                    $total->$index = $total->values[$index];
                });
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

            return ['data' => $regions];
        }
    }

    public function GetColumnsClientsByCallState(Request $request, $callResult)
    {
        $agentName = $request->get('agent_name');
        $agenceCode = $request->get('agence_code');
        $key_groupement = $request->get('key_groupement');
        $key_groupement = $key_groupement ? clean($key_groupement) : null;

        $_route = getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);
        list($filter, $queryFilters) = makeFilterSubQuery($request, $route, 'Nom_Region');

        $columns = \DB::table('stats as st')
            ->select('Code_Intervention')
            ->distinct()
            ->whereNotNull('Nom_Region')
            ->whereNull('isNotReady');

        $columns = applyFilter($columns, $filter, 'Nom_Region');

        if ($callResult == 'Joignable') {
            $columns = $columns->whereIn('Resultat_Appel', [
                'Appels préalables - RDV confirmé',
                'Appels préalables - RDV confirmé Client non informé',
                'Appels préalables - RDV repris et confirmé'
            ]);
        } else {
            $columns = $columns->whereIn('Resultat_Appel', [
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

        if ($key_groupement) {
            $columns = $columns->where('key_groupement', 'like', $key_groupement);
        }
        if ($agentName) {
            $columns = $columns->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $columns = $columns->where('st.Nom_Region', 'like', "%$agenceCode");
        }
        $rowsKeys = \DB::table('stats as st')
            ->select('Nom_Region')
            ->distinct()
            ->whereNotNull('Nom_Region')
            ->whereNull('isNotReady');
        if ($key_groupement) {
            $rowsKeys = $rowsKeys->where('key_groupement', 'like', $key_groupement);
        }
        if ($agentName) {
            $rowsKeys = $rowsKeys->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $rowsKeys = $rowsKeys->where('st.Nom_Region', 'like', "%$agenceCode");
        }
        $rowsKeys = $rowsKeys->pluck('Nom_Region');

        $columns = $columns->where('Gpmt_Appel_Pre', 'like', $callResult);

        $keys = $columns->pluck('Code_Intervention');

        if (!count($keys)) {
            $data = ['filter' => $filter, 'columns' => [], 'rows' => $rowsKeys, 'rowsFilterHeader' => 'Region'];
            return $data;
        } else {

            $columns = [];
            $keys->map(function ($key, $index) use (&$columns) {
                $columns[$index + 1] = new \stdClass();
                $columns[$index + 1]->text =
                $columns[$index + 1]->data =
                $columns[$index + 1]->name =
                $columns[$index + 1]->title = $key ?? '';
            });
            usort($columns, function ($item1, $item2) {
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
            array_unshift($columns, $first);
            array_push($columns, $last);

            $data = ['filter' => $filter, 'columns' => $columns, 'rows' => $rowsKeys, 'rowsFilterHeader' => 'Region'];
            return $data;
        }
    }

    public function GetDataClientsByCallState(Request $request, $callResult)
    {
        $agentName = $request->get('agent_name');
        $agenceCode = $request->get('agence_code');
        $key_groupement = $request->get('key_groupement');
        $key_groupement = $key_groupement ? clean($key_groupement) : null;

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
                ($key_groupement ? 'and key_groupement like "' . $key_groupement . '"' : '') .
                'and Gpmt_Appel_Pre like "' . $callResult . '"' .
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
                ' and isNotReady is null ' .
                ' GROUP BY Id_Externe, Code_Intervention, Nom_Region) groupedst'),
                function ($join) {
                    $join->on('st.Id_Externe', '=', 'groupedst.Id_Externe');
                    $join->on('st.Date_Heure_Note', '=', 'groupedst.MaxDateTime');
                })
            ->whereNotNull('Nom_Region')
            ->whereNull('isNotReady');

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

        if ($key_groupement) {
            $codes = $codes->where('key_groupement', 'like', $key_groupement);
        }
        if ($agentName) {
            $codes = $codes->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $codes = $codes->where('st.Nom_Region', 'like', "%$agenceCode");
        }

        $codes = $codes->where('Gpmt_Appel_Pre', 'like', $callResult);

        $codes = $codes->groupBy('Code_Intervention', 'Nom_Region')->get();
        $keys = $codes->groupBy(['Code_Intervention'])->keys();

        if (!count($codes)) {
            $data = ['data' => []];
            return $data;
        } else {

            $temp = $codes->groupBy(['Nom_Region']);

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
            $codes = $temp->flatten();

            $codes = $codes->groupBy(['Nom_Region']);

            $total = new \stdClass();
            $total->values = [];
            $codes = $codes->map(function ($region) use (&$codes_names, &$total, $keys) {
                $row = new \stdClass(); //[];
                $row->values = [];
                $col_arr = $keys->all();
                $item = $region->map(function ($call, $index) use (&$row, &$codes_names, &$col_arr) {
                    $row->Nom_Region = $call->Nom_Region;
                    $code_intervention = $call->Code_Intervention;
                    $col_arr = array_diff($col_arr, [$code_intervention]);
                    $row->values[$code_intervention ?? ''] = $call->total;
                    $row->$code_intervention = $call->total . '|' . $call->$code_intervention . '%';
                    $row->total = isset($row->total) ? $row->total + $call->total : $call->total;
                    return $row;
                });
                $_item = $item->last();
                foreach ($col_arr as $col) {
                    $_item->values[$col] = 0;
                    $_item->$col = '0%';
                }
                ksort($_item->values);

                collect($_item->values)->map(function ($value, $index) use (&$total) {
                    $value = str_replace('%', '', $value);
                    $total->values[$index] = (round(!isset($total->values[$index]) ? $value : $value + $total->values[$index], 2));
                    $total->$index = $total->values[$index];
                });
                $_item->values = collect($_item->values)->values();
                return $_item;
            });
            collect($total->values)->map(function ($value, $index) use (&$total) {
                $total->values[$index] = ceil(round($total->values[$index], 2)); // round($total->values[$index] / $dataCount, 2);
                $total->$index = $total->values[$index] . '%';
            });

            $total->Nom_Region = 'Total Général';
            $total->total = ceil(round(array_sum($total->values), 2)); //round(array_sum($total->values) / count($total->values), 2) . '%';

            $codes = $codes->values();
            $data = ['data' => $codes];
            return $data;
        }
    }

    public function GetColumnsNonValidatedFolders(Request $request, $intervCol)
    {
        $agentName = $request->get('agent_name');
        $agenceCode = $request->get('agence_code');
        $key_groupement = $request->get('key_groupement');
        $key_groupement = $key_groupement ? clean($key_groupement) : null;

        $_route = getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);
        list($filter, $queryFilters) = makeFilterSubQuery($request, $route, $intervCol);


        $columns = \DB::table('stats as st')
            ->select('Nom_Region')
            ->distinct()
            ->whereNotNull('Nom_Region')
            ->where('Groupement', 'Appels clôture')
            ->whereNull('isNotReady');
        $columns = applyFilter($columns, $filter, $intervCol);

        if ($key_groupement) {
            $columns = $columns->where('key_groupement', 'like', $key_groupement);
        }
        if ($agentName) {
            $columns = $columns->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $columns = $columns->where('st.Nom_Region', 'like', "%$agenceCode");
        }

        $rowsKeys = \DB::table('stats as st')
            ->select($intervCol)
            ->distinct()
            ->whereNotNull('Nom_Region')
            ->where('Groupement', 'Appels clôture')
            ->whereNull('isNotReady');
        if ($key_groupement) {
            $rowsKeys = $rowsKeys->where('key_groupement', 'like', $key_groupement);
        }
        if ($agentName) {
            $rowsKeys = $rowsKeys->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $rowsKeys = $rowsKeys->where('st.Nom_Region', 'like', "%$agenceCode");
        }
        $rowsKeys = $rowsKeys->pluck($intervCol);

        $keys = $columns->pluck('Nom_Region');

        if (!count($keys)) {
            $data = ['filter' => $filter, 'columns' => [], 'rows' => $rowsKeys, 'rowsFilterHeader' => ($intervCol == 'Code_Intervention' ? 'Code Intervention' : 'Type Intervention')];
            return $data;
        } else {
            $regions_names = [];
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

            $data = ['filter' => $filter, 'columns' => $regions_names, 'rows' => $rowsKeys, 'rowsFilterHeader' => ($intervCol == 'Code_Intervention' ? 'Code Intervention' : 'Type Intervention')];
            return $data;
        }
    }

    public function GetDataNonValidatedFolders(Request $request, $intervCol)
    {
        $agentName = $request->get('agent_name');
        $agenceCode = $request->get('agence_code');
        $key_groupement = $request->get('key_groupement');
        $key_groupement = $key_groupement ? clean($key_groupement) : null;

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
                ($key_groupement ? 'and key_groupement like "' . $key_groupement . '"' : '') .
                ' and isNotReady is null ' .
                ' GROUP BY Id_Externe, Nom_Region, ' . $intervCol . ', Resultat_Appel) groupedst'),
                function ($join) {
                    $join->on('st.Id_Externe', '=', 'groupedst.Id_Externe');
                    $join->on('st.Date_Heure_Note', '=', 'groupedst.MaxDateTime');
                })
            ->whereNotNull('Nom_Region')
            ->where('Groupement', 'Appels clôture')
            ->whereNull('isNotReady');
        $regions = applyFilter($regions, $filter, $intervCol);

        if ($key_groupement) {
            $regions = $regions->where('key_groupement', 'like', $key_groupement);
        }
        if ($agentName) {
            $regions = $regions->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $regions = $regions->where('st.Nom_Region', 'like', "%$agenceCode");
        }

        $regions = $regions->groupBy('Nom_Region', $intervCol, 'Resultat_Appel')->get();

        $keys = $regions->groupBy(['Nom_Region'])->keys();

        if (!count($regions)) {
            $data = ['data' => []];
            return $data;
        } else {
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
            $regions = $regions->groupBy($intervCol);


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

                    $row->values[$nom_region] = $call->total;
                    $row->$nom_region = $call->total . '|' . (isset($call->itemTotal) ? $call->itemTotal : 0) . '|' . (isset($call->$index) ? $call->$index : 0) . '%';
//                    $row->$nom_region = $call->total . '|' . $call->$nom_region . '%';
                    $row->total = isset($row->total) ? $row->total + $call->total : $call->total;
//                    $row->total = round(array_sum($row->values), 2); //round(array_sum($row->values) / count($row->values), 2) . '%';
                    return $row;
                });
                $_item = $item->last();
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

            collect($total->values)->map(function ($value, $index) use (&$total) {
                $total->values[$index] = ceil(round($total->values[$index], 2)); // round($total->values[$index] / $dataCount, 2);
                $total->$index = $total->values[$index] . '%';
            });

//            $total->$intervCol = 'Total Général';
//            $total->total = round(array_sum($total->values)); //round(array_sum($total->values) / count($total->values), 2) . '%';
//            $total->values = collect($total->values)->values();
//            $total->isTotal = true;
//            $regions->push($total);

            $regions = $regions->values();
            $data = ['data' => $regions];
            return $data;
        }
    }

    public function GetColumnsClientsByPerimeter(Request $request)
    {
        $agenceCode = $request->get('agence_code');
        $agentName = $request->get('agent_name');
        $key_groupement = $request->get('key_groupement');
        $key_groupement = $key_groupement ? clean($key_groupement) : null;

        $_route = getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);
        list($filter, $queryFilters) = makeFilterSubQuery($request, $route, 'Nom_Region');


        $results = \DB::table('stats as st')
            ->select('st.Groupement')
            ->distinct()
            ->whereNotNull('st.Groupement')
            ->whereNotNull('st.Nom_Region')
            ->where('st.Groupement', 'not like', 'Non renseigné')
            ->where('st.Groupement', 'not like', 'Appels post')
            ->where('Type_Note', 'like', 'CAM')
            ->whereNull('isNotReady');
        $results = applyFilter($results, $filter, 'Nom_Region');

        if ($key_groupement) {
            $results = $results->where('key_groupement', 'like', $key_groupement);
        }
        if ($agentName) {
            $results = $results->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $results = $results->where('st.Nom_Region', 'like', "%$agenceCode");
        }

        $rowsKeys = \DB::table('stats as st')
            ->select('st.Nom_Region')
            ->distinct()
            ->whereNotNull('st.Groupement')
            ->whereNotNull('st.Nom_Region')
            ->where('st.Groupement', 'not like', 'Non renseigné')
            ->where('st.Groupement', 'not like', 'Appels post')
            ->where('Type_Note', 'like', 'CAM')
            ->whereNull('isNotReady');
        if ($key_groupement) {
            $rowsKeys = $rowsKeys->where('key_groupement', 'like', $key_groupement);
        }
        if ($agentName) {
            $rowsKeys = $rowsKeys->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $rowsKeys = $rowsKeys->where('st.Nom_Region', 'like', "%$agenceCode");
        }
        $rowsKeys = $rowsKeys->pluck('Nom_Region');

        $keys = $results->pluck('Groupement');

        if (!count($keys)) {
            $data = ['filter' => $filter, 'columns' => [], 'rows' => $rowsKeys, 'rowsFilterHeader' => 'Region'];
            return $data;
        } else {
            $column_names = [];
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

            $data = ['filter' => $filter, 'columns' => $column_names, 'rows' => $rowsKeys, 'rowsFilterHeader' => 'Region'];
            return $data;
        }
    }

    public function GetDataClientsByPerimeter(Request $request)
    {
        $agenceCode = $request->get('agence_code');
        $agentName = $request->get('agent_name');
        $key_groupement = $request->get('key_groupement');
        $key_groupement = $key_groupement ? clean($key_groupement) : null;

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
                ($key_groupement ? 'and key_groupement like "' . $key_groupement . '"' : '') .
                ' and isNotReady is null ' .
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
            ->where('Type_Note', 'like', 'CAM')
            ->whereNull('isNotReady');
        $results = applyFilter($results, $filter, 'Nom_Region');
        if ($key_groupement) {
            $results = $results->where('key_groupement', 'like', $key_groupement);
        }
        if ($agentName) {
            $results = $results->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $results = $results->where('st.Nom_Region', 'like', "%$agenceCode");
        }

        $results = $results->groupBy('Groupement', 'Nom_Region')->get();

        $keys = $results->groupBy(['Groupement'])->keys();

        if (!count($results)) {
            $data = ['data' => []];
            return $data;
        } else {

            $temp = $results->groupBy(['Nom_Region']);
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

            $results = $temp->flatten();

            $results = $results->groupBy(['Nom_Region']);

            $total = new \stdClass();
            $total->values = [];
            $results = $results->map(function ($region) use (&$column_names, &$total, $keys) {
                $row = new \stdClass();
                $row->values = [];
                $col_arr = $keys->all();
                $item = $region->map(function ($call, $index) use (&$row, &$codes_names, &$total, &$col_arr) {
                    $row->Nom_Region = $call->Nom_Region;
                    $column_name = $call->Groupement;
                    $col_arr = array_diff($col_arr, [$column_name]);
                    $row->values[$column_name] = $call->total;
                    $row->$column_name = $call->total . '|' . $call->$column_name . '%';
                    $row->total = isset($row->total) ? $row->total + $call->total : $call->total;
                    return $row;
                });
                $_item = $item->last();
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
            $total->Nom_Region = 'Total Général';
            $total->total = round(array_sum($total->values), 2);
            $total->values = collect($total->values)->values();
            $total->isTotal = true;
            $results->push($total);
            $results = $results->values();

            $data = ['data' => $results];
            return $data;
        }
    }

    public function GetColumnsClientsWithCallStates(Request $request, $filter = null)
    {
        $agentName = $request->get('agent_name');
        $agenceCode = $request->get('agence_code');
        $key_groupement = $request->get('key_groupement');
        $key_groupement = $key_groupement ? clean($key_groupement) : null;

        $_route = getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);

        list($filter, $queryFilters) = makeFilterSubQuery($request, $route, 'Nom_Region');

        $codes = \DB::table('stats as st')
            ->select('Code_Intervention')
            ->distinct()
            ->whereNotNull('Nom_Region')
            ->whereNull('isNotReady');

        $codes = applyFilter($codes, $filter, 'Nom_Region');

        $codes = $codes->whereIn('Gpmt_Appel_Pre', ["Joignable", "Injoignable"]);

        $codes = $codes->whereIn('Resultat_Appel', [
            'Appels préalables - RDV confirmé',
            'Appels préalables - RDV confirmé Client non informé',
            'Appels préalables - RDV repris et confirmé',
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

        if ($key_groupement) {
            $codes = $codes->where('key_groupement', 'like', $key_groupement);
        }
        if ($agentName) {
            $codes = $codes->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $codes = $codes->where('st.Nom_Region', 'like', "%$agenceCode");
        }
        $rowsKeys = \DB::table('stats as st')
            ->select('Nom_Region')
            ->distinct()
            ->whereNotNull('Nom_Region')
            ->whereNull('isNotReady');
        if ($key_groupement) {
            $rowsKeys = $rowsKeys->where('key_groupement', 'like', $key_groupement);
        }
        if ($agentName) {
            $rowsKeys = $rowsKeys->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $rowsKeys = $rowsKeys->where('st.Nom_Region', 'like', "%$agenceCode");
        }
        $rowsKeys = $rowsKeys->pluck('Nom_Region');


        $keys = $codes->pluck('Code_Intervention');

        if (!count($keys)) {
            $data = ['filter' => $filter, 'columns' => [], 'rows' => $rowsKeys, 'rowsFilterHeader' => 'Region'];
            return $data;
        } else {
            $codes_names = [];
            $keys->map(function ($key, $index) use (&$codes_names) {
                $codes_names[$index + 1] = new \stdClass();
                $codes_names[$index + 1]->title =
                $codes_names[$index + 1]->text =
                $codes_names[$index + 1]->data =
                $codes_names[$index + 1]->name = $key ?? '';
            });
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

            $data = ['filter' => $filter, 'columns' => $codes_names, 'rows' => $rowsKeys, 'rowsFilterHeader' => 'Region'];
            return $data;
        }
    }

    public function GetDataClientsWithCallStates(Request $request, $filter = null)
    {
        $agentName = $request->get('agent_name');
        $agenceCode = $request->get('agence_code');
        $key_groupement = $request->get('key_groupement');
        $key_groupement = $key_groupement ? clean($key_groupement) : null;

        $_route = getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);

        list($filter, $queryFilters) = makeFilterSubQuery($request, $route, 'Nom_Region');

        $codes = \DB::table('stats as st')
            ->select('Code_Intervention', 'Nom_Region', \DB::raw('count(distinct st.Id_Externe) as total'))
            ->join(\DB::raw('(SELECT Id_Externe, MAX(Date_Heure_Note) AS MaxDateTime FROM stats
             where Nom_Region is not null ' .
                ' and ' . $queryFilters .
                ($key_groupement ? 'and key_groupement like "' . $key_groupement . '"' : '') .
                ($agentName ? 'and Utilisateur like "' . $agentName . '"' : '') .
                ($agenceCode ? 'and Nom_Region like "%' . $agenceCode . '"' : '') .
                ' and Gpmt_Appel_Pre in ("Joignable", "Injoignable")' .
                ' and Resultat_Appel in ("Appels préalables - RDV confirmé",
                                        "Appels préalables - RDV confirmé Client non informé",
                                        "Appels préalables - RDV repris et confirmé",

                                        "Appels préalables - RDV confirmé",
                                        "Appels préalables - RDV confirmé Client non informé",
                                        "Appels préalables - RDV repris et confirmé",
                                        "Appels préalables - Annulation RDV client non informé",
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
                                        "Appels préalables - RDV repris Mais non confirmé")' .
                ' and isNotReady is null ' .
                ' GROUP BY Id_Externe, Code_Intervention, Nom_Region) groupedst'),
                function ($join) {
                    $join->on('st.Id_Externe', '=', 'groupedst.Id_Externe');
                    $join->on('st.Date_Heure_Note', '=', 'groupedst.MaxDateTime');
                })
            ->whereNotNull('Nom_Region')
            ->whereNull('isNotReady');

        $codes = applyFilter($codes, $filter, 'Nom_Region');

        $codes = $codes->whereIn('Gpmt_Appel_Pre', ["Joignable", "Injoignable"]);

        $codes = $codes->whereIn('Resultat_Appel', [
            'Appels préalables - RDV confirmé',
            'Appels préalables - RDV confirmé Client non informé',
            'Appels préalables - RDV repris et confirmé',

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

        if ($key_groupement) {
            $codes = $codes->where('key_groupement', 'like', $key_groupement);
        }
        if ($agentName) {
            $codes = $codes->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $codes = $codes->where('st.Nom_Region', 'like', "%$agenceCode");
        }
        $codes = $codes->groupBy('Code_Intervention', 'Nom_Region')->get();
        $keys = $codes->pluck('Code_Intervention');

        if (!count($codes)) {
            $data = ['data' => []];
            return $data;
        } else {
            $temp = $codes->groupBy(['Nom_Region']);
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
            $codes = $temp->flatten();
            $codes = $codes->groupBy(['Nom_Region']);

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
                    $row->values[$code_intervention ?? ''] = $call->total;
                    $row->$code_intervention = $call->total . '|' . $call->$code_intervention . '%';
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

            collect($total->values)->map(function ($value, $index) use (&$total) {
                $total->values[$index] = ceil(round($total->values[$index], 2)); // round($total->values[$index] / $dataCount, 2);
                $total->$index = $total->values[$index] . '%';
            });

            $total->Nom_Region = 'Total Général';
            $total->total = ceil(round(array_sum($total->values), 2)); //round(array_sum($total->values) / count($total->values), 2) . '%';

            $codes = $codes->values();
            $data = ['data' => $codes];
            return $data;
        }
    }


    public function GetColumnsCloturetechCall(Request $request, $filter = null)
    {
        $agentName = $request->get('agent_name');
        $agenceCode = $request->get('agence_code');
        $_route = getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);
        list($filter, $queryFilters) = makeFilterSubQuery($request, $route);

        $columns = \DB::table('stats as st')
            ->select('Nom_Region')
            ->distinct()
            ->whereNotNull('EXPORT_ALL_Date_VALIDATION')
            ->whereNotNull('EXPORT_ALL_Date_SOLDE')
            ->whereNotNull('Nom_Region')
            ->whereNull('isNotReady');

        $columns = applyFilter($columns, $filter);
        if ($agentName) {
            $columns = $columns->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $columns = $columns->where('st.Nom_Region', 'like', "%$agenceCode");
        }

        $keys = $columns->pluck('Nom_Region');


        if (!count($keys)) {
            $data = ['filter' => $filter, 'columns' => [], 'rows' => [], 'rowsFilterHeader' => ''];
            return $data;
        } else {
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
            $first->title = 'Nom region';
            $first->name = 'Nom_region';
            $first->data = 'Nom_region';
            $first->orderable = false;
            array_unshift($regions_names, $first);

            return ['filter' => $filter, 'columns' => $regions_names, 'rows' => [], 'rowsFilterHeader' => ''];
        }
    }

    public function GetCloturetechCall(Request $request, $filter = null)
    {
        $agentName = $request->get('agent_name');
        $agenceCode = $request->get('agence_code');
        $_route = getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);
        list($filter, $queryFilters) = makeFilterSubQuery($request, $route);
        $regions = \DB::table('stats as st')
            ->select('Nom_Region', \DB::raw('count(distinct st.Id_Externe) as count'), \DB::raw('CASE
                    WHEN TIMESTAMPDIFF(MINUTE,EXPORT_ALL_Date_SOLDE,EXPORT_ALL_Date_VALIDATION) > 1440 THEN "4-superieur un jour"
                    WHEN TIMESTAMPDIFF(MINUTE,EXPORT_ALL_Date_SOLDE,EXPORT_ALL_Date_VALIDATION) between 60 and 360   then "3-Entre 1H et 6h"
                    WHEN TIMESTAMPDIFF(MINUTE,EXPORT_ALL_Date_SOLDE,EXPORT_ALL_Date_VALIDATION) BETWEEN 30 and 60 then "2-Entre 30min et 1H"
                    WHEN TIMESTAMPDIFF(MINUTE,EXPORT_ALL_Date_SOLDE,EXPORT_ALL_Date_VALIDATION) < 30 then  "1-Entre 0 et 30min"
                    END as Title'))
            ->whereNotNull('EXPORT_ALL_Date_VALIDATION')
            ->whereNotNull('EXPORT_ALL_Date_SOLDE')
            ->whereNotNull('Nom_Region')
            ->whereRaw('TIMESTAMPDIFF(MINUTE,EXPORT_ALL_Date_SOLDE,EXPORT_ALL_Date_VALIDATION) not between 360 and 1440')
            ->whereNull('isNotReady');
        if ($agentName) {
            $regions = $regions->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $regions = $regions->where('st.Nom_Region', 'like', "%$agenceCode");
        }

        $regions = applyFilter($regions, $filter);

        $regions = $regions->orderBy('Title');
        $regions = $regions->groupBy('Nom_Region', 'Title')->get();
        $keys = $regions->groupBy(['Nom_Region'])->keys();

        if (!count($regions)) {
            $data = ['data' => []];
            return $data;
        } else {
            $temp = $regions->groupBy(['Nom_Region']);
            $temp = $temp->map(function ($calls, $index) {
                $totalZone = $calls->reduce(function ($carry, $call) {
                    return $carry + $call->count;
                }, 0);
                return $calls->map(function ($call) use ($index, $totalZone) {
                    $call->$index = $totalZone == 0 ? 0.00 : round($call->count * 100 / $totalZone, 2);
                    return $call;
                });
            });
            $regions = $temp->flatten();

            $regions = $regions->groupBy('Title');
            $regions = $regions->map(function ($region) use ($keys) {
                $row = new \stdClass();
                $row->values = [];

                $col_arr = $keys->all();
                $items = $region->map(function ($call, $index) use (&$row, &$col_arr) {
                    $row->Nom_region = explode('-', $call->Title)[1];
                    $region_name = $call->Nom_Region;
                    $row->$region_name = $call->count . '|' . $call->$region_name . '%';
                    $row->values[$region_name] = $call->count;
                    $col_arr = array_diff($col_arr, [$region_name]);
                    return $row;
                });

                $_item = $items->last();

                foreach ($col_arr as $col) {
                    $_item->values[$col] = 0;
                    $_item->$col = '0';
                }

                ksort($_item->values);

                $_item->values = collect($_item->values)->values();

                return $_item;
            });
            $regions = $regions->values();

            return ['data' => $regions];
        }
    }


    public function GetColumnsGlobalDelayCall(Request $request, $filter = null)
    {
        $agentName = $request->get('agent_name');
        $agenceCode = $request->get('agence_code');
        $_route = getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);
        list($filter, $queryFilters) = makeFilterSubQuery($request, $route);

        $columns = \DB::table('stats as st')
            ->select('Nom_Region')
            ->distinct()
            ->whereNotNull('EXPORT_ALL_Date_VALIDATION')
            ->whereNotNull('EXPORT_ALL_Date_SOLDE')
            ->whereNotNull('Nom_Region')
            ->whereNull('isNotReady');

        $columns = applyFilter($columns, $filter);
        if ($agentName) {
            $columns = $columns->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $columns = $columns->where('st.Nom_Region', 'like', "%$agenceCode");
        }

        $keys = $columns->pluck('Nom_Region');


        if (!count($keys)) {
            $data = ['filter' => $filter, 'columns' => [], 'rows' => [], 'rowsFilterHeader' => ''];
            return $data;
        } else {
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
            $first->title = 'Nom region';
            $first->name = 'Nom_region';
            $first->data = 'Nom_region';
            $first->orderable = false;
            array_unshift($regions_names, $first);

            return ['filter' => $filter, 'columns' => $regions_names, 'rows' => [], 'rowsFilterHeader' => ''];
        }
    }

    public function GetGlobalDelayCall(Request $request, $filter = null)
    {
        $agentName = $request->get('agent_name');
        $agenceCode = $request->get('agence_code');
        $_route = getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);
        list($filter, $queryFilters) = makeFilterSubQuery($request, $route);

        $regions = \DB::table('stats as st')
            ->select('Nom_Region', \DB::raw('count(DISTINCT Id_Externe) as count'), \DB::raw('CASE
                        WHEN TIMESTAMPDIFF(DAY,Date_Creation,EXPORT_ALL_Date_VALIDATION) > 15 THEN "3-Superieur 15 Jours"
                        WHEN TIMESTAMPDIFF(DAY,Date_Creation,EXPORT_ALL_Date_VALIDATION) between 7 and 15   then "2-Entre une semaine et 15 jours"
                        ELSE "1-Moins une semaine"
                    END as Title')
            )->whereNotNull('EXPORT_ALL_Date_VALIDATION')
            ->whereNotNull('EXPORT_ALL_Date_SOLDE')
            ->whereNotNull('Nom_Region')
            ->whereNull('isNotReady');

        $regions = applyFilter($regions, $filter);
        if ($agentName) {
            $regions = $regions->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $regions = $regions->where('st.Nom_Region', 'like', "%$agenceCode");
        }

        $regions = $regions->orderBy('Title');
        $regions = $regions->groupBy('Nom_Region', 'Title')->get();
        $keys = $regions->groupBy(['Nom_Region'])->keys();


        if (!count($regions)) {
            $data = ['data' => []];
            return $data;
        } else {
            $temp = $regions->groupBy(['Nom_Region']);
            $temp = $temp->map(function ($calls, $index) {
                $totalZone = $calls->reduce(function ($carry, $call) {
                    return $carry + $call->count;
                }, 0);
                return $calls->map(function ($call) use ($index, $totalZone) {
                    $call->$index = $totalZone == 0 ? 0.00 : round($call->count * 100 / $totalZone, 2);
                    return $call;
                });
            });
            $regions = $temp->flatten();

            $regions = $regions->groupBy('Title');
            $regions = $regions->map(function ($region) use ($keys) {
                $row = new \stdClass();
                $row->values = [];

                $col_arr = $keys->all();
                $items = $region->map(function ($call, $index) use (&$row, &$col_arr) {
                    $row->Nom_region = explode('-', $call->Title)[1];
                    $region_name = $call->Nom_Region;
                    $row->$region_name = $call->count . '|' . $call->$region_name . '%';
                    $row->values[$region_name] = $call->count;
                    $col_arr = array_diff($col_arr, [$region_name]);
                    return $row;
                });

                $_item = $items->last();

                foreach ($col_arr as $col) {
                    $_item->values[$col] = 0;
                    $_item->$col = '0';
                }

                ksort($_item->values);

                $_item->values = collect($_item->values)->values();

                return $_item;
            });
            $regions = $regions->values();

            return ['data' => $regions];
        }
    }

    public function GetColumnsProcessingDelayCall(Request $request, $filter = null)
    {
        $agentName = $request->get('agent_name');
        $agenceCode = $request->get('agence_code');
        $_route = getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);
        list($filter, $queryFilters) = makeFilterSubQuery($request, $route);

        $columns = \DB::table('stats as st')
            ->select('EXPORT_ALL_EXTRACT_CUI')
            ->distinct()
            ->whereNotNull('Nom_Region')
            ->whereIn('EXPORT_ALL_EXTRACT_CUI', ['bf5', 'bf8'])
            ->whereNull('isNotReady');

        $columns = applyFilter($columns, $filter);
        if ($agentName) {
            $columns = $columns->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $columns = $columns->where('st.Nom_Region', 'like', "%$agenceCode");
        }

        $keys = $columns->pluck('EXPORT_ALL_EXTRACT_CUI');


        if (!count($keys)) {
            $data = ['filter' => $filter, 'columns' => [], 'rows' => [], 'rowsFilterHeader' => ''];
            return $data;
        } else {
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
            $first->title = 'EXPORT ALL EXTRACT CUI';
            $first->name = 'EXPORT_ALL_EXTRACT_CUI';
            $first->data = 'EXPORT_ALL_EXTRACT_CUI';
            $first->orderable = false;
            array_unshift($regions_names, $first);

            return ['filter' => $filter, 'columns' => $regions_names, 'rows' => [], 'rowsFilterHeader' => ''];
        }
    }

    public function GetProcessingDelayCall(Request $request, $filter = null)
    {
        $agentName = $request->get('agent_name');
        $agenceCode = $request->get('agence_code');
        $_route = getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);
        list($filter, $queryFilters) = makeFilterSubQuery($request, $route);

        $regions = \DB::table('stats as st')
            ->select('EXPORT_ALL_EXTRACT_CUI', \DB::raw('count(DISTINCT Id_Externe) as count'), \DB::raw('CASE
                        WHEN TIMESTAMPDIFF(HOUR,Date_Creation,Date_Heure_Note) > 24 THEN "3-Superieur 24 Heurs"
                        WHEN TIMESTAMPDIFF(HOUR,Date_Creation,Date_Heure_Note) between 4 and 24   then "2-Entre 4 Heurs Et 24 Heurs"
                        ELSE "1-Moins De 4 Heurs"
                    END as Title')
            )->whereNotNull('Nom_Region')
            ->whereIn('EXPORT_ALL_EXTRACT_CUI', ['bf5', 'bf8'])
            ->whereNull('isNotReady');

        $regions = applyFilter($regions, $filter);
        if ($agentName) {
            $regions = $regions->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $regions = $regions->where('st.Nom_Region', 'like', "%$agenceCode");
        }

        $regions = $regions->orderBy('Title');
        //dd($regions->groupBy('EXPORT_ALL_EXTRACT_CUI', 'Title')->toSql(),  $regions->groupBy('EXPORT_ALL_EXTRACT_CUI', 'Title')->getBindings());
        $regions = $regions->groupBy('EXPORT_ALL_EXTRACT_CUI', 'Title')->get();
        $array_regions = $regions->toArray();
        $missing = new \stdClass();
        $first = 0;
        $second = 0;
        $third = 0;
        if (!empty($array_regions)) {
            foreach ($array_regions as $object) {
                if ($object->Title === "1-Moins De 4 Heurs") {
                    $first++;
                }
                if ($object->Title === "2-Entre 4 Heurs Et 24 Heurs") {
                    $second++;
                }
                if ($object->Title === "3-Superieur 24 Heurs") {
                    $third++;
                }
            }

            if ($first == 0) {
                $missing->EXPORT_ALL_EXTRACT_CUI = "BF5";
                $missing->count = 0;
                $missing->Title = "1-Moins De 4 Heurs";
                array_push($array_regions, $missing);
            }
            if ($second == 0) {
                $missing->EXPORT_ALL_EXTRACT_CUI = "BF5";
                $missing->count = 0;
                $missing->Title = "2-Entre 4 Heurs Et 24 Heurs";
                array_push($array_regions, $missing);
            }
            if ($third == 0) {
                $missing->EXPORT_ALL_EXTRACT_CUI = "BF5";
                $missing->count = 0;
                $missing->Title = "3-Superieur 24 Heurs";
                array_push($array_regions, $missing);
            }
            $regions = collect($array_regions);
        }
        $keys = $regions->groupBy(['EXPORT_ALL_EXTRACT_CUI'])->keys();

        if (!count($regions)) {
            $data = ['data' => []];
            return $data;
        } else {
            $temp = $regions->groupBy(['EXPORT_ALL_EXTRACT_CUI']);
            $temp = $temp->map(function ($calls, $index) {
                $totalZone = $calls->reduce(function ($carry, $call) {
                    return $carry + $call->count;
                }, 0);
                return $calls->map(function ($call) use ($index, $totalZone) {
                    $call->$index = $totalZone == 0 ? 0.00 : round($call->count * 100 / $totalZone, 2);
                    return $call;
                });
            });
            $regions = $temp->flatten();
            $regions = $regions->sortby('Title');
            $regions = $regions->groupBy('Title');
            $regions = $regions->map(function ($region) use ($keys) {
                $row = new \stdClass();
                $row->values = [];

                $col_arr = $keys->all();
                $items = $region->map(function ($call, $index) use (&$row, &$col_arr) {
                    $row->EXPORT_ALL_EXTRACT_CUI = explode('-', $call->Title)[1];
                    $ALL_EXTRACT_CUI = $call->EXPORT_ALL_EXTRACT_CUI;
                    $row->$ALL_EXTRACT_CUI = $call->count . '|' . $call->$ALL_EXTRACT_CUI . '%';
                    $row->values[$ALL_EXTRACT_CUI] = $call->count;
                    $col_arr = array_diff($col_arr, [$ALL_EXTRACT_CUI]);
                    return $row;
                });

                $_item = $items->last();

                foreach ($col_arr as $col) {
                    $_item->values[$col] = 0;
                    $_item->$col = '0';
                }

                ksort($_item->values);

                $_item->values = collect($_item->values)->values();

                return $_item;
            });

            $regions = $regions->values();

            return ['data' => $regions];
        }
    }

    public function GetColumnsTypeIntervention(Request $request, $filter = null)
    {
        $agentName = $request->get('agent_name');
        $agenceCode = $request->get('agence_code');
        $_route = getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);
        list($filter, $queryFilters) = makeFilterSubQuery($request, $route);

        $columns = \DB::table('stats as st')
            ->select('produit')
            ->distinct()
            ->where('produit', '!=', '');

        $columns = applyFilter($columns, $filter);
        if ($agentName) {
            $columns = $columns->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $columns = $columns->where('st.Nom_Region', 'like', "%$agenceCode");
        }

        $keys = $columns->pluck('produit');


        if (!count($keys)) {
            $data = ['filter' => $filter, 'columns' => [], 'rows' => [], 'rowsFilterHeader' => ''];
            return $data;
        } else {
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
            $first->title = 'produit';
            $first->name = 'produit';
            $first->data = 'produit';
            $first->orderable = false;
            array_unshift($regions_names, $first);

            return ['filter' => $filter, 'columns' => $regions_names, 'rows' => [], 'rowsFilterHeader' => ''];
        }
    }

    public function getTypeIntervention(Request $request, $filter = null)
    {
        $agenceCode = $request->get('agence_code');
        $agentName = $request->get('agent_name');
        $route_request = $request->get('route');
        $_route = $route_request ?? getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);
        list($filter, $queryFilters) = makeFilterSubQuery($request, $route);

        $regions = \DB::table('stats as st')
            ->select(\DB::raw('SUBSTRING_INDEX(Code_Type_Intervention,"_",1) as Type_Intervention'), \DB::raw('count(distinct st.Id_Externe) as count'), 'produit')
            ->where('produit', '!=', '')
            ->whereNull('isNotReady');

        $regions = applyFilter($regions, $filter);
        if ($agentName) {
            $regions = $regions->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $regions = $regions->where('st.Nom_Region', 'like', "%$agenceCode");
        }

        $regions = $regions->orderBy('Type_Intervention', 'DESC');
        $regions = $regions->groupBy('Type_Intervention', 'produit')->get();
        $keys = $regions->groupBy(['produit'])->keys();


        if (!count($regions)) {
            $data = ['data' => []];
            return $data;
        } else {
            $temp = $regions->groupBy(['produit']);
            $temp = $temp->map(function ($calls, $index) {
                $totalZone = $calls->reduce(function ($carry, $call) {
                    return $carry + $call->count;
                }, 0);
                return $calls->map(function ($call) use ($index, $totalZone) {
                    $call->$index = $totalZone == 0 ? 0.00 : round($call->count * 100 / $totalZone, 2);
                    return $call;
                });
            });
            $regions = $temp->flatten();

            $regions = $regions->groupBy('Type_Intervention');
            $regions = $regions->map(function ($region) use ($keys) {
                $row = new \stdClass();
                $row->values = [];

                $col_arr = $keys->all();
                $items = $region->map(function ($call, $index) use (&$row, &$col_arr) {
                    $row->produit = $call->Type_Intervention;
                    $produit = $call->produit;
                    $row->$produit = $call->count . '|' . $call->$produit . '%';
                    $row->values[$produit] = $call->count;
                    $col_arr = array_diff($col_arr, [$produit]);
                    return $row;
                });

                $_item = $items->last();

                foreach ($col_arr as $col) {
                    $_item->values[$col] = 0;
                    $_item->$col = '0';
                }

                ksort($_item->values);

                $_item->values = collect($_item->values)->values();

                return $_item;
            });
            $regions = $regions->values();

            return ['data' => $regions];
        }
    }

    public function GetColumnsTypeInterventionGrpCall(Request $request, $filter = null)
    {
        $agentName = $request->get('agent_name');
        $agenceCode = $request->get('agence_code');
        $key_groupement = $request->get('key_groupement');
        $_route = getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);
        list($filter, $queryFilters) = makeFilterSubQuery($request, $route);

        $columns = \DB::table('stats as st')
            ->select('produit')
            ->distinct()
            ->where('produit', '!=', '')
            ->where('Code_Type_Intervention', 'like', $key_groupement . '%');

        $columns = applyFilter($columns, $filter);
        if ($agentName) {
            $columns = $columns->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $columns = $columns->where('st.Nom_Region', 'like', "%$agenceCode");
        }

        $keys = $columns->pluck('produit');


        if (!count($keys)) {
            $data = ['filter' => $filter, 'columns' => [], 'rows' => [], 'rowsFilterHeader' => ''];
            return $data;
        } else {
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
            $first->title = 'produit';
            $first->name = 'produit';
            $first->data = 'produit';
            $first->orderable = false;
            array_unshift($regions_names, $first);

            return ['filter' => $filter, 'columns' => $regions_names, 'rows' => [], 'rowsFilterHeader' => ''];
        }
    }

    public function getTypeInterventionGrpCall(Request $request, $filter = null)
    {
        $agenceCode = $request->get('agence_code');
        $agentName = $request->get('agent_name');
        $route_request = $request->get('route');
        $key_groupement = $request->get('key_groupement');
        $_route = $route_request ?? getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);
        list($filter, $queryFilters) = makeFilterSubQuery($request, $route);


        $regions = \DB::table('stats as st')
            ->select('Code_Type_Intervention', 'produit', \DB::raw('count(distinct st.Id_Externe) as count'))
            ->where('produit', '!=', '')
            ->where('Code_Type_Intervention', 'like', $key_groupement . '%')
            ->whereNull('isNotReady');

        $regions = applyFilter($regions, $filter);
        if ($agentName) {
            $regions = $regions->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $regions = $regions->where('st.Nom_Region', 'like', "%$agenceCode");
        }

        $regions = $regions->orderBy('Code_Type_Intervention');
        //dd($regions->groupBy('Code_Type_Intervention', 'Nom_Region')->toSql(), $regions->getBindings());
        $regions = $regions->groupBy('Code_Type_Intervention', 'produit')->get();
        $keys = $regions->groupBy(['produit'])->keys();


        if (!count($regions)) {
            $data = ['data' => []];
            return $data;
        } else {
            $temp = $regions->groupBy(['produit']);
            $temp = $temp->map(function ($calls, $index) {
                $totalZone = $calls->reduce(function ($carry, $call) {
                    return $carry + $call->count;
                }, 0);
                return $calls->map(function ($call) use ($index, $totalZone) {
                    $call->$index = $totalZone == 0 ? 0.00 : round($call->count * 100 / $totalZone, 2);
                    return $call;
                });
            });
            $regions = $temp->flatten();

            $regions = $regions->groupBy('Code_Type_Intervention');
            $regions = $regions->map(function ($region) use ($keys) {
                $row = new \stdClass();
                $row->values = [];

                $col_arr = $keys->all();
                $items = $region->map(function ($call, $index) use (&$row, &$col_arr) {
                    $row->produit = $call->Code_Type_Intervention;
                    $produit = $call->produit;
                    $row->$produit = $call->count . '|' . $call->$produit . '%';
                    $row->values[$produit] = $call->count;
                    $col_arr = array_diff($col_arr, [$produit]);
                    return $row;
                });

                $_item = $items->last();

                foreach ($col_arr as $col) {
                    $_item->values[$col] = 0;
                    $_item->$col = '0';
                }

                ksort($_item->values);

                $_item->values = collect($_item->values)->values();

                return $_item;
            });
            $regions = $regions->values();

            return ['data' => $regions];
        }
    }

    public function GetColumnsValTypeIntervention(Request $request, $filter = null)
    {
        $agentName = $request->get('agent_name');
        $agenceCode = $request->get('agence_code');
        $_route = getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);
        list($filter, $queryFilters) = makeFilterSubQuery($request, $route);

        $columns = \DB::table('stats as st')
            ->select('Resultat_Appel')
            ->distinct()
            ->where('Groupement', 'like', 'Appels clôture')
            ->whereIn('Resultat_Appel', ['Appels clôture - Validé conforme', 'Appels clôture - CRI non conforme']);

        $columns = applyFilter($columns, $filter);
        if ($agentName) {
            $columns = $columns->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $columns = $columns->where('st.Nom_Region', 'like', "%$agenceCode");
        }

        $keys = $columns->pluck('Resultat_Appel');


        if (!count($keys)) {
            $data = ['filter' => $filter, 'columns' => [], 'rows' => [], 'rowsFilterHeader' => ''];
            return $data;
        } else {
            $regions_names = [];
            $keys->map(function ($key, $index) use (&$regions_names) {
                $regions_names[$index + 1] = new \stdClass();
                $regions_names[$index + 1]->data = $key;
                $regions_names[$index + 1]->name = $key;
                $regions_names[$index + 1]->text = $key;
                $regions_names[$index + 1]->title = ($key == 'Appels clôture - Validé conforme') ? 'Validé Conforme' : 'Validé Non Conforme';
            });
            usort($regions_names, function ($item1, $item2) {
                return ($item1->data == $item2->data) ? 0 :
                    ($item1->data < $item2->data) ? -1 : 1;
            });
            $first = new \stdClass();
            $first->title = 'Resultat Appel';
            $first->name = 'Resultat_Appel';
            $first->data = 'Resultat_Appel';
            $first->orderable = false;
            array_unshift($regions_names, $first);

            return ['filter' => $filter, 'columns' => $regions_names, 'rows' => [], 'rowsFilterHeader' => ''];
        }
    }

    public function getValTypeIntervention(Request $request, $filter = null)
    {
        $agenceCode = $request->get('agence_code');
        $agentName = $request->get('agent_name');
        $route_request = $request->get('route');
        $_route = $route_request ?? getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);
        list($filter, $queryFilters) = makeFilterSubQuery($request, $route);

        $regions = \DB::table('stats as st')
            ->select(\DB::raw('SUBSTRING_INDEX(Code_Type_Intervention,"_",1) as Type_Intervention'), \DB::raw('count(distinct st.Id_Externe) as count'), 'Resultat_Appel')
            ->where('Groupement', 'like', 'Appels clôture')
            ->whereIn('Resultat_Appel', ['Appels clôture - Validé conforme', 'Appels clôture - CRI non conforme'])
            ->whereNull('isNotReady');

        $regions = applyFilter($regions, $filter);
        if ($agentName) {
            $regions = $regions->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $regions = $regions->where('st.Nom_Region', 'like', "%$agenceCode");
        }

        $regions = $regions->orderBy('Type_Intervention', 'DESC');
        $regions = $regions->groupBy('Type_Intervention', 'Resultat_Appel')->get();
        $keys = $regions->groupBy(['Resultat_Appel'])->keys();


        if (!count($regions)) {
            $data = ['data' => []];
            return $data;
        } else {
            $temp = $regions->groupBy(['Resultat_Appel']);
            $temp = $temp->map(function ($calls, $index) {
                $totalZone = $calls->reduce(function ($carry, $call) {
                    return $carry + $call->count;
                }, 0);
                return $calls->map(function ($call) use ($index, $totalZone) {
                    $call->$index = $totalZone == 0 ? 0.00 : round($call->count * 100 / $totalZone, 2);
                    return $call;
                });
            });
            $regions = $temp->flatten();

            $regions = $regions->groupBy('Type_Intervention');
            $regions = $regions->map(function ($region) use ($keys) {
                $row = new \stdClass();
                $row->values = [];

                $col_arr = $keys->all();
                $items = $region->map(function ($call, $index) use (&$row, &$col_arr) {
                    $row->Resultat_Appel = $call->Type_Intervention;
                    $Resultat_Appel = $call->Resultat_Appel;
                    $row->$Resultat_Appel = $call->count . '|' . $call->$Resultat_Appel . '%';
                    $row->values[$Resultat_Appel] = $call->count;
                    $col_arr = array_diff($col_arr, [$Resultat_Appel]);
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

            return ['data' => $regions];
        }
    }

    public function GetColumnsValTypeInterventionGrpCall(Request $request, $filter = null)
    {
        $agentName = $request->get('agent_name');
        $agenceCode = $request->get('agence_code');
        $_route = getRoute(Route::current());
        $key_groupement = $request->get('key_groupement');
        $route = str_replace('/columns', '', $_route);
        list($filter, $queryFilters) = makeFilterSubQuery($request, $route);

        $columns = \DB::table('stats as st')
            ->select('Resultat_Appel')
            ->distinct()
            ->where('Groupement', 'like', 'Appels clôture')
            ->whereIn('Resultat_Appel', ['Appels clôture - Validé conforme', 'Appels clôture - CRI non conforme'])
            ->where('Code_Type_Intervention', 'like', $key_groupement . '%');

        $columns = applyFilter($columns, $filter);
        if ($agentName) {
            $columns = $columns->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $columns = $columns->where('st.Nom_Region', 'like', "%$agenceCode");
        }

        $keys = $columns->pluck('Resultat_Appel');

        if (!count($keys)) {
            $data = ['filter' => $filter, 'columns' => [], 'rows' => [], 'rowsFilterHeader' => ''];
            return $data;
        } else {
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
            $first->title = 'Code Type Intervention';
            $first->name = 'Code_Type_Intervention';
            $first->data = 'Code_Type_Intervention';
            $first->orderable = false;
            array_unshift($regions_names, $first);

            return ['filter' => $filter, 'columns' => $regions_names, 'rows' => [], 'rowsFilterHeader' => ''];
        }
    }

    public function getValTypeInterventionGrpCall(Request $request, $filter = null)
    {
        $agenceCode = $request->get('agence_code');
        $agentName = $request->get('agent_name');
        $route_request = $request->get('route');
        $key_groupement = $request->get('key_groupement');
        $_route = $route_request ?? getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);
        list($filter, $queryFilters) = makeFilterSubQuery($request, $route);


        $regions = \DB::table('stats as st')
            ->select('Code_Type_Intervention', 'Resultat_Appel', \DB::raw('count(distinct st.Id_Externe) as count'))
            ->where('Groupement', 'like', 'Appels clôture')
            ->whereIn('Resultat_Appel', ['Appels clôture - Validé conforme', 'Appels clôture - CRI non conforme'])
            ->where('Code_Type_Intervention', 'like', $key_groupement . '%')
            ->whereNull('isNotReady');

        $regions = applyFilter($regions, $filter);
        if ($agentName) {
            $regions = $regions->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $regions = $regions->where('st.Nom_Region', 'like', "%$agenceCode");
        }

        $regions = $regions->orderBy('Code_Type_Intervention');
        $regions = $regions->groupBy('Code_Type_Intervention', 'Resultat_Appel')->get();
        $keys = $regions->groupBy(['Resultat_Appel'])->keys();


        if (!count($regions)) {
            $data = ['data' => []];
            return $data;
        } else {
            $temp = $regions->groupBy(['Resultat_Appel']);
            $temp = $temp->map(function ($calls, $index) {
                $totalZone = $calls->reduce(function ($carry, $call) {
                    return $carry + $call->count;
                }, 0);
                return $calls->map(function ($call) use ($index, $totalZone) {
                    $call->$index = $totalZone == 0 ? 0.00 : round($call->count * 100 / $totalZone, 2);
                    return $call;
                });
            });
            $regions = $temp->flatten();

            $regions = $regions->groupBy('Code_Type_Intervention');
            $regions = $regions->map(function ($region) use ($keys) {
                $row = new \stdClass();
                $row->values = [];

                $col_arr = $keys->all();
                $items = $region->map(function ($call, $index) use (&$row, &$col_arr) {
                    $row->Code_Type_Intervention = $call->Code_Type_Intervention;
                    $Resultat_Appel = $call->Resultat_Appel;
                    $row->$Resultat_Appel = $call->count . '|' . $call->$Resultat_Appel . '%';
                    $row->values[$Resultat_Appel] = $call->count;
                    $col_arr = array_diff($col_arr, [$Resultat_Appel]);
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

            return ['data' => $regions];
        }
    }

    public function GetColumnsRepTypeIntervention(Request $request, $filter = null)
    {
        $agentName = $request->get('agent_name');
        $agenceCode = $request->get('agence_code');
        $_route = getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);
        list($filter, $queryFilters) = makeFilterSubQuery($request, $route);

        $columns = \DB::table('stats as st')
            ->select('Code_Intervention')
            ->distinct()
            ->where('Groupement', 'like', 'Appels clôture');

        $columns = applyFilter($columns, $filter);
        if ($agentName) {
            $columns = $columns->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $columns = $columns->where('st.Nom_Region', 'like', "%$agenceCode");
        }

        $keys = $columns->pluck('Code_Intervention');


        if (!count($keys)) {
            $data = ['filter' => $filter, 'columns' => [], 'rows' => [], 'rowsFilterHeader' => ''];
            return $data;
        } else {
            $regions_names = [];
            $keys->map(function ($key, $index) use (&$regions_names) {
                $regions_names[$index + 1] = new \stdClass();
                $regions_names[$index + 1]->data =
                $regions_names[$index + 1]->name =
                $regions_names[$index + 1]->text =
                $regions_names[$index + 1]->title = $key ?? '';
            });
            usort($regions_names, function ($item1, $item2) {
                return ($item1->data == $item2->data) ? 0 :
                    ($item1->data < $item2->data) ? -1 : 1;
            });
            $first = new \stdClass();
            $first->title = 'Code Intervention';
            $first->name = 'Code_Intervention';
            $first->data = 'Code_Intervention';
            $first->orderable = false;
            array_unshift($regions_names, $first);

            return ['filter' => $filter, 'columns' => $regions_names, 'rows' => [], 'rowsFilterHeader' => ''];
        }
    }

    public function getRepTypeIntervention(Request $request, $filter = null)
    {
        $agenceCode = $request->get('agence_code');
        $agentName = $request->get('agent_name');
        $route_request = $request->get('route');
        $_route = $route_request ?? getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);
        list($filter, $queryFilters) = makeFilterSubQuery($request, $route);

        $regions = \DB::table('stats as st')
            ->select(\DB::raw('SUBSTRING_INDEX(Code_Type_Intervention,"_",1) as Type_Intervention'), \DB::raw('count(distinct st.Id_Externe) as count'), 'Code_Intervention')
            ->where('Groupement', '=', 'Appels clôture')
            ->whereNull('isNotReady');

        $regions = applyFilter($regions, $filter);
        if ($agentName) {
            $regions = $regions->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $regions = $regions->where('st.Nom_Region', 'like', "%$agenceCode");
        }

        $regions = $regions->orderBy('Code_Intervention', 'DESC');
        $regions = $regions->groupBy('Type_Intervention', 'Code_Intervention')->get();
        $keys = $regions->groupBy(['Code_Intervention'])->keys();


        if (!count($regions)) {
            $data = ['data' => []];
            return $data;
        } else {
            $temp = $regions->groupBy(['Code_Intervention']);
            $temp = $temp->map(function ($calls, $index) {
                $totalZone = $calls->reduce(function ($carry, $call) {
                    return $carry + $call->count;
                }, 0);
                return $calls->map(function ($call) use ($index, $totalZone) {
                    $call->$index = $totalZone == 0 ? 0.00 : round($call->count * 100 / $totalZone, 2);
                    return $call;
                });
            });
            $regions = $temp->flatten();
            $regions = $regions->groupBy('Type_Intervention');
            $regions = $regions->map(function ($region) use ($keys) {
                $row = new \stdClass();
                $row->values = [];

                $col_arr = $keys->all();

                $items = $region->map(function ($call, $index) use (&$row, &$col_arr) {
                    $row->Code_Intervention = $call->Type_Intervention;
                    $Code_Intervention = $call->Code_Intervention;
                    $row->$Code_Intervention = $call->count . '|' . $call->$Code_Intervention . '%';
                    $row->values[$Code_Intervention ?? ''] = $call->count;
                    $col_arr = array_diff($col_arr, [$Code_Intervention]);
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

            return ['data' => $regions];
        }
    }

    public function GetColumnsRepTypeInterventionGrpCall(Request $request, $filter = null)
    {
        $agentName = $request->get('agent_name');
        $agenceCode = $request->get('agence_code');
        $key_groupement = $request->get('key_groupement');
        $_route = getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);
        list($filter, $queryFilters) = makeFilterSubQuery($request, $route);

        $columns = \DB::table('stats as st')
            ->select('Code_Intervention')
            ->distinct()
            ->where('Groupement', 'like', 'Appels clôture')
            ->where('Code_Type_Intervention', 'like', $key_groupement . '%');

        $columns = applyFilter($columns, $filter);
        if ($agentName) {
            $columns = $columns->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $columns = $columns->where('st.Nom_Region', 'like', "%$agenceCode");
        }

        $keys = $columns->pluck('Code_Intervention');

        if (!count($keys)) {
            $data = ['filter' => $filter, 'columns' => [], 'rows' => [], 'rowsFilterHeader' => ''];
            return $data;
        } else {
            $regions_names = [];
            $keys->map(function ($key, $index) use (&$regions_names) {
                $regions_names[$index + 1] = new \stdClass();
                $regions_names[$index + 1]->data =
                $regions_names[$index + 1]->name =
                $regions_names[$index + 1]->text =
                $regions_names[$index + 1]->title = $key ?? '';
            });
            usort($regions_names, function ($item1, $item2) {
                return ($item1->data == $item2->data) ? 0 :
                    ($item1->data < $item2->data) ? -1 : 1;
            });
            $first = new \stdClass();
            $first->title = 'Code Type Intervention';
            $first->name = 'Code_Type_Intervention';
            $first->data = 'Code_Type_Intervention';
            $first->orderable = false;
            array_unshift($regions_names, $first);

            return ['filter' => $filter, 'columns' => $regions_names, 'rows' => [], 'rowsFilterHeader' => ''];
        }
    }

    public function getRepTypeInterventionGrpCall(Request $request, $filter = null)
    {
        $agenceCode = $request->get('agence_code');
        $agentName = $request->get('agent_name');
        $route_request = $request->get('route');
        $key_groupement = $request->get('key_groupement');
        $_route = $route_request ?? getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);
        list($filter, $queryFilters) = makeFilterSubQuery($request, $route);


        $regions = \DB::table('stats as st')
            ->select('Code_Type_Intervention', 'Code_Intervention', \DB::raw('count(distinct st.Id_Externe) as count'))
            ->where('Groupement', 'like', 'Appels clôture')
            ->where('Code_Type_Intervention', 'like', $key_groupement . '%')
            ->whereNull('isNotReady');

        $regions = applyFilter($regions, $filter);
        if ($agentName) {
            $regions = $regions->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $regions = $regions->where('st.Nom_Region', 'like', "%$agenceCode");
        }

        $regions = $regions->orderBy('Code_Type_Intervention');
        //dd($regions->groupBy('Code_Type_Intervention', 'Nom_Region')->toSql(), $regions->getBindings());
        $regions = $regions->groupBy('Code_Type_Intervention', 'Code_Intervention')->get();
        $keys = $regions->groupBy(['Code_Intervention'])->keys();


        if (!count($regions)) {
            $data = ['data' => []];
            return $data;
        } else {
            $temp = $regions->groupBy(['Code_Intervention']);
            $temp = $temp->map(function ($calls, $index) {
                $totalZone = $calls->reduce(function ($carry, $call) {
                    return $carry + $call->count;
                }, 0);
                return $calls->map(function ($call) use ($index, $totalZone) {
                    $call->$index = $totalZone == 0 ? 0.00 : round($call->count * 100 / $totalZone, 2);
                    return $call;
                });
            });
            $regions = $temp->flatten();

            $regions = $regions->groupBy('Code_Type_Intervention');
            $regions = $regions->map(function ($region) use ($keys) {
                $row = new \stdClass();
                $row->values = [];

                $col_arr = $keys->all();
                $items = $region->map(function ($call, $index) use (&$row, &$col_arr) {
                    $row->Code_Type_Intervention = $call->Code_Type_Intervention;
                    $Code_Intervention = $call->Code_Intervention;
                    $row->$Code_Intervention = $call->count . '|' . $call->$Code_Intervention . '%';
                    $row->values[$Code_Intervention ?? ''] = $call->count;
                    $col_arr = array_diff($col_arr, [$Code_Intervention]);
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
            return ['data' => $regions];
        }
    }

    public function GetColumnsRepJoiDepartement(Request $request, $filter = null)
    {
        $agentName = $request->get('agent_name');
        $agenceCode = $request->get('agence_code');
        $_route = getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);
        list($filter, $queryFilters) = makeFilterSubQuery($request, $route);

        $columns = \DB::table('stats as st')
            ->select(\DB::raw('SUBSTRING(Nom_Agence, -3, 2) as departement'))
            ->distinct()
            ->where('Groupement', 'like', 'Appels préalables')
            ->whereIn('produit', ['CUIVRE', 'FTTH', 'CUIVRE/FTTH']);

        $columns = applyFilter($columns, $filter);
        if ($agentName) {
            $columns = $columns->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $columns = $columns->where('st.Nom_Region', 'like', "%$agenceCode");
        }

        $keys = $columns->pluck('departement');


        if (!count($keys)) {
            $data = ['filter' => $filter, 'columns' => [], 'rows' => [], 'rowsFilterHeader' => ''];
            return $data;
        } else {
            $regions_names = [];
            $keys->map(function ($key, $index) use (&$regions_names) {
                $regions_names[$index + 1] = new \stdClass();
                $regions_names[$index + 1]->data =
                $regions_names[$index + 1]->name =
                $regions_names[$index + 1]->text =
                $regions_names[$index + 1]->title = $key ?? '';
            });
            usort($regions_names, function ($item1, $item2) {
                return ($item1->data == $item2->data) ? 0 :
                    ($item1->data < $item2->data) ? -1 : 1;
            });
            $first = new \stdClass();
            $first->title = 'departement';
            $first->name = 'departement';
            $first->data = 'departement';
            $first->orderable = false;
            array_unshift($regions_names, $first);

            return ['filter' => $filter, 'columns' => $regions_names, 'rows' => [], 'rowsFilterHeader' => ''];
        }
    }

    public function getRepJoiDepartement(Request $request, $filter = null)
    {
        $agenceCode = $request->get('agence_code');
        $agentName = $request->get('agent_name');
        $route_request = $request->get('route');
        $_route = $route_request ?? getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);
        list($filter, $queryFilters) = makeFilterSubQuery($request, $route);

        $regions = \DB::table('stats as st')
            ->select(\DB::raw('SUBSTRING_INDEX(Code_Type_Intervention,"_",1) as Type_Intervention'), \DB::raw('SUBSTRING(Nom_Agence, -3, 2) as departement'),
                \DB::raw('count(distinct st.Id_Externe) as count'))
            ->where('Groupement', 'like', 'Appels préalables')
            ->whereIn('produit', ['CUIVRE', 'FTTH', 'CUIVRE/FTTH'])
            ->whereNull('isNotReady');

        $regions = applyFilter($regions, $filter);
        if ($agentName) {
            $regions = $regions->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $regions = $regions->where('st.Nom_Region', 'like', "%$agenceCode");
        }

        $regions = $regions->orderBy('Type_Intervention', 'DESC');
        //dd($regions->groupBy('Type_Intervention', 'departement')->toSql(),$regions->getBindings());
        $regions = $regions->groupBy('Type_Intervention', 'departement')->get();
        $keys = $regions->groupBy(['departement'])->keys();


        if (!count($regions)) {
            $data = ['data' => []];
            return $data;
        } else {
            $temp = $regions->groupBy(['departement']);
            $temp = $temp->map(function ($calls, $index) {
                $totalZone = $calls->reduce(function ($carry, $call) {
                    return $carry + $call->count;
                }, 0);
                return $calls->map(function ($call) use ($index, $totalZone) {
                    $call->$index = $totalZone == 0 ? 0.00 : round($call->count * 100 / $totalZone, 2);
                    return $call;
                });
            });
            $regions = $temp->flatten();

            $regions = $regions->groupBy('Type_Intervention');
            $regions = $regions->map(function ($region) use ($keys) {
                $row = new \stdClass();
                $row->values = [];

                $col_arr = $keys->all();
                $items = $region->map(function ($call, $index) use (&$row, &$col_arr) {
                    $row->departement = $call->Type_Intervention;
                    $departement = $call->departement;
                    $row->$departement = $call->count . '|' . $call->$departement . '%';
                    $row->values[$departement ?? ''] = $call->count;
                    $col_arr = array_diff($col_arr, [$departement]);
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

            return ['data' => $regions];
        }
    }

    public function GetColumnsRepJoiDepartementGrpCall(Request $request, $filter = null)
    {
        $agentName = $request->get('agent_name');
        $agenceCode = $request->get('agence_code');
        $_route = getRoute(Route::current());
        $key_groupement = $request->get('key_groupement');
        $route = str_replace('/columns', '', $_route);
        list($filter, $queryFilters) = makeFilterSubQuery($request, $route);

        $columns = \DB::table('stats as st')
            ->select(\DB::raw('SUBSTRING(Nom_Agence, -3, 2) as departement'))
            ->distinct()
            ->where('Groupement', 'like', 'Appels préalables')
            ->whereIn('produit', ['CUIVRE', 'FTTH', 'CUIVRE/FTTH'])
            ->where('Code_Type_Intervention', 'like', $key_groupement . '%');

        $columns = applyFilter($columns, $filter);
        if ($agentName) {
            $columns = $columns->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $columns = $columns->where('st.Nom_Region', 'like', "%$agenceCode");
        }

        $keys = $columns->pluck('departement');

        if (!count($keys)) {
            $data = ['filter' => $filter, 'columns' => [], 'rows' => [], 'rowsFilterHeader' => ''];
            return $data;
        } else {
            $regions_names = [];
            $keys->map(function ($key, $index) use (&$regions_names) {
                $regions_names[$index + 1] = new \stdClass();
                $regions_names[$index + 1]->data =
                $regions_names[$index + 1]->name =
                $regions_names[$index + 1]->text =
                $regions_names[$index + 1]->title = $key ?? '';
            });
            usort($regions_names, function ($item1, $item2) {
                return ($item1->data == $item2->data) ? 0 :
                    ($item1->data < $item2->data) ? -1 : 1;
            });
            $first = new \stdClass();
            $first->title = 'departement';
            $first->name = 'departement';
            $first->data = 'departement';
            $first->orderable = false;
            array_unshift($regions_names, $first);

            return ['filter' => $filter, 'columns' => $regions_names, 'rows' => [], 'rowsFilterHeader' => ''];
        }
    }

    public function getRepJoiDepartementGrpCall(Request $request, $filter = null)
    {
        $agenceCode = $request->get('agence_code');
        $agentName = $request->get('agent_name');
        $route_request = $request->get('route');
        $key_groupement = $request->get('key_groupement');
        $_route = $route_request ?? getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);
        list($filter, $queryFilters) = makeFilterSubQuery($request, $route);


        $regions = \DB::table('stats as st')
            ->select(\DB::raw('SUBSTRING(Nom_Agence, -3, 2) as departement'), 'produit', \DB::raw('count(distinct st.Id_Externe) as count'))
            ->where('Groupement', 'like', 'Appels préalables')
            ->whereIn('produit', ['CUIVRE', 'FTTH', 'CUIVRE/FTTH'])
            ->where('Code_Type_Intervention', 'like', $key_groupement . '%')
            ->whereNull('isNotReady');

        $regions = applyFilter($regions, $filter);
        if ($agentName) {
            $regions = $regions->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $regions = $regions->where('st.Nom_Region', 'like', "%$agenceCode");
        }

        $regions = $regions->orderBy('produit');
        //dd($regions->groupBy('Code_Type_Intervention', 'Nom_Region')->toSql(), $regions->getBindings());
        $regions = $regions->groupBy('produit', 'departement')->get();
        $keys = $regions->groupBy(['departement'])->keys();


        if (!count($regions)) {
            $data = ['data' => []];
            return $data;
        } else {
            $temp = $regions->groupBy(['departement']);
            $temp = $temp->map(function ($calls, $index) {
                $totalZone = $calls->reduce(function ($carry, $call) {
                    return $carry + $call->count;
                }, 0);
                return $calls->map(function ($call) use ($index, $totalZone) {
                    $call->$index = $totalZone == 0 ? 0.00 : round($call->count * 100 / $totalZone, 2);
                    return $call;
                });
            });
            $regions = $temp->flatten();

            $regions = $regions->groupBy('produit');
            $regions = $regions->map(function ($region) use ($keys) {
                $row = new \stdClass();
                $row->values = [];

                $col_arr = $keys->all();
                $items = $region->map(function ($call, $index) use (&$row, &$col_arr) {
                    $row->departement = $call->produit;
                    $departement = $call->departement;
                    $row->$departement = $call->count . '|' . $call->$departement . '%';
                    $row->values[$departement ?? ''] = $call->count;
                    $col_arr = array_diff($col_arr, [$departement]);
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

            return ['data' => $regions];
        }
    }

    public function GetColumnsRepJoiAutreDepartement(Request $request, $filter = null)
    {
        $agentName = $request->get('agent_name');
        $agenceCode = $request->get('agence_code');
        $_route = getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);
        list($filter, $queryFilters) = makeFilterSubQuery($request, $route);

        $columns = \DB::table('stats as st')
            ->select(\DB::raw('Gpmt_Appel_Pre'))
            ->distinct()
            ->where('Groupement', 'like', 'Appels préalables')
            ->whereIn('Gpmt_Appel_Pre', ['Joignable', 'Injoignable']);

        $columns = applyFilter($columns, $filter);
        if ($agentName) {
            $columns = $columns->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $columns = $columns->where('st.Nom_Region', 'like', "%$agenceCode");
        }

        $keys = $columns->pluck('Gpmt_Appel_Pre');


        if (!count($keys)) {
            $data = ['filter' => $filter, 'columns' => [], 'rows' => [], 'rowsFilterHeader' => ''];
            return $data;
        } else {
            $regions_names = [];
            $keys->map(function ($key, $index) use (&$regions_names) {
                $regions_names[$index + 1] = new \stdClass();
                $regions_names[$index + 1]->data =
                $regions_names[$index + 1]->name =
                $regions_names[$index + 1]->text =
                $regions_names[$index + 1]->title = $key ?? '';
            });
            usort($regions_names, function ($item1, $item2) {
                return ($item1->data == $item2->data) ? 0 :
                    ($item1->data < $item2->data) ? -1 : 1;
            });
            $first = new \stdClass();
            $first->title = 'Joignabilité';
            $first->name = 'Gpmt_Appel_Pre';
            $first->data = 'Gpmt_Appel_Pre';
            $first->orderable = false;
            array_unshift($regions_names, $first);

            return ['filter' => $filter, 'columns' => $regions_names, 'rows' => [], 'rowsFilterHeader' => ''];
        }
    }

    public function getRepJoiAutreDepartement(Request $request, $filter = null)
    {
        $agenceCode = $request->get('agence_code');
        $agentName = $request->get('agent_name');
        $route_request = $request->get('route');
        $_route = $route_request ?? getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);
        list($filter, $queryFilters) = makeFilterSubQuery($request, $route);

        $regions = \DB::table('stats as st')
            ->select(\DB::raw('SUBSTRING_INDEX(Code_Type_Intervention,"_",1) as Type_Intervention'), 'Gpmt_Appel_Pre',
                \DB::raw('count(distinct st.Id_Externe) as count'))
            ->where('Groupement', 'like', 'Appels préalables')
            ->whereIn('Gpmt_Appel_Pre', ['Joignable', 'Injoignable'])
            ->whereNull('isNotReady');

        $regions = applyFilter($regions, $filter);
        if ($agentName) {
            $regions = $regions->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $regions = $regions->where('st.Nom_Region', 'like', "%$agenceCode");
        }

        $regions = $regions->orderBy('Type_Intervention');
        $regions = $regions->groupBy('Type_Intervention', 'Gpmt_Appel_Pre')->get();
        $keys = $regions->groupBy(['Gpmt_Appel_Pre'])->keys();


        if (!count($regions)) {
            $data = ['data' => []];
            return $data;
        } else {
            $temp = $regions->groupBy(['Gpmt_Appel_Pre']);
            $temp = $temp->map(function ($calls, $index) {
                $totalZone = $calls->reduce(function ($carry, $call) {
                    return $carry + $call->count;
                }, 0);
                return $calls->map(function ($call) use ($index, $totalZone) {
                    $call->$index = $totalZone == 0 ? 0.00 : round($call->count * 100 / $totalZone, 2);
                    return $call;
                });
            });
            $regions = $temp->flatten();

            $regions = $regions->groupBy('Type_Intervention');
            $regions = $regions->map(function ($region) use ($keys) {
                $row = new \stdClass();
                $row->values = [];

                $col_arr = $keys->all();
                $items = $region->map(function ($call, $index) use (&$row, &$col_arr) {
                    $row->Gpmt_Appel_Pre = $call->Type_Intervention;
                    $Gpmt_Appel_Pre = $call->Gpmt_Appel_Pre;
                    $row->$Gpmt_Appel_Pre = $call->count . '|' . $call->$Gpmt_Appel_Pre . '%';
                    $row->values[$Gpmt_Appel_Pre ?? ''] = $call->count;
                    $col_arr = array_diff($col_arr, [$Gpmt_Appel_Pre]);
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

            return ['data' => $regions];
        }
    }

    public function GetColumnsRepJoiAutreDepartementGrpCall(Request $request, $filter = null)
    {
        $agentName = $request->get('agent_name');
        $agenceCode = $request->get('agence_code');
        $_route = getRoute(Route::current());
        $key_groupement = $request->get('key_groupement');
        $route = str_replace('/columns', '', $_route);
        list($filter, $queryFilters) = makeFilterSubQuery($request, $route);

        $columns = \DB::table('stats as st')
            ->select(\DB::raw('Gpmt_Appel_Pre'))
            ->distinct()
            ->where('Groupement', 'like', 'Appels préalables')
            ->whereIn('Gpmt_Appel_Pre', ['Joignable', 'Injoignable'])
            ->where('Code_Type_Intervention', 'like', $key_groupement . '%');

        $columns = applyFilter($columns, $filter);
        if ($agentName) {
            $columns = $columns->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $columns = $columns->where('st.Nom_Region', 'like', "%$agenceCode");
        }

        $keys = $columns->pluck('Gpmt_Appel_Pre');

        if (!count($keys)) {
            $data = ['filter' => $filter, 'columns' => [], 'rows' => [], 'rowsFilterHeader' => ''];
            return $data;
        } else {
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
            $first->title = 'joignabilité';
            $first->name = 'Gpmt_Appel_Pre';
            $first->data = 'Gpmt_Appel_Pre';
            $first->orderable = false;
            array_unshift($regions_names, $first);

            return ['filter' => $filter, 'columns' => $regions_names, 'rows' => [], 'rowsFilterHeader' => ''];
        }
    }

    public function getRepJoiAutreDepartementGrpCall(Request $request, $filter = null)
    {
        $agenceCode = $request->get('agence_code');
        $agentName = $request->get('agent_name');
        $route_request = $request->get('route');
        $key_groupement = $request->get('key_groupement');
        $_route = $route_request ?? getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);
        list($filter, $queryFilters) = makeFilterSubQuery($request, $route);


        $regions = \DB::table('stats as st')
            ->select('Gpmt_Appel_Pre', 'produit', \DB::raw('count(distinct st.Id_Externe) as count'))
            ->where('Groupement', 'like', 'Appels préalables')
            ->whereIn('produit', ['CUIVRE', 'FTTH', 'CUIVRE/FTTH'])
            ->where('Code_Type_Intervention', 'like', $key_groupement . '%')
            ->whereNull('isNotReady');

        $regions = applyFilter($regions, $filter);
        if ($agentName) {
            $regions = $regions->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $regions = $regions->where('st.Nom_Region', 'like', "%$agenceCode");
        }

        $regions = $regions->orderBy('produit');
        //dd($regions->groupBy('Code_Type_Intervention', 'Nom_Region')->toSql(), $regions->getBindings());
        $regions = $regions->groupBy('produit', 'Gpmt_Appel_Pre')->get();
        $keys = $regions->groupBy(['Gpmt_Appel_Pre'])->keys();


        if (!count($regions)) {
            $data = ['data' => []];
            return $data;
        } else {
            $temp = $regions->groupBy(['Gpmt_Appel_Pre']);
            $temp = $temp->map(function ($calls, $index) {
                $totalZone = $calls->reduce(function ($carry, $call) {
                    return $carry + $call->count;
                }, 0);
                return $calls->map(function ($call) use ($index, $totalZone) {
                    $call->$index = $totalZone == 0 ? 0.00 : round($call->count * 100 / $totalZone, 2);
                    return $call;
                });
            });
            $regions = $temp->flatten();

            $regions = $regions->groupBy('produit');
            $regions = $regions->map(function ($region) use ($keys) {
                $row = new \stdClass();
                $row->values = [];

                $col_arr = $keys->all();
                $items = $region->map(function ($call, $index) use (&$row, &$col_arr) {
                    $row->Gpmt_Appel_Pre = $call->produit;
                    $Gpmt_Appel_Pre = $call->Gpmt_Appel_Pre;
                    $row->$Gpmt_Appel_Pre = $call->count . '|' . $call->$Gpmt_Appel_Pre . '%';
                    $row->values[$Gpmt_Appel_Pre] = $call->count;
                    $col_arr = array_diff($col_arr, [$Gpmt_Appel_Pre]);
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

            return ['data' => $regions];
        }
    }

    public function GetColumnsGlobalView(Request $request)
    {
        $agenceCode = $request->get('agence_code');
        $agentName = $request->get('agent_name');
        $key_groupement = $request->get('key_groupement');
        $key_groupement = $key_groupement ? clean($key_groupement) : null;

        $_route = getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);
        list($filter, $queryFilters) = makeFilterSubQuery($request, $route, 'Nom_Region');


        $results = \DB::table('stats as st')
            ->select('st.Groupement')
            ->distinct()
            ->whereNotNull('st.Groupement')
            ->whereNotNull('st.Nom_Region')
            ->where('st.Groupement', 'not like', 'Non renseigné')
            ->where('st.Groupement', 'not like', 'Appels post')
            ->where('Code_Type_Intervention', 'NOT LIKE', '%AUTRE%')
            ->where('st.Produit', '!=', '')
            ->whereNull('isNotReady');
        $results = applyFilter($results, $filter, 'Nom_Region');

        if ($key_groupement) {
            $results = $results->where('key_groupement', 'like', $key_groupement);
        }
        if ($agentName) {
            $results = $results->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $results = $results->where('st.Nom_Region', 'like', "%$agenceCode");
        }

        $rowsKeys = [];
//        $rowsKeys = \DB::table('stats as st')
//            ->select('st.Nom_Region')
//            ->distinct()
//            ->whereNotNull('st.Groupement')
//            ->whereNotNull('st.Nom_Region')
//            ->where('st.Groupement', 'not like', 'Non renseigné')
//            ->where('st.Groupement', 'not like', 'Appels post')
//            ->where('Type_Note', 'like', 'CAM')
//            ->whereNull('isNotReady');
//        if ($key_groupement) {
//            $rowsKeys = $rowsKeys->where('key_groupement', 'like', $key_groupement);
//        }
//        if ($agentName) {
//            $rowsKeys = $rowsKeys->where('st.Utilisateur', $agentName);
//        }
//        if ($agenceCode) {
//            $rowsKeys = $rowsKeys->where('st.Nom_Region', 'like', "%$agenceCode");
//        }
//        $rowsKeys = $rowsKeys->pluck('Nom_Region');

        $keys = $results->pluck('Groupement');

        if (!count($keys)) {
            $data = ['filter' => $filter, 'columns' => [], 'rows' => $rowsKeys, 'rowsFilterHeader' => ''];
            return $data;
        } else {

            $keys = sortGroupementColumns($keys);

            $column_names = [];
            $keys->map(function ($key, $index) use (&$column_names) {
                $column_names[$index + 1] = new \stdClass();
                $column_names[$index + 1]->text =
                $column_names[$index + 1]->data =
                $column_names[$index + 1]->name =
                $column_names[$index + 1]->title = $key;
            });

//            $header = new \stdClass();
//            $header->title = 'Code Type Intervention';
//            $header->text = 'Code Type Intervention';
//            $header->name = $header->data = 'Code_Type_Intervention';
//            $header->orderable = false;

            $first = new \stdClass();
            $first->title = 'Produit';
            $first->text = 'Produit';
            $first->name = $first->data = 'Produit';
            $first->orderable = false;
            $last = new \stdClass();
            $last->data =
            $last->name =
            $last->text =
            $last->title = 'total';
            array_unshift($column_names, $first);
//            array_unshift($column_names, $header);
            array_push($column_names, $last);

            $data = ['filter' => $filter, 'columns' => $column_names, 'rows' => $rowsKeys, 'rowsFilterHeader' => ''];
            return $data;
        }
    }

    public function GetDataGlobalView(Request $request)
    {
        $agenceCode = $request->get('agence_code');
        $agentName = $request->get('agent_name');
        $key_groupement = $request->get('key_groupement');
        $key_groupement = $key_groupement ? clean($key_groupement) : null;

        $_route = getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);
        list($filter, $queryFilters) = makeFilterSubQuery($request, $route, 'Nom_Region');


        $results = \DB::table('stats as st')
            ->select(\DB::raw('case
                       when Code_Type_Intervention like "%MAINTENANCE%" then "MAINTENANCE"
                       when Code_Type_Intervention like "%PRODUCTION%" then "PRODUCTION"
                       end as "cti"'), 'Produit', 'st.Groupement', \DB::raw('count(distinct st.Id_Externe) as total'))
            ->join(\DB::raw('(SELECT Id_Externe, MAX(Date_Heure_Note) AS MaxDateTime,
                groupement , Nom_Region
            FROM stats
             where Groupement IS NOT NULL
             AND Nom_Region IS NOT NULL
             AND Code_Type_Intervention NOT LIKE "%AUTRE%"
             AND Produit != ""
             AND Groupement not LIKE "Non renseigné"
             AND Groupement not LIKE "Appels post"' .
                ' and ' . $queryFilters .
                ($agentName ? 'and Utilisateur like "' . $agentName . '"' : '') .
                ($agenceCode ? 'and Nom_Region like "%' . $agenceCode . '"' : '') .
                ($key_groupement ? 'and key_groupement like "' . $key_groupement . '"' : '') .
                ' and isNotReady is null ' .
                ' GROUP BY Id_Externe, Groupement, Nom_Region ) groupedst'),
                function ($join) {
                    $join->on('st.Id_Externe', '=', 'groupedst.Id_Externe');
                    $join->on('st.Date_Heure_Note', '=', 'groupedst.MaxDateTime');
                    $join->on('st.Groupement', '=', 'groupedst.Groupement');
                    $join->on('st.Nom_Region', '=', 'groupedst.Nom_Region');
                })
            ->whereNotNull('st.Groupement')
            ->whereNotNull('st.Nom_Region')
            ->where('Code_Type_Intervention', 'NOT LIKE', '%AUTRE%')
            ->where('st.Produit', '!=', '')
            ->where('st.Groupement', 'not like', 'Non renseigné')
            ->where('st.Groupement', 'not like', 'Appels post')
            ->whereNull('isNotReady');
        $results = applyFilter($results, $filter);
        if ($key_groupement) {
            $results = $results->where('key_groupement', 'like', $key_groupement);
        }
        if ($agentName) {
            $results = $results->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $results = $results->where('st.Nom_Region', 'like', "%$agenceCode");
        }

//        dd($results->groupBy('Nom_Region', 'cti', 'Produit', 'Groupement')->toSql());

        $results = $results->groupBy('st.Nom_Region', 'cti', 'Produit', 'Groupement')->get();

//        dd($results);

        $keys = $results->groupBy(['Groupement'])->keys();

        if (!count($results)) {
            $data = ['data' => []];
            return $data;
        } else {

            $temp = $results->groupBy(['Groupement']);

            $temp = $temp->map(function ($product, $index) {
                $totalZone = $product->reduce(function ($carry, $call) {
                    return $carry + $call->total;
                }, 0);
                return $product->map(function ($call) use ($index, $totalZone) {
                    $Code = $call->Groupement;
                    $call->$index = $call->$Code = $totalZone == 0 ? 0.00 : round($call->total * 100 / $totalZone, 2);
                    return $call;
                });
            });
            $results = $temp->flatten();

            $total = new \stdClass();
            $total->values = [];
            $results = $results->groupBy(['cti', 'Produit']);
            $results = $results->map(function ($group, $index) use (&$column_names, &$total, $keys) {
                return $group->map(function ($product, $index) use (&$column_names, &$total, $keys) {
                    $row = new \stdClass();
                    $row->values = [];
                    $col_arr = $keys->all();
                    $item = $product->map(function ($grpCall, $index) use (&$row, &$codes_names, &$total, &$col_arr) {

//                        $row->Code_Type_Intervention = $grpCall->cti; // preg_replace('/\_.+/', '', $grpCall->Code_Type_Intervention);
//                        $row->Produit = $grpCall->Produit;

                        $row->Produit = $grpCall->cti . ' / ' . $grpCall->Produit;
                        $column_name = $grpCall->Groupement;
                        $col_arr = array_diff($col_arr, [$column_name]);
                        $row->values[$column_name] = $grpCall->total;
                        $row->$column_name = $grpCall->total . '|' . $grpCall->$column_name . '%';
                        $row->total = isset($row->total) ? $row->total + $grpCall->total : $grpCall->total;
                        return $row;
                    });
                    $_item = $item->last();
                    foreach ($col_arr as $col) {
                        $_item->values[$col] = 0; //'0%';
                        $_item->$col = 0; //'0%';
                    }
                    collect($_item->values)->map(function ($value, $index) use (&$total) {
                        $total->values[$index] = round(!isset($total->values[$index]) ? $value : $value + $total->values[$index], 2);
                        $total->$index = $total->values[$index];
                    });
                    $_item->values = sortGroupementColumnsPreserveKeys(collect($_item->values))->all();
                    $_item->values = collect($_item->values)->values();
                    return $_item;
                })->flatten();
            })->flatten();
//            $total->Produit = 'Total Général';
//            $total->total = round(array_sum($total->values), 2);
//            $total->values = collect($total->values)->values();
//            $total->isTotal = true;
//            $results->push($total);
            $results = $results->values();

            $data = ['data' => $results];
            return $data;
        }
    }


    public function GetColumnsGlobalViewDetails(Request $request)
    {
        $agenceCode = $request->get('agence_code');
        $agentName = $request->get('agent_name');
        list($cti, $produit) = explode(' / ', $request->get('key_groupement'));

        $_route = getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);
        list($filter, $queryFilters) = makeFilterSubQuery($request, $route, 'Nom_Region');


        $results = \DB::table('stats as st')
            ->select('st.Groupement')
            ->distinct()
            ->whereNotNull('st.Groupement')
            ->whereNotNull('st.Nom_Region')
            ->where('st.Groupement', 'not like', 'Non renseigné')
            ->where('st.Groupement', 'not like', 'Appels post')
            ->where('Code_Type_Intervention', 'NOT LIKE', '%AUTRE%')
            ->where('st.Produit', '!=', '')
            ->whereNull('isNotReady');
        $results = applyFilter($results, $filter, 'Nom_Region');

//        if ($cti) {
//            $results = $results->where('Code_Type_Intervention', 'like', '%' . $cti . '%');
//        }
//        if ($produit) {
//            $results = $results->where('Produit', 'like', $produit);
//        }
        if ($agentName) {
            $results = $results->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $results = $results->where('st.Nom_Region', 'like', "%$agenceCode");
        }

        $rowsKeys = [];
//        $rowsKeys = \DB::table('stats as st')
//            ->select('st.Nom_Region')
//            ->distinct()
//            ->whereNotNull('st.Groupement')
//            ->whereNotNull('st.Nom_Region')
//            ->where('st.Groupement', 'not like', 'Non renseigné')
//            ->where('st.Groupement', 'not like', 'Appels post')
//            ->where('Type_Note', 'like', 'CAM')
//            ->whereNull('isNotReady');
//        if ($key_groupement) {
//            $rowsKeys = $rowsKeys->where('key_groupement', 'like', $key_groupement);
//        }
//        if ($agentName) {
//            $rowsKeys = $rowsKeys->where('st.Utilisateur', $agentName);
//        }
//        if ($agenceCode) {
//            $rowsKeys = $rowsKeys->where('st.Nom_Region', 'like', "%$agenceCode");
//        }
//        $rowsKeys = $rowsKeys->pluck('Nom_Region');

        $keys = $results->pluck('Groupement');

        if (!count($keys)) {
            $data = ['filter' => $filter, 'columns' => [], 'rows' => $rowsKeys, 'rowsFilterHeader' => ''];
            return $data;
        } else {

            $keys = sortGroupementColumns($keys);

            $column_names = [];
            $keys->map(function ($key, $index) use (&$column_names) {
                $column_names[$index + 1] = new \stdClass();
                $column_names[$index + 1]->text =
                $column_names[$index + 1]->data =
                $column_names[$index + 1]->name =
                $column_names[$index + 1]->title = $key;
            });

//            $header = new \stdClass();
//            $header->title = 'Code Type Intervention';
//            $header->text = 'Code Type Intervention';
//            $header->name = $header->data = 'Code_Type_Intervention';
//            $header->orderable = false;

            $first = new \stdClass();
            $first->title = 'Agence';
            $first->text = 'Agence';
            $first->name = $first->data = 'Nom_Agence';
            $first->orderable = false;
//            $last = new \stdClass();
//            $last->data =
//            $last->name =
//            $last->text =
//            $last->title = 'total';
            array_unshift($column_names, $first);
//            array_unshift($column_names, $header);
//            array_push($column_names, $last);

            $data = ['filter' => $filter, 'columns' => $column_names, 'rows' => $rowsKeys, 'rowsFilterHeader' => ''];
            return $data;
        }
    }

    public function GetDataGlobalViewDetails(Request $request)
    {
        $agenceCode = $request->get('agence_code');
        $agentName = $request->get('agent_name');
        list($cti, $produit) = explode(' / ', $request->get('key_groupement'));

        $_route = getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);
        list($filter, $queryFilters) = makeFilterSubQuery($request, $route, 'Nom_Region');


        $results = \DB::table('stats as st')
            ->select(\DB::raw('substr(right(Nom_Agence, 4), 2, 2) as na'), 'st.Groupement', \DB::raw('count(distinct st.Id_Externe) as total'))
            ->join(\DB::raw('(SELECT Id_Externe, MAX(Date_Heure_Note) AS MaxDateTime,
                groupement , Nom_Region
            FROM stats
             where Groupement IS NOT NULL
             AND Nom_Region IS NOT NULL
             AND Nom_Agence IS NOT NULL
             AND Code_Type_Intervention NOT LIKE "%AUTRE%"
             AND Produit != ""
             AND Groupement not LIKE "Non renseigné"
             AND Groupement not LIKE "Appels post"' .
                ' and ' . $queryFilters .
                ($agentName ? 'and Utilisateur like "' . $agentName . '"' : '') .
                ($agenceCode ? 'and Nom_Region like "%' . $agenceCode . '"' : '') .
                ($cti ? 'and Code_Type_Intervention like "%' . $cti . '%"' : '') .
                ($produit ? 'and Produit like "' . $produit . '"' : '') .
                ' and isNotReady is null ' .
                ' GROUP BY Id_Externe, Groupement, Nom_Region ) groupedst'),
                function ($join) {
                    $join->on('st.Id_Externe', '=', 'groupedst.Id_Externe');
                    $join->on('st.Date_Heure_Note', '=', 'groupedst.MaxDateTime');
                    $join->on('st.Groupement', '=', 'groupedst.Groupement');
                    $join->on('st.Nom_Region', '=', 'groupedst.Nom_Region');
                })
            ->whereNotNull('st.Groupement')
            ->whereNotNull('st.Nom_Region')
            ->whereNotNull('st.Nom_Agence')
            ->where('Code_Type_Intervention', 'NOT LIKE', '%AUTRE%')
            ->where('st.Produit', '!=', '')
            ->where('st.Groupement', 'not like', 'Non renseigné')
            ->where('st.Groupement', 'not like', 'Appels post')
            ->whereNull('isNotReady');
        $results = applyFilter($results, $filter);

        if ($cti) {
            $results = $results->where('Code_Type_Intervention', 'like', '%' . $cti . '%');
        }
        if ($produit) {
            $results = $results->where('Produit', 'like', $produit);
        }
        if ($agentName) {
            $results = $results->where('st.Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $results = $results->where('st.Nom_Region', 'like', "%$agenceCode");
        }


        $results = $results->groupBy('na', 'Groupement')->get();


//        $keys = $results->groupBy(['Groupement'])->keys();
        $keys = $this->GetColumnsGlobalViewDetails($request)['columns'];
        array_shift($keys);
        $keys = collect($keys);
        $keys = $keys->transform(function ($key)
        {
            return $key->title;
        });

        if (!count($results)) {
            $data = ['data' => []];
            return $data;
        } else {
            $temp = $results->groupBy(['Groupement']);

            $temp = $temp->map(function ($product, $index) {
                $totalZone = $product->reduce(function ($carry, $call) {
                    return $carry + $call->total;
                }, 0);
                return $product->map(function ($call) use ($index, $totalZone) {
                    $Code = $call->Groupement;
                    $call->$index = $call->$Code = $totalZone == 0 ? 0.00 : round($call->total * 100 / $totalZone, 2);
                    return $call;
                });
            });
            $results = $temp->flatten();

            $total = new \stdClass();
            $total->values = [];
            $results = $results->groupBy(['na']);
            $results = $results->map(function ($product, $index) use (&$column_names, &$total, $keys) {
                $row = new \stdClass();
                $row->values = [];
                $col_arr = $keys->all();
                $item = $product->map(function ($grpCall, $index) use (&$row, &$codes_names, &$total, &$col_arr) {
//                        $row->Code_Type_Intervention = $grpCall->cti; // preg_replace('/\_.+/', '', $grpCall->Code_Type_Intervention);
//                        $row->Produit = $grpCall->Produit;

                    $row->Nom_Agence = $grpCall->na;
                    $column_name = $grpCall->Groupement;
                    $col_arr = array_diff($col_arr, [$column_name]);
                    $row->values[$column_name] = $grpCall->total;
                    $row->$column_name = $grpCall->total . '|' . $grpCall->$column_name . '%';
//                    $row->total = isset($row->total) ? $row->total + $grpCall->total : $grpCall->total;
                    return $row;
                });
                $_item = $item->last();
                foreach ($col_arr as $col) {
                    $_item->values[$col] = 0; //'0%';
                    $_item->$col = 0; //'0%';
                }
                collect($_item->values)->map(function ($value, $index) use (&$total) {
                    $total->values[$index] = round(!isset($total->values[$index]) ? $value : $value + $total->values[$index], 2);
                    $total->$index = $total->values[$index];
                });
//                ksort($_item->values);
                $_item->values = sortGroupementColumnsPreserveKeys(collect($_item->values))->all();
                $_item->values = collect($_item->values)->values();
                return $_item;
            });
            $total->Produit = 'Total Général';
            $total->total = round(array_sum($total->values), 2);
            $total->values = collect($total->values)->values();
            $total->isTotal = true;
//            $results->push($total);
            $results = $results->values();

            $data = ['data' => $results];
            return $data;
        }
    }


    public function importStats($request)
    {

        $statImport = new StatsImport($request->days);
        Excel::import($statImport, $request->file('file'));
        $user_flag = getImportedData(false);
        $user_flag->flags = [
            'imported_data' => $user_flag->flags['imported_data'],
            'is_importing' => 2
        ];
        $user_flag->update();
        \DB::table('stats')
            ->whereNotNull('isNotReady')
            ->update(['isNotReady' => null]);
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
