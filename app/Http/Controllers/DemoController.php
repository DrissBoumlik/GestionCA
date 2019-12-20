<?php

namespace App\Http\Controllers;

use App\Models\Stats;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DemoController extends Controller
{
    public function index()
    {
        $dataRegions = $this->GetDataRegions();
        $dataTypeInterv = $this->getDataNonValidatedFolders('Code_Type_Intervention');
        $dataCodeInterv = $this->getDataNonValidatedFolders('Code_Intervention');

        $dataCallsPos = $this->getDataClientsByCallState('Joignable');
        $dataCallsNeg = $this->getDataClientsByCallState('Injoignable');


        return view('_demo.index')->with([
            'regions_names' => $dataRegions['regions_names'],
            'regions_names_type' => $dataTypeInterv['regions_names'],
            'regions_names_code' => $dataCodeInterv['regions_names'],
            'calls_pos' => $dataCallsPos['codes_names'],
            'calls_neg' => $dataCallsNeg['codes_names'],
        ]);
    }

    #region Regions

    public function getRegionsColumn(Request $request)
    {
        $dates = $request->get('dates');
        $data = $this->GetDataRegions($dates);
        return ['columns' => $data['regions_names'], 'data' => $data['regions']];
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

    #endregion

    #region NonValidatedFolders

    public function getNonValidatedFoldersColumn(Request $request, $intervCol)
    {
        $dates = $request->get('dates');
        $data = $this->getDataNonValidatedFolders($intervCol, $dates);
        return ['columns' => $data['regions_names']];
    }

    public function getNonValidatedFolders(Request $request, $intervCol)
    {
        $dates = $request->get('dates');
        $data = $this->getDataNonValidatedFolders($intervCol, $dates);
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
        $regions_names[0] = new \stdClass();
        $regions_names[0]->data = $intervCol;
        $regions_names[0]->name = $intervCol;

        $regions = $regions->map(function ($region) use (&$regions_names) {
            $row = new \stdClass();
            $row->regions = [];
            $item = $region->map(function ($call, $index) use (&$row, &$regions_names) {
                $_index = $index + 1;
                $regions_names[$_index] = new \stdClass();
                $regions_names[$_index]->data = $call->Nom_Region;
                $regions_names[$_index]->name = $call->Nom_Region;

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
        $regions_names[] = new \stdClass();
        $regions_names[count($regions_names) - 1]->data = 'total';
        $regions_names[count($regions_names) - 1]->name = 'total';

        $regions_names = collect($regions_names)->unique()->values();
        $regions = $regions->values();
        $data = ['regions_names' => $regions_names, 'codes' => $regions];
        return $data;
    }

    #endregion

    #region ClientsByCallState

    public function getClientsByCallStateColumn(Request $request, $callResult)
    {
        $dates = $request->get('dates');
        $data = $this->getDataClientsByCallState($callResult, $dates);
        return ['columns' => $data['codes_names']];
    }

    public function getClientsByCallState(Request $request, $callResult)
    {
        $dates = $request->get('dates');
        $data = $this->getDataClientsByCallState($callResult, $dates);
        return DataTables::of($data['regions'])->toJson();
    }

    public function getDataClientsByCallState($callResult, $dates = null)
    {
        $codes = \DB::table('stats')
            ->select('Code_Intervention', 'Nom_Region', \DB::raw('count(*) as total'))
            ->where('Gpmt_Appel_Pre', $callResult);
//            ->groupBy('Code_Intervention', 'Nom_Region')
//            ->get();
        if ($dates) {
            $dates = array_values($dates);
            $codes = $codes->whereIn('Date_Note', $dates);
        }
        $codes = $codes->groupBy('Code_Intervention', 'Nom_Region')->get();
        $totalCount = Stats::all()->count();
        $codes = $codes->map(function ($code) use ($totalCount) {
            $Code = $code->Code_Intervention;
            $code->$Code = round($code->total * 100 / $totalCount, 2);;
            return $code;
        });
        $codes = $codes->groupBy(['Nom_Region']);

        $codes_names = [];
        $codes_names[0] = new \stdClass();
        $codes_names[0]->data = 'Nom_Region';
        $codes_names[0]->name = 'Nom_Region';


        $total = new \stdClass();
        $codes = $codes->map(function ($region) use (&$codes_names, &$total) {
            $row = new \stdClass(); //[];
            $row->codes = [];
            $item = $region->map(function ($call, $index) use (&$row, &$codes_names, &$total) {
                $_index = $index + 1;
                $codes_names[$_index] = new \stdClass();
                $codes_names[$_index]->data = $call->Code_Intervention;
                $codes_names[$_index]->name = $call->Code_Intervention;

//                $codes_names[] = $call->Code_Intervention;
                $row->Nom_Region = $call->Nom_Region;
                $code_intervention = $call->Code_Intervention;
                $row->codes['code_' . $index] = $call->$code_intervention;
                $row->$code_intervention = $call->$code_intervention;
//                $row->$code_intervention = $call->$code_intervention;
                $row->total = round(array_sum($row->codes) / count($row->codes), 2);
//                dump($code_intervention ? $total->{$code_intervention}[0] : 1);
//                if ($code_intervention)
//                    $total->$code_intervention =
//                        $total->$index == 0 ?
//                        $total->$index + $call->$code_intervention : 0;
//                    $total[$index] += $call->$code_intervention;

                return $row;
            });
            return $item->last();
        });
//        dd($total);
        $codes_names[] = new \stdClass();
        $codes_names[count($codes_names) - 1]->data = 'total';
        $codes_names[count($codes_names) - 1]->name = 'total';

        $codes_names = collect($codes_names)->unique()->values();
        $codes = $codes->values();

        $data = ['codes_names' => $codes_names, 'regions' => $codes];
        return $data;
    }

    #endregion

}
