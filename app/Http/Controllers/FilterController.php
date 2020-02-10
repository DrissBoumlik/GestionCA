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

    public function dashboard_filter(Request $request, $filter)
    {
        $viewName = $filter;
        return view('stats.details.' . $viewName);
    }

    public function getUserFilter(Request $request)
    {
        return $this->filterRepository->getUserFilter($request);
    }

    public function saveUserFilter(Request $request)
    {
        return $this->filterRepository->saveUserFilter($request);
    }
}
