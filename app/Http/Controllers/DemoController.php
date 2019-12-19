<?php

namespace App\Http\Controllers;

use App\Models\Stats;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DemoController extends Controller
{
    public function index()
    {
        $data = $this->GetDataRegions();

        return view('_demo.index')->with(['columns' => $data['regions_names']]);
    }

    public function getRegionsColumn(Request $request, $dates = null)
    {
        $data = $this->GetDataRegions($dates);
        return ['columns' => $data['regions_names']];
    }

    public function getRegions(Request $request)
    {
        $dates = $request->get('dates');
        $data = $this->GetDataRegions($dates);

        return DataTables::of($data['regions'])->toJson();
    }

    private function GetDataRegions($dates = null)
    {
        $regions = \DB::table('stats')
            ->select('Nom_Region', 'Resultat_Appel', \DB::raw('count(*) as total'));
        if ($dates) {
            $dates = array_values($dates);
            $regions = $regions->whereIn('Date_Note', $dates);
        }
//        $regions = ($dates ? $regions->whereIn('Date_Note', $dates)->get() : $regions)->get();

        $regions = $regions->groupBy('Nom_Region', 'Resultat_Appel')->get();

        $totalCount = Stats::all()->count();
        $regions = $regions->map(function ($region) use ($totalCount) {
            $Region = $region->Nom_Region;
            $region->$Region = round($region->total * 100 / $totalCount, 2);;
            return $region;
        });
        $regions = $regions->groupBy(['Resultat_Appel']);

        $regions_names = [];
        $regions_names[0] = new \stdClass();
        $regions_names[0]->data = 'Resultat_Appel';
        $regions_names[0]->name = 'Resultat_Appel';

        $regions = $regions->map(function ($region) use (&$regions_names) {
            $row = new \stdClass();
            $row->regions = [];
            $item = $region->map(function ($call, $index) use (&$row, &$regions_names) {
                $_index = $index + 1;
                $regions_names[$_index] = new \stdClass();
                $regions_names[$_index]->data = $call->Nom_Region;
                $regions_names[$_index]->name = $call->Nom_Region;
                $row->Resultat_Appel = $call->Resultat_Appel;
                $nom_region = $call->Nom_Region;
                $row->regions['zone_' . $index] = $call->$nom_region;
                $row->$nom_region = $call->$nom_region;
                $row->total = round(array_sum($row->regions) / count($row->regions), 2);
                return $row;
            });
            return $item->last();
        });
        $regions_names[] = new \stdClass();
        $regions_names[count($regions_names) - 1]->data = 'total';
        $regions_names[count($regions_names) - 1]->name = 'total';


        $regions_names = collect($regions_names)->unique()->filter()->values();
        $regions = $regions->values();

        return ['regions_names' => $regions_names, 'regions' => $regions];
    }

    public function getNonValidatedFoldersColumn(Request $request, $dates = null)
    {
        $data = $this->getDataNonValidatedFolders($dates);
        return ['columns' => $data['regions_names']];
    }

    public function getNonValidatedFolders(Request $request)
    {
        $dates = $request->get('dates');
        $data = $this->getDataNonValidatedFolders($dates);

        return DataTables::of($data['codes'])->toJson();
    }

    public function getDataNonValidatedFolders($intervCol, $dates = null)
    {
        $regions = \DB::table('stats')
            ->select('Nom_Region', $intervCol, \DB::raw('count(*) as total'));
        if ($dates) {
            $dates = array_values($dates);
            $regions = $regions->whereIn('Date_Note', $dates);
        }
        $regions = $regions->groupBy('Nom_Region', $intervCol)->get();

        $totalCount = Stats::all()->count();
        $regions = $regions->map(function ($region) use ($totalCount) {
            $Region = $region->Nom_Region;
            $region->$Region = round($region->total * 100 / $totalCount, 2);;
            return $region;
        });
        $regions = $regions->groupBy([$intervCol]);

        $regions_names = [];

        $regions = $regions->map(function ($region) use (&$regions_names) {
            $row = new \stdClass();
            $row->regions = [];
            $item = $region->map(function ($call, $index) use (&$row, &$regions_names) {
                $regions_names[] = $call->Nom_Region;

                if (property_exists($call, 'Code_Type_Intervention')) {
                    $row->Code_Type_Intervention = $call->Code_Type_Intervention;
                } elseif (property_exists($call, 'Code_Intervention')) {
                    $row->Code_Intervention = $call->Code_Intervention;
                }

                $nom_region = $call->Nom_Region;
                $row->regions['zone_' . $index] = $call->$nom_region;
                $row->$nom_region = $call->$nom_region;
                $row->total = round(array_sum($row->regions) / count($row->regions), 2);
                return $row;
            });
            return $item->last();
        });
        $regions_names = collect($regions_names)->unique()->values();
        $regions = $regions->values();
        $data = ['regions_names' => $regions_names, 'codes' => $regions];
        return $data;
    }
}
