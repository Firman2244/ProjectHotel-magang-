<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login - Laporan Hotel</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-sky-950/5 min-h-screen flex items-center justify-center p-6 relative overflow-hidden">
    <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-sky-400/20 rounded-full blur-3xl"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-sky-600/10 rounded-full blur-3xl"></div>

    <div class="w-full max-w-md bg-white/90 backdrop-blur-xl rounded-3xl shadow-xl border border-sky-100 p-8 relative z-10">
        <div class="text-center mb-10">
            <div class="w-16 h-16 bg-sky-600 text-white rounded-2xl flex items-center justify-center font-black text-3xl shadow-lg shadow-sky-600/30 mx-auto mb-4 border-2 border-white">
                H
            </div>
            <h1 class="font-black text-2xl text-slate-800 tracking-tight">LAPORAN HOTEL</h1>
            <p class="text-xs font-bold text-sky-600 uppercase tracking-widest mt-1">Sistem Manajemen</p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf
            <div>
                <label for="email" class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="w-full border-sky-200 bg-sky-50/50 text-slate-800 font-bold rounded-xl shadow-sm py-3 px-4 focus:border-sky-500 focus:ring-sky-500 transition">
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-rose-600" />
            </div>

            <div>
                <div class="flex justify-between items-center mb-2">
                    <label for="password" class="block text-xs font-black text-slate-500 uppercase tracking-wider">Password</label>
                </div>
                <input id="password" type="password" name="password" required autocomplete="current-password" class="w-full border-sky-200 bg-sky-50/50 text-slate-800 font-bold rounded-xl shadow-sm py-3 px-4 focus:border-sky-500 focus:ring-sky-500 transition">
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-rose-600" />
            </div>

            <div class="flex items-center justify-between">
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <input id="remember_me" type="checkbox" name="remember" class="rounded border-sky-300 text-sky-600 shadow-sm focus:ring-sky-500 w-4 h-4 cursor-pointer">
                    <span class="ms-2 text-sm font-bold text-slate-600">Ingat Saya</span>
                </label>
            </div>

            <button type="submit" class="w-full bg-sky-600 hover:bg-sky-700 text-white font-black py-3.5 px-4 rounded-xl shadow-lg shadow-sky-600/30 transition duration-200 transform hover:-translate-y-0.5">
                LOG IN KE SISTEM
            </button>
        </form>
    </div>
</body>
</html>
