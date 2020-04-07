<?php

namespace App\Http\Controllers;

use App\Models\Filter;
use App\Models\User;
use App\Models\UserFlag;
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

    public function editImportingStatus($flag)
    {
        $user_flag = UserFlag::firstOrCreate([
            'user_id' => getAuthUser()->id
        ]);
        $user_flag->flags = [
            'imported_data' => 0,
            'is_importing' => (int)$flag
        ];
        $user_flag->save();
        return [
            'flags' => $user_flag->flags
        ];
    }

    public function getInsertedData()
    {
        return [
            'flags' => getImportedData(true)
        ];
    }
}
