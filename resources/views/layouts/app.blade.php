<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Hotel Management') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <style>
            ::-webkit-scrollbar { width: 6px; height: 6px; }
            ::-webkit-scrollbar-track { background: transparent; }
            ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
            ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
            .dark ::-webkit-scrollbar-thumb { background: #334155; }
            .dark ::-webkit-scrollbar-thumb:hover { background: #475569; }
        </style>

        <script>
            // Mencegah layar kedip putih (Flash of Unstyled Content) saat di-refresh
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-sky-50/40 dark:bg-slate-900 transition-colors duration-300">
        <div class="min-h-screen">

            @if(Auth::check() && Auth::user()->role !== 'admin')
                @include('layouts.navigation')
            @endif

            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- Script Super Ringan untuk Switch Toggle -->
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const toggleBtns = document.querySelectorAll('.theme-toggle');
                const toggleBalls = document.querySelectorAll('.theme-toggle-ball');

                // Fungsi sinkronisasi bola UI
                const syncToggleUI = (isDark) => {
                    toggleBalls.forEach(ball => {
                        if (isDark) {
                            ball.classList.replace('translate-x-1', 'translate-x-9');
                        } else {
                            ball.classList.replace('translate-x-9', 'translate-x-1');
                        }
                    });
                };

                // Pengecekan saat halaman pertama dimuat
                const isDarkMode = document.documentElement.classList.contains('dark');
                syncToggleUI(isDarkMode);

                // Event listener untuk tombol switch
                toggleBtns.forEach(btn => {
                    btn.addEventListener('click', () => {
                        const root = document.documentElement;
                        root.classList.toggle('dark');

                        const currentlyDark = root.classList.contains('dark');
                        localStorage.theme = currentlyDark ? 'dark' : 'light';
                        syncToggleUI(currentlyDark);
                    });
                });
            });
        </script>
    </body>
</html>
