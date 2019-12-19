<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ToolController extends Controller
{
    public function unauthorized()
    {
        return view('tools.unauthorized');
    }

    public function home()
    {
        return redirect('dashboard');
    }
}
