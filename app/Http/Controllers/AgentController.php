<?php

namespace App\Http\Controllers;

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
        return $this->agentRepository->allData();
    }

    public function getData(Request $request)
    {
        return DataTables::of($this->agentRepository->getData($request))->toJson();
    }

    public function importView()
    {
        // Authorization
        $this->authorize('view', auth()->user());
        return $this->agentRepository->importView();
    }

    public function import(Request $request)
    {
        // Authorization
//        $this->authorize('view', auth()->user());
        return response()->json($this->agentRepository->import($request));
    }
}
