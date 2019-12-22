<?php

namespace App\Http\Controllers;

use App\Models\Stats;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class StatsController extends Controller
{
    private $statsRepository;

    public function __construct(StatsRepository $statsRepository)
    {
//        $this->middleware('auth');
        $this->statsRepository = $statsRepository;
    }

    public function dashboard()
    {
        $dataRegionsCallResult = $this->statsRepository->GetDataRegions('Resultat_Appel');

        $dataRegionsCallStateByRegions = $this->statsRepository->GetDataRegionsCallState('Nom_Region');
        $dataRegionsCallStateByWeek = $this->statsRepository->GetDataRegionsCallState('Date_Heure_Note_Semaine');
//        dd($dataRegionsCallStateByRegions, $dataRegionsCallStateByWeek);
        $dataCallsPos = $this->statsRepository->getDataClientsByCallState('Joignable');
        $dataCallsNeg = $this->statsRepository->getDataClientsByCallState('Injoignable');

        $dataTypeInterv = $this->statsRepository->getDataNonValidatedFolders('Code_Type_Intervention');
        $dataCodeInterv = $this->statsRepository->getDataNonValidatedFolders('Code_Intervention');


        return view('stats.dashboard')->with([
            'calls_results' => $dataRegionsCallResult['regions_names'],

            'calls_states_regions' => $dataRegionsCallStateByRegions['columns'],
            'calls_states_weeks' => $dataRegionsCallStateByWeek['columns'],

            'regions_names_type' => $dataTypeInterv['regions_names'],
            'regions_names_code' => $dataCodeInterv['regions_names'],

            'calls_pos' => $dataCallsPos['codes_names'],
            'calls_neg' => $dataCallsNeg['codes_names'],
        ]);
    }

    public function getDates(Request $request)
    {
//        $dates = Stats::getDateNotes();
//        $dates = Stats::getDateNotes2();
        $dates = $this->statsRepository->getDateNotes();
        return ['dates' => $dates];
    }

    #region Regions =====================================================

    public function getRegionsColumn(Request $request, $callResult)
    {
        $dates = $request->get('dates');
        $data = $this->statsRepository->GetDataRegions($callResult, $dates);
        return ['columns' => $data['regions_names'], 'data' => $data['regions']];
    }

    public function getRegions(Request $request, $callResult)
    {
        $dates = $request->get('dates');
        $data = $this->statsRepository->GetDataRegions($callResult, $dates);
        return DataTables::of($data['regions'])->toJson();
    }



    #endregion

    #region Call Stats

    public function getRegionsCallStateColumn(Request $request, $column)
    {
        $dates = $request->get('dates');
        $data = $this->statsRepository->GetDataRegionsCallState($column, $dates);
        return ['columns' => $data['columns'], 'data' => $data['regions']];
    }

    public function getRegionsCallState(Request $request, $column)
    {
        $dates = $request->get('dates');
        $data = $this->statsRepository->GetDataRegionsCallState($column, $dates);
        return DataTables::of($data['regions'])->toJson();
    }

    #endregion

    #region NonValidatedFolders =====================================================

    public function getNonValidatedFoldersColumn(Request $request, $intervCol)
    {
        $dates = $request->get('dates');
        $data = $this->statsRepository->getDataNonValidatedFolders($intervCol, $dates);
        return ['columns' => $data['regions_names']];
    }

    public function getNonValidatedFolders(Request $request, $intervCol)
    {
        $dates = $request->get('dates');
        $data = $this->statsRepository->getDataNonValidatedFolders($intervCol, $dates);
        return DataTables::of($data['codes'])->toJson();
    }

    #endregion

    #region ClientsByCallState =====================================================

    public function getClientsByCallStateColumn(Request $request, $callResult)
    {
        $dates = $request->get('dates');
        $data = $this->statsRepository->getDataClientsByCallState($callResult, $dates);
        return ['columns' => $data['codes_names']];
    }

    public function getClientsByCallState(Request $request, $callResult)
    {
        $dates = $request->get('dates');
        $data = $this->statsRepository->getDataClientsByCallState($callResult, $dates);
        return DataTables::of($data['regions'])->toJson();
    }

    #endregion

    #region OldCode
    //    private $statsRepository;
    //
    //    public function __construct(StatsRepository $statsRepository)
    //    {
    ////        $this->middleware('auth');
    //        $this->statsRepository = $statsRepository;
    //    }
    //
    //    public function dashboard(Request $request)
    //    {
    //        $data = $this->statsRepository->dashboard($request);
    //
    //        if ($request->exists('json')) {
    //            return $data;
    //        }
    //        return view('stats.dashboard')->with($data);
    //    }
    //
    //    public function getDates(Request $request)
    //    {
    //        $dates = Stats::getDateNotes();
    //        return ['dates' => $dates];
    //    }
    //
    //    public function getRegionsByDates(Request $request)
    //    {
    //        $data = $this->statsRepository->getRegionsByDates($request);
    //
    //        if ($request->exists('json')) {
    //            return $data;
    //        }
    //        return view('stats.dashboard')->with($data);
    //    }
    //
    //    public function getNonValidatedFoldersByCodeByDates(Request $request)
    //    {
    //        $data = $this->statsRepository->getNonValidatedFoldersByCodeByDates($request);
    //
    //        if ($request->exists('json')) {
    //            return $data;
    //        }
    //        return view('stats.dashboard')->with($data);
    //    }
    //
    //    public function getNonValidatedFoldersByTypeByDates(Request $request)
    //    {
    //        $data = $this->statsRepository->getNonValidatedFoldersByTypeByDates($request);
    //
    //        if ($request->exists('json')) {
    //            return $data;
    //        }
    //        return view('stats.dashboard')->with($data);
    //    }
    //
    //    public function getClientsByCallStateJoiByDates(Request $request)
    //    {
    //        $data = $this->statsRepository->getClientsByCallStateJoiByDates($request);
    //
    //        if ($request->exists('json')) {
    //            return $data;
    //        }
    //        return view('stats.dashboard')->with($data);
    //    }
    //
    //    public function getClientsByCallStateInjByDates(Request $request)
    //    {
    //        $data = $this->statsRepository->getClientsByCallStateInjByDates($request);
    //
    //        if ($request->exists('json')) {
    //            return $data;
    //        }
    //        return view('stats.dashboard')->with($data);
    //    }
    //
    //
    //    public function getRegions(Request $request)
    //    {
    //        $dates = null;
    //        if ($request->exists('dates')) {
    //            $dates = array_filter($request->dates, function ($date) {
    //                return $date != null;
    //            });
    //        }
    //
    //        $regions = \DB::table('stats')
    //            ->select('Nom_Region', 'Resultat_Appel', \DB::raw('count(*) as total'));
    //
    //        if ($dates) {
    //            $dates = array_values($dates);
    //            $regions = $regions->whereIn('Date_Note', $dates);
    //        }
    ////        $regions = ($dates ? $regions->whereIn('Date_Note', $dates)->get() : $regions)->get();
    //
    //        $regions = $regions->groupBy('Nom_Region', 'Resultat_Appel')->get();
    //
    //        $totalCount = Stats::all()->count();
    //        $regions = $regions->map(function ($region) use ($totalCount) {
    //            $Region = $region->Nom_Region;
    //            $region->$Region = round($region->total * 100 / $totalCount, 2);;
    //            return $region;
    //        });
    //        $regions = $regions->groupBy(['Resultat_Appel']);
    //
    //        $regions_names = [];
    //
    //        $regions = $regions->map(function ($region) use (&$regions_names) {
    //            $row = new \stdClass();
    //            $row->regions = [];
    //            $item = $region->map(function ($call, $index) use (&$row, &$regions_names) {
    //                $regions_names[] = $call->Nom_Region;
    //                $row->Resultat_Appel = $call->Resultat_Appel;
    //                $nom_region = $call->Nom_Region;
    //                $row->regions['zone_' . $index] = $call->$nom_region;
    //                $row->$nom_region = $call->$nom_region;
    //                $row->total = round(array_sum($row->regions) / count($row->regions), 2);
    //                return $row;
    //            });
    //            return $item->last();
    //        });
    //        $regions_names = collect($regions_names)->unique()->values();
    //        $regions = $regions->values();
    //        $data = ['regions_names' => $regions_names, 'calls' => $regions];
    //        return $data;
    //    }
    //
    //    public function byAgency(Request $request)
    //    {
    //        return view('stats.agencies');
    //    }
    //
    //    public function byAgencyJson()
    //    {
    //
    //    }
    //
    //
    //    public function index()
    //    {
    //        return view('stats.import');
    //    }
    //
    //    public function importStats(Request $request)
    //    {
    //        return response()->json($this->statsRepository->importStats($request));
    //    }
    #endregion

}
