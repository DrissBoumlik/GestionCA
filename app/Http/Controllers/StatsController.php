<?php

namespace App\Http\Controllers;

use App\Models\Stats;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StatsController extends Controller
{
    public function dashboard(Request $request)
    {
        $regions = Stats::getRegions();
        $joignable = Stats::getClientsByCallState('Joignable');
        $inJoignable = Stats::getClientsByCallState('Injoignable');
//        return [$regions, $joignable, $inJoignable];
        if ($request->exists('json'))
            return [
                'regions' => $regions,
                'joignable' => $joignable,
                'inJoignable' => $inJoignable
            ];
        return view('stats.dashboard', [
            'regions' => $regions,
            'joignable' => $joignable,
            'inJoignable' => $inJoignable
        ]);
    }

    public function getDates(Request $request)
    {
        $dates = Stats::distinct()->pluck('Date_Note');

        $dates = $dates->map(function ($date, $index) {
            $item = new \stdClass();
            $item->date = $date;
            $item->id = $index;
//            $date = ['date' => $date, 'id' => $date];
            return $item;
        });


        if ($request->searchTerm) {
            $dates = $dates->filter(function ($date) use ($request) {
                return Str::contains(strtolower($date->date), strtolower($request->searchTerm));
            })->values();
            return ['dates' => $dates];
        }
        return ['dates' => $dates];
    }

    public function getRegionsByDate(Request $request)
    {
//        dd($request->dates);
        $data = array_filter($request->dates, function ($date) {
            return $date != null;
        });
//        dd($data);
        $regions = Stats::getRegions($data);

        dd($regions);
    }
}
