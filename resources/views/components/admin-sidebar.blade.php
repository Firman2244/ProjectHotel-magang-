<aside class="w-64 bg-sky-100/85 backdrop-blur-md text-slate-700 hidden md:flex flex-col justify-between flex-shrink-0 shadow-sm border-r border-sky-200/60">
    <div>
        <div class="p-6 border-b border-sky-200/60 flex items-center gap-3">
            <div class="w-10 h-10 bg-sky-600 text-white rounded-xl flex items-center justify-center font-black text-xl shadow-sm">
                H
            </div>
            <div>
                <h1 class="font-black text-slate-800 text-base tracking-wide">Hotel Management</h1>
                <p class="text-[10px] text-sky-700 font-bold uppercase tracking-widest">System Admin</p>
            </div>
        </div>

        <div class="px-4 py-3 bg-sky-200/40 border-b border-sky-200/60">
            <label class="block text-[10px] font-black text-sky-800 uppercase tracking-wider mb-1.5">Pilih Hotel Aktif</label>
            <select id="hotel-selector" class="w-full bg-white/90 border border-sky-300 text-slate-800 font-bold rounded-xl text-xs py-2 px-3 focus:border-sky-500 focus:ring-sky-500 cursor-pointer shadow-sm">
                <option value="wahyu" {{ ($currentHotel ?? request('hotel', 'wahyu')) == 'wahyu' ? 'selected' : '' }}>Hotel Wahyu</option>
                <option value="nirwana" {{ ($currentHotel ?? request('hotel', 'wahyu')) == 'nirwana' ? 'selected' : '' }}>Hotel Nirwana</option>
            </select>
        </div>

        <div class="px-4 py-6 space-y-1.5">
            <p class="px-3 text-[10px] font-black text-sky-800/70 uppercase tracking-wider mb-2">Main Menu</p>

            <a href="{{ route('admin.dashboard', ['hotel' => request('hotel', 'wahyu')]) }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.dashboard') ? 'bg-sky-600 text-white font-bold shadow-sm' : 'hover:bg-sky-200/60 text-slate-600 hover:text-slate-900 font-semibold' }} text-sm transition">
                <span>📊</span> Dashboard
            </a>

            <a href="{{ route('admin.hotels.index', ['hotel' => request('hotel', 'wahyu')]) }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.hotels.*') ? 'bg-sky-600 text-white font-bold shadow-sm' : 'hover:bg-sky-200/60 text-slate-600 hover:text-slate-900 font-semibold' }} text-sm transition">
                <span>🏨</span> Data Hotel
            </a>

            <a href="{{ route('admin.staff.index', ['hotel' => request('hotel', 'wahyu')]) }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.staff.*') ? 'bg-sky-600 text-white font-bold shadow-sm' : 'hover:bg-sky-200/60 text-slate-600 hover:text-slate-900 font-semibold' }} text-sm transition">
                <span>👥</span> Manajemen Staf
            </a>

            <a href="{{ route('admin.shifts.index', ['hotel' => request('hotel', 'wahyu')]) }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.shifts.*') ? 'bg-sky-600 text-white font-bold shadow-sm' : 'hover:bg-sky-200/60 text-slate-600 hover:text-slate-900 font-semibold' }} text-sm transition">
                <span>⏱️</span> Pengaturan Shift
            </a>

            <a href="{{ route('admin.tasks.index', ['hotel' => request('hotel', 'wahyu')]) }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.tasks.*') ? 'bg-sky-600 text-white font-bold shadow-sm' : 'hover:bg-sky-200/60 text-slate-600 hover:text-slate-900 font-semibold' }} text-sm transition">
                <span>📋</span> Master Tugas (SOP)
            </a>

            <a href="{{ route('admin.reports.summary', ['hotel' => request('hotel', 'wahyu')]) }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('admin.reports.summary') ? 'bg-sky-600 text-white font-bold shadow-sm' : 'hover:bg-sky-200/60 text-slate-600 hover:text-slate-900 font-semibold' }} text-sm transition">
                <span>📈</span> Rangkuman Laporan
            </a>
        </div>
    </div>

    <div class="p-4 border-t border-sky-200/60 bg-sky-200/20">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 font-bold py-3 px-4 rounded-xl transition border border-rose-500/20 text-xs">
                <span>🚪</span> Log Out System
            </button>
        </form>
    </div>
</aside>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const hotelSelector = document.getElementById('hotel-selector');
        if (hotelSelector) {
            hotelSelector.addEventListener('change', function() {
                const selectedHotel = this.value;
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('hotel', selectedHotel);
                window.location.href = currentUrl.toString();
            });
        }
    });
</script>
