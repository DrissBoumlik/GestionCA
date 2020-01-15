<?php

namespace App\Repositories;


use App\Imports\StatsImport;
use App\Models\Filter;
use App\Models\Stats;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Maatwebsite\Excel\Facades\Excel;

class StatsRepository
{
    public function getStats(Request $request)
    {
        return DB::table('stats')->select([
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
            'key_Groupement',
            'Gpmt_Appel_Pre',
            'Code_Intervention',
            'EXPORT_ALL_Nom_SITE',
            'EXPORT_ALL_Nom_TECHNICIEN',
            'EXPORT_ALL_PRENom_TECHNICIEN',
            'EXPORT_ALL_Nom_EQUIPEMENT',
            'EXPORT_ALL_EXTRACT_CUI',
            'EXPORT_ALL_Date_CHARGEMENT_PDA',
            'EXPORT_ALL_Date_SOLDE',
            'EXPORT_ALL_Date_VALIDATION'
        ]);

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
        $stats = $stats->distinct('Utilisateur')->limit(10)->get()->map(function ($s) {
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
        $stats = Stats::select([$column])
            ->distinct($column)
            ->where($column, 'not like', '=%')
            ->whereNotNull($column)
            ->where('Groupement', 'not like', 'Non Renseigné');
//        if ($column === 'Groupement') {
//            $stats = $stats->where('Groupement', 'not like', 'Non Renseigné');
//        }
        if ($agentName) {
            $stats = $stats->where('Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $stats = $stats->where('Nom_Region', 'like', "%$agenceCode");
        }
        $stats = $stats->orderBy($column)->get();
        return $stats->map(function ($s) use ($column) {
            return $s[$column];
        });
    }

    public function addRegionWithZero(Request $request, $regions, $columns, $column = null)
    {
        $agenceCode = $request->get('agence_code');
        $agentName = $request->get('agent_name');
        //$resultatAppel = $request->get('resultatAppel');
        $groupement = $request->get('groupement');
        $nomRegion = $request->get('nomRegion');
        $codeRdvInterventionConfirm = $request->get('codeRdvInterventionConfirm');
        $codeRdvIntervention = $request->get('codeRdvIntervention');
        $codeTypeIntervention = $request->get('codeTypeIntervention');
        $codeIntervention = $request->get('codeIntervention');
        $gpmtAppelPre = $request->get('gpmtAppelPre');

        if ($nomRegion) {
            $groupmentColumns = Stats::select('Groupement')->distinct('Groupement')
                ->where('Groupement', 'not like', 'Non Renseigné')
                ->when($agenceCode, function ($query, $agenceCode) {
                    return $query->where('Nom_Region', 'like', "%$agenceCode");
                })->when($agentName, function ($query, $agentName) {
                    return $query->where('Utilisateur', $agentName);
                })->get();
            if ($nomRegion) {
                foreach ($nomRegion as $gr) {
                    foreach ($groupmentColumns as $col) {
                        if (!in_array($col->Groupement, $regions->filter(function ($r) use ($gr) {
                            return $r->Nom_Region === $gr;
                        })->map(function ($r) {
                            return $r->Groupement;
                        })->toArray())) {
                            if ($col->Groupement) {
                                $rObj = new \stdClass();
                                $rObj->Groupement = $col->Groupement;
                                $rObj->Key_Groupement = $col->Key_Groupement;
                                $rObj->Nom_Region = $gr;
                                $rObj->total = 0;
                                $columns[] = $rObj;
                            }
                        }
                    }
                }
            }
        }
        if ($groupement || $codeIntervention || $codeTypeIntervention || $gpmtAppelPre) {
            $regionsColumns = Stats::select('Nom_Region')->distinct('Nom_Region')
                ->where('Groupement', 'not like', 'Non Renseigné')
                ->when($agenceCode, function ($query, $agenceCode) {
                    return $query->where('Nom_Region', 'like', "%$agenceCode");
                })
                ->when($agentName, function ($query, $agentName) {
                    return $query->where('Utilisateur', $agentName);
                })->get();

            if ($groupement) {
                foreach ($groupement as $gr) {
                    foreach ($regionsColumns as $col) {
                        if (!in_array($col->Nom_Region, $regions->filter(function ($r) use ($gr) {
                            return $r->Groupement === $gr;
                        })->map(function ($r) {
                            return $r->Nom_Region;
                        })->toArray())) {
                            $rObj = new \stdClass();
                            $rObj->Nom_Region = $col->Nom_Region;
                            $rObj->Key_Groupement = $col->Key_Groupement;
                            $rObj->Groupement = $gr;
                            $rObj->total = 0;
                            $columns[] = $rObj;
                        }
                    }
                }
            }
            if ($codeTypeIntervention) {
                foreach ($codeTypeIntervention as $type) {
                    foreach ($regionsColumns as $col) {
                        if (!in_array($col->Nom_Region, $regions->filter(function ($r) use ($type) {
                            return $r->Code_Type_Intervention === $type;
                        })->map(function ($r) {
                            return $r->Nom_Region;
                        })->toArray())) {
                            $rObj = new \stdClass();
                            $rObj->Nom_Region = $col->Nom_Region;
                            $rObj->Code_Type_Intervention = $type;
                            $rObj->total = 0;
                            $columns[] = $rObj;
                        }
                    }
                }
            }
            if ($codeIntervention) {
                foreach ($codeIntervention as $type) {
                    foreach ($regionsColumns as $col) {
                        if (!in_array($col->Nom_Region, $regions->filter(function ($r) use ($type) {
                            return $r->Code_Intervention === $type;
                        })->map(function ($r) {
                            return $r->Nom_Region;
                        })->toArray())) {
                            $rObj = new \stdClass();
                            $rObj->Nom_Region = $col->Nom_Region;
                            $rObj->Code_Intervention = $type;
                            $rObj->total = 0;
                            $columns[] = $rObj;
                        }
                    }
                }
            }
            if ($column !== 'Date_Heure_Note_Semaine') {
                if ($gpmtAppelPre) {
                    $column = $column ?? 'Nom_Region';
                    foreach ($gpmtAppelPre as $gpm) {
                        foreach ($regionsColumns as $col) {
                            if (!in_array($col->$column, $regions->filter(function ($r) use ($gpm, $column) {
                                return $r->Gpmt_Appel_Pre === $gpm;
                            })->map(function ($r) use ($column) {
                                return $r->$column;
                            })->toArray())) {
                                $rObj = new \stdClass();
                                $rObj->$column = $col->$column;
                                $rObj->Gpmt_Appel_Pre = $gpm;
                                $rObj->total = 0;
                                $columns[] = $rObj;
                            }
                        }
                    }
                }
            }
        }
        if ($codeRdvInterventionConfirm || $codeRdvIntervention) {
            $codeColumns = Stats::select('Code_Intervention')->distinct('Code_Intervention')
                ->whereNotNull('Code_Intervention')
                ->when($agenceCode, function ($query, $agenceCode) {
                    return $query->where('Nom_Region', 'like', "%$agenceCode");
                })
                ->when($agentName, function ($query, $agentName) {
                    return $query->where('Utilisateur', $agentName);
                })->get();

            $code = $codeRdvInterventionConfirm ? $codeRdvInterventionConfirm : $codeRdvIntervention;
            if ($code) {
                foreach ($code as $gr) {
                    foreach ($codeColumns as $col) {
                        if (!in_array($col->Code_Intervention, $regions->filter(function ($r) use ($gr) {
                            return $r->Nom_Region === $gr;
                        })->map(function ($r) {
                            return $r->Code_Intervention;
                        })->toArray())) {
                            if ($col->Nom_Region !== '') {
                                $rObj = new \stdClass();
                                $rObj->Nom_Region = $col->Nom_Region;
                                $rObj->Code_Intervention = $col->Code_Intervention;
                                $rObj->total = 0;
                                $columns[] = $rObj;
                            }
                        }
                    }
                }
            }
        }

        return $columns;
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

    public function GetDataRegions($callResult, Request $request = null)
    {
        $resultatAppel = $request->get('resultatAppel');
        $groupement = $request->get('groupement');
        $dates = $request->get('dates');
        $agenceCode = $request->get('agence_code');
        $agentName = $request->get('agent_name');

//        TODO: Get Route URI -> replace params with actual value (as ID) - search in filter table if filter exists
//        TODO => if not check request if it exists save the new filter or just get full data and delete old filter

//        $regions = \DB::table('stats')
//            ->select('Nom_Region', $callResult, 'Key_Groupement', \DB::raw('count(Nom_Region) as total'))
//            ->whereNotNull('Nom_Region')
//            ->where($callResult, 'not like', '=%')
//            ->where('Groupement', 'not like', 'Non Renseigné')
//            ->where('Groupement', 'not like', 'Appels post');

//        DB::enableQueryLog();
        $regions = \DB::table('stats as st')
            ->select('Nom_Region', $callResult, 'Key_Groupement', \DB::raw('count(Nom_Region) as total'))
            ->join(\DB::raw('(SELECT Id_Externe, MAX(Date_Heure_Note) AS MaxDateTime FROM stats GROUP BY Id_Externe) groupedst'),
                function ($join) {
                    $join->on('st.Id_Externe', '=', 'groupedst.Id_Externe');
                    $join->on('st.Date_Heure_Note', '=', 'groupedst.MaxDateTime');
                })
            ->whereNotNull('Nom_Region')
            ->where($callResult, 'not like', '=%')
            ->where('Groupement', 'not like', 'Non Renseigné')
            ->where('Groupement', 'not like', 'Appels post');;
//        dd(DB::getQueryLog());

        // BUILDING THE USER FILTER
        if ($agentName) {
            $regions = $regions->where('Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $regions = $regions->where('Nom_Region', 'like', "%$agenceCode");
        }

        $keys = ($regions->groupBy('Nom_Region', $callResult, 'Key_Groupement')->get())->groupBy(['Nom_Region'])->keys();

        if ($resultatAppel) {
            $resultatAppel = array_values($resultatAppel);
            $regions = $regions->whereIn('Resultat_Appel', $resultatAppel);
        }
        if ($groupement) {
            $groupement = array_values($groupement);
            $regions = $regions->whereIn('Groupement', $groupement);
        }
//        $route = getRoute(Route::current());
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
        $columns = $regions->groupBy('Nom_Region', $callResult, 'Key_Groupement')->get();
        $regions = $regions->groupBy('Nom_Region', $callResult, 'Key_Groupement')->get();
//        dd($regions); // STOP
        $regions = $columns = $this->addRegionWithZero($request, $regions, $columns);
        // logger($regions);
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
                $regions_names[$index + 1]->data = $regions_names[$index + 1]->name = $regions_names[$index + 1]->text = $key;
            });
            usort($regions_names, function ($item1, $item2) {
                return ($item1->data == $item2->data) ? 0 :
                    ($item1->data < $item2->data) ? -1 : 1;
            });
            $first = new \stdClass();
            $first->text = 'Résultats Appels Préalables (Clients Joignable)';
            $first->name = $first->data = $callResult;
            array_unshift($regions_names, $first);
//            $detailCol = new \stdClass();
//            $detailCol->name = 'Key_Groupement';
//            $detailCol->data = 'Key_Groupement';
//            array_unshift($regions_names, $detailCol);
//            $regions_names[] = new \stdClass();
//            $regions_names[count($regions_names) - 1]->data = 'total';
//            $regions_names[count($regions_names) - 1]->name = 'total';

            $regions = $regions->map(function ($region) use (&$regions_names, $keys, $callResult) {
                $row = new \stdClass();
                $row->values = [];

                $col_arr = $keys->all();

                $item = $region->map(function ($call, $index) use (&$row, &$regions_names, &$col_arr, $callResult) {
                    $row->Key_Groupement = $call->Key_Groupement;
                    $row->$callResult = $call->$callResult;
                    $nom_region = $call->Nom_Region;

                    $col_arr = array_diff($col_arr, [$nom_region]);

                    $row->values[$nom_region] = $call->$nom_region;
                    $row->$nom_region = $call->$nom_region . '%';
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

            return ['filter' => $filter, 'columns' => $regions_names, 'data' => $regions];
        }
    }

    public function GetDataRegionsByGrpCall(Request $request = null)
    {
        $resultatAppel = $request->get('resultatAppel');
        $groupement = $request->get('groupement');
        $dates = $request->get('dates');
        $agenceCode = $request->get('agence_code');
        $agentName = $request->get('agent_name');

        $key_groupement = $request->get('key_groupement');
        $regions = \DB::table('stats')
            ->select('Nom_Region', 'Groupement', 'Key_Groupement', 'Resultat_Appel', \DB::raw('count(Resultat_Appel) as total'))
            ->where('Resultat_Appel', 'not like', '=%')
            ->whereNotNull('Nom_Region');

        $keys = ($regions->groupBy('Nom_Region', 'Groupement', 'Key_Groupement', 'Resultat_Appel')->get())->groupBy(['Nom_Region'])->keys();

        $columns = $regions->groupBy('Nom_Region', 'Groupement', 'Key_Groupement', 'Resultat_Appel')->get();
        $key_groupement = clean($key_groupement);
        $regions = $regions->where('key_groupement', 'like', $key_groupement);
//        $columns = $regions->groupBy('Nom_Region', $callResult, 'Key_Groupement')->get();
        // BUILDING THE USER FILTER
        if ($agentName) {
            $regions = $regions->where('Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $regions = $regions->where('Nom_Region', 'like', "%$agenceCode");
        }
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
            });
            usort($regions_names, function ($item1, $item2) {
                return ($item1->data == $item2->data) ? 0 :
                    ($item1->data < $item2->data) ? -1 : 1;
            });
            $first = new \stdClass();
            $first->name = 'Résultats Appels Préalables (Clients Joignable)';
            $first->data = 'Groupement';
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
                    $row->Groupement = $call->Groupement;
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

    public function GetDataFolders($callResult, Request $request)
    {
        $resultatAppel = $request->get('resultatAppel');
        $groupement = $request->get('groupement');
        $dates = $request->get('dates');
        $agenceCode = $request->get('agence_code');
        $agentName = $request->get('agent_name');
        $route = getRoute(Route::current());
        $regions = \DB::table('stats')
            ->select('Nom_Region', $callResult, \DB::raw('count(Nom_Region) as total'))
            ->where($callResult, 'not like', '=%')
            ->where('Groupement', 'not like', 'Non Renseigné')
            ->where('Groupement', 'not like', 'Appels post')
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
        $regions = $columns = $this->addRegionWithZero($request, $regions, $columns);
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
                $regions_names[$index + 1]->data = $regions_names[$index + 1]->name = $regions_names[$index + 1]->text = $key;
            });

            usort($regions_names, function ($item1, $item2) {
                return ($item1->data == $item2->data) ? 0 :
                    ($item1->data < $item2->data) ? -1 : 1;
            });
            $first = new \stdClass();
            $first->name = $callResult;
            $first->data = $callResult;
            $first->text = 'Type Traitement';
            $first->orderable = false;
            array_unshift($regions_names, $first);
            $last = new \stdClass();
            $last->data = $last->name = $last->text = 'total';
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
                    $row->total = $call->$nom_region; //round(array_sum($row->values) / count($row->values), 2) . '%';
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

    public function GetDataRegionsCallState($column, Request $request)
    {
        $gpmtAppelPre = $request->get('gpmtAppelPre');
        $dates = $request->get('dates');
        $agenceCode = $request->get('agence_code');
        $agentName = $request->get('agent_name');
        $route = getRoute(Route::current());
        $regions = \DB::table('stats')
            ->select($column, 'Gpmt_Appel_Pre', \DB::raw('count(*) as total'))
            ->where('Groupement', 'not like', 'Non Renseigné')
            ->whereNotNull('Nom_Region');


        if ($agentName) {
            $regions = $regions->where('Utilisateur', $agentName);
        }
        if ($agenceCode) {
            $regions = $regions->where('Nom_Region', 'like', "%$agenceCode");
        }
        $keys = ($regions->groupBy($column, 'Gpmt_Appel_Pre')->get())->groupBy([$column])->keys();
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


        $columns = $regions->groupBy($column, 'Gpmt_Appel_Pre')->get();

//        $regions = ($dates ? $regions->whereIn('Date_Note', $dates)->get() : $regions)->get();
        $regions = $regions->groupBy($column, 'Gpmt_Appel_Pre')->get();
        $regions = $columns = $this->addRegionWithZero($request, $regions, $columns, $column);

        if (!count($regions)) {
            $data = ['columns' => [], 'data' => []];
            return $data;
        } else {
            $totalCount = Stats::count();

            // ====================
//            $temp = $regions->groupBy(['Nom_Region']);
////            dd($temp);
////            dd($temp);
//            $temp = $temp->map(function ($calls, $index) {
//                $totalZone = $calls->reduce(function ($carry, $call) {
//                    return $carry + $call->total;
//                }, 0);
//                return $calls->map(function ($call) use ($index, $totalZone) {
//                    $call->$index = $totalZone == 0 ? 0.00 : round($call->total * 100 / $totalZone, 2);
//                    return $call;
//                });
//            });
////            dd($temp);
//
//            $regions = $temp->flatten();
////            $regions = $regions->groupBy($column);
////            dd($regions);
            // ======================


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

//            $keys = $columns->groupBy([$column])->keys();
            $regions = $regions->groupBy(['Gpmt_Appel_Pre']);
//        $regions = $regions->groupBy(['Nom_Region']);

            $columns = [];
//            $columns[0] = new \stdClass();
//            $columns[0]->data = 'Gpmt_Appel_Pre';
//            $columns[0]->name = 'Gpmt_Appel_Pre';
            $keys->map(function ($key, $index) use (&$columns) {
                $columns[$index + 1] = new \stdClass();
                $columns[$index + 1]->data = $columns[$index + 1]->name = $columns[$index + 1]->text = $key;
            });
//            $columns = $columns->all();
            usort($columns, function ($item1, $item2) {
                return ($item1->data == $item2->data) ? 0 :
                    ($item1->data < $item2->data) ? -1 : 1;
            });
            $first = new \stdClass();
            $first->text = 'Résultats Appels Préalables';
            $first->name = $first->data = 'Gpmt_Appel_Pre';
            $first->orderable = false;
            $last = new \stdClass();
            $last->data = $last->name = $last->text = 'total';
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
//                dd($index, $call);
                    $row->Gpmt_Appel_Pre = $call->Gpmt_Appel_Pre;
                    $column_name = $call->$column;
                    $col_arr = array_diff($col_arr, [$column_name]);
                    $row->values[$column_name] = $call->$column_name;
                    $row->$column_name = $call->$column_name;
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
                collect($_item->values)->map(function ($value, $index) use (&$total) {
                    $total->values[$index] = round(!isset($total->values[$index]) ? $value : $value + $total->values[$index], 2);
                    $total->$index = $total->values[$index];
                });


                $_item->values = collect($_item->values)->values();
                return $_item;
            });
            $total->Gpmt_Appel_Pre = 'Total Général';
            $total->total = round(array_sum($total->values), 2);
            $total->values = collect($total->values)->values();
            $regions->push($total);

            $regions = $regions->values();

            return ['filter' => $filter, 'columns' => $columns, 'data' => $regions];
        }
    }

    public function getDataNonValidatedFolders($intervCol, Request $request)
    {
        $route = getRoute(Route::current());

        $dates = $request->get('dates');
        $agentName = $request->get('agent_name');
        $codeTypeIntervention = $request->get('codeTypeIntervention');
        $codeIntervention = $request->get('codeIntervention');
        $agenceCode = $request->get('agence_code');

        $regions = \DB::table('stats')
            ->select('Nom_Region', $intervCol, \DB::raw('count(*) as total'))
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
        $regions = $columns = $this->addRegionWithZero($request, $regions, $columns);

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
                $regions_names[$index + 1]->text = $regions_names[$index + 1]->data = $regions_names[$index + 1]->name = $key;
            });
            usort($regions_names, function ($item1, $item2) {
                return ($item1->data == $item2->data) ? 0 :
                    ($item1->data < $item2->data) ? -1 : 1;
            });
            $first = new \stdClass();
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

            $regions->push($total);

            $regions = $regions->values();
            $data = ['filter' => $filter, 'columns' => $regions_names, 'data' => $regions];
            return $data;
        }
    }

