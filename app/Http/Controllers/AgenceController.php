<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;


class AgenceController extends Controller
{
    protected $agenceRepository;
    public function __construct(AgenceRepository $agenceRepository)
    {
        $this->agenceRepository = $agenceRepository;
    }

    public function getAgencies (Request $request) {
        return $this->agenceRepository->getAgencies($request);
    }

    public function index(Request $request)
    {
        $agenceCode = $request->agence_code;
        $dataRegionsCallResult = $this->agenceRepository->GetDataRegions('Resultat_Appel', $request);
        $dataRegionsCallStateByRegions = $this->agenceRepository->GetDataRegionsCallState('Nom_Region', $request);
        $dataRegionsCallStateByWeek = $this->agenceRepository->GetDataRegionsCallState('Date_Heure_Note_Semaine', $request);
//        dd($dataRegionsCallStateByRegions, $dataRegionsCallStateByWeek);
        $dataCallsPos = $this->agenceRepository->getDataClientsByCallState('Joignable', null, $agenceCode);
        $dataCallsNeg = $this->agenceRepository->getDataClientsByCallState('Injoignable', null, $agenceCode);

        $dataTypeInterv = $this->agenceRepository->getDataNonValidatedFolders('Code_Type_Intervention', $request);
        $dataCodeInterv = $this->agenceRepository->getDataNonValidatedFolders('Code_Intervention', $request);


        return view('stats.agencies')->with([
            'calls_results' => $dataRegionsCallResult['columns'],

            'calls_states_regions' => $dataRegionsCallStateByRegions['columns'],
            'calls_states_weeks' => $dataRegionsCallStateByWeek['columns'],

            'regions_names_type' => $dataTypeInterv['columns'],
            'regions_names_code' => $dataCodeInterv['columns'],

            'calls_pos' => $dataCallsPos['columns'],
            'calls_neg' => $dataCallsNeg['columns'],
            'agence' => $agenceCode
        ]);
    }

    public function getDates(Request $request)
    {
//        $dates = Stats::getDateNotes();
//        $dates = Stats::getDateNotes2();
        $agenceCode = $request->get('agence_code');
        $dates = $this->agenceRepository->getDateNotes($agenceCode);
        return ['dates' => $dates];
    }

    #region Regions =====================================================

    public function getRegionsColumn(Request $request, $callResult)
    {
        $data = $this->agenceRepository->GetDataRegions($callResult, $request);
        return ['columns' => $data['columns'], 'data' => $data['data']];
    }

    public function filterList ($column, Request $request) {
        $stats = $this->agenceRepository->filterList($column, $request);
        return [
            'data' => $stats
        ];
    }

    public function getRegions(Request $request, $callResult)
    {
        $data = $this->agenceRepository->GetDataRegions($callResult, $request);
        return DataTables::of($data['data'])->toJson();
    }
    #endregion

    #region Call Stats

    public function getRegionsCallStateColumn(Request $request, $column)
    {
        $data = $this->agenceRepository->GetDataRegionsCallState($column, $request);
        return ['columns' => $data['columns'], 'data' => $data['data']];
    }

    public function getRegionsCallState(Request $request, $column)
    {
        $data = $this->agenceRepository->GetDataRegionsCallState($column, $request);
        return DataTables::of($data['data'])->toJson();
    }

    #endregion

    #region NonValidatedFolders =====================================================

    public function getNonValidatedFoldersColumn(Request $request, $column)
    {
        $data = $this->agenceRepository->getDataNonValidatedFolders($column, $request);
        return ['columns' => $data['columns'], 'data' => $data['data']];
    }

    public function getNonValidatedFolders(Request $request, $column)
    {
        $data = $this->agenceRepository->getDataNonValidatedFolders($column, $request);
        return DataTables::of($data['data'])->toJson();
    }

    #endregion

    #region ClientsByCallState =====================================================

    public function getClientsByCallStateColumn(Request $request, $callResult)
    {
        $dates = $request->get('dates');
        $agenceCode = $request->get('agence_code');
        $data = $this->agenceRepository->getDataClientsByCallState($callResult, $dates, $agenceCode);
        return ['columns' => $data['columns'], 'data' => $data['data']];
    }

    public function getClientsByCallState(Request $request, $callResult)
    {
        $dates = $request->get('dates');
        $agenceCode = $request->get('agence_code');
        $data = $this->agenceRepository->getDataClientsByCallState($callResult, $dates, $agenceCode);
        return DataTables::of($data['data'])->toJson();
    }

    #endregion

}
