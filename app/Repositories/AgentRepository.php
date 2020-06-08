<?php

namespace App\Repositories;

use App\Models\Agent;
use Illuminate\Http\Request;
use App\Imports\AgentsImport;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class AgentRepository
{
    public function allData()
    {
        return view('agents.data');
    }

    public function getData(Request $request)
    {
        $agents = Agent::orderBy('updated_at', 'desc')->get();
        return $agents;
    }

    public function importView()
    {
        return view('agents.import');
    }

    public function import(Request $request)
    {
        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $stored = Storage::disk('public')->put('storage/data_source/' . $fileName, file_get_contents($file));

        $agentsImport = new AgentsImport($request->dates);
        Excel::import($agentsImport, $request->file('file'));
        \DB::table('agents')
            ->whereNotNull('isNotReady')
            ->update(['isNotReady' => null]);
        return [
            'success' => true,
            'message' => 'Le fichier a été importé avec succès'
        ];
    }
}
