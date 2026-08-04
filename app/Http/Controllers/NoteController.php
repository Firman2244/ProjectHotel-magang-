<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;

class NoteController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:150',
            'message' => 'required|string|max:1000',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:10240'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = Str::random(40) . '.jpg';
            $imagePath = 'notes/' . $filename;

            $compressedImage = Image::make($image)
                ->resize(800, 800, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->encode('jpg', 75);

            Storage::disk('public')->put($imagePath, $compressedImage);
        }

        Note::create([
            'user_id' => Auth::id(),
            'hotel_id' => Auth::user()->hotel_id,
            'title' => $request->title,
            'message' => $request->message,
            'image' => $imagePath,
            'is_read' => false
        ]);

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
