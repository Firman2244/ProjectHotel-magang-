<aside class="w-64 h-screen sticky top-0 flex flex-col justify-between bg-sky-100/85 dark:bg-slate-900 backdrop-blur-md text-slate-700 dark:text-slate-200 shadow-xl border-r border-sky-200/60 dark:border-slate-800 z-30 transition-colors duration-300">
    <div class="flex-1 overflow-y-auto">
        <div class="p-6 border-b border-sky-200/60 dark:border-slate-800 flex items-center justify-between gap-2">
            <div class="flex items-center gap-3">
                <div>
                    <h1 class="font-black text-slate-800 dark:text-white text-base tracking-wide">Hotel Management</h1>
                    <p class="text-[10px] text-sky-700 dark:text-sky-400 font-bold uppercase tracking-widest">System Admin</p>
                </div>
            </div>

            <button type="button" class="theme-toggle relative inline-flex h-7 w-14 items-center rounded-full bg-slate-200 dark:bg-slate-700 transition-colors duration-300 focus:outline-none shadow-inner border border-slate-300 dark:border-slate-600 flex-shrink-0">
                <span class="sr-only">Toggle Dark Mode</span>
                <span class="theme-toggle-ball flex h-5 w-5 transform items-center justify-center rounded-full bg-white shadow-md transition-transform duration-300 translate-x-1 dark:translate-x-8">
                    <svg class="w-3.5 h-3.5 text-amber-500 hidden dark:block" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4.22 4.22a1 1 0 011.415 0l.849.849a1 1 0 01-1.414 1.414l-.849-.849a1 1 0 010-1.414zm-9.855 0a1 1 0 010 1.414l-.849.849a1 1 0 01-1.414-1.414l-.849-.849a1 1 0 011.414 0zM10 6a4 4 0 100 8 4 4 0 000-8zm-4 4a1 1 0 11-2 0 1 1 0 012 0zm11-1a1 1 0 110 2h-1a1 1 0 110-2h1zM5.636 15.636a1 1 0 011.414 0l.849.849a1 1 0 01-1.414 1.414l-.849-.849a1 1 0 010-1.414zm9.855 0a1 1 0 010 1.414l.849.849a1 1 0 01-1.414-1.414l-.849-.849a1 1 0 011.414 0zM10 16a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1z"></path>
                    </svg>
                    <svg class="w-3.5 h-3.5 text-slate-700 block dark:hidden" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                    </svg>
                </span>
            </button>
        </div>

        <div class="px-4 py-3 bg-sky-200/40 dark:bg-slate-800/50 border-b border-sky-200/60 dark:border-slate-800">
            <label class="block text-[10px] font-black text-sky-800 dark:text-sky-400 uppercase tracking-wider mb-1.5">
                Pilih Hotel Aktif
            </label>

            <select id="hotel-selector"
                    class="w-full bg-white/90 dark:bg-slate-800 border border-sky-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 font-bold rounded-xl text-xs py-2 px-3 focus:border-sky-500 focus:ring-sky-500 cursor-pointer shadow-sm transition-colors duration-300">
                <option value="wahyu" {{ ($currentHotel ?? request('hotel', 'wahyu')) == 'wahyu' ? 'selected' : '' }}>
                    Hotel Wahyu
                </option>
                <option value="nirwana" {{ ($currentHotel ?? request('hotel', 'wahyu')) == 'nirwana' ? 'selected' : '' }}>
                    Hotel Nirwana
                </option>
            </select>
        </div>

        <nav class="px-4 py-6 space-y-1.5">
            <p class="px-3 text-[10px] font-black text-sky-800/70 dark:text-sky-400/70 uppercase tracking-wider mb-2">
                Main Menu
            </p>

            <a href="{{ route('admin.dashboard', ['hotel' => request('hotel', 'wahyu')]) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.dashboard') ? 'bg-sky-600 text-white font-bold shadow-sm' : 'hover:bg-sky-200/60 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-semibold' }} text-sm transition">
                <span>📊</span> Dashboard
            </a>

            <a href="{{ route('admin.hotels.index', ['hotel' => request('hotel', 'wahyu')]) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.hotels.*') ? 'bg-sky-600 text-white font-bold shadow-sm' : 'hover:bg-sky-200/60 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-semibold' }} text-sm transition">
                <span>🏨</span> Data Hotel
            </a>

            <a href="{{ route('admin.staff.index', ['hotel' => request('hotel', 'wahyu')]) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.staff.index') || request()->routeIs('admin.staff.create') || request()->routeIs('admin.staff.edit') ? 'bg-sky-600 text-white font-bold shadow-sm' : 'hover:bg-sky-200/60 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-semibold' }} text-sm transition">
                <span>👥</span> Manajemen Staf
            </a>

            <a href="{{ route('admin.staff.scores', ['hotel' => request('hotel', 'wahyu')]) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.staff.scores') ? 'bg-sky-600 text-white font-bold shadow-sm' : 'hover:bg-sky-200/60 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-semibold' }} text-sm transition">
                <span>🏆</span> Leaderboard Skor Staf
            </a>

            <a href="{{ route('admin.shifts.index', ['hotel' => request('hotel', 'wahyu')]) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.shifts.*') ? 'bg-sky-600 text-white font-bold shadow-sm' : 'hover:bg-sky-200/60 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-semibold' }} text-sm transition">
                <span>⏱️</span> Pengaturan Shift
            </a>

            <a href="{{ route('admin.tasks.index', ['hotel' => request('hotel', 'wahyu')]) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.tasks.*') ? 'bg-sky-600 text-white font-bold shadow-sm' : 'hover:bg-sky-200/60 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-semibold' }} text-sm transition">
                <span>📋</span> Master Tugas (SOP)
            </a>

            <a href="{{ route('admin.reports.summary', ['hotel' => request('hotel', 'wahyu')]) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.reports.summary') ? 'bg-sky-600 text-white font-bold shadow-sm' : 'hover:bg-sky-200/60 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-semibold' }} text-sm transition">
                <span>📈</span> Rangkuman Laporan
            </a>

            <a href="{{ route('admin.storage.index', ['hotel' => request('hotel', 'wahyu')]) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.storage.*') ? 'bg-sky-600 text-white font-bold shadow-sm' : 'hover:bg-sky-200/60 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-semibold' }} text-sm transition">
                <span>💾</span> Manajemen Storage
            </a>

            @php
                $unreadNotes = \App\Models\Note::where('is_read', false)->count();
            @endphp
            <a href="{{ route('admin.notes.index', ['hotel' => request('hotel', 'wahyu')]) }}"
               class="flex items-center justify-between px-4 py-3 rounded-xl {{ request()->routeIs('admin.notes.*') ? 'bg-sky-600 text-white font-bold shadow-sm' : 'hover:bg-sky-200/60 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-semibold' }} text-sm transition">
                <div class="flex items-center gap-3">
                    <span>📋</span> Catatan & Laporan
                </div>
                @if($unreadNotes > 0)
                    <span class="bg-rose-500 text-white text-[10px] font-black px-2 py-0.5 rounded-full shadow-sm animate-pulse">{{ $unreadNotes }}</span>
                @endif
            </a>

            <a href="{{ route('admin.activity-logs', ['hotel' => request('hotel', 'wahyu')]) }}"
               class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.activity-logs') ? 'bg-sky-600 text-white font-bold shadow-sm' : 'hover:bg-sky-200/60 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white font-semibold' }} text-sm transition">
                <span>🛡️</span> Activity Log
            </a>
        </nav>
    </div>

    <div class="p-4 border-t border-sky-200/60 dark:border-slate-800 bg-sky-200/25 dark:bg-slate-900/50 flex-shrink-0 transition-colors duration-300">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full flex items-center justify-center gap-2 bg-rose-500/10 dark:bg-rose-500/20 hover:bg-rose-500/20 dark:hover:bg-rose-500/30 text-rose-600 dark:text-rose-400 font-bold py-3 px-4 rounded-xl transition border border-rose-500/20 dark:border-rose-500/30 text-xs">
                <span>🚪</span> Log Out System
            </button>
        </form>
    </div>
</aside>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const hotelSelector = document.getElementById('hotel-selector');

    if (hotelSelector) {
        hotelSelector.addEventListener('change', function () {
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('hotel', this.value);
            window.location.href = currentUrl.toString();
        });
    }
});
</script>
