<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TaskController extends Controller
{
    private function getCachedDepartments()
    {
        return Cache::remember('task_departments_list', 3600, fn() => Task::select('department')->distinct()->pluck('department'));
    }

    public function index(Request $request)
    {
        $department = $request->query('department');
        $departments = $this->getCachedDepartments();
        $tasks = $department ? Task::where('department', $department)->get() : Task::all();

        return view('admin.tasks.index', compact('tasks', 'department', 'departments'));
    }

    public function create()
    {
        return view('admin.tasks.create', ['departments' => $this->getCachedDepartments()]);
    }

    public function store(Request $request)
    {
        Task::create($request->validate(['name' => 'required|string|max:255', 'department' => 'required|string']));
        Cache::forget('task_departments_list');
        return redirect()->route('admin.tasks.index')->with('success', 'Tugas SOP berhasil ditambahkan!');
    }

    public function edit(Task $task)
    {
        return view('admin.tasks.edit', ['task' => $task, 'departments' => $this->getCachedDepartments()]);
    }

    public function update(Request $request, Task $task)
    {
        $task->update($request->validate(['name' => 'required|string|max:255', 'department' => 'required|string']));
        Cache::forget('task_departments_list');
        return redirect()->route('admin.tasks.index')->with('success', 'Tugas SOP berhasil diperbarui!');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        Cache::forget('task_departments_list');
        return redirect()->route('admin.tasks.index')->with('success', 'Tugas SOP berhasil dihapus!');
    }
}
