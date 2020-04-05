<?php

namespace App\Http\Controllers;

use App\Models\Filter;
use App\Models\User;
use App\Repositories\ToolRepository;
use Illuminate\Http\Request;

class ToolController extends Controller
{
    private $toolRepository;

    public function __construct(ToolRepository $toolRepository)
    {
//        $this->middleware('auth');
        $this->toolRepository = $toolRepository;
    }

    public function unauthorized()
    {
        return view('tools.unauthorized');
    }

    public function home()
    {
        return redirect('dashboard');
    }

    public function getInsertedData()
    {
        return [
            'flags' => getImportedData(true)
        ];
    }
}
