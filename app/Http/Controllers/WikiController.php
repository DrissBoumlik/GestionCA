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
        return view('wiki.index');
    }
}
