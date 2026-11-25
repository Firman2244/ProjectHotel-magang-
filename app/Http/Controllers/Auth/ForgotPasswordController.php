<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ForgotPasswordController extends Controller
{
    private const SESSION_KEY = 'forgot_password_user_id';

    /**
     * Langkah 1: halaman input email.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Cocokkan email dengan database. Jika cocok, lanjut ke halaman akun.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
        ]);

        $user = User::whereRaw('LOWER(email) = ?', [mb_strtolower(trim($request->email))])->first();

        if (! $user) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email tidak terdaftar di sistem.']);
        }

        $request->session()->put(self::SESSION_KEY, $user->id);

        return redirect()->route('password.account');
    }

    /**
     * Langkah 2: tampilkan email + password (disamarkan), pilih ganti password / lanjut login.
     */
    public function account(Request $request): View|RedirectResponse
    {
        $user = $this->resolveUser($request);

        if (! $user) {
            return redirect()->route('password.request');
        }

        return view('auth.forgot-password-account', ['user' => $user]);
    }

    /**
     * Langkah 3: simpan password baru (harus sama dengan konfirmasi).
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $this->resolveUser($request);

        if (! $user) {
            return redirect()->route('password.request');
        }

        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ], [
            'password.required' => 'Password baru wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak cocok dengan password baru.',
            'password.min' => 'Password minimal :min karakter.',
        ]);

        // Cast 'hashed' pada model User otomatis melakukan hashing.
        $user->password = $request->password;
        $user->save();

        $request->session()->forget(self::SESSION_KEY);

        return redirect()
            ->route('login', ['email' => $user->email])
            ->with('status', 'Password berhasil diubah. Silakan login dengan password baru.');
    }

    private function resolveUser(Request $request): ?User
    {
        $id = $request->session()->get(self::SESSION_KEY);

        return $id ? User::find($id) : null;
    }
}