    public function getDataClientsByCallState($callResult, Request $request)
    {
        $route = getRoute(Route::current());
        $dates = $request->get('dates');
        $agentName = $request->get('agent_name');
        $agenceCode = $request->get('agence_code');
        $codeRdvInterventionConfirm = $request->get('codeRdvInterventionConfirm');
        $codeRdvIntervention = $request->get('codeRdvIntervention');

        $codes = \DB::table('stats')
            ->select('Code_Intervention', 'Nom_Region', \DB::raw('count(Nom_Region) as total'))
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
        $codes = $columns = $this->addRegionWithZero($request, $codes, $columns, null, 'Gpmt_Appel_Pre', $callResult);
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
                $codes_names[$index + 1]->text = $codes_names[$index + 1]->data = $codes_names[$index + 1]->name = $key;
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
            $first->text = 'Résultats Appels Préalables';
            $first->name = $first->data = 'Nom_Region';
            $first->orderable = false;
            $last = new \stdClass();
            $last->text = $last->data = $last->name = 'total';
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

    public function getDataClientsByPerimeter(Request $request)
    {
        $route = getRoute(Route::current());

        $dates = $request->get('dates');
        $nomRegion = $request->get('nomRegion');

        $agenceCode = $request->get('agence_code');
        $agentName = $request->get('agent_name');
        $results = \DB::table('stats')
            ->select('Groupement', 'Nom_Region', \DB::raw('count(*) as total'))
            ->whereNotNull('Groupement')
            ->where('Groupement', 'not like', 'Non Renseigné')
            ->where('Groupement', 'not like', 'Appels post')
            ->whereNotNull('Nom_Region');

        if ($agentName) {
            $results = $results->where('Utilisateur', $agentName);
        }
        if ($nomRegion) {
            $nomRegion = array_values($nomRegion);
            $results = $results->whereIn('Nom_Region', $nomRegion);
        }
        $keys = ($results->groupBy('Groupement', 'Nom_Region')->get())->groupBy(['Groupement'])->keys();
        if ($agenceCode) {
            $results = $results->where('Nom_Region', 'like', "%$agenceCode");
        }
        if ($dates) {
            $dates = array_values($dates);
            $results = $results->whereIn('Date_Note', $dates);
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
                    $regions = $results->whereIn('Date_Note', $dates);
                } else {
                    $filter = Filter::where(['route' => $route, 'user_id' => $user->id])->first();
                    if ($filter) {
                        $filter->forceDelete();
                    }
                }
            } else {
                $filter = Filter::where(['route' => $route, 'user_id' => $user->id])->first();
                if ($filter) {
                    $regions = $results->whereIn('Date_Note', $filter->date_filter);
                }
            }
        } else {
            $filter = Filter::where(['route' => $route, 'user_id' => $user->id])->first();
            if ($filter) {
                $regions = $results->whereIn('Date_Note', $filter->date_filter);
            }
        }

