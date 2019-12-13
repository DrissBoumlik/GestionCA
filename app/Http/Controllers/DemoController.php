<?php

namespace App\Http\Controllers;

use App\Models\Stats;
use Illuminate\Http\Request;

class DemoController extends Controller
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
}
