<?php

namespace App\Http\Controllers;

use App\Models\Filter;
use App\Models\User;
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
        return $this->filterRepository->GetDataRegionsCallState($request, $column);
    }

    public function getRegionsCallState(Request $request, $column)
    {
        $data = $this->filterRepository->GetDataRegionsCallState($request, $column);
        return DataTables::of($data['data'])->toJson();
    }

    public function getClientsByCallStateColumn(Request $request, $callResult)
    {
        return $this->filterRepository->getDataClientsByCallState($request, $callResult);
    }

    public function getClientsByCallState(Request $request, $callResult)
    {
        $data = $this->filterRepository->getDataClientsByCallState($request, $callResult);
        return DataTables::of($data['data'])->toJson();
    }

    public function getFoldersColumn(Request $request, $callResult)
    {
        $data = $this->filterRepository->GetDataFolders($request, $callResult);
        return $data;
    }

    public function getFolders(Request $request, $callResult)
    {
        $data = $this->filterRepository->GetDataFolders($request, $callResult);
        return DataTables::of($data['data'])->toJson();
    }

    public function getNonValidatedFoldersColumn(Request $request, $column)
    {
//        if ($column == 'Code_Type_Intervention') {
//            $data = $this->filterRepository->getDataNonValidatedFolders_2($request, $column);
//        } else {
//            $data = $this->filterRepository->getDataNonValidatedFolders($request, $column);
//        }

        $data = $this->filterRepository->getDataNonValidatedFolders_2($request, $column);
        return $data;
    }

    public function getNonValidatedFolders(Request $request, $column)
    {
//        if ($column == 'Code_Type_Intervention') {
//            $data = $this->filterRepository->getDataNonValidatedFolders_2($request, $column);
//        } else {
//            $data = $this->filterRepository->getDataNonValidatedFolders($request, $column);
//        }
        $data = $this->filterRepository->getDataNonValidatedFolders_2($request, $column);
        return DataTables::of($data['data'])->toJson();
    }

    public function getCloturetech(Request $request)
    {
        $data = $this->filterRepository->getCloturetechCall($request);
        return DataTables::of($data['data'])->toJson();

    }

    public function getCloturetechColumn(Request $request)
    {

        return $this->filterRepository->getCloturetechCall($request);
    }

    public function getGlobalDelay(Request $request)
    {
        $data = $this->filterRepository->getGlobalDelayCall($request);
        return DataTables::of($data['data'])->toJson();

    }

    public function GlobalDelayColumn(Request $request)
    {

        return $this->filterRepository->getGlobalDelayCall($request);
    }

    public function dashboard_filter(Request $request, $filter)
    {
        $viewName = $filter;
        return view('stats.details.' . $viewName);
    }

    public function getUserFilter(Request $request)
    {
        $user = auth()->user() ?? User::find(1);

        $userFilter = $request->get('filter');
        if ($userFilter) {
            $globalFilter = new Filter([
                'user_id' => $user->id,
                'date_filter' => $userFilter,
                'isGlobal' => true
            ]);
            $globalFilter->save();
        } else {
            $globalFilter = Filter::where('user_id', $user->id)->where('isGlobal', true)->first();
        }
        return ['userFilter' => $globalFilter];
    }
}
