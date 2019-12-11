<?php

namespace App\Http\Controllers;

use App\Models\Stats;
use Illuminate\Http\Request;

class DemoController extends Controller
{
    public function demo()
    {
        $all = Stats::all();
        $all = $all->groupBy('Resultat_Appel');

        return ['data' => [
            'stats' => $all
        ]];
    }
}
