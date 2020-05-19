<?php

namespace App\Repositories;

use Illuminate\Http\Request;
use App\Imports\AgentsImport;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class AgentRepository
{
    public function import(Request $request)
    {
        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $stored = Storage::disk('public')->put('storage/data_source/' . $fileName, file_get_contents($file));

        $agentsImport = new AgentsImport($request->days);
        Excel::import($agentsImport, $request->file('file'));
//        $user_flag = getImportedData(false);
//        $user_flag->flags = [
//            'imported_data' => $user_flag->flags['imported_data'],
//            'is_importing' => 2
//        ];
//        $user_flag->update();
        \DB::table('agents')
            ->whereNotNull('isNotReady')
            ->update(['isNotReady' => null]);
        return [
            'success' => true,
            'message' => 'Le fichier a été importé avec succès'
        ];
    }
}
