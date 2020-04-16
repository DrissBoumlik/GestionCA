<?php

namespace App\Http\Controllers;

use App\Models\Stats;
use App\Repositories\StatsRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
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

    public function index(Request $request)
    {
        return view('stats.index')->with(['data' => $request->all()]);
    }

    public function getStats(Request $request)
    {
        $stats = $this->statsRepository->getStats($request);
//        return $stats;
        return DataTables::of($stats)->toJson();
    }

    //region Agencies / Agents
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
    //endregion

    public function dashboard(Request $request)
    {
        $agenceCode = $request->agence_code;
        $AgentName = $request->agent_name;
        $data = ['agence' => $agenceCode, 'agent' => $AgentName];
        return view('stats.dashboard')->with($data);
    }

    public function getDates(Request $request)
    {
        $dates = $this->statsRepository->getDateNotes($request);
        return ['dates' => $dates];
    }

    #region Regions / Folders

    public function getRegionsColumn(Request $request, $callResult)
    {
        $data = $this->statsRepository->GetColumnsRegions($request, $callResult);
        return $data;
    }

    public function getRegions(Request $request, $callResult)
    {
        $data = $this->statsRepository->GetDataRegions($request, $callResult);
        return DataTables::of($data['data'])->toJson();
    }

    public function getRegionsByGrpCallColumns(Request $request)
    {
        $data = $this->statsRepository->GetColumnsRegionsByGrpCall($request);
        return $data;
    }

    public function getRegionsByGrpCall(Request $request)
    {
        $data = $this->statsRepository->GetDataRegionsByGrpCall($request);
        return DataTables::of($data['data'])->toJson();
    }

    #endregion

    #region Call Stats

    public function getRegionsCallStateColumn(Request $request, $column)
    {
        $data = $this->statsRepository->GetColumnsRegionsCallState($request, $column);
        return $data;
    }

    public function getRegionsCallState(Request $request, $column)
    {
        $data = $this->statsRepository->GetDataRegionsCallState($request, $column);
        return DataTables::of($data['data'])->toJson();
    }

    #endregion

    #region NonValidatedFolders

    public function getNonValidatedFoldersColumn(Request $request, $column)
    {
        $data = $this->statsRepository->GetColumnsNonValidatedFolders($request, $column);
        return $data;
    }

    public function getNonValidatedFolders(Request $request, $column)
    {
        $data = $this->statsRepository->GetDataNonValidatedFolders($request, $column);
        return DataTables::of($data['data'])->toJson();
    }

    #endregion

    #region ClientsByCallState

    public function getClientsByCallStateColumn(Request $request, $callResult)
    {
        $data = $this->statsRepository->GetColumnsClientsByCallState($request, $callResult);
        return $data;
    }

    public function getClientsByCallState(Request $request, $callResult)
    {
        $data = $this->statsRepository->GetDataClientsByCallState($request, $callResult);
        return DataTables::of($data['data'])->toJson();
    }

    public function getClientsWithCallStatesColumn(Request $request)
    {
        return $this->statsRepository->GetColumnsClientsWithCallStates($request);
    }

    public function getClientsWithCallStates(Request $request)
    {
        $data = $this->statsRepository->GetDataClientsWithCallStates($request);
        return DataTables::of($data['data'])->toJson();
    }
    #endregion

    #region ClientsByPerimeter

    public function getClientsByPerimeterColumn(Request $request)
    {
        $data = $this->statsRepository->GetColumnsClientsByPerimeter($request);
        return $data;
    }

    public function getClientsByPerimeter(Request $request)
    {
        $data = $this->statsRepository->GetDataClientsByPerimeter($request);
        return DataTables::of($data['data'])->toJson();
    }

    #endregion

    //region Global Stats
    public function getCloturetechColumn(Request $request)
    {
        return $this->statsRepository->GetColumnsCloturetechCall($request);
    }

    public function getCloturetech(Request $request)
    {
        $data = $this->statsRepository->GetCloturetechCall($request);
        return DataTables::of($data['data'])->toJson();
    }

    public function getGlobalDelayColumn(Request $request)
    {
        return $this->statsRepository->GetColumnsGlobalDelayCall($request);
    }

    public function getGlobalDelay(Request $request)
    {
        $data = $this->statsRepository->GetGlobalDelayCall($request);
        return DataTables::of($data['data'])->toJson();
    }

    public function getProcessingDelayColumn(Request $request)
    {
        return $this->statsRepository->GetColumnsProcessingDelayCall($request);
    }

    public function getProcessingDelay(Request $request)
    {
        $data = $this->statsRepository->GetProcessingDelayCall($request);
        return DataTables::of($data['data'])->toJson();
    }

    public function getTypeInterventionColumn(Request $request)
    {
        $data = $this->statsRepository->GetColumnsTypeIntervention($request);
        return $data;
    }

    public function getTypeIntervention(Request $request)
    {
        $data = $this->statsRepository->getTypeIntervention($request);
        return DataTables::of($data['data'])->toJson();
    }

    public function getTypeInterventionColumnGrpCall(Request $request)
    {
        $data = $this->statsRepository->GetColumnsTypeInterventionGrpCall($request);
        return $data;
    }

    public function getTypeInterventionGrpCall(Request $request)
    {
        $data = $this->statsRepository->getTypeInterventionGrpCall($request);
        return DataTables::of($data['data'])->toJson();
    }

    public function getValTypeInterventionColumn(Request $request)
    {
        $data = $this->statsRepository->GetColumnsValTypeIntervention($request);
        return $data;
    }

    public function getValTypeIntervention(Request $request)
    {
        $data = $this->statsRepository->getValTypeIntervention($request);
        return DataTables::of($data['data'])->toJson();
    }

    public function getValTypeInterventionColumnGrpCall(Request $request)
    {
        $data = $this->statsRepository->GetColumnsValTypeInterventionGrpCall($request);
        return $data;
    }

    public function getValTypeInterventionGrpCall(Request $request)
    {
        $data = $this->statsRepository->getValTypeInterventionGrpCall($request);
        return DataTables::of($data['data'])->toJson();
    }

    public function getRepTypeInterventionColumn(Request $request)
    {
        $data = $this->statsRepository->GetColumnsRepTypeIntervention($request);
        return $data;
    }

    public function getRepTypeIntervention(Request $request)
    {
        $data = $this->statsRepository->getRepTypeIntervention($request);
        return DataTables::of($data['data'])->toJson();
    }

    public function getRepTypeInterventionColumnGrpCall(Request $request)
    {
        $data = $this->statsRepository->GetColumnsRepTypeInterventionGrpCall($request);
        return $data;
    }

    public function getRepTypeInterventionGrpCall(Request $request)
    {
        $data = $this->statsRepository->getRepTypeInterventionGrpCall($request);
        return DataTables::of($data['data'])->toJson();
    }

    //endregion


    public function getGlobalViewColumns(Request $request)
    {
        return $this->statsRepository->GetColumnsGlobalView($request);
    }

    public function getGlobalView(Request $request)
    {
        return $this->statsRepository->GetDataGlobalView($request);
    }


    //region Import / Export
    public function import()
    {
        // Authorization
        $this->authorize('view', auth()->user());

        return view('stats.import');
    }

    public function importStats(Request $request)
    {
        // Authorization
//        $this->authorize('view', auth()->user());
//        try {
            return response()->json($this->statsRepository->importStats($request));
//        } catch (\Exception $exception) {
//            return response()->json([
//                'success' => false,
//                'message' => $exception->getMessage()
////                'message' => 'Une erreur est survenue'
//            ], 422);
//        }

    }

    public function exportXls(Request $request)
    {
        return $this->statsRepository->exportXlsCall($request);
    }
    //endregion
}
