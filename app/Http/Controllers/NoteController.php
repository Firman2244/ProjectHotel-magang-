<?php

namespace App\Http\Controllers;

use App\Models\Note;
use App\Http\Requests\SubmitNoteRequest;
use App\Jobs\ProcessNoteImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class NoteController extends Controller
{
    public function store(SubmitNoteRequest $request)
    {
        // 1. Buat data Note dulu tanpa gambar
        $note = Note::create([
            'user_id'  => Auth::id(),
            'hotel_id' => Auth::user()->hotel_id,
            'title'    => $request->title,
            'message'  => $request->message,
            'image'    => null,
            'is_read'  => false
        ]);

        // 2. Jika ada foto, simpan ke temp dan lempar ke Job
        if ($request->hasFile('image')) {
            $tempPath = $request->file('image')->store('notes/tmp', 'public');

            if (app()->environment('local')) {
                ProcessNoteImage::dispatchSync($note->id, $tempPath, Auth::user()->hotel_id);
            } else {
                ProcessNoteImage::dispatch($note->id, $tempPath, Auth::user()->hotel_id);
            }
        }

        return redirect()->back()->with('success', 'Catatan kerusakan berhasil dikirim ke Admin!');
    }

    public function indexAdmin()
    {
        $notes = Note::with('user')
            ->orderBy('is_read', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.notes.index', compact('notes'));
    }

    public function markAsRead(Note $note)
    {
        $note->update(['is_read' => true]);
        return redirect()->back()->with('success', 'Catatan ditandai sudah dibaca.');
    }

    public function destroyAdmin(Note $note)
    {
        if ($note->image && Storage::disk('public')->exists($note->image)) {
            Storage::disk('public')->delete($note->image);
        }

        $note->delete();

        return redirect()->back()->with('success', 'Laporan kerusakan berhasil dihapus.');
    }
}
