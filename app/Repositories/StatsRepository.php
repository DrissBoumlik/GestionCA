<?php

namespace App\Http\Controllers;


use App\Imports\StatsImport;
use App\Models\Stats;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StatsRepository
{

    public function dashboard(Request $request)
    {
        $regions = Stats::getRegions();
        $foldersByIntervCode = Stats::getNonValidatedFolders('Code_Intervention');
        $foldersByIntervType = Stats::getNonValidatedFolders('Code_Type_Intervention');
//        return [$folders, $regions];
        $joignable = Stats::getClientsByCallState('Joignable');
        $inJoignable = Stats::getClientsByCallState('Injoignable');
//        return [$regions, $joignable, $inJoignable];
        return [
            'regions' => $regions,
            'foldersByIntervType' => $foldersByIntervType,
            'foldersByIntervCode' => $foldersByIntervCode,
            'joignable' => $joignable,
            'inJoignable' => $inJoignable
        ];
    }

    public function getRegionsByDates(Request $request)
    {
        $data = array_filter($request->dates_0, function ($date) {
            return $date != null;
        });

        $regions = Stats::getRegions($data);
        $foldersByIntervCode = Stats::getNonValidatedFolders('Code_Intervention');
        $foldersByIntervType = Stats::getNonValidatedFolders('Code_Type_Intervention');

        $joignable = Stats::getClientsByCallState('Joignable');
        $inJoignable = Stats::getClientsByCallState('Injoignable');
//        return [$regions, $joignable, $inJoignable];

        return [
            'regions' => $regions,
            'foldersByIntervType' => $foldersByIntervType,
            'foldersByIntervCode' => $foldersByIntervCode,
            'joignable' => $joignable,
            'inJoignable' => $inJoignable
        ];

    }

    public function getNonValidatedFoldersByCodeByDates(Request $request)
    {
        $data = array_filter($request->dates_4, function ($date) {
            return $date != null;
        });

        $regions = Stats::getRegions();
        $foldersByIntervCode = Stats::getNonValidatedFolders('Code_Intervention', $data);
        $foldersByIntervType = Stats::getNonValidatedFolders('Code_Type_Intervention');
//        return [$regions, $joignable, $inJoignable];
        $joignable = Stats::getClientsByCallState('Joignable');
        $inJoignable = Stats::getClientsByCallState('Injoignable');
        return [
            'regions' => $regions,
            'foldersByIntervType' => $foldersByIntervType,
            'foldersByIntervCode' => $foldersByIntervCode,
            'joignable' => $joignable,
            'inJoignable' => $inJoignable

        ];
    }

    public function getNonValidatedFoldersByTypeByDates(Request $request)
    {
        $data = array_filter($request->dates_3, function ($date) {
            return $date != null;
        });

        $regions = Stats::getRegions($data);
        $foldersByIntervCode = Stats::getNonValidatedFolders('Code_Intervention');
        $foldersByIntervType = Stats::getNonValidatedFolders('Code_Type_Intervention', $data);
//        return [$regions, $joignable, $inJoignable];
        $joignable = Stats::getClientsByCallState('Joignable');
        $inJoignable = Stats::getClientsByCallState('Injoignable');

        return [
            'regions' => $regions,
            'foldersByIntervType' => $foldersByIntervType,
            'foldersByIntervCode' => $foldersByIntervCode,
            'joignable' => $joignable,
            'inJoignable' => $inJoignable
        ];
    }

    public function getClientsByCallStateJoiByDates(Request $request)
    {
        $data = array_filter($request->dates_1, function ($date) {
            return $date != null;
        });

        $regions = Stats::getRegions($data);
        $foldersByIntervCode = Stats::getNonValidatedFolders('Code_Intervention');
        $foldersByIntervType = Stats::getNonValidatedFolders('Code_Type_Intervention');
//        return [$regions, $joignable, $inJoignable];
        $joignable = Stats::getClientsByCallState('Joignable', $data);
        $inJoignable = Stats::getClientsByCallState('Injoignable');

        return [
            'regions' => $regions,
            'foldersByIntervType' => $foldersByIntervType,
            'foldersByIntervCode' => $foldersByIntervCode,
            'joignable' => $joignable,
            'inJoignable' => $inJoignable
        ];
    }

    public function getClientsByCallStateInjByDates(Request $request)
    {
        $data = array_filter($request->dates_2, function ($date) {
            return $date != null;
        });

        $regions = Stats::getRegions($data);
        $foldersByIntervCode = Stats::getNonValidatedFolders('Code_Intervention');
        $foldersByIntervType = Stats::getNonValidatedFolders('Code_Type_Intervention');
//        return [$regions, $joignable, $inJoignable];
        $joignable = Stats::getClientsByCallState('Joignable');
        $inJoignable = Stats::getClientsByCallState('Injoignable', $data);

        return [
            'regions' => $regions,
            'foldersByIntervType' => $foldersByIntervType,
            'foldersByIntervCode' => $foldersByIntervCode,
            'joignable' => $joignable,
            'inJoignable' => $inJoignable
        ];
    }

    public function importStats($request)
    {
        try {
            Excel::import(new StatsImport, $request->file('file'));
            return [
                'success' => true,
                'message' => 'Le fichier a été importé avec succès'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Une erreur est survenue'
            ];
        }
    }
}
