<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdminOrManager()) {
            $tasks = Task::with(['assignee', 'creator'])->latest()->paginate(10);
        } else {
            $tasks = Task::where('assigned_to', $user->id)->with(['assignee', 'creator'])->latest()->paginate(10);
        }

        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        $staff = User::where('role', 'staff')->get();
        return view('tasks.create', compact('staff'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority'    => 'required|in:low,medium,high,urgent',
            'status'      => 'required|in:pending,in_progress,completed,cancelled',
            'category'    => 'required|string|max:255',
            'room_number' => 'nullable|string|max:50',
            'assigned_to' => 'nullable|exists:users,id',
            'due_date'    => 'nullable|date',
            'notes'       => 'nullable|string',
        ]);

        Task::create(array_merge($request->all(), [
            'created_by' => Auth::id(),
        ]));

        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    public function show(Task $task)
    {
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $this->authorize('update', $task);
        $staff = User::where('role', 'staff')->get();
        return view('tasks.edit', compact('task', 'staff'));
    }

    public function update(Request $request, Task $task)
    {
        $this->authorize('update', $task);

        $user = Auth::user();

        if ($user->isAdminOrManager()) {
            $request->validate([
                'title'       => 'required|string|max:255',
                'description' => 'nullable|string',
                'priority'    => 'required|in:low,medium,high,urgent',
                'status'      => 'required|in:pending,in_progress,completed,cancelled',
                'category'    => 'required|string|max:255',
                'room_number' => 'nullable|string|max:50',
                'assigned_to' => 'nullable|exists:users,id',
                'due_date'    => 'nullable|date',
                'notes'       => 'nullable|string',
            ]);
            $task->update($request->all());
        } else {
            // Staff can only update status
            $request->validate([
                'status' => 'required|in:pending,in_progress,completed,cancelled',
            ]);
            $task->update($request->only('status'));
        }

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    public function destroy(Task $task)
    {
        $this->authorize('delete', $task);
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted successfully.');
    }
}