        $columns = $results->groupBy('Groupement', 'Nom_Region')->get();

        $results = $results->groupBy('Groupement', 'Nom_Region')->get();
        $results = $columns = $this->addRegionWithZero($request, $results, $columns);

        if (!count($results)) {
            $data = ['columns' => [], 'data' => []];
            return $data;
        } else {

            $totalCount = Stats::count();
            $results = $results->map(function ($resultItem) use ($totalCount) {
                $Code = $resultItem->Groupement;
                $resultItem->$Code = $resultItem->total; //round($resultItem->total * 100 / $totalCount, 2);;
                return $resultItem;
            });
            $columns = $columns->filter(function ($code) use ($totalCount) {
//            if ($code->Code_Intervention) {
                $Code = $code->Groupement;
                $code->$Code = $code->total; //round($code->total * 100 / $totalCount, 2);;
                return $code;
//            }
//            return;
            });

//            $keys = $columns->groupBy(['Groupement'])->keys();
            $results = $results->groupBy(['Nom_Region']);
            $column_names = [];
//        $codes_names[0] = new \stdClass();
//        $codes_names[0]->data = 'Nom_Region';
//        $codes_names[0]->name = 'Nom_Region';
            $keys->map(function ($key, $index) use (&$column_names) {
                $column_names[$index + 1] = new \stdClass();
                $column_names[$index + 1]->text = $column_names[$index + 1]->data = $column_names[$index + 1]->name = $key;
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
            $first->text = 'Région';
            $first->name = $first->data = 'Nom_Region';
            $first->orderable = false;
            $last = new \stdClass();
            $last->data = $last->name = $last->text = 'total';
            array_unshift($column_names, $first);
            array_push($column_names, $last);


            $total = new \stdClass();
            $total->values = [];

            $results = $results->map(function ($region) use (&$column_names, &$total, $keys) {
                $row = new \stdClass(); //[];
                $row->values = [];
                $col_arr = $keys->all();
                $item = $region->map(function ($call, $index) use (&$row, &$codes_names, &$total, &$col_arr) {

//                $codes_names[] = $call->Code_Intervention;
                    $row->Nom_Region = $call->Nom_Region;
                    $column_name = $call->Groupement;

                    $col_arr = array_diff($col_arr, [$column_name]);
//                dd($call->Code_Intervention);
                    $row->values[$column_name] = $call->$column_name;
                    $row->$column_name = $call->$column_name;
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

            $results->push($total);
            $results = $results->values();

            $data = ['filter' => $filter, 'columns' => $column_names, 'data' => $results];
            return $data;
        }
    }

    public function importStats($request)
    {
        try {
            Excel::import(new StatsImport($request->days), $request->file('file'));
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
