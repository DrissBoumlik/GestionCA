<?php

namespace App\Http\Controllers;

use App\Models\Stats;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StatsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard(Request $request)
    {
        $regions = Stats::getRegions();
        $foldersByIntervCode = Stats::getNonValidatedFolders('Code_Intervention');
        $foldersByIntervType = Stats::getNonValidatedFolders('Code_Type_Intervention');
//        return [$folders, $regions];
        $joignable = Stats::getClientsByCallState('Joignable');
        $inJoignable = Stats::getClientsByCallState('Injoignable');
//        return [$regions, $joignable, $inJoignable];
        if ($request->exists('json'))
            return [
                'regions' => $regions,
                'foldersByIntervType' => $foldersByIntervType,
                'foldersByIntervCode' => $foldersByIntervCode,
                'joignable' => $joignable,
                'inJoignable' => $inJoignable
            ];
        return view('stats.dashboard')->with([
            'regions' => $regions,
            'foldersByIntervType' => $foldersByIntervType,
            'foldersByIntervCode' => $foldersByIntervCode,
            'joignable' => $joignable,
            'inJoignable' => $inJoignable
        ]);
    }

    public function getDates(Request $request)
    {
        $dates = Stats::getDateNotes();
        return ['dates' => $dates];
    }

    public function getRegionsByDate(Request $request)
    {
        $data = array_filter($request->dates, function ($date) {
            return $date != null;
        });

        $regions = Stats::getRegions($data);
        $joignable = Stats::getClientsByCallState('Joignable');
        $inJoignable = Stats::getClientsByCallState('Injoignable');
//        return [$regions, $joignable, $inJoignable];
        if ($request->exists('json'))
            return [
                'regions' => $regions,
                'joignable' => $joignable,
                'inJoignable' => $inJoignable
            ];
        return back()->with([
            'regions' => $regions,
            'joignable' => $joignable,
            'inJoignable' => $inJoignable
        ]);
    }

    public function byAgency(Request $request)
    {
        return view('stats.agencies');
    }

    public function byAgencyJson()
    {

    }
}
