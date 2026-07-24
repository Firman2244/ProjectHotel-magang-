<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $department = $request->query('department');

        // Ambil daftar departemen unik untuk pilihan filter
        $departments = Task::select('department')->distinct()->pluck('department');

        if ($department) {
            $tasks = Task::where('department', $department)->get();
        } else {
            $tasks = Task::all();
        }

        return view('admin.tasks.index', compact('tasks', 'department', 'departments'));
    }

    public function create()
    {
        $departments = Task::select('department')->distinct()->pluck('department');
        return view('admin.tasks.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'department' => 'required|string',
        ]);

        Task::create($request->all());

        return redirect()->route('admin.tasks.index')->with('success', 'Tugas SOP berhasil ditambahkan!');
    }

    public function edit(Task $task)
    {
        $departments = Task::select('department')->distinct()->pluck('department');
        return view('admin.tasks.edit', compact('task', 'departments'));
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'department' => 'required|string',
        ]);

        $task->update($request->all());

        return redirect()->route('admin.tasks.index')->with('success', 'Tugas SOP berhasil diperbarui!');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('admin.tasks.index')->with('success', 'Tugas SOP berhasil dihapus!');
    }
}
