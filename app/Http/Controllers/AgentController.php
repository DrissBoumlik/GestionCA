<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;


class AgentController extends Controller
{
    protected $agentRepository;
    public function __construct(AgentRepository $agentRepository)
    {
        $this->agentRepository = $agentRepository;
    }

    public function getAgents (Request $request) {
        return $this->agentRepository->getAgents($request);
    }

    public function index(Request $request)
    {
        $AgentName = $request->agent_name;
        $dataRegionsCallResult = $this->agentRepository->GetDataRegions('Resultat_Appel', $request);
        $dataRegionsCallStateByRegions = $this->agentRepository->GetDataRegionsCallState('Nom_Region', $request);
        $dataRegionsCallStateByWeek = $this->agentRepository->GetDataRegionsCallState('Date_Heure_Note_Semaine', $request);
//        dd($dataRegionsCallStateByRegions, $dataRegionsCallStateByWeek);
        $dataCallsPos = $this->agentRepository->getDataClientsByCallState('Joignable', $request);
        $dataCallsNeg = $this->agentRepository->getDataClientsByCallState('Injoignable', $request);

        $dataTypeInterv = $this->agentRepository->getDataNonValidatedFolders('Code_Type_Intervention', $request);
        $dataCodeInterv = $this->agentRepository->getDataNonValidatedFolders('Code_Intervention', $request);


        return view('stats.agents')->with([
            'calls_results' => $dataRegionsCallResult['columns'],

            'calls_states_regions' => $dataRegionsCallStateByRegions['columns'],
            'calls_states_weeks' => $dataRegionsCallStateByWeek['columns'],

            'regions_names_type' => $dataTypeInterv['columns'],
            'regions_names_code' => $dataCodeInterv['columns'],

            'calls_pos' => $dataCallsPos['columns'],
            'calls_neg' => $dataCallsNeg['columns'],
            'agent' => $AgentName
        ]);
    }

    public function getDates(Request $request)
    {
//        $dates = Stats::getDateNotes();
//        $dates = Stats::getDateNotes2();
        $AgentName = $request->get('agent_name');
        $dates = $this->agentRepository->getDateNotes($AgentName);
        return ['dates' => $dates];
    }

    #region Regions =====================================================

    public function getRegionsColumn(Request $request, $callResult)
    {
        $data = $this->agentRepository->GetDataRegions($callResult, $request);
//        $_data = new \stdClass();
//        $_data->data = $data['regions'];
//        $_data->column = $callResult;
        return ['columns' => $data['columns'], 'data' => $data['data']];
    }

    public function filterList ($column, Request $request) {
        $stats = $this->agentRepository->filterList($column, $request);
        return [
            'data' => $stats
        ];
    }

    public function getRegions(Request $request, $callResult)
    {
        $data = $this->agentRepository->GetDataRegions($callResult, $request);
        return DataTables::of($data['data'])->toJson();
    }
    #endregion

    #region Call Stats

    public function getRegionsCallStateColumn(Request $request, $column)
    {
        $data = $this->agentRepository->GetDataRegionsCallState($column, $request);
        return ['columns' => $data['columns'], 'data' => $data['data']];
    }

    public function getRegionsCallState(Request $request, $column)
    {
        $data = $this->agentRepository->GetDataRegionsCallState($column, $request);
        return DataTables::of($data['data'])->toJson();
    }

    #endregion

    #region NonValidatedFolders =====================================================

    public function getNonValidatedFoldersColumn(Request $request, $column)
    {
        $data = $this->agentRepository->getDataNonValidatedFolders($column, $request);
        return ['columns' => $data['columns'], 'data' => $data['data']];
    }

    public function getNonValidatedFolders(Request $request, $column)
    {
        $data = $this->agentRepository->getDataNonValidatedFolders($column, $request);
        return DataTables::of($data['data'])->toJson();
    }

    #endregion

    #region ClientsByCallState =====================================================

    public function getClientsByCallStateColumn(Request $request, $callResult)
    {
        $data = $this->agentRepository->getDataClientsByCallState($callResult, $request);
        return ['columns' => $data['columns'], 'data' => $data['data']];
    }

    public function getClientsByCallState(Request $request, $callResult)
    {
        $data = $this->agentRepository->getDataClientsByCallState($callResult, $request);
        return DataTables::of($data['data'])->toJson();
    }

    #endregion

}
