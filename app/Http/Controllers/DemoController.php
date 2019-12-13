<?php

namespace App\Http\Controllers;

use App\Models\Stats;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DemoController extends Controller
{
    public function dashboard(Request $request)
    {
        $regions = Stats::getRegions();
        $codes = Stats::getCodes();
        return $codes;
        if ($request->exists('json')) return $regions;
        return view('stats.dashboard', $regions);
    }
}
