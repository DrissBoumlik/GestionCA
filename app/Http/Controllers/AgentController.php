<?php

namespace App\Http\Controllers;

use App\Models\Agent;
use Illuminate\Http\Request;
use App\Repositories\AgentRepository;
use Yajra\DataTables\Facades\DataTables;

class AgentController extends Controller
{
    private $agentRepository;

    public function __construct(AgentRepository $agentRepository)
    {
        $this->agentRepository = $agentRepository;
    }

    public function allData()
    {
        $this->authorize('view', auth()->user());
        return view('agents.data');
    }

    public function getData(Request $request)
    {
        $agents = Agent::all();
        return DataTables::of($agents)->toJson();
    }

    public function importView()
    {
        // Authorization
        $this->authorize('view', auth()->user());

        return view('agents.import');
    }

    public function import(Request $request)
    {
        // Authorization
//        $this->authorize('view', auth()->user());
        return response()->json($this->agentRepository->import($request));
    }
}
