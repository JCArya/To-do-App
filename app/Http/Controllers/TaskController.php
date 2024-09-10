<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Task;

class TaskController extends Controller
{
    public function show()
    {
        return Task::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|unique:tasks',
        ]);

        $task = Task::create([
            'title' => $request->title,
            'is_completed' => false,
        ]);

        return response()->json($task);
    }

    public function update(Request $request, Task $task)
    {
        $task->update([
            'is_completed' => $request->is_completed,
        ]);

        return response()->json($task);
    }

    public function destroy(Task $task)
    {
        $task->delete();

        return response()->json(['message' => 'Task deleted']);
    }
}
