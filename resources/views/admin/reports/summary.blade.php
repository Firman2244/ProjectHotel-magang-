<x-app-layout>
    <style>
        [x-cloak] { display: none !important; }
    </style>

    <div class="min-h-screen bg-slate-50/50 dark:bg-slate-900 flex flex-col md:flex-row relative transition-colors duration-300" x-data="{ sidebarOpen: false, openReportModal: null, imageModalOpen: false, imageModalSrc: '' }">
        <div class="md:hidden sticky top-0 z-40 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between h-16 px-4 transition-colors duration-300">
            <h1 class="font-black text-xl text-slate-800 dark:text-white">Admin Panel</h1>
            <button type="button" @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <div x-cloak :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'" class="fixed inset-y-0 left-0 z-50 w-64 h-screen bg-white dark:bg-slate-800 shadow-2xl md:shadow-none transform transition-transform duration-300 ease-in-out flex-shrink-0 md:fixed border-r dark:border-slate-700">
            <x-admin-sidebar />
        </div>

        <div x-cloak x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-40 md:hidden"></div>

        <div class="flex-1 flex flex-col min-w-0 md:ml-64">
            <header class="bg-white/80 dark:bg-slate-900/50 backdrop-blur-md border-b border-slate-200 dark:border-slate-700 h-16 hidden md:flex items-center justify-between px-6 shadow-sm sticky top-0 z-20 transition-colors duration-300">
                <span class="font-extrabold text-xl text-slate-800 dark:text-white tracking-tight">Rangkuman Laporan</span>
                <button type="button" onclick="document.getElementById('exportModal').classList.remove('hidden')" class="bg-slate-900 dark:bg-slate-700 text-white font-bold text-xs px-5 py-2.5 rounded-lg hover:bg-slate-800 dark:hover:bg-slate-600 transition inline-flex items-center gap-2 shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Export Excel
                </button>
            </header>

            <div class="p-4 md:p-8 space-y-6">
                <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 transition-colors duration-300">
                    <form method="GET" action="{{ route('admin.reports.summary') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 items-end">
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Mulai Tanggal</label>
                            <input type="date" name="start_date" value="{{ request('start_date', $startDate) }}" class="w-full text-sm font-semibold border-slate-200 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-white focus:border-sky-500 focus:ring-sky-500 transition-colors duration-300">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                            <input type="date" name="end_date" value="{{ request('end_date', $endDate) }}" class="w-full text-sm font-semibold border-slate-200 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-white focus:border-sky-500 focus:ring-sky-500 transition-colors duration-300">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Cabang Hotel</label>
                            <select name="hotel_id" class="w-full text-sm font-semibold border-slate-200 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-white focus:border-sky-500 focus:ring-sky-500 transition-colors duration-300">
                                <option value="">Semua Cabang</option>
                                @foreach($hotels as $hotel)
                                    <option value="{{ $hotel->id }}" {{ request('hotel_id', $hotelId) == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Departemen</label>
                            <select name="department" class="w-full text-sm font-semibold border-slate-200 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-white focus:border-sky-500 focus:ring-sky-500 transition-colors duration-300">
                                <option value="">Semua Dept</option>
                                @foreach($availableDepartments as $dept)
                                    <option value="{{ $dept }}" {{ request('department', $department) == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Shift</label>
                            <select name="shift_id" class="w-full text-sm font-semibold border-slate-200 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-white focus:border-sky-500 focus:ring-sky-500 transition-colors duration-300">
                                <option value="">Semua Shift</option>
                                <option value="1" {{ request('shift_id') == '1' ? 'selected' : '' }}>Shift 1</option>
                                <option value="2" {{ request('shift_id') == '2' ? 'selected' : '' }}>Shift 2</option>
                                <option value="3" {{ request('shift_id') == '3' ? 'selected' : '' }}>Shift 3</option>
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 bg-sky-600 hover:bg-sky-700 text-white text-sm font-bold py-2.5 px-3 rounded-xl transition">Filter</button>
                            <a href="{{ route('admin.reports.summary') }}" class="px-3 py-2.5 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-600 font-bold text-sm border border-slate-200 dark:border-slate-600 flex items-center justify-center transition-colors">Reset</a>
                        </div>
                    </form>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="space-y-4 lg:col-span-1">
                        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 flex justify-between items-center border-l-4 border-l-sky-500 transition-colors duration-300">
                            <div>
                                <p class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Laporan Masuk</p>
                                <p class="text-3xl font-black text-slate-800 dark:text-white mt-1">{{ $totalKaryawanMasuk }} <span class="text-sm font-bold text-slate-400 dark:text-slate-500">Shift</span></p>
                            </div>
                            <div class="w-12 h-12 bg-sky-50 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 flex justify-between items-center border-l-4 border-l-emerald-500 transition-colors duration-300">
                            <div>
                                <p class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tepat Waktu</p>
                                <p class="text-3xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $laporanTepatWaktu }} <span class="text-sm font-bold text-emerald-400 dark:text-emerald-500">Shift</span></p>
                            </div>
                            <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 flex justify-between items-center border-l-4 border-l-rose-500 transition-colors duration-300">
                            <div>
                                <p class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">Terlambat</p>
                                <p class="text-3xl font-black text-rose-600 dark:text-rose-400 mt-1">{{ $laporanTerlambat }} <span class="text-sm font-bold text-rose-400 dark:text-rose-500">Shift</span></p>
                            </div>
                            <div class="w-12 h-12 bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 flex justify-between items-center border-l-4 border-l-amber-500 transition-colors duration-300">
                            <div>
                                <p class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Tugas Tambahan</p>
                                <p class="text-3xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ $totalTugasTambahan }} <span class="text-sm font-bold text-amber-400 dark:text-amber-500">Tugas</span></p>
                            </div>
                            <div class="w-12 h-12 bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 lg:col-span-2 flex flex-col transition-colors duration-300">
                        <h3 class="text-sm font-black text-slate-800 dark:text-white mb-4">Grafik Trend Kepatuhan Waktu Harian</h3>
                        <div class="relative flex-1 w-full min-h-[250px]">
                            <canvas id="complianceChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden transition-colors duration-300">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                        <h3 class="text-sm font-black text-slate-800 dark:text-white">Detail Rekapitulasi Data</h3>
                        <span class="text-xs text-slate-500 dark:text-slate-400 font-semibold">{{ $reports->total() }} Data Ditemukan</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-sm" id="reportTable">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-700/50 text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-600">
                                    <th class="px-5 py-3 font-bold uppercase tracking-wider text-[11px] cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-700 transition" onclick="sortTable(0)">Tgl Laporan ↕️</th>
                                    <th class="px-5 py-3 font-bold uppercase tracking-wider text-[11px] cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-700 transition" onclick="sortTable(1)">Nama Staf ↕️</th>
                                    <th class="px-5 py-3 font-bold uppercase tracking-wider text-[11px] cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-700 transition" onclick="sortTable(2)">Dept | Shift ↕️</th>
                                    <th class="px-5 py-3 font-bold uppercase tracking-wider text-[11px]">Jam Datang</th>
                                    <th class="px-5 py-3 font-bold uppercase tracking-wider text-[11px]">Jam Selesai</th>
                                    <th class="px-5 py-3 font-bold uppercase tracking-wider text-[11px] cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-700 transition" onclick="sortTable(5)">Status Waktu ↕️</th>
                                    <th class="px-5 py-3 font-bold uppercase tracking-wider text-[11px] text-center">Total Tugas</th>
                                    <th class="px-5 py-3 font-bold uppercase tracking-wider text-[11px] text-center cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-700 transition" onclick="sortTable(7)">Skor ↕️</th>
                                    <th class="px-5 py-3 font-bold uppercase tracking-wider text-[11px] text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @forelse($reports as $r)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition">
                                        <td class="px-5 py-3 font-semibold text-slate-700 dark:text-slate-300 whitespace-nowrap">{{ \Carbon\Carbon::parse($r->report_date)->format('d/m/Y') }}</td>
                                        <td class="px-5 py-3 font-bold text-slate-800 dark:text-slate-200 whitespace-nowrap">{{ $r->user->name }}</td>
                                        <td class="px-5 py-3 text-slate-600 dark:text-slate-400 whitespace-nowrap">{{ $r->user->department }} (S{{ $r->shift_id }})</td>
                                        <td class="px-5 py-3 text-slate-600 dark:text-slate-400 font-mono text-xs">{{ \Carbon\Carbon::parse($r->created_at)->format('H:i') }}</td>
                                        <td class="px-5 py-3 text-slate-600 dark:text-slate-400 font-mono text-xs">{{ $r->status == 'completed' ? \Carbon\Carbon::parse($r->updated_at)->format('H:i') : '-' }}</td>
                                        <td class="px-5 py-3">
                                            @if($r->is_late || $r->is_late_submit)
                                                <span class="text-rose-600 dark:text-rose-400 font-semibold bg-rose-50 dark:bg-rose-900/30 px-2.5 py-1 rounded-md text-xs">Terlambat</span>
                                            @else
                                                <span class="text-emerald-600 dark:text-emerald-400 font-semibold bg-emerald-50 dark:bg-emerald-900/30 px-2.5 py-1 rounded-md text-xs">Tepat Waktu</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3 font-semibold text-slate-700 dark:text-slate-300 text-center">{{ $r->items->count() }}</td>
                                        <td class="px-5 py-3 font-bold text-sky-600 dark:text-sky-400 text-center">{{ $r->total_score ?? 0 }}</td>
                                        <td class="px-5 py-3 text-center">
                                            <button type="button" @click="openReportModal = {{ $r->id }}" class="inline-block bg-white dark:bg-slate-700 text-sky-600 dark:text-sky-400 border border-sky-200 dark:border-slate-600 hover:bg-sky-50 dark:hover:bg-slate-600 font-semibold py-1.5 px-4 rounded-lg transition text-xs">Detail</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-5 py-8 text-center text-slate-500 dark:text-slate-400 font-medium">Tidak ada data pada rentang waktu/filter ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-700">
                        {{ $reports->links() }}
                    </div>
                </div>
            </div>
        </div>

        @foreach($reports as $r)
            <div x-cloak x-show="openReportModal === {{ $r->id }}" class="fixed inset-0 z-[80] flex items-center justify-center">
                <div x-show="openReportModal === {{ $r->id }}" x-transition.opacity class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm" @click="openReportModal = null"></div>

                <div x-show="openReportModal === {{ $r->id }}"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative z-[90] bg-white dark:bg-slate-800 rounded-3xl shadow-2xl w-full max-w-5xl mx-4 max-h-[90vh] flex flex-col overflow-hidden border border-slate-200 dark:border-slate-700">

                    <div class="p-6 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 flex justify-between items-start gap-4 flex-shrink-0">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 bg-sky-100 dark:bg-sky-900/30 rounded-xl flex items-center justify-center text-sky-700 dark:text-sky-400 font-bold text-xl overflow-hidden shadow-sm border border-sky-200 dark:border-sky-800">
                                @if(!empty($r->user->avatar))
                                    <img src="{{ asset('storage/' . $r->user->avatar) }}" class="w-full h-full object-cover" loading="lazy">
                                @else
                                    {{ strtoupper(substr($r->user->name, 0, 1)) }}
                                @endif
                            </div>
                            <div>
                                <p class="text-xs font-bold text-sky-600 dark:text-sky-400 uppercase tracking-wider">Karyawan</p>
                                <p class="font-black text-xl text-slate-800 dark:text-white mt-0.5">{{ $r->user->name }}</p>
                                <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 mt-1">{{ $r->user->department }} | Shift {{ $r->shift_id }}</p>
                            </div>
                        </div>
                        <div class="text-right flex flex-col items-end gap-2">
                            <button @click="openReportModal = null" class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400 hover:bg-rose-100 hover:text-rose-600 dark:hover:bg-rose-900/50 dark:hover:text-rose-400 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                            <span class="px-3 py-1 rounded-md text-[10px] font-black uppercase tracking-wider {{ $r->status == 'completed' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800' }}">
                                Status: {{ ucfirst($r->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="p-6 overflow-y-auto bg-slate-50/50 dark:bg-slate-900 flex-1">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="font-black text-slate-800 dark:text-white">Daftar Pekerjaan</h4>
                            <span class="inline-block bg-white dark:bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm text-xs font-bold text-slate-600 dark:text-slate-400">
                                📅 {{ \Carbon\Carbon::parse($r->created_at)->translatedFormat('d M Y') }} &nbsp;|&nbsp; ⏰ {{ \Carbon\Carbon::parse($r->created_at)->format('H:i') }} WIB
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($r->items as $item)
                                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200/60 dark:border-slate-700 p-4 flex flex-col justify-between hover:border-sky-300 dark:hover:border-sky-600 transition duration-300">
                                    <div>
                                        <div class="flex items-start justify-between mb-2">
                                            <h5 class="text-sm font-bold text-slate-800 dark:text-white leading-tight">
                                                {{ $item->task ? $item->task->name : ($item->task_name ?? 'Tugas Tambahan') }}
                                            </h5>
                                            @if($item->is_additional)
                                                <span class="px-2 py-0.5 bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-400 text-[10px] font-black rounded uppercase ml-2 flex-shrink-0">Extra</span>
                                            @endif
                                        </div>
                                        @if($item->notes)
                                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium bg-slate-50 dark:bg-slate-900/50 p-2 rounded-lg mb-3 border border-slate-100 dark:border-slate-700">{{ $item->notes }}</p>
                                        @endif
                                    </div>

                                    <div class="flex gap-2 mt-auto pt-3 border-t border-slate-100 dark:border-slate-700">
                                        <div class="flex-1">
                                            <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase mb-1">Sebelum</p>
                                            @if($item->before_image)
                                                <img src="{{ asset('storage/' . $item->before_image) }}" @click="imageModalSrc = '{{ asset('storage/' . $item->before_image) }}'; imageModalOpen = true" class="w-full h-20 object-cover rounded-lg cursor-zoom-in hover:opacity-80 transition shadow-sm border border-slate-100 dark:border-slate-700" loading="lazy">
                                            @else
                                                <div class="w-full h-20 bg-slate-100 dark:bg-slate-700/50 rounded-lg flex items-center justify-center text-xs text-slate-400 dark:text-slate-500 font-bold border border-slate-200 dark:border-slate-700">Kosong</div>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase mb-1">Sesudah</p>
                                            @if($item->after_image)
                                                <img src="{{ asset('storage/' . $item->after_image) }}" @click="imageModalSrc = '{{ asset('storage/' . $item->after_image) }}'; imageModalOpen = true" class="w-full h-20 object-cover rounded-lg cursor-zoom-in hover:opacity-80 transition shadow-sm border border-slate-100 dark:border-slate-700" loading="lazy">
                                            @else
                                                <div class="w-full h-20 bg-slate-100 dark:bg-slate-700/50 rounded-lg flex items-center justify-center text-xs text-slate-400 dark:text-slate-500 font-bold border border-slate-200 dark:border-slate-700">Kosong</div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        <div x-cloak x-show="imageModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center">
            <div x-show="imageModalOpen" x-transition.opacity class="absolute inset-0 bg-slate-900/95 backdrop-blur-sm" @click="imageModalOpen = false"></div>
            <div x-show="imageModalOpen"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-90"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-90"
                 class="relative z-[110] max-w-5xl w-full mx-4 flex flex-col items-center">
                <img @click="imageModalOpen = false" :src="imageModalSrc" class="max-h-[90vh] w-auto max-w-full rounded-2xl shadow-2xl border-4 border-white/10 object-contain cursor-zoom-out" title="Klik untuk menutup" loading="lazy">
            </div>
        </div>

        <div id="exportModal" class="hidden fixed inset-0 z-[60] overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="document.getElementById('exportModal').classList.add('hidden')"></div>

                <div class="relative bg-white dark:bg-slate-800 rounded-2xl p-6 w-full max-w-md shadow-xl border border-slate-200 dark:border-slate-700 transform transition-all">
                    <div class="flex items-center justify-between mb-5">
                        <h3 class="text-lg font-extrabold text-slate-800 dark:text-white tracking-tight">Export Data Excel</h3>
                        <button type="button" onclick="document.getElementById('exportModal').classList.add('hidden')" class="text-slate-400 dark:text-slate-500 hover:text-rose-500 dark:hover:text-rose-400 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form method="GET" action="{{ route('admin.reports.export') }}" class="space-y-4">
                        <input type="hidden" name="hotel" value="{{ $hotelSlug ?? '' }}">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Mulai Tanggal</label>
                                <input type="date" name="start_date" value="{{ request('start_date', $startDate) }}" class="w-full text-sm font-semibold border-slate-200 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-white focus:border-sky-500 focus:ring-sky-500" required>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                                <input type="date" name="end_date" value="{{ request('end_date', $endDate) }}" class="w-full text-sm font-semibold border-slate-200 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-white focus:border-sky-500 focus:ring-sky-500" required>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Cabang Hotel</label>
                            <select name="hotel_id" class="w-full text-sm font-semibold border-slate-200 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-white focus:border-sky-500 focus:ring-sky-500">
                                <option value="">Semua Cabang (Sesuai Filter)</option>
                                @foreach($hotels as $hotel)
                                    <option value="{{ $hotel->id }}" {{ request('hotel_id', $hotelId) == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Departemen</label>
                            <select name="department" class="w-full text-sm font-semibold border-slate-200 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-white focus:border-sky-500 focus:ring-sky-500">
                                <option value="">Semua Departemen</option>
                                @foreach($availableDepartments as $dept)
                                    <option value="{{ $dept }}" {{ request('department', $department) == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex gap-3 pt-4 border-t border-slate-100 dark:border-slate-700">
                            <button type="button" onclick="document.getElementById('exportModal').classList.add('hidden')" class="flex-1 px-4 py-2.5 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-600 font-bold text-sm border border-slate-200 dark:border-slate-600 transition">Batal</button>
                            <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold py-2.5 px-4 rounded-xl transition inline-flex items-center justify-center gap-2">
                                📥 Download
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('complianceChart').getContext('2d');

            const isDark = document.documentElement.classList.contains('dark');
            Chart.defaults.color = isDark ? '#94a3b8' : '#64748b';
            Chart.defaults.borderColor = isDark ? 'rgba(148, 163, 184, 0.1)' : 'rgba(148, 163, 184, 0.2)';

            const labels = @json($chartDates);
            const dataTepat = @json($chartTepat);
            const dataTelat = @json($chartTelat);

            const gradientTepat = ctx.createLinearGradient(0, 0, 0, 300);
            gradientTepat.addColorStop(0, 'rgba(5, 150, 105, 0.3)');
            gradientTepat.addColorStop(1, 'rgba(5, 150, 105, 0.0)');

            const gradientTelat = ctx.createLinearGradient(0, 0, 0, 300);
            gradientTelat.addColorStop(0, 'rgba(225, 29, 72, 0.3)');
            gradientTelat.addColorStop(1, 'rgba(225, 29, 72, 0.0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Tepat Waktu',
                            data: dataTepat,
                            borderColor: '#059669',
                            backgroundColor: gradientTepat,
                            fill: true,
                            tension: 0.4,
                            borderWidth: 2,
                            pointRadius: 4,
                            pointBackgroundColor: isDark ? '#1e293b' : '#ffffff',
                            pointBorderColor: '#059669',
                        },
                        {
                            label: 'Terlambat',
                            data: dataTelat,
                            borderColor: '#e11d48',
                            backgroundColor: gradientTelat,
                            fill: true,
                            tension: 0.4,
                            borderWidth: 2,
                            pointRadius: 4,
                            pointBackgroundColor: isDark ? '#1e293b' : '#ffffff',
                            pointBorderColor: '#e11d48',
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', align: 'end' }
                    },
                    scales: {
                        x: { grid: { display: false } },
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });
        });

        function sortTable(n) {
            let table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
            table = document.getElementById("reportTable");
            switching = true;
            dir = "asc";
            while (switching) {
                switching = false;
                rows = table.rows;
                for (i = 1; i < (rows.length - 1); i++) {
                    shouldSwitch = false;
                    x = rows[i].getElementsByTagName("TD")[n];
                    y = rows[i + 1].getElementsByTagName("TD")[n];
                    let xVal = x.innerHTML.toLowerCase().replace(/(<([^>]+)>)/gi, "");
                    let yVal = y.innerHTML.toLowerCase().replace(/(<([^>]+)>)/gi, "");
                    if (!isNaN(xVal) && !isNaN(yVal)) {
                        xVal = parseFloat(xVal);
                        yVal = parseFloat(yVal);
                    }
                    if (dir == "asc") {
                        if (xVal > yVal) { shouldSwitch = true; break; }
                    } else if (dir == "desc") {
                        if (xVal < yVal) { shouldSwitch = true; break; }
                    }
                }
                if (shouldSwitch) {
                    rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
                    switching = true;
                    switchcount ++;
                } else {
                    if (switchcount == 0 && dir == "asc") {
                        dir = "desc";
                        switching = true;
                    }
                }
            }
        }
    </script>
</x-app-layout>
