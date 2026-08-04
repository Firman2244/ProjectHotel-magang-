<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TaskController extends Controller
{
    /**
     * OPTIMASI: query distinct department dipanggil identik di index(),
     * create(), dan edit() — padahal data ini jarang berubah (cuma berubah
     * saat ada Task baru dengan department baru). Di-cache 60 menit,
     * cache di-hapus otomatis tiap kali ada perubahan data Task
     * (lihat store/update/destroy di bawah).
     */
    private function getCachedDepartments()
    {
        return Cache::remember('task_departments_list', 3600, function () {
            return Task::select('department')->distinct()->pluck('department');
        });
    }

    public function index(Request $request)
    {
        $department = $request->query('department');
        $departments = $this->getCachedDepartments();

        if ($department) {
            $tasks = Task::where('department', $department)->get();
        } else {
            $tasks = Task::all();
        }

        return view('admin.tasks.index', compact('tasks', 'department', 'departments'));
    }

    public function create()
    {
        $departments = $this->getCachedDepartments();
        return view('admin.tasks.create', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'department' => 'required|string',
        ]);

        Task::create($request->all());

        Cache::forget('task_departments_list');

        return redirect()->route('admin.tasks.index')->with('success', 'Tugas SOP berhasil ditambahkan!');
    }

    public function edit(Task $task)
    {
        $departments = $this->getCachedDepartments();
        return view('admin.tasks.edit', compact('task', 'departments'));
    }

    public function update(Request $request, Task $task)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'department' => 'required|string',
        ]);

        $task->update($request->all());

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
