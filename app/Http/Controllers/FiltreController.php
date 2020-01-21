<?php

namespace App\Http\Controllers;

use App\Repositories\FilterRepository;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class FilterController extends Controller
{
    private $filterRepository;

    public function __construct(FilterRepository $filterRepository)
    {
        $this->filterRepository = $filterRepository;
    }

    public function getRegionsByGrpCallColumns(Request $request)
    {
        return $this->filterRepository->GetDataRegionsByGrpCall($request);
    }

    public function getRegionsByGrpCall(Request $request)
    {
        $data = $this->filterRepository->GetDataRegionsByGrpCall($request);
        return DataTables::of($data['data'])->toJson();
    }

    public function getRegionsCallStateColumn(Request $request, $column)
    {
         return  $this->filterRepository->GetDataRegionsCallState($request, $column);
    }

    public function getRegionsCallState(Request $request, $column)
    {
        $data = $this->filterRepository->GetDataRegionsCallState($request, $column);
        return DataTables::of($data['data'])->toJson();
    }

    public function getClientsByCallStateColumn(Request $request, $callResult)
    {
        return  $this->filterRepository->getDataClientsByCallState($request, $callResult);

    }

    public function getClientsByCallState(Request $request, $callResult)
    {
        $data = $this->filterRepository->getDataClientsByCallState($request, $callResult);
        return DataTables::of($data['data'])->toJson();
    }

    public function dashboard_filter(Request $request, $filter)
    {
        $functionName = 'getColumns_' . str_replace('-', '_', $filter);
        $columns = $this->$functionName($request, $filter);
//        $columns = call_user_func('getColumns_' . $functionName, $request);
//        $columns = $this->getColumns($request, $filter);
        $viewName = $filter;
        return view('stats.details.' . $viewName)->with($columns);
    }

    #region Get Columns
    public function getColumns_appels_pralables(Request $request, $filter = null)
    {
        $agenceCode = $request->agence_code;
        $AgentName = $request->agent_name;
        $dataRegionsCallResultDetails = $this->filterRepository->GetDataRegionsByGrpCall($request, $filter);
        $dataRegionsCallStateByRegions = $this->filterRepository->GetDataRegionsCallState($request, 'Nom_Region', $filter);
        $dataRegionsCallStateByWeek = $this->filterRepository->GetDataRegionsCallState($request, 'Date_Heure_Note_Semaine', $filter);
        $dataCallsPos = $this->filterRepository->getDataClientsByCallState($request, 'Joignable', $filter);
        $dataCallsNeg = $this->filterRepository->getDataClientsByCallState($request, 'Injoignable', $filter);

        return [
            'calls_results_details' => $dataRegionsCallResultDetails['columns'],

            'calls_states_regions' => $dataRegionsCallStateByRegions['columns'],
            'calls_states_weeks' => $dataRegionsCallStateByWeek['columns'],

            'calls_pos' => $dataCallsPos['columns'],
            'calls_neg' => $dataCallsNeg['columns'],

            'agence' => $agenceCode,
            'agent' => $AgentName
        ];
    }
//    public function getColumns_appels_clture(Request $request, $filter = null)
//    {
//        $agenceCode = $request->agence_code;
//        $AgentName = $request->agent_name;
//        $dataFoldersCallResult = $this->filterRepository->GetDataFolders($request, 'Groupement');
//
//        $dataTypeInterv = $this->filterRepository->getDataNonValidatedFolders($request, 'Code_Type_Intervention');
//        $dataCodeInterv = $this->filterRepository->getDataNonValidatedFolders($request, 'Code_Intervention');
//
//        return [
//            'calls_folders' => $dataFoldersCallResult['columns'],
//
//            'regions_names_type' => $dataTypeInterv['columns'],
//            'regions_names_code' => $dataCodeInterv['columns'],
//
//            'agence' => $agenceCode,
//            'agent' => $AgentName
//        ];
//    }
//    public function getColumns_appels_gem(Request $request, $filter = null)
//    {
//        $agenceCode = $request->agence_code;
//        $AgentName = $request->agent_name;
//        $dataRegionsCallResultDetails = $this->filterRepository->GetDataRegionsByGrpCall($request, $filter);
//
//        $dataRegionsCallStateByRegions = $this->filterRepository->GetDataRegionsCallState($request, 'Nom_Region');
//        $dataRegionsCallStateByWeek = $this->filterRepository->GetDataRegionsCallState($request, 'Date_Heure_Note_Semaine');
//        return [
//            'calls_results_details' => $dataRegionsCallResultDetails['columns'],
//
//            'calls_states_regions' => $dataRegionsCallStateByRegions['columns'],
//            'calls_states_weeks' => $dataRegionsCallStateByWeek['columns'],
//
//            'agence' => $agenceCode,
//            'agent' => $AgentName
//        ];
//    }
//    public function getColumns_production_globale_cam(Request $request, $filter = null)
//    {
//        $agenceCode = $request->agence_code;
//        $AgentName = $request->agent_name;
//        $dataPerimeter = $this->filterRepository->getDataClientsByPerimeter($request, $filter);
//        return [
//            'perimeters' => $dataPerimeter['columns'],
//            'agence' => $agenceCode,
//            'agent' => $AgentName
//        ];
//    }
    #endregion

}
