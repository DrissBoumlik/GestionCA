<?php

namespace App\Http\Controllers;

use App\Models\Stats;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DemoController extends Controller
{
    public function dashboard()
    {
        return view('stats.dashboard');
    }

    public function getStats(Request $request)
    {
        $regions = \DB::table('stats')
            ->select('Nom_Region', 'Resultat_Appel', \DB::raw('count(*) as total'))
            ->groupBy('Nom_Region', 'Resultat_Appel')
            ->get();
        $totalCount = Stats::all()->count();
//        $regions = $regions->groupBy('Nom_Region');
//        $regions = $regions->map(function ($region) use ($totalCount) {
////            $calls = $calls->map(function ($call) use ($totalCount) {
////                $call->percent = round($call->total * 100 / $totalCount, 2);
////                return $call;
////            });
////            return $calls;
//            $region->percent = round($region->total * 100 / $totalCount, 2);
//            $Region = $region->Nom_Region;
//            $region->$Region = $region->percent;
//            return $region;
//        });


        $regionsNames = $regions->groupBy('Nom_Region')->keys();
        $regions = $regions->groupBy('Nom_Region');

        $regions = $regions->map(function ($region) use ($totalCount) {
            $calls = $region->map(function ($call) use ($totalCount) {
                $call->percent = round($call->total * 100 / $totalCount, 2);
                $Region = $call->Nom_Region;
                $call->$Region = $call->percent;
                return $call;
            });
            return $calls;
//            $region->percent = round($region->total * 100 / $totalCount, 2);
//            $Region = $region->Nom_Region;
//            $region->$Region = $region->percent;
//            return $region;
        });

        return $regions;
        $index = 0;
        $dt = DataTables::of($regions);
//            ->setRowId(function ($region) use (&$index) {
//                return 'region-' . $index++;
//            });
//        $regions = $regions->map(function ($region) use (&$dt) {
//            $region->map(function ($calls) {
//
//            });
////            $dt->addColumn($region->Nom_Region, $region->percent);
//        });

        return $dt->toJson();
    }
}
