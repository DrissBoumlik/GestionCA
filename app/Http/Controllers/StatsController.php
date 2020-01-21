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
        return view('stats.dashboard');
    }

    public function getDates(Request $request)
    {
        $dates = $this->statsRepository->getDateNotes($request);
        return ['dates' => $dates];
    }

    #region Regions / Folders =====================================================

    public function getRegionsColumn(Request $request, $callResult)
    {
        $data = $this->statsRepository->GetDataRegions($request, $callResult);
        return $data;
    }

    public function getRegions(Request $request, $callResult)
    {
        $data = $this->statsRepository->GetDataRegions($request, $callResult);
        return DataTables::of($data['data'])->toJson();
    }

    public function getRegionsByGrpCallColumns(Request $request)
    {
        $data = $this->statsRepository->GetDataRegionsByGrpCall($request);
        return $data;
    }

    public function getRegionsByGrpCall(Request $request)
    {
        $data = $this->statsRepository->GetDataRegionsByGrpCall($request);
        return DataTables::of($data['data'])->toJson();
    }

    public function getFoldersColumn(Request $request, $callResult)
    {
        $data = $this->statsRepository->GetDataFolders($request, $callResult);
        return $data;
    }

    public function getFolders(Request $request, $callResult)
    {
        $data = $this->statsRepository->GetDataFolders($request, $callResult);
        return DataTables::of($data['data'])->toJson();
    }

    #endregion

    #region Call Stats ======================================================

    public function getRegionsCallStateColumn(Request $request, $column)
    {
        $data = $this->statsRepository->GetDataRegionsCallState($request, $column);
        return $data;
    }

    public function getRegionsCallState(Request $request, $column)
    {
        $data = $this->statsRepository->GetDataRegionsCallState($request, $column);
        return DataTables::of($data['data'])->toJson();
    }

    #endregion

    #region NonValidatedFolders =====================================================

    public function getNonValidatedFoldersColumn(Request $request, $column)
    {
        $data = $this->statsRepository->getDataNonValidatedFolders($request, $column);
        return $data;
    }

    public function getNonValidatedFolders(Request $request, $column)
    {
        $data = $this->statsRepository->getDataNonValidatedFolders($request, $column);
        return DataTables::of($data['data'])->toJson();
    }

    #endregion

    #region ClientsByCallState =====================================================

    public function getClientsByCallStateColumn(Request $request, $callResult)
    {
        $data = $this->statsRepository->getDataClientsByCallState($request, $callResult);
        return $data;
    }

    public function getClientsByCallState(Request $request, $callResult)
    {
        $data = $this->statsRepository->getDataClientsByCallState($request, $callResult);
        return DataTables::of($data['data'])->toJson();
    }

    #endregion

    #region ClientsByPerimeter =====================================================

    public function getClientsByPerimeterColumn(Request $request)
    {
        $data = $this->statsRepository->getDataClientsByPerimeter($request);
        return $data;
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
