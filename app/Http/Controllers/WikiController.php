<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;

class WikiController extends Controller
{
    public function index(Request $request)
    {
        $user = getAuthUser();
        if (!$user->isSuperAdmin()) {
            throw new AuthorizationException();
        }
        $wikiData = config('wiki_data.wiki_data');
        $global = $wikiData['global'];
        $pages = $wikiData['pages'];
        $pages_types = $wikiData['pages_types'];
        return view('wiki.index')->with(['pages' => $pages, 'global' => $global, 'pages_types' => $pages_types]);
    }
}
