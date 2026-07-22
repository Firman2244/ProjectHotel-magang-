<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $file = $request->file('avatar');
            $filename = 'avatars/' . uniqid() . '.jpg';
            $destinationPath = storage_path('app/public/' . $filename);

            if (!extension_loaded('gd') ||
                !function_exists('imagecreatefromjpeg') ||
                !function_exists('imagecreatefrompng') ||
                !function_exists('imagecreatefromwebp')) {
                $path = $file->store('avatars', 'public');
                $user->avatar = $path;
            } else {
                list($width, $height, $type) = getimagesize($file->getRealPath());
                $maxW = 500;
                $newW = $width > $maxW ? $maxW : $width;
                $newH = round($height * ($newW / $width));

                switch ($type) {
                    case IMAGETYPE_JPEG:
                        $srcImg = imagecreatefromjpeg($file->getRealPath());
                        break;
                    case IMAGETYPE_PNG:
                        $srcImg = imagecreatefrompng($file->getRealPath());
                        break;
                    case IMAGETYPE_WEBP:
                        $srcImg = imagecreatefromwebp($file->getRealPath());
                        break;
                    default:
                        $srcImg = imagecreatefromjpeg($file->getRealPath());
                }

                $dstImg = imagecreatetruecolor($newW, $newH);
                if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_WEBP) {
                    $white = imagecolorallocate($dstImg, 255, 255, 255);
                    imagefill($dstImg, 0, 0, $white);
                }

                imagecopyresampled($dstImg, $srcImg, 0, 0, 0, 0, $newW, $newH, $width, $height);
                imagejpeg($dstImg, $destinationPath, 80);

                imagedestroy($srcImg);
                imagedestroy($dstImg);

                $user->avatar = $filename;
            }
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
