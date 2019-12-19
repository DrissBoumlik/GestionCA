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

    public function getRegionsByDates(Request $request)
    {
        $data = array_filter($request->dates_1, function ($date) {
            return $date != null;
        });

        $regions = Stats::getRegions($data);
        $foldersByIntervCode = Stats::getNonValidatedFolders('Code_Intervention');
        $foldersByIntervType = Stats::getNonValidatedFolders('Code_Type_Intervention');

        $joignable = Stats::getClientsByCallState('Joignable');
        $inJoignable = Stats::getClientsByCallState('Injoignable');
//        return [$regions, $joignable, $inJoignable];
        if ($request->exists('json')) {
            return [
                'regions' => $regions,
                'foldersByIntervType' => $foldersByIntervType,
                'foldersByIntervCode' => $foldersByIntervCode,
                'joignable' => $joignable,
                'inJoignable' => $inJoignable
            ];
        }
        return back()->with([
            'regions' => $regions,
            'foldersByIntervType' => $foldersByIntervType,
            'foldersByIntervCode' => $foldersByIntervCode,
            'joignable' => $joignable,
            'inJoignable' => $inJoignable
        ]);
    }

    public function getNonValidatedFoldersByCodeByDates(Request $request)
    {
        $data = array_filter($request->dates_4, function ($date) {
            return $date != null;
        });

        $regions = Stats::getRegions();
        $foldersByIntervCode = Stats::getNonValidatedFolders('Code_Intervention', $data);
        $foldersByIntervType = Stats::getNonValidatedFolders('Code_Type_Intervention');
//        return [$regions, $joignable, $inJoignable];
        $joignable = Stats::getClientsByCallState('Joignable');
        $inJoignable = Stats::getClientsByCallState('Injoignable');
        if ($request->exists('json')) {
            return [
                'regions' => $regions,
                'foldersByIntervType' => $foldersByIntervType,
                'foldersByIntervCode' => $foldersByIntervCode,
                'joignable' => $joignable,
                'inJoignable' => $inJoignable

            ];
        }
        return back()->with([
            'regions' => $regions,
            'foldersByIntervType' => $foldersByIntervType,
            'foldersByIntervCode' => $foldersByIntervCode,
            'joignable' => $joignable,
            'inJoignable' => $inJoignable
        ]);
    }

    public function getNonValidatedFoldersByTypeByDates(Request $request)
    {
        $data = array_filter($request->dates_3, function ($date) {
            return $date != null;
        });

        $regions = Stats::getRegions($data);
        $foldersByIntervCode = Stats::getNonValidatedFolders('Code_Intervention');
        $foldersByIntervType = Stats::getNonValidatedFolders('Code_Type_Intervention', $data);
//        return [$regions, $joignable, $inJoignable];
        $joignable = Stats::getClientsByCallState('Joignable');
        $inJoignable = Stats::getClientsByCallState('Injoignable');
        if ($request->exists('json')) {
            return [
                'regions' => $regions,
                'foldersByIntervType' => $foldersByIntervType,
                'foldersByIntervCode' => $foldersByIntervCode,
                'joignable' => $joignable,
                'inJoignable' => $inJoignable
            ];
        }
        return back()->with([
            'regions' => $regions,
            'foldersByIntervType' => $foldersByIntervType,
            'foldersByIntervCode' => $foldersByIntervCode,
            'joignable' => $joignable,
            'inJoignable' => $inJoignable
        ]);
    }

    public function getClientsByCallStateJoiByDates(Request $request)
    {
        $data = array_filter($request->dates_1, function ($date) {
            return $date != null;
        });

        $regions = Stats::getRegions($data);
        $foldersByIntervCode = Stats::getNonValidatedFolders('Code_Intervention');
        $foldersByIntervType = Stats::getNonValidatedFolders('Code_Type_Intervention');
//        return [$regions, $joignable, $inJoignable];
        $joignable = Stats::getClientsByCallState('Joignable', $data);
        $inJoignable = Stats::getClientsByCallState('Injoignable');
        if ($request->exists('json')) {
            return [
                'regions' => $regions,
                'foldersByIntervType' => $foldersByIntervType,
                'foldersByIntervCode' => $foldersByIntervCode,
                'joignable' => $joignable,
                'inJoignable' => $inJoignable
            ];
        }
        return back()->with([
            'regions' => $regions,
            'foldersByIntervType' => $foldersByIntervType,
            'foldersByIntervCode' => $foldersByIntervCode,
            'joignable' => $joignable,
            'inJoignable' => $inJoignable
        ]);
    }

    public function getClientsByCallStateInjByDates(Request $request)
    {
        $data = array_filter($request->dates_2, function ($date) {
            return $date != null;
        });

        $regions = Stats::getRegions($data);
        $foldersByIntervCode = Stats::getNonValidatedFolders('Code_Intervention');
        $foldersByIntervType = Stats::getNonValidatedFolders('Code_Type_Intervention');
//        return [$regions, $joignable, $inJoignable];
        $joignable = Stats::getClientsByCallState('Joignable');
        $inJoignable = Stats::getClientsByCallState('Injoignable', $data);
        if ($request->exists('json')) {
            return [
                'regions' => $regions,
                'foldersByIntervType' => $foldersByIntervType,
                'foldersByIntervCode' => $foldersByIntervCode,
                'joignable' => $joignable,
                'inJoignable' => $inJoignable
            ];
        }
        return back()->with([
            'regions' => $regions,
            'foldersByIntervType' => $foldersByIntervType,
            'foldersByIntervCode' => $foldersByIntervCode,
            'joignable' => $joignable,
            'inJoignable' => $inJoignable
        ]);
    }


    public function getRegions(Request $request)
    {
        $dates = null;
        if ($request->exists('dates')) {
            $dates = array_filter($request->dates, function ($date) {
                return $date != null;
            });
        }

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

        $regions = $regions->map(function ($region) use (&$regions_names) {
            $row = new \stdClass();
            $row->regions = [];
            $item = $region->map(function ($call, $index) use (&$row, &$regions_names) {
                $regions_names[] = $call->Nom_Region;
                $row->Resultat_Appel = $call->Resultat_Appel;
                $nom_region = $call->Nom_Region;
                $row->regions['zone_' . $index] = $call->$nom_region;
                $row->$nom_region = $call->$nom_region;
                $row->total = round(array_sum($row->regions) / count($row->regions), 2);
                return $row;
            });
            return $item->last();
        });
        $regions_names = collect($regions_names)->unique()->values();
        $regions = $regions->values();
        $data = ['regions_names' => $regions_names, 'calls' => $regions];
        return $data;
    }

    public function byAgency(Request $request)
    {
        return view('stats.agencies');
    }

    public function byAgencyJson()
    {

    }
}
