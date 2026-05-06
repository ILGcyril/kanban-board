<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Board;
use App\Models\Space;
use App\Models\Task;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    use AuthorizesRequests;

    public function store(StoreTaskRequest $request, Space $space, Board $board)
    {
        $this->authorize('create', [Task::class, $board]);

        $data = $request->validated();

        Task::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'board_id' => $board->id,
            'status' => $data['status'],
            'order_column' => 0
        ]);

        return redirect()->back();
    }

    public function update(UpdateTaskRequest $request, Space $space, Board $board, Task $task)
    {
        $this->authorize('update', $task);
    
        $data = $request->validated();
    
        $task->update([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? $task->status,
        ]);
    
        if ($request->has('tags')) {
            $task->tags()->sync($request->tags);
        } else {
            $task->tags()->detach();
        }
    
        return redirect()->back();
    }

    public function destroy(Space $space, Board $board, Task $task)
    {
        $this->authorize('delete', $task);

        $task->delete();

        return redirect()->back();
    }

    public function sort(Request $request)
    {
        try {
            $request->validate([
                'items' => 'required|array',
                'items.*' => 'exists:tasks,id',
                'status' => 'required|in:todo,in_progress,done',
            ]);
    
            $status = $request->status;
            $items = $request->items;
    
            foreach ($items as $index => $id) {
                Task::where('id', $id)->update([
                    'order_column' => $index,
                    'status' => $status // Обновляем статус, если задача перешла в другую колонку
                ]);
            }
    
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            Log::error('Error sorting tasks: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
