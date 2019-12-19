<?php

namespace App\Repositories;

use App\Imports\StatsImport;
use Exception;
use App\Models\Task;
use Datatables;
use App\Imports\TasksImport;
use Maatwebsite\Excel\Facades\Excel;

class TaskRepository
{
    public function importTasks($request)
    {
        try {
            Excel::import(new TasksImport, $request->file('file'));
            return [
                'success' => true,
                'message' => 'Le fichier a été importé avec succès'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Une erreur est survenue'
            ];
        }
    }

    public function getTasks()
    {
        $tasks = Task::all();
        return datatables()->of($tasks)->make(true);
    }
}
