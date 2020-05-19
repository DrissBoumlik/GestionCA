<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\AgentRepository;

class AgentController extends Controller
{
    private $agentRepository;

    public function __construct(AgentRepository $agentRepository)
    {
        $this->agentRepository = $agentRepository;
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
