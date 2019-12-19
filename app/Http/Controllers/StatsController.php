<?php

namespace App\Http\Controllers;

use App\Models\Stats;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StatsController extends Controller
{

    private $statsRepository;

    public function __construct(StatsRepository $statsRepository)
    {
        $this->middleware('auth');
        $this->statsRepository = $statsRepository;
    }

    public function dashboard(Request $request)
    {
        $data = $this->statsRepository->dashboard($request);

        if ($request->exists('json')) {
            return $data;
        }
        return view('stats.dashboard')->with($data);
    }

    public function getDates(Request $request)
    {
        $dates = Stats::getDateNotes();
        return ['dates' => $dates];
    }

    public function getRegionsByDates(Request $request)
    {
        $data = $this->statsRepository->getRegionsByDates($request);

        if ($request->exists('json')) {
            return $data;
        }
        return view('stats.dashboard')->with($data);
    }

    public function getNonValidatedFoldersByCodeByDates(Request $request)
    {
        $data = $this->statsRepository->getNonValidatedFoldersByCodeByDates($request);

        if ($request->exists('json')) {
            return $data;
        }
        return view('stats.dashboard')->with($data);
    }

    public function getNonValidatedFoldersByTypeByDates(Request $request)
    {
        $data = $this->statsRepository->getNonValidatedFoldersByTypeByDates($request);

        if ($request->exists('json')) {
            return $data;
        }
        return view('stats.dashboard')->with($data);
    }

    public function getClientsByCallStateJoiByDates(Request $request)
    {
        $data = $this->statsRepository->getClientsByCallStateJoiByDates($request);

        if ($request->exists('json')) {
            return $data;
        }
        return view('stats.dashboard')->with($data);
    }

    public function getClientsByCallStateInjByDates(Request $request)
    {
        $data = $this->statsRepository->getClientsByCallStateInjByDates($request);

        if ($request->exists('json')) {
            return $data;
        }
        return view('stats.dashboard')->with($data);
    }


    public function getRegions(Request $request)
    {
        $dates = null;
        if ($request->exists('dates')) {
            $dates = array_filter($request->dates, function ($date) {
                return $date != null;
            });
        }

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

        $regions = $regions->map(function ($region) use (&$regions_names) {
            $row = new \stdClass();
            $row->regions = [];
            $item = $region->map(function ($call, $index) use (&$row, &$regions_names) {
                $regions_names[] = $call->Nom_Region;
                $row->Resultat_Appel = $call->Resultat_Appel;
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
        $data = ['regions_names' => $regions_names, 'calls' => $regions];
        return $data;
    }

    public function byAgency(Request $request)
    {
        return view('stats.agencies');
    }

    public function byAgencyJson()
    {

    }


    public function index()
    {
        return view('stats.import');
    }

    public function importStats(Request $request)
    {
        return response()->json($this->statsRepository->importStats($request));
    }

}
