<?php

namespace App\Http\Controllers;

use App\Models\Task;

class TasksController extends Controller
{
    public function view(Task $task)
    {
        return view('tasks.view', compact('task'));
    }
}

