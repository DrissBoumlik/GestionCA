<?php

namespace App\Repositories;


use App\Models\Filter;
use App\Models\User;
use Illuminate\Http\Request;

class ToolRepository
{
    public function applyFilter(Request $request, $route, $results, $column = null, $rowsFilter = null)
    {
        $user = getAuthUser();
        $filters = ['route' => $route, 'user_id' => $user->id, 'agence_name' => null, 'agent_name' => null];
        $agenceCode = $request->get('agence_code');
        $agentName = $request->get('agent_name');
        if ($agentName) {
            $filters['agent_name'] = $agentName;
        }
        if ($agenceCode) {
            $filters['agence_name'] = $agenceCode;
        }
        $dates = $request->get('dates');
        if ($request->exists('refreshMode')) {
            if ($dates || $rowsFilter) {
                $dates = $dates ? array_values($dates) : $dates;
                $filter = Filter::firstOrNew($filters);
                $filter->date_filter = $dates;
                $filter->rows_filter = $rowsFilter;
                $filter->save();
                if ($dates) {
                    $results = $results->whereIn('Date_Note', $dates);
                }
                if ($column && $rowsFilter) {
                    $results = $results->whereIn($column, $rowsFilter);
                }
            } else {
                $filter = Filter::where($filters)->first();
                if ($filter) {
                    $filter->forceDelete();
                }
            }
        } else {
            $filter = Filter::where($filters)->first();
            if ($filter) {
                if ($filter->date_filter) {
                    $results = $results->whereIn('Date_Note', $filter->date_filter);
                }
                if ($column && $filter->rows_filter) {
                    $results = $results->whereIn($column, $filter->rows_filter);
                }
            }
        }
        return [$filter, $results];
    }
}
