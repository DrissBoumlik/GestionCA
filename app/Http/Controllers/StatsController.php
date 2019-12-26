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

        $dataPerimeter = $this->statsRepository->getDataClientsByPerimeter();


        return view('stats.dashboard')->with([
            'calls_results' => $dataRegionsCallResult['columns'],

            'calls_states_regions' => $dataRegionsCallStateByRegions['columns'],
            'calls_states_weeks' => $dataRegionsCallStateByWeek['columns'],

            'regions_names_type' => $dataTypeInterv['columns'],
            'regions_names_code' => $dataCodeInterv['columns'],

            'calls_pos' => $dataCallsPos['columns'],
            'calls_neg' => $dataCallsNeg['columns'],

            'perimeters' => $dataPerimeter['columns'],
        ]);
    }

    public function getDates(Request $request)
    {
        $dates = $this->statsRepository->getDateNotes();
        return ['dates' => $dates];
    }

    #region Regions =====================================================

    public function getRegionsColumn(Request $request, $callResult)
    {
        $dates = $request->get('data');
        $data = $this->statsRepository->GetDataRegions($callResult, $dates);
//        $_data = new \stdClass();
//        $_data->data = $data['regions'];
//        $_data->column = $callResult;
//        dd(count($data['data']));
        return $data; //['columns' => $data['columns'], 'data' => $data['data']];
    }

    public function getRegions(Request $request, $callResult)
    {
        $dates = $request->get('data');
        $data = $this->statsRepository->GetDataRegions($callResult, $dates);
        return DataTables::of($data['data'])->toJson();
    }



    #endregion

    #region Call Stats ======================================================

    public function getRegionsCallStateColumn(Request $request, $column)
    {
        $dates = $request->get('dates');
        $data = $this->statsRepository->GetDataRegionsCallState($column, $dates);
        return $data; // ['columns' => $data['columns'], 'data' => $data['data']];
    }

    public function getRegionsCallState(Request $request, $column)
    {
        $dates = $request->get('dates');
        $data = $this->statsRepository->GetDataRegionsCallState($column, $dates);
        return DataTables::of($data['data'])->toJson();
    }

    #endregion

    #region NonValidatedFolders =====================================================

    public function getNonValidatedFoldersColumn(Request $request, $column)
    {
        $dates = $request->get('dates');
        $data = $this->statsRepository->getDataNonValidatedFolders($column, $dates);
        return $data; // ['columns' => $data['columns'], 'data' => $data['data']];
    }

    public function getNonValidatedFolders(Request $request, $column)
    {
        $dates = $request->get('dates');
        $data = $this->statsRepository->getDataNonValidatedFolders($column, $dates);
        return DataTables::of($data['data'])->toJson();
    }

    #endregion

    #region ClientsByCallState =====================================================

    public function getClientsByCallStateColumn(Request $request, $callResult)
    {
        $dates = $request->get('dates');
        $data = $this->statsRepository->getDataClientsByCallState($callResult, $dates);
        return $data; // ['columns' => $data['columns'], 'data' => $data['data']];
    }

    public function getClientsByCallState(Request $request, $callResult)
    {
        $dates = $request->get('dates');
        $data = $this->statsRepository->getDataClientsByCallState($callResult, $dates);
        return DataTables::of($data['data'])->toJson();
    }

    #endregion


    #region ClientsByPerimeter =====================================================

    public function getClientsByPerimeterColumn(Request $request)
    {
        $dates = $request->get('dates');
        $data = $this->statsRepository->getDataClientsByPerimeter($dates);
        return $data; // ['columns' => $data['columns'], 'data' => $data['data']];
    }

    public function getClientsByPerimeter(Request $request)
    {
        $dates = $request->get('dates');
        $data = $this->statsRepository->getDataClientsByPerimeter($dates);
        return DataTables::of($data['data'])->toJson();
    }

    #endregion

    public function index()
    {
        return view('stats.import');
    }

    public function importStats(Request $request)
    {
        return response()->json($this->statsRepository->importStats($request));
    }

}
