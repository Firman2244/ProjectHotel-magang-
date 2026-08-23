<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Hotel Management') }}</title>
        <link rel="icon" href='data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><text y=".9em" font-size="90">🏨</text></svg>'>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <style>
            ::-webkit-scrollbar { width: 6px; height: 6px; }
            ::-webkit-scrollbar-track { background: transparent; }
            ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
            ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
            .dark ::-webkit-scrollbar-thumb { background: #334155; }
            .dark ::-webkit-scrollbar-thumb:hover { background: #475569; }
            [x-cloak] { display: none !important; }
        </style>

        <script>
            function applyStoredTheme() {
                if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            }
            applyStoredTheme();
            document.addEventListener('livewire:navigated', applyStoredTheme);
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="font-sans antialiased bg-sky-50/40 dark:bg-slate-900">
        @if(Auth::check() && Auth::user()->role === 'admin')
            <div class="min-h-screen flex flex-col md:flex-row relative" x-data="{ sidebarOpen: false }">

                <div class="md:hidden sticky top-0 z-40 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between h-16 px-4">
                    <h1 class="font-black text-xl text-slate-800 dark:text-white">Admin Panel</h1>
                    <button type="button" @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>

                <div x-cloak :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'" class="fixed inset-y-0 left-0 z-50 w-64 h-screen bg-white dark:bg-slate-800 shadow-2xl md:shadow-none transform transition-transform duration-300 ease-in-out flex-shrink-0 md:fixed dark:border-r dark:border-slate-700">
                    <x-admin-sidebar />
                </div>

                <div x-cloak x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-40 md:hidden"></div>

                <div class="flex-1 flex flex-col min-w-0 md:ml-64 overflow-y-auto relative">
                    {{ $slot }}
                </div>

            </div>
        @else
            <div class="min-h-screen">
                @if(Auth::check())
                    @include('layouts.navigation')
                @endif

                <main>
                    {{ $slot }}
                </main>
            </div>
        @endif

        <script>
            function initThemeToggles() {
                const toggleBtns = document.querySelectorAll('.theme-toggle');
                const toggleBalls = document.querySelectorAll('.theme-toggle-ball');

                const syncToggleUI = (isDark) => {
                    toggleBalls.forEach(ball => {
                        if (isDark) {
                            ball.classList.replace('translate-x-1', 'translate-x-9');
                        } else {
                            ball.classList.replace('translate-x-9', 'translate-x-1');
                        }
                    });
                };

                const isDarkMode = document.documentElement.classList.contains('dark');
                syncToggleUI(isDarkMode);

                toggleBtns.forEach(btn => {
                    if (btn.hasAttribute('data-theme-bound')) return;
                    btn.setAttribute('data-theme-bound', 'true');
                    btn.addEventListener('click', () => {
                        const root = document.documentElement;
                        root.classList.toggle('dark');

                        const currentlyDark = root.classList.contains('dark');
                        localStorage.theme = currentlyDark ? 'dark' : 'light';
                        syncToggleUI(currentlyDark);
                    });
                });
            }

            function initGlobalHotelSelector() {
                document.addEventListener('change', function(e) {
                    if (e.target && e.target.id === 'hotel-selector') {
                        const currentUrl = new URL(window.location.href);
                        currentUrl.searchParams.set('hotel', e.target.value);
                        if (typeof Livewire !== 'undefined') {
                            Livewire.navigate(currentUrl.toString());
                        } else {
                            window.location.href = currentUrl.toString();
                        }
                    }
                });
            }

            document.addEventListener('DOMContentLoaded', () => {
                initThemeToggles();
                initGlobalHotelSelector();
            });
            document.addEventListener('livewire:navigated', () => {
                initThemeToggles();
            });
        </script>

        @livewireScripts
    </body>
</html>
