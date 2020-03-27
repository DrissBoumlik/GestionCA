<?php


namespace App\Repositories;


use App\Models\Filter;
use App\Models\Stats;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class FilterRepository
{
    public function getViewFilter(Request $request, $filter)
    {
        $viewName = $filter;
        return view('stats.details.' . $viewName)->with([
            'agence' => $request->agence_code,
            'agent' => $request->agent_name
        ]);
    }

    public function getUserFilter(Request $request)
    {
        $user = getAuthUser();
        $globalFilter = Filter::where(['user_id' => $user->id, 'isGlobal' => true])->first();
        return ['userFilter' => $globalFilter];
    }

    public function saveUserFilter(Request $request)
    {
        $user = getAuthUser();

        $userFilter = $request->get('filter');
        $globalFilter = Filter::firstOrNew(['user_id' => $user->id, 'isGlobal' => true]);
        if ($userFilter) {
            $globalFilter->date_filter = $userFilter;
            $globalFilter->save();
            Filter::where('user_id', $user->id)->update(['date_filter' => $userFilter]);
        } else {
            $globalFilter->forceDelete();
            Filter::where('user_id', $user->id)->forceDelete();
            $globalFilter = null;
        }
        return ['userFilter' => $globalFilter];
    }
}
