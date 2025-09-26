<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Events\TaskCreated;
use App\Events\NotificationEvent;


class TaskController extends Controller
{

    public function all()
    {
        $tasks = Task::orderBy('created_at', 'desc')->get();

        return response()->json($tasks);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
    
        $task = Task::create([
            'user_id' => auth('api')->id(),
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'completed' => false,
        ]);
    
       $notification = Notification::create([
    'user_id' => auth('api')->id(),
    'type' => 'success',
    'message' => "New task \"{$task->title}\" created",
    'data' => [
        'user_id' => auth('api')->id(), // add this
        'task_id' => $task->id,
        'task_title' => $task->title,
    ],
]);

broadcast(new NotificationEvent($notification->message, $notification->type, $notification->data))->toOthers();

        return response()->json([
            'task' => $task,
        ], 201);
    }

    public function update(Request $request, Task $task)
    {
        
        $task = Task::find($request->id);
        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }
        
        $task->update($request->only(['title', 'description', 'completed']));

        return response()->json($task);
    }

    public function destroy(Request $request, Task $task)
    {        
        $task = Task::find($request->id);
        if (!$task) {
            return response()->json(['error' => 'Task not found'], 404);
        }
        $task->delete();

        return response()->json(['message' => 'Task deleted successfully']);
    }
}
