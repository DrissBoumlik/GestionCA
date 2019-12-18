<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\TaskRepository;

use App\Models\Task;

class TaskController extends Controller
{
    private $taskRepository;

    public function __construct(TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }

    public function index()
    {
        return view('tasks/index');
    }

    public function importTasks(Request $request)
    {
        return response()->json($this->taskRepository->importTasks($request));
    }

    public function getTasks()
    {
        return $this->taskRepository->getTasks();
    }
}
