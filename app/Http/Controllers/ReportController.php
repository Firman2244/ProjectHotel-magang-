<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function create()
    {
        $user = Auth::user();

        $tasks = Task::where('department', $user->department)->get();

        return view('reports.create', compact('user', 'tasks'));
    }
}
