<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class NoteController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'image' => 'nullable|image|max:5120'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('notes', 'public');
        }

        $finalTitle = '[' . strtoupper($request->category) . '] ' . $request->title;

        Note::create([
            'user_id' => Auth::id(),
            'title' => $finalTitle,
            'message' => $request->message,
            'image' => $imagePath,
            'is_read' => false,
        ]);

        return redirect()->back()->with('success', 'Catatan berhasil dikirim ke Admin!');
    }

    public function indexAdmin()
    {
        if (Auth::user()->role !== 'admin') abort(403);
        $notes = Note::with('user')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.notes.index', compact('notes'));
    }

    public function markAsRead(Note $note)
    {
        if (Auth::user()->role !== 'admin') abort(403);
        $note->update(['is_read' => true]);
        return redirect()->back()->with('success', 'Catatan ditandai sudah dibaca.');
    }

    public function destroyAdmin(Note $note)
    {
        if (Auth::user()->role !== 'admin') abort(403);
        if ($note->image && Storage::disk('public')->exists($note->image)) {
            Storage::disk('public')->delete($note->image);
        }
        $note->delete();
        return redirect()->back()->with('success', 'Catatan berhasil dihapus.');
    }
}
