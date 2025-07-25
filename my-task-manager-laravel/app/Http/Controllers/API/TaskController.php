<?php

namespace App\Http\Controllers\Api;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\Task\CreateTaskRequest;
use App\Http\Requests\Task\UpdateTaskRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;



class TaskController extends Controller
{
    use AuthorizesRequests;
   
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try{
           $tasks = Auth::user()->tasks()->latest()->paginate(15);
              return response()->json([
                 'status' => 'success',
                 'data' => $tasks
                ], 200);
                

        }catch(\Exception $e) {
             return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
                'error' => app()->environment('production') ? 'Server Error' : $e->getMessage()
            ], 500);
        }   
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateTaskRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = Auth::id();
        try {
            $task = Task::create($data);

            return response()->json([
                'status' => 'success',
                'data' => $task
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to create task',
                'error' => app()->environment('production') ? 'Server Error' : $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task): JsonResponse
    {
        try {
            $this->authorize('view', $task);

            if ($task->user_id !== Auth::id() || !$task) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access to this task'
                ], 403);
            }

            return response()->json([
                'status' => 'success',
                'data' => $task
            ], 200);

        }catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Task not found',
                'error' => app()->environment('production') ? 'Server Error' : $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        try {
            $this->authorize('update', $task);

            $data = $request->validated();
            $task->update($data);

            return response()->json([
                'status' => 'success',
                'data' => $task
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to update task',
                'error' => app()->environment('production') ? 'Server Error' : $e->getMessage()
            ], 500);
        }
    }
   
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task): JsonResponse
    {
        try {
            $this->authorize('delete', $task);

            $task->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Task deleted successfully'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to delete task',
                'error' => app()->environment('production') ? 'Server Error' : $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display a listing of trashed tasks.
     */
    public function trashed(): JsonResponse
    {
        try {
            $tasks = Auth::user()->tasks()->onlyTrashed()->latest()->paginate(15);

            return response()->json([
                'status' => 'success',
                'data' => $tasks
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve trashed tasks',
                'error' => app()->environment('production') ? 'Server Error' : $e->getMessage()
            ], 500);
        }
    }

    /**
     * Restore the specified trashed task.
     */
    public function restore($id): JsonResponse
    {
        try{
            $task = Task::onlyTrashed()->where('id', $id)
            ->where('user_id', Auth::id())->firstOrFail();

            $this->authorize('restore', $task);
            $task->restore();

            return response()->json([
                'status' => 'success',
                'message' => 'Task restored successfully',
                'data' => $task
            ], 200);

        }catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve trashed tasks',
                'error' => app()->environment('production') ? 'Server Error' : $e->getMessage()
            ], 500);
        }
    }

    /**
     * Permanently delete the specified trashed task.
     */
    public function forceDelete($id): JsonResponse
    {
        try {
            $task = Task::onlyTrashed()->where('id', $id)
            ->where('user_id', Auth::id())->firstOrFail();

            $this->authorize('forceDelete', $task);
            $task->forceDelete();

            return response()->json([
                'status' => 'success',
                'message' => 'Task permanently deleted successfully'
            ], 200);

        }catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve trashed tasks',
                'error' => app()->environment('production') ? 'Server Error' : $e->getMessage()
            ], 500);
        }
    }

    /**
     * Filter tasks by status.
     */
    public function filter(string $status): JsonResponse
    {
        try{
            $tasks = Auth::user()->tasks()
            ->where('status', $status)->latest()->get();

            return response()->json([
                'status' => 'success',
                'data' => $tasks
            ], 200);

        }catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve trashed tasks',
                'error' => app()->environment('production') ? 'Server Error' : $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the status of a task.
     */
    public function updateStatus(Request $request, Task $task): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:new,in_progress,completed,canceled',
        ]);

        try{
            $this->authorize('update', $task);

            $task->status = $request->status;
            $task->save();

            return response()->json([
                'status' => 'success',
                'data' => $task
            ], 200);


        }catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve trashed tasks',
                'error' => app()->environment('production') ? 'Server Error' : $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a summary of tasks by status.
     */
    public function summary(): JsonResponse
    {
        try {
            $statuses = ['new', 'in_progress', 'completed', 'canceled'];
            $summary = [];
            foreach ($statuses as $status) {
                $summary[$status] = auth()->user()->tasks()
                    ->where('status', $status)
                ->count();
            }

            return response()->json([
                'status' => 'success',
                'data' => $summary
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to retrieve task summary',
                'error' => app()->environment('production') ? 'Server Error' : $e->getMessage()
            ], 500);
        }
    }

}