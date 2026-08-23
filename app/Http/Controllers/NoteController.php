<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;
use App\Models\PointHistory;
use App\Jobs\ProcessNoteImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

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

        $finalTitle = '[' . strtoupper($request->category) . '] ' . $request->title;

        $note = Note::create([
            'user_id' => Auth::id(),
            'hotel_id' => Auth::user()->hotel_id,
            'category' => $request->category,
            'title' => $finalTitle,
            'message' => $request->message,
            'image' => null,
            'status' => 'open',
            'is_read' => false,
        ]);

        if ($request->hasFile('image')) {
            $tempPath = $request->file('image')->store('notes/tmp', 'public');
            dispatch_sync(new ProcessNoteImage($note->id, $tempPath, Auth::user()->hotel_id, 'image'));
        }

        return redirect()->back()->with('success', 'Catatan berhasil dikirim ke Admin!');
    }

    public function resolveTask(Request $request, Note $note)
    {
        if (Auth::user()->department !== 'Engineering') abort(403);

        $request->validate([
            'resolved_images' => 'required|array',
            'resolved_images.*' => 'image|max:5120',
            'resolved_note' => 'nullable|string',
            'helpers' => 'nullable|array',
            'helpers.*' => 'exists:users,id'
        ]);

        $imagePaths = [];
        if ($request->hasFile('resolved_images')) {
            $hotelId = Auth::user()->hotel_id;
            $month = Carbon::now('Asia/Jakarta')->format('Y-m');

            foreach ($request->file('resolved_images') as $file) {
                $path = $file->store("notes/hotel_{$hotelId}/{$month}/resolved", 'public');
                $this->optimizeImage($path);
                $imagePaths[] = $path;
            }
        }

        $note->update([
            'status' => 'resolved',
            'resolved_by' => Auth::id(),
            'resolved_note' => $request->resolved_note,
            'resolved_at' => Carbon::now('Asia/Jakarta'),
            'resolved_image' => json_encode($imagePaths),
            'helpers' => json_encode($request->helpers ?? [])
        ]);

        return redirect()->back()->with('success', 'Perbaikan berhasil dilaporkan!');
    }

    public function verifyTask(Note $note)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        if ($note->status === 'verified') {
            return redirect()->back()->with('error', 'Laporan ini sudah diverifikasi sebelumnya.');
        }
        if ($note->status !== 'resolved' || is_null($note->resolved_by)) {
            return redirect()->back()->with('error', 'Laporan belum diselesaikan oleh staf.');
        }

        $note->update([
            'status' => 'verified',
            'verified_by' => Auth::id(),
            'verified_at' => Carbon::now('Asia/Jakarta'),
            'is_read' => true
        ]);

        $rewardPoints = 15;
        $userIds = [$note->resolved_by];

        $helpersArray = is_string($note->helpers) ? json_decode($note->helpers, true) : $note->helpers;

        if (!empty($helpersArray) && is_array($helpersArray)) {
            $userIds = array_merge($userIds, $helpersArray);
        }
        $userIds = array_unique($userIds);

        $now = Carbon::now('Asia/Jakarta');

        $pointData = [];
        foreach ($userIds as $userId) {
            $pointData[] = [
                'user_id' => $userId,
                'type' => 'WORK_ORDER',
                'description' => 'Perbaikan: ' . $note->title,
                'points' => $rewardPoints,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }
        PointHistory::insert($pointData);

        return redirect()->back()->with('success', 'Laporan perbaikan telah diverifikasi dan poin dibagikan!');
    }

    public function indexAdmin()
    {
        if (Auth::user()->role !== 'admin') abort(403);
        $notes = Note::with(['user:id,name,hotel_id,department', 'resolver:id,name', 'verifier:id,name'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('admin.notes.index', compact('notes'));
    }

    public function markAsRead(Note $note)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        $note->update([
            'is_read' => true,
            'status' => 'verified',
            'verified_by' => Auth::id(),
            'verified_at' => Carbon::now('Asia/Jakarta')
        ]);

        return redirect()->back()->with('success', 'Catatan ditandai sudah dibaca dan selesai.');
    }

    public function destroyAdmin(Note $note)
    {
        if (Auth::user()->role !== 'admin') abort(403);

        if ($note->image && Storage::disk('public')->exists($note->image)) {
            Storage::disk('public')->delete($note->image);
        }

        if ($note->resolved_image) {
            $images = json_decode($note->resolved_image, true);
            if (is_array($images)) {
                foreach ($images as $img) {
                    if (Storage::disk('public')->exists($img)) {
                        Storage::disk('public')->delete($img);
                    }
                }
            } elseif (is_string($note->resolved_image) && Storage::disk('public')->exists($note->resolved_image)) {
                Storage::disk('public')->delete($note->resolved_image);
            }
        }

        $note->delete();
        return redirect()->back()->with('success', 'Catatan berhasil dihapus.');
    }

    private function optimizeImage(string $filePath)
    {
        $fullPath = Storage::disk('public')->path($filePath);
        if (!file_exists($fullPath)) return;

        $info = @getimagesize($fullPath);
        if (!$info) return;

        $width = $info[0];
        $height = $info[1];
        $mime = $info['mime'];

        $maxWidth = 800;
        if ($width > $maxWidth) {
            $newWidth = $maxWidth;
            $newHeight = floor($height * ($maxWidth / $width));
        } else {
            $newWidth = $width;
            $newHeight = $height;
        }

        if ($mime == 'image/jpeg') {
            $image = @imagecreatefromjpeg($fullPath);
        } elseif ($mime == 'image/png') {
            $image = @imagecreatefrompng($fullPath);
        } else {
            return;
        }

        if ($image) {
            $resized = imagecreatetruecolor($newWidth, $newHeight);

            if ($mime == 'image/png') {
                imagealphablending($resized, false);
                imagesavealpha($resized, true);
            }

            imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

            if ($mime == 'image/png') {
                imagepng($resized, $fullPath, 8);
            } else {
                imagejpeg($resized, $fullPath, 60);
            }

            imagedestroy($image);
            imagedestroy($resized);
        }
    }
}
