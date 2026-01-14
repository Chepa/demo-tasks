<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRequest;
use App\Http\Requests\Task\UpdateStatusRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;

class TasksController extends Controller
{
    /**
     *  Получение списка задач
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $tasks = Task::where('user_id', auth()->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tasks
        ]);
    }

    /**
     *  Создание новой задачи
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $task = Task::create([
            'user_id' => auth()->user()->id,
            'title' => $request->title,
            'text' => $request->text,
            'completed' => $request->completed ?? false,
        ]);

        return response()->json([
            'success' => true,
            'data' => $task
        ], 201);
    }

    /**
     * Обновление статуса задачи
     * @param UpdateStatusRequest $request
     * @param Task $task
     * @return JsonResponse
     */
    public function update(UpdateStatusRequest $request, Task $task): JsonResponse
    {
        if ($task->user_id !== auth()->user()->id) {
            return response()->json([
                'success' => false,
                'errors' => 'unauthorized'
            ], 403);
        }

        $task->update($request->only(['title', 'text', 'completed']));

        return response()->json([
            'success' => true,
            'data' => $task->fresh()
        ]);
    }

    /**
     * Удаление задачи
     * @param Task $task
     * @return JsonResponse
     */
    public function destroy(Task $task): JsonResponse
    {
        if ($task->user_id !== auth()->user()->id) {
            return response()->json([
                'success' => false,
                'errors' => 'unauthorized'
            ], 401);
        }

        $task->delete();

        return response()->json([
            'success' => true,
            'message' => 'Task deleted successfully'
        ]);
    }

    /**
     * Удаление всех завершённых задач
     * @return JsonResponse
     */
    public function clearCompleted(): JsonResponse
    {
        Task::where('completed', true)
            ->where('user_id', auth()->user()->id)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'Completed task cleared'
        ]);
    }
}

