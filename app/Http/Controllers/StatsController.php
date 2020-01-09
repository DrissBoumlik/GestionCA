<?php

namespace App\Http\Controllers;

use App\Models\Stats;
use App\Repositories\StatsRepository;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\ParameterBag;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Database\Query\JoinClause;

class StatsController extends Controller
{
    private $statsRepository;

    public function __construct(StatsRepository $statsRepository)
    {
//        $this->middleware('auth');
        $this->statsRepository = $statsRepository;
    }

    public function index()
    {
        return view('stats.index');
    }

    public function getStats(Request $request)
    {
        $stats = $this->statsRepository->getStats($request);
//        return $stats;
        return DataTables::of($stats)->toJson();
    }

    public function getAgencies(Request $request)
    {
        return $this->statsRepository->getAgencies($request);
    }

    public function getAgents(Request $request)
    {
        return $this->statsRepository->getAgents($request);
    }

    public function filterList($column, Request $request)
    {
        $stats = $this->statsRepository->filterList($column, $request);
        return [
            'data' => $stats
        ];
    }

    public function dashboard(Request $request)
    {
        $agenceCode = $request->agence_code;
        $AgentName = $request->agent_name;
        $dataRegionsCallResult = $this->statsRepository->GetDataRegions('Groupement', $request);
        $dataFoldersCallResult = $this->statsRepository->GetDataFolders('Groupement', $request);

        $dataRegionsCallStateByRegions = $this->statsRepository->GetDataRegionsCallState('Nom_Region', $request);
        $dataRegionsCallStateByWeek = $this->statsRepository->GetDataRegionsCallState('Date_Heure_Note_Semaine', $request);
//        dd($dataRegionsCallStateByRegions, $dataRegionsCallStateByWeek);
        $dataCallsPos = $this->statsRepository->getDataClientsByCallState('Joignable', $request);
        $dataCallsNeg = $this->statsRepository->getDataClientsByCallState('Injoignable', $request);

        $dataTypeInterv = $this->statsRepository->getDataNonValidatedFolders('Code_Type_Intervention', $request);
        $dataCodeInterv = $this->statsRepository->getDataNonValidatedFolders('Code_Intervention', $request);

        $dataPerimeter = $this->statsRepository->getDataClientsByPerimeter($request);

        $columns = [
            'calls_results' => $dataRegionsCallResult['columns'],
            'calls_folders' => $dataFoldersCallResult['columns'],

            'calls_states_regions' => $dataRegionsCallStateByRegions['columns'],
            'calls_states_weeks' => $dataRegionsCallStateByWeek['columns'],

            'regions_names_type' => $dataTypeInterv['columns'],
            'regions_names_code' => $dataCodeInterv['columns'],

            'calls_pos' => $dataCallsPos['columns'],
            'calls_neg' => $dataCallsNeg['columns'],

            'perimeters' => $dataPerimeter['columns'],
            'agence' => $agenceCode,
            'agent' => $AgentName
        ];
//        return $columns;
        return view('stats.dashboard')->with($columns);
    }

    public function getDates(Request $request)
    {
        $dates = $this->statsRepository->getDateNotes($request);
        return ['dates' => $dates];
    }

    #region Regions / Folders =====================================================

    public function getRegionsColumn(Request $request, $callResult)
    {
//        $dates = $request->get('data');
        $data = $this->statsRepository->GetDataRegions($callResult, $request);
//        $_data = new \stdClass();
//        $_data->data = $data['regions'];
//        $_data->column = $callResult;
//        dd(count($data['data']));
        return $data; //['columns' => $data['columns'], 'data' => $data['data']];
    }

    public function getRegions(Request $request, $callResult)
    {
        // $dates = $request->get('data');
//        dd($request);
        $data = $this->statsRepository->GetDataRegions($callResult, $request);
        return DataTables::of($data['data'])->toJson();
    }

    public function getRegionsByGrpCall(Request $request)
    {
        // $dates = $request->get('data');
//        dd($request);
        $data = $this->statsRepository->GetDataRegionsByGrpCall($request);
        return DataTables::of($data['data'])->toJson();
    }

    public function getFoldersColumn(Request $request, $callResult)
    {
        $data = $this->statsRepository->GetDataFolders($callResult, $request);
//        $_data = new \stdClass();
//        $_data->data = $data['regions'];
//        $_data->column = $callResult;
//        dd(count($data['data']));
        return $data; //['columns' => $data['columns'], 'data' => $data['data']];
    }

    public function getFolders(Request $request, $callResult)
    {
        $data = $this->statsRepository->GetDataFolders($callResult, $request);
        return DataTables::of($data['data'])->toJson();
    }

    #endregion

    #region Call Stats ======================================================

    public function getRegionsCallStateColumn(Request $request, $column)
    {
        $data = $this->statsRepository->GetDataRegionsCallState($column, $request);
        return $data; // ['columns' => $data['columns'], 'data' => $data['data']];
    }

    public function getRegionsCallState(Request $request, $column)
    {
        $data = $this->statsRepository->GetDataRegionsCallState($column, $request);
        return DataTables::of($data['data'])->toJson();
    }

    #endregion

    #region NonValidatedFolders =====================================================

    public function getNonValidatedFoldersColumn(Request $request, $column)
    {
        $data = $this->statsRepository->getDataNonValidatedFolders($column, $request);
        return $data; // ['columns' => $data['columns'], 'data' => $data['data']];
    }

    public function getNonValidatedFolders(Request $request, $column)
    {
        $data = $this->statsRepository->getDataNonValidatedFolders($column, $request);
        return DataTables::of($data['data'])->toJson();
    }

    #endregion

    #region ClientsByCallState =====================================================

    public function getClientsByCallStateColumn(Request $request, $callResult)
    {
        $data = $this->statsRepository->getDataClientsByCallState($callResult, $request);
        return $data; // ['columns' => $data['columns'], 'data' => $data['data']];
    }

    public function getClientsByCallState(Request $request, $callResult)
    {
        $data = $this->statsRepository->getDataClientsByCallState($callResult, $request);
        return DataTables::of($data['data'])->toJson();
    }

    #endregion


    #region ClientsByPerimeter =====================================================

    public function getClientsByPerimeterColumn(Request $request)
    {
        $data = $this->statsRepository->getDataClientsByPerimeter($request);
        return $data; // ['columns' => $data['columns'], 'data' => $data['data']];
    }

    public function getClientsByPerimeter(Request $request)
    {
        $data = $this->statsRepository->getDataClientsByPerimeter($request);
        return DataTables::of($data['data'])->toJson();
    }

    #endregion

    public function import()
    {
        // Authorization
        $this->authorize('view', auth()->user());

        return view('stats.import');
    }

    public function importStats(Request $request)
    {
        // Authorization
        $this->authorize('view', auth()->user());

        return response()->json($this->statsRepository->importStats($request));
    }
}
