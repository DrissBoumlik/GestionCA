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
        $dataRegionsCallResult = $this->agentRepository->GetDataRegions('Resultat_Appel', null, $AgentName);
        $dataRegionsCallStateByRegions = $this->agentRepository->GetDataRegionsCallState('Nom_Region', null, $AgentName);
        $dataRegionsCallStateByWeek = $this->agentRepository->GetDataRegionsCallState('Date_Heure_Note_Semaine', null, $AgentName);
//        dd($dataRegionsCallStateByRegions, $dataRegionsCallStateByWeek);
        $dataCallsPos = $this->agentRepository->getDataClientsByCallState('Joignable', null, $AgentName);
        $dataCallsNeg = $this->agentRepository->getDataClientsByCallState('Injoignable', null, $AgentName);

        $dataTypeInterv = $this->agentRepository->getDataNonValidatedFolders('Code_Type_Intervention', null, $AgentName);
        $dataCodeInterv = $this->agentRepository->getDataNonValidatedFolders('Code_Intervention', null, $AgentName);


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
        $dates = $request->get('dates');
        $AgentName = $request->get('agent_name');
        logger($AgentName);
        $data = $this->agentRepository->GetDataRegions($callResult, $dates, $AgentName);
//        $_data = new \stdClass();
//        $_data->data = $data['regions'];
//        $_data->column = $callResult;
        return ['columns' => $data['columns'], 'data' => $data['data']];
    }

    public function getRegions(Request $request, $callResult)
    {
        $dates = $request->get('dates');
        $AgentName = $request->get('agent_name');
        $data = $this->agentRepository->GetDataRegions($callResult, $dates, $AgentName);
        return DataTables::of($data['data'])->toJson();
    }
    #endregion

    #region Call Stats

    public function getRegionsCallStateColumn(Request $request, $column)
    {
        $dates = $request->get('dates');
        $AgentName = $request->get('agent_name');
        $data = $this->agentRepository->GetDataRegionsCallState($column, $dates, $AgentName);
        return ['columns' => $data['columns'], 'data' => $data['data']];
    }

    public function getRegionsCallState(Request $request, $column)
    {
        $dates = $request->get('dates');
        $AgentName = $request->get('agent_name');
        $data = $this->agentRepository->GetDataRegionsCallState($column, $dates, $AgentName);
        return DataTables::of($data['data'])->toJson();
    }

    #endregion

    #region NonValidatedFolders =====================================================

    public function getNonValidatedFoldersColumn(Request $request, $column)
    {
        $dates = $request->get('dates');
        $AgentName = $request->get('agent_name');
        $data = $this->agentRepository->getDataNonValidatedFolders($column, $dates, $AgentName);
        return ['columns' => $data['columns'], 'data' => $data['data']];
    }

    public function getNonValidatedFolders(Request $request, $column)
    {
        $dates = $request->get('dates');
        $AgentName = $request->get('agent_name');
        $data = $this->agentRepository->getDataNonValidatedFolders($column, $dates, $AgentName);
        return DataTables::of($data['data'])->toJson();
    }

    #endregion

    #region ClientsByCallState =====================================================

    public function getClientsByCallStateColumn(Request $request, $callResult)
    {
        $dates = $request->get('dates');
        $AgentName = $request->get('agent_name');
        $data = $this->agentRepository->getDataClientsByCallState($callResult, $dates, $AgentName);
        return ['columns' => $data['columns'], 'data' => $data['data']];
    }

    public function getClientsByCallState(Request $request, $callResult)
    {
        $dates = $request->get('dates');
        $AgentName = $request->get('agent_name');
        $data = $this->agentRepository->getDataClientsByCallState($callResult, $dates, $AgentName);
        return DataTables::of($data['data'])->toJson();
    }

    #endregion

}
