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

    <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-sky-400/20 dark:bg-sky-400/10 rounded-full blur-3xl"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-sky-600/10 dark:bg-sky-600/5 rounded-full blur-3xl"></div>

    <div class="absolute top-6 right-6 z-50">
        <button type="button" id="theme-toggle-btn" class="theme-toggle relative inline-flex h-8 w-16 items-center rounded-full bg-slate-200 dark:bg-slate-700 transition-colors duration-300 focus:outline-none shadow-inner border border-slate-300 dark:border-slate-600 cursor-pointer">
            <span class="sr-only">Toggle Dark Mode</span>
            <span class="theme-toggle-ball flex h-6 w-6 transform items-center justify-center rounded-full bg-white dark:bg-slate-800 shadow-md transition-transform duration-300 translate-x-1 dark:translate-x-9">
                <svg class="w-4 h-4 text-amber-500 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                <svg class="w-4 h-4 text-indigo-400 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
            </span>
        </button>
    </div>

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

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const toggleBtn = document.getElementById('theme-toggle-btn');
            const htmlElement = document.documentElement;

            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                htmlElement.classList.add('dark');
            } else {
                htmlElement.classList.remove('dark');
            }

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
