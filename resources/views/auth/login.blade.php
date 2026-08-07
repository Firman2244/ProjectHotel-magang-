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
<body class="font-sans antialiased bg-sky-950/5 dark:bg-slate-900 min-h-screen flex items-center justify-center p-6 relative overflow-hidden transition-colors duration-300">

    <!-- Dekorasi Background -->
    <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-sky-400/20 dark:bg-sky-400/10 rounded-full blur-3xl"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-sky-600/10 dark:bg-sky-600/5 rounded-full blur-3xl"></div>

    <!-- Tombol Dark Mode Kapsul (Pojok Kanan Atas) -->
    <div class="absolute top-6 right-6 z-50">
        <button type="button" id="theme-toggle-btn" class="theme-toggle relative inline-flex h-8 w-16 items-center rounded-full bg-slate-200 dark:bg-slate-700 transition-colors duration-300 focus:outline-none shadow-inner border border-slate-300 dark:border-slate-600">
            <span class="sr-only">Toggle Dark Mode</span>
            <span class="theme-toggle-ball flex h-6 w-6 transform items-center justify-center rounded-full bg-white shadow-md transition-transform duration-300 translate-x-1 dark:translate-x-9">
                <!-- Icon Matahari (Light Mode) -->
                <svg class="w-4 h-4 text-amber-500 hidden dark:block" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4.22 4.22a1 1 0 011.415 0l.849.849a1 1 0 01-1.414 1.414l-.849-.849a1 1 0 010-1.414zm-9.855 0a1 1 0 010 1.414l-.849.849a1 1 0 01-1.414-1.414l.849-.849a1 1 0 011.414 0zM10 6a4 4 0 100 8 4 4 0 000-8zm-4 4a1 1 0 11-2 0 1 1 0 012 0zm11-1a1 1 0 110 2h-1a1 1 0 110-2h1zM5.636 15.636a1 1 0 011.414 0l.849.849a1 1 0 01-1.414 1.414l-.849-.849a1 1 0 010-1.414zm9.855 0a1 1 0 010 1.414l.849.849a1 1 0 01-1.414-1.414l.849-.849a1 1 0 011.414 0zM10 16a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1z"></path>
                </svg>
                <!-- Icon Bulan (Dark Mode) -->
                <svg class="w-4 h-4 text-slate-700 block dark:hidden" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                </svg>
            </span>
        </button>
    </div>

    <!-- Kotak Login -->
    <div class="w-full max-w-md bg-white/90 dark:bg-slate-800/90 backdrop-blur-xl rounded-3xl shadow-xl border border-sky-100 dark:border-slate-700 p-8 relative z-10 transition-colors duration-300">
        <div class="text-center mb-10">
            <h1 class="font-black text-2xl text-slate-800 dark:text-white tracking-tight">LAPORAN HOTEL</h1>
            <p class="text-xs font-bold text-sky-600 dark:text-sky-400 uppercase tracking-widest mt-1">Sistem Manajemen</p>
        </div>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-6">
            @csrf
            <div>
                <label for="email" class="block text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="w-full border-sky-200 dark:border-slate-600 bg-sky-50/50 dark:bg-slate-900/50 text-slate-800 dark:text-white font-bold rounded-xl shadow-sm py-3 px-4 focus:border-sky-500 focus:ring-sky-500 transition-colors duration-300">
                <x-input-error :messages="$errors->get('email')" class="mt-2 text-rose-600 dark:text-rose-400" />
            </div>

            <div>
                <div class="flex justify-between items-center mb-2">
                    <label for="password" class="block text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">Password</label>
                </div>
                <input id="password" type="password" name="password" required autocomplete="current-password" class="w-full border-sky-200 dark:border-slate-600 bg-sky-50/50 dark:bg-slate-900/50 text-slate-800 dark:text-white font-bold rounded-xl shadow-sm py-3 px-4 focus:border-sky-500 focus:ring-sky-500 transition-colors duration-300">
                <x-input-error :messages="$errors->get('password')" class="mt-2 text-rose-600 dark:text-rose-400" />
            </div>

            <div class="flex items-center justify-between">
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <input id="remember_me" type="checkbox" name="remember" class="rounded border-sky-300 dark:border-slate-600 bg-white dark:bg-slate-900 text-sky-600 shadow-sm focus:ring-sky-500 w-4 h-4 cursor-pointer transition-colors duration-300">
                    <span class="ms-2 text-sm font-bold text-slate-600 dark:text-slate-400">Ingat Saya</span>
                </label>
            </div>

            <button type="submit" class="w-full bg-sky-600 hover:bg-sky-700 text-white font-black py-3.5 px-4 rounded-xl shadow-lg shadow-sky-600/30 transition duration-200 transform hover:-translate-y-0.5">
                LOG IN KE SISTEM
            </button>
        </form>
    </div>

    <!-- Script Pengatur Dark Mode Lokal -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleBtn = document.getElementById('theme-toggle-btn');
            const htmlElement = document.documentElement;

            // Cek status dark mode saat load
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                htmlElement.classList.add('dark');
            } else {
                htmlElement.classList.remove('dark');
            }

            // Fungsi klik tombol
            if (toggleBtn) {
                toggleBtn.addEventListener('click', () => {
                    htmlElement.classList.toggle('dark');
                    if (htmlElement.classList.contains('dark')) {
                        localStorage.theme = 'dark';
                    } else {
                        localStorage.theme = 'light';
                    }
                });
            }
        });
    </script>
</body>
</html>
