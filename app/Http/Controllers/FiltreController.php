<?php

namespace App\Http\Controllers;

use App\Repositories\FilterRepository;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class FiltreController extends Controller
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
         return  $this->filterRepository->GetDataRegionsCallState($column, $request);
    }

    public function getRegionsCallState(Request $request, $column)
    {
        $data = $this->filterRepository->GetDataRegionsCallState($column, $request);
        return DataTables::of($data['data'])->toJson();
    }

    public function getClientsByCallStateColumn(Request $request, $callResult)
    {
        return  $this->filterRepository->getDataClientsByCallState($callResult, $request);

    }

    public function getClientsByCallState(Request $request, $callResult)
    {
        $data = $this->filterRepository->getDataClientsByCallState($callResult, $request);
        return DataTables::of($data['data'])->toJson();
    }



}
