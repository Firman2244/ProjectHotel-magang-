@php
    $hotels = \Illuminate\Support\Facades\Cache::remember('admin_sidebar_hotels', 600, fn() => \App\Models\Hotel::all());
    $reqHotel = request('hotel');
    $currentHotelId = $reqHotel instanceof \App\Models\Hotel ? $reqHotel->id : $reqHotel;
    if (!$currentHotelId && $hotels->isNotEmpty()) {
        $currentHotelId = $hotels->first()->id;
    }
    $unreadNotes = \Illuminate\Support\Facades\Cache::remember('unread_notes_count', 60, fn() => \App\Models\Note::where('is_read', false)->count());

    $isGlobalData = request()->is('admin/hotels*') || request()->is('admin/tasks*');

    // Tambahin '*/point-history' biar dropdown otomatis kekunci pas buka histori
    $isFormLocked = request()->is('*/create') || request()->is('*/edit') || request()->is('*/point-history');
@endphp

<aside class="w-64 h-screen sticky top-0 flex flex-col justify-between bg-sky-100/85 dark:bg-slate-900 text-slate-700 dark:text-slate-200 shadow-xl border-r border-sky-200/60 dark:border-slate-800 z-30">
    <div class="flex-1 overflow-y-auto">
        <div class="p-4 border-b border-sky-200/60 dark:border-slate-800 flex items-center justify-between gap-2">
            <div>
                <h1 class="font-black text-slate-800 dark:text-white text-[15px] tracking-wide">Hotel Management</h1>
                <p class="text-[9px] text-sky-700 dark:text-sky-400 font-bold uppercase tracking-widest">System Admin</p>
            </div>
            <button type="button" class="theme-toggle relative inline-flex h-6 w-12 items-center rounded-full bg-slate-200 dark:bg-slate-700 focus:outline-none shadow-inner border border-slate-300 dark:border-slate-600 flex-shrink-0">
                <span class="sr-only">Toggle Dark Mode</span>
                <span class="theme-toggle-ball flex h-4 w-4 transform items-center justify-center rounded-full bg-white dark:bg-slate-800 shadow-md transition-transform duration-300 translate-x-1 dark:translate-x-7">
                    <svg class="w-3 h-3 text-amber-500 block dark:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <svg class="w-3 h-3 text-indigo-400 hidden dark:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                </span>
            </button>
        </div>

        @if($isGlobalData)
        <div class="px-4 py-2 bg-slate-200/40 dark:bg-slate-800/30 border-b border-slate-300/60 dark:border-slate-800">
            <label class="block text-[9px] font-black text-slate-500 dark:text-slate-500 uppercase tracking-wider mb-1">
                Mode Sistem
            </label>
            <div class="w-full bg-slate-100/50 dark:bg-slate-800/50 border border-slate-300/50 dark:border-slate-700/50 text-slate-500 dark:text-slate-500 font-bold rounded-lg text-[11px] py-1.5 px-2.5 flex items-center shadow-inner cursor-not-allowed uppercase tracking-wide">
                🌐 Global (Lintas Cabang)
            </div>
        </div>
        @else
        <div class="px-4 py-2 bg-sky-200/40 dark:bg-slate-800/50 border-b border-sky-200/60 dark:border-slate-800">
            <label class="block text-[9px] font-black text-sky-800 dark:text-sky-400 uppercase tracking-wider mb-1">
                Pilih Hotel Aktif
            </label>
            <select id="hotel-selector"
                    {{ $isFormLocked ? 'disabled' : '' }}
                    class="w-full bg-white/90 dark:bg-slate-800 border border-sky-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 font-bold rounded-lg text-xs py-1.5 px-2.5 focus:border-sky-500 focus:ring-sky-500 shadow-sm {{ $isFormLocked ? 'opacity-50 cursor-not-allowed bg-slate-100 dark:bg-slate-900' : 'cursor-pointer' }}">
                @foreach($hotels as $h)
                    <option value="{{ $h->id }}" {{ $currentHotelId == $h->id ? 'selected' : '' }}>
                        {{ $h->name }}
                    </option>
                @endforeach
            </select>
        </div>
        @endif

        <nav class="px-3 py-4 space-y-1">
            <p class="px-3 text-[9px] font-black text-sky-800/70 dark:text-slate-500 uppercase tracking-wider mb-1.5 mt-1">Operasional Harian</p>
            <a href="{{ route('admin.dashboard', ['hotel' => $currentHotelId]) }}" wire:navigate class="flex items-center gap-2.5 px-3 py-2 rounded-lg {{ request()->is('admin/dashboard*') ? 'bg-sky-600 text-white font-bold shadow-sm' : 'hover:bg-sky-200/60 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 font-semibold' }} text-[13px]"><span>📊</span> Dashboard</a>
            <a href="{{ route('admin.reports.summary', ['hotel' => $currentHotelId]) }}" wire:navigate class="flex items-center gap-2.5 px-3 py-2 rounded-lg {{ request()->is('admin/reports/summary*') ? 'bg-sky-600 text-white font-bold shadow-sm' : 'hover:bg-sky-200/60 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 font-semibold' }} text-[13px]"><span>📈</span> Rangkuman Laporan</a>

            <!-- Ditambahin request()->is('admin/staff/*/point-history') biar Leaderboard nyala -->
            <a href="{{ route('admin.staff.scores', ['hotel' => $currentHotelId]) }}" wire:navigate class="flex items-center gap-2.5 px-3 py-2 rounded-lg {{ request()->is('admin/staff-scores*') || request()->is('admin/staff/*/point-history') ? 'bg-sky-600 text-white font-bold shadow-sm' : 'hover:bg-sky-200/60 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 font-semibold' }} text-[13px]"><span>🏆</span> Leaderboard Skor Staf</a>

            <p class="px-3 text-[9px] font-black text-sky-800/70 dark:text-slate-500 uppercase tracking-wider mb-1.5 mt-4">Master Data</p>
            <a href="{{ route('admin.hotels.index') }}" wire:navigate class="flex items-center gap-2.5 px-3 py-2 rounded-lg {{ request()->is('admin/hotels*') ? 'bg-sky-600 text-white font-bold shadow-sm' : 'hover:bg-sky-200/60 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 font-semibold' }} text-[13px]"><span>🏨</span> Data Hotel</a>

            <!-- Ditambahin !request()->is('admin/staff/*/point-history') biar Staf gak nyala pas buka histori -->
            <a href="{{ route('admin.staff.index', ['hotel' => $currentHotelId]) }}" wire:navigate class="flex items-center gap-2.5 px-3 py-2 rounded-lg {{ request()->is('admin/staff*') && !request()->is('admin/staff-scores*') && !request()->is('admin/staff/*/point-history') ? 'bg-sky-600 text-white font-bold shadow-sm' : 'hover:bg-sky-200/60 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 font-semibold' }} text-[13px]"><span>👥</span> Manajemen Staf</a>

            <a href="{{ route('admin.shifts.index', ['hotel' => $currentHotelId]) }}" wire:navigate class="flex items-center gap-2.5 px-3 py-2 rounded-lg {{ request()->is('admin/shifts*') ? 'bg-sky-600 text-white font-bold shadow-sm' : 'hover:bg-sky-200/60 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 font-semibold' }} text-[13px]"><span>⏱️</span> Pengaturan Shift</a>
            <a href="{{ route('admin.tasks.index') }}" wire:navigate class="flex items-center gap-2.5 px-3 py-2 rounded-lg {{ request()->is('admin/tasks*') ? 'bg-sky-600 text-white font-bold shadow-sm' : 'hover:bg-sky-200/60 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 font-semibold' }} text-[13px]"><span>📋</span> Master Tugas (SOP)</a>

            <p class="px-3 text-[9px] font-black text-sky-800/70 dark:text-slate-500 uppercase tracking-wider mb-1.5 mt-4">Sistem & Log</p>
            <a href="{{ route('admin.storage.index', ['hotel' => $currentHotelId]) }}" wire:navigate class="flex items-center gap-2.5 px-3 py-2 rounded-lg {{ request()->is('admin/storage*') ? 'bg-sky-600 text-white font-bold shadow-sm' : 'hover:bg-sky-200/60 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 font-semibold' }} text-[13px]"><span>💾</span> Manajemen Storage</a>
            <a href="{{ route('admin.notes.index', ['hotel' => $currentHotelId]) }}" wire:navigate class="flex items-center justify-between px-3 py-2 rounded-lg {{ request()->is('admin/notes*') ? 'bg-sky-600 text-white font-bold shadow-sm' : 'hover:bg-sky-200/60 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 font-semibold' }} text-[13px]">
                <div class="flex items-center gap-2.5"><span>📋</span> Catatan & Laporan</div>
                @if($unreadNotes > 0)
                    <span class="bg-rose-500 text-white text-[9px] font-black px-1.5 py-0.5 rounded-full shadow-sm animate-pulse">{{ $unreadNotes }}</span>
                @endif
            </a>
            <a href="{{ route('admin.activity-logs.index', ['hotel' => $currentHotelId]) }}" wire:navigate class="flex items-center gap-2.5 px-3 py-2 rounded-lg {{ request()->is('admin/activity-logs*') ? 'bg-sky-600 text-white font-bold shadow-sm' : 'hover:bg-sky-200/60 dark:hover:bg-slate-800 text-slate-600 dark:text-slate-400 font-semibold' }} text-[13px]"><span>🛡️</span> Activity Log</a>
        </nav>
    </div>

    <div class="p-3 border-t border-sky-200/60 dark:border-slate-800 bg-sky-200/25 dark:bg-slate-900/50 flex-shrink-0">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 bg-rose-500/10 dark:bg-rose-500/20 hover:bg-rose-500/20 text-rose-600 dark:text-rose-400 font-bold py-2.5 px-3 rounded-lg border border-rose-500/20 text-[13px]">
                <span>🚪</span> Log Out System
            </button>
        </form>
    </div>
</aside>
