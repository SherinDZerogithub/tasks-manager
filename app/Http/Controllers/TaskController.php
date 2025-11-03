<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User; // <-- ADD THIS
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::query();

        if (Auth::user()->is_admin) {
            $query->with('user');
        } else {
            $query->where('user_id', Auth::id());
        }

        // Filter by status
        if ($request->has('status')) {
            if ($request->status === 'overdue') {
                $query->where('due_date', '<', now())->where('status', '!=', 'completed');
            } elseif (in_array($request->status, ['pending', 'in_progress', 'completed'])) {
                $query->where('status', $request->status);
            }
        }

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $tasks = $query->latest()->paginate(10);

        return view('tasks.index', compact('tasks'));
    }


    public function create()
    {
        return view('tasks.create');
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date|after_or_equal:today',
            'user_id' => 'nullable|exists:users,id'
        ]);

        // If user is admin and user_id is provided, use that. Otherwise use current user.
        if (Auth::user()?->is_admin && isset($validated['user_id'])) {
            // Use the selected user_id
        } else {
            $validated['user_id'] = Auth::id();
        }

        Task::create($validated);

        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }
    public function show(Task $task)
    {
        $this->authorize('view', $task);
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $this->authorize('update', $task);
        return view('tasks.edit', compact('task'));
    }
    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:pending,in_progress,completed',
            'due_date' => 'nullable|date',
            'user_id' => 'nullable|exists:users,id'
        ]);

        // If user is not admin, remove user_id from update
        if (!Auth::user()->is_admin) {
            unset($validated['user_id']);
        }

        $task->update($validated);

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }
    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $task->delete();

        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }
}
