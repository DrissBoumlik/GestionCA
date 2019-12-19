<?php

namespace App\Http\Controllers;

use App\Models\Stats;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DemoController extends Controller
{
    public function index()
    {
        return view('_demo.index');
    }

    public function getRegionsColumnJson(Request $request, $dates = null)
    {
        $regions = \DB::table('stats')
            ->select('Nom_Region', 'Resultat_Appel', \DB::raw('count(*) as total'));
        if ($dates) {
            $dates = array_values($dates);
            $regions = $regions->whereIn('Date_Note', $dates);
        }
//        $regions = ($dates ? $regions->whereIn('Date_Note', $dates)->get() : $regions)->get();

        $regions = $regions->groupBy('Nom_Region', 'Resultat_Appel')->get();

        $totalCount = Stats::all()->count();
        $regions = $regions->map(function ($region) use ($totalCount) {
            $Region = $region->Nom_Region;
            $region->$Region = round($region->total * 100 / $totalCount, 2);;
            return $region;
        });
        $regions = $regions->groupBy(['Resultat_Appel']);

        $regions_names = [];
        $regions_names[0] = new \stdClass();
        $regions_names[0]->data = 'Resultat_Appel';
        $regions_names[0]->name = 'Resultat_Appel';

        $regions = $regions->map(function ($region) use (&$regions_names) {
            $row = new \stdClass();
            $row->regions = [];
            $item = $region->map(function ($call, $index) use (&$row, &$regions_names) {
                $regions_names[$index + 1] = new \stdClass();
                $regions_names[$index + 1]->data = $call->Nom_Region;
                $regions_names[$index + 1]->name = $call->Nom_Region;
                $row->Resultat_Appel = $call->Resultat_Appel;
                $nom_region = $call->Nom_Region;
                $row->regions['zone_' . $index] = $call->$nom_region;
                $row->$nom_region = $call->$nom_region;
                $row->total = round(array_sum($row->regions) / count($row->regions), 2);
                return $row;
            });
            return $item->last();
        });

        $regions_names[] = new \stdClass();
        $regions_names[count($regions_names) - 1]->data = 'total';
        $regions_names[count($regions_names) - 1]->name = 'total';

        $regions_names = collect($regions_names)->unique()->filter()->values();
        return ['regions_names' => $regions_names];
    }

    public function getRegionsJson(Request $request, $dates = null)
    {
        $regions = \DB::table('stats')
            ->select('Nom_Region', 'Resultat_Appel', \DB::raw('count(*) as total'));
        if ($dates) {
            $dates = array_values($dates);
            $regions = $regions->whereIn('Date_Note', $dates);
        }
//        $regions = ($dates ? $regions->whereIn('Date_Note', $dates)->get() : $regions)->get();

        $regions = $regions->groupBy('Nom_Region', 'Resultat_Appel')->get();

        $totalCount = Stats::all()->count();
        $regions = $regions->map(function ($region) use ($totalCount) {
            $Region = $region->Nom_Region;
            $region->$Region = round($region->total * 100 / $totalCount, 2);;
            return $region;
        });
        $regions = $regions->groupBy(['Resultat_Appel']);

        $regions_names = [];
        $regions_names[0] = new \stdClass();
        $regions_names[0]->data = 'Resultat_Appel';
        $regions_names[0]->name = 'Resultat_Appel';

        $regions = $regions->map(function ($region) use (&$regions_names) {
            $row = new \stdClass();
            $row->regions = [];
            $item = $region->map(function ($call, $index) use (&$row, &$regions_names) {
                $regions_names[$index + 1] = new \stdClass();
                $regions_names[$index + 1]->data = $call->Nom_Region;
                $regions_names[$index + 1]->name = $call->Nom_Region;
                $row->Resultat_Appel = $call->Resultat_Appel;
                $nom_region = $call->Nom_Region;
                $row->regions['zone_' . $index] = $call->$nom_region;
                $row->$nom_region = $call->$nom_region;
                $row->total = round(array_sum($row->regions) / count($row->regions), 2);
                return $row;
            });
            return $item->last();
        });
        $regions_names[] = new \stdClass();
        $regions_names[count($regions_names) - 1]->data = 'total';
        $regions_names[count($regions_names) - 1]->name = 'total';

        $regions_names = collect($regions_names)->unique()->filter()->values();
        $regions = $regions->values();
//        dd($regions);

        $dt = DataTables::of($regions);
//            ->setRowId(function ($role) {
//                return 'region-' . $role->id;
//            })
//        $dt = $regions_names->map(function ($region, $index) use ($dt, $regions) {
////            return $dt->rawColumns([$region->data, $regions[0]->Resultat_Appel]);
////            return $dt->rawColumns([$region->data, $regions[0]->Resultat_Appel]);
////            return $dt->rawColumns([$region->data, $regions[0]->Resultat_Appel]);
//        });
        $dt = $dt->toJson();
        return $dt;
    }
}
