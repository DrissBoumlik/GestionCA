<?php

namespace App\Repositories;

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
//            ->select('Nom_Region', $callResult, 'Key_Groupement', \DB::raw('count(st.Id_Externe) as total'))
//            ->whereNotNull('Nom_Region')
//            ->where($callResult, 'not like', '=%')
//            ->where('Groupement', 'not like', 'Non Renseigné')
//            ->where('Groupement', 'not like', 'Appels post');

        $_route = getRoute(Route::current());
        $route = str_replace('/columns', '', $_route);

        list($filter, $queryFilters) = makeFilterSubQuery($request, $route, 'Groupement', $groupement);

        $regions = \DB::table('stats as st')
            ->select('Nom_Region', $callResult, 'Key_Groupement', \DB::raw('count(st.Id_Externe) as total'))
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
}
