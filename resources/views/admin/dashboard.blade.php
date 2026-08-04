<x-app-layout>
    <style>
        [x-cloak] { display: none !important; }
    </style>

    <div class="min-h-screen bg-sky-950/5 dark:bg-slate-900 flex flex-col md:flex-row relative transition-colors duration-300" x-data="{ sidebarOpen: false, openReportModal: null, imageModalOpen: false, imageModalSrc: '' }">

        <div class="md:hidden sticky top-0 z-40 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between h-16 px-4">
            <h1 class="font-black text-xl text-slate-800 dark:text-white">Admin Panel</h1>
            <div class="flex items-center gap-2">
                <button id="theme-toggle-mobile" type="button" class="text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 focus:outline-none rounded-xl text-sm p-2.5 transition">
                    <svg id="theme-toggle-light-icon-mobile" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path>
                    </svg>
                    <svg id="theme-toggle-dark-icon-mobile" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                    </svg>
                </button>
                <button type="button" @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>

        <div x-cloak :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'" class="fixed inset-y-0 left-0 z-50 w-64 h-screen bg-white dark:bg-slate-800 shadow-2xl md:shadow-none transform transition-transform duration-300 ease-in-out flex-shrink-0 md:fixed dark:border-r dark:border-slate-700">
            <x-admin-sidebar />
        </div>

        <div x-cloak x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-40 md:hidden"></div>

        <div class="flex-1 flex flex-col min-w-0 md:ml-64 overflow-y-auto relative">
            <header class="bg-sky-50/30 dark:bg-slate-900/50 backdrop-blur-md border-b border-sky-100 dark:border-slate-700 h-16 hidden md:flex items-center justify-between px-6 shadow-sm transition-colors duration-300">
                <div class="flex items-center">
                    <span class="font-extrabold text-xl text-blue-700 dark:text-sky-400 tracking-tight">Laporan Harian</span>
                </div>
                <div class="flex items-center gap-3">
                    <button id="theme-toggle" type="button" class="text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 focus:outline-none rounded-xl text-sm p-2.5 transition">
                        <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" fill-rule="evenodd" clip-rule="evenodd"></path>
                        </svg>
                        <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                        </svg>
                    </button>
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Login as: <span class="text-sky-700 dark:text-sky-400 font-black">{{ Auth::user()->name }}</span></span>
                </div>
            </header>

            <div class="p-4 md:p-8 space-y-6">
                <div class="md:hidden flex items-center justify-between bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-sm border border-sky-100 dark:border-slate-700">
                    <span class="font-extrabold text-sm text-blue-700 dark:text-sky-400 tracking-tight">Laporan Harian</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-sky-50/40 dark:bg-slate-800/90 backdrop-blur-md p-6 rounded-2xl shadow-sm border border-sky-200/60 dark:border-slate-700 flex items-center gap-4">
                        <div class="w-12 h-12 bg-sky-100/70 dark:bg-sky-900/40 text-sky-700 dark:text-sky-400 rounded-xl flex items-center justify-center text-xl font-bold border border-sky-200 dark:border-sky-800">
                            👥
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Staf Terdaftar</p>
                            <p class="text-2xl font-black text-slate-800 dark:text-white">{{ $totalStaff ?? 0 }} Orang</p>
                        </div>
                    </div>

                    <div class="bg-sky-50/40 dark:bg-slate-800/90 backdrop-blur-md p-6 rounded-2xl shadow-sm border border-sky-200/60 dark:border-slate-700 flex items-center gap-4">
                        <div class="w-12 h-12 bg-emerald-100/70 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 rounded-xl flex items-center justify-center text-xl font-bold border border-emerald-200 dark:border-emerald-800">
                            ✅
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Sudah Submit Hari Ini</p>
                            <p class="text-2xl font-black text-emerald-800 dark:text-emerald-400">{{ $submittedCount ?? 0 }} Laporan</p>
                        </div>
                    </div>

                    <div class="bg-sky-50/40 dark:bg-slate-800/90 backdrop-blur-md p-6 rounded-2xl shadow-sm border border-sky-200/60 dark:border-slate-700 flex items-center gap-4">
                        <div class="w-12 h-12 bg-rose-100/70 dark:bg-rose-900/40 text-rose-700 dark:text-rose-400 rounded-xl flex items-center justify-center text-xl font-bold border border-rose-200 dark:border-rose-800">
                            ⚠️
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tercatat Terlambat</p>
                            <p class="text-2xl font-black text-rose-700 dark:text-rose-400">{{ $lateCount ?? 0 }} Laporan</p>
                        </div>
                    </div>
                </div>

                <div class="bg-sky-50/40 dark:bg-slate-800/90 backdrop-blur-md p-6 rounded-2xl shadow-sm border border-sky-200/60 dark:border-slate-700">
                    <form id="filterForm" method="GET" action="{{ route('admin.dashboard') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                        <input type="hidden" name="hotel" value="{{ $currentHotel ?? 'wahyu' }}">

                        <div>
                            <label class="block text-xs font-black text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-2">Tanggal Laporan</label>
                            <input type="date" id="filterDate" name="date" value="{{ request('date', date('Y-m-d')) }}" class="w-full border-sky-200 dark:border-slate-600 bg-white/80 dark:bg-slate-700 text-slate-800 dark:text-white font-semibold rounded-xl shadow-sm py-2.5 px-3 text-sm focus:border-sky-500 focus:ring-sky-500 cursor-pointer">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-2">Departemen</label>
                            <select id="filterDept" name="department" class="w-full border-sky-200 dark:border-slate-600 bg-white/80 dark:bg-slate-700 text-slate-800 dark:text-white font-semibold rounded-xl shadow-sm py-2.5 px-3 text-sm focus:border-sky-500 focus:ring-sky-500 cursor-pointer">
                                <option value="">Semua Departemen</option>
                                @foreach($departments ?? [] as $dept)
                                    <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-2">Status</label>
                            <select id="filterStatus" name="status" class="w-full border-sky-200 dark:border-slate-600 bg-white/80 dark:bg-slate-700 text-slate-800 dark:text-white font-semibold rounded-xl shadow-sm py-2.5 px-3 text-sm focus:border-sky-500 focus:ring-sky-500 cursor-pointer">
                                <option value="">Semua Status</option>
                                <option value="planned" {{ request('status') == 'planned' ? 'selected' : '' }}>Planned (Belum Submit)</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed (Selesai)</option>
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.dashboard', ['hotel' => $currentHotel ?? 'wahyu']) }}" class="w-full bg-white/80 dark:bg-slate-700 hover:bg-white dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-bold py-2.5 px-4 rounded-xl transition text-sm flex items-center justify-center border border-sky-200 dark:border-slate-600 shadow-sm" title="Reset Filter">
                                Reset Filter
                            </a>
                        </div>
                    </form>
                </div>

                <div class="bg-sky-50/40 dark:bg-slate-800/90 backdrop-blur-md rounded-2xl shadow-sm border border-sky-200/60 dark:border-slate-700 overflow-hidden">
                    <div class="p-6 border-b border-sky-200/60 dark:border-slate-700 flex justify-between items-center bg-sky-100/30 dark:bg-slate-800">
                        <h3 class="text-lg font-black text-slate-800 dark:text-white">
                            Dokumentasi Laporan Masuk — <span class="text-sky-700 dark:text-sky-400 uppercase">{{ $currentHotel ?? 'wahyu' }}</span>
                        </h3>
                        <span class="text-xs font-bold text-sky-700 dark:text-sky-400 uppercase tracking-wider">Total Ditampilkan: {{ count($reports ?? []) }}</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-sky-100/40 dark:bg-slate-700/50 border-b border-sky-200/60 dark:border-slate-600">
                                    <th class="px-6 py-4 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama Karyawan</th>
                                    <th class="px-6 py-4 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">Departemen / Shift</th>
                                    <th class="px-6 py-4 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">Waktu Submit</th>
                                    <th class="px-6 py-4 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-sky-200/50 dark:divide-slate-700">
                                @forelse($reports ?? [] as $report)
                                    <tr class="hover:bg-sky-100/30 dark:hover:bg-slate-700/50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-xl overflow-hidden bg-sky-700 dark:bg-sky-900 flex items-center justify-center text-white font-bold flex-shrink-0 text-sm shadow-sm border dark:border-slate-600">
                                                    @if(!empty($report->user->avatar))
                                                        <img src="{{ asset('storage/' . $report->user->avatar) }}" class="w-full h-full object-cover">
                                                    @else
                                                        {{ strtoupper(substr($report->user->name, 0, 1)) }}
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $report->user->name }}</div>
                                                    <div class="text-xs text-slate-500 dark:text-slate-400 font-medium">ID: #{{ $report->user->id }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ $report->user->department }}</div>
                                            <div class="text-xs text-sky-700 dark:text-sky-400 font-semibold">Shift ID: {{ $report->shift_id }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ \Carbon\Carbon::parse($report->created_at)->timezone('Asia/Jakarta')->format('Y-m-d H:i') }} WIB</div>
                                            @if($report->is_late)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-400 mt-1 uppercase border border-orange-200 dark:border-orange-800">
                                                    Late Apply
                                                </span>
                                            @endif
                                            @if($report->is_late_submit)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black bg-rose-100 dark:bg-rose-900/30 text-rose-800 dark:text-rose-400 mt-1 uppercase border border-rose-200 dark:border-rose-800">
                                                    Late Submit
                                                </span>
                                            @endif
                                            @if(!$report->is_late && !$report->is_late_submit)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-400 mt-1 uppercase border border-emerald-200 dark:border-emerald-800">
                                                    Tepat Waktu
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full {{ $report->status == 'completed' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-400 border border-amber-200 dark:border-amber-800' }}">
                                                {{ ucfirst($report->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end gap-2">
                                                <button type="button" @click="openReportModal = {{ $report->id }}" class="inline-flex items-center px-4 py-2 bg-white/80 dark:bg-slate-700 text-sky-700 dark:text-sky-300 hover:bg-sky-50 dark:hover:bg-slate-600 font-bold rounded-xl transition border border-sky-200 dark:border-slate-600 shadow-sm">
                                                    Periksa Laporan
                                                </button>
                                                <button type="button" onclick="openDeleteModal('{{ route('reports.destroy', $report->id) }}')" class="inline-flex items-center px-4 py-2 bg-rose-50 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/50 font-bold rounded-xl transition border border-rose-200 dark:border-rose-800 shadow-sm">
                                                    Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400 font-medium">
                                            Tidak ada laporan masuk untuk hotel ini pada tanggal atau filter yang dipilih.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 border-t border-sky-200/60 dark:border-slate-700">
                        {{ $reports->links() }}
                    </div>
                </div>
            </div>
        </div>

        @foreach($reports ?? [] as $r)
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
                                    <img src="{{ asset('storage/' . $r->user->avatar) }}" class="w-full h-full object-cover">
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
                                                <img src="{{ asset('storage/' . $item->before_image) }}" @click="imageModalSrc = '{{ asset('storage/' . $item->before_image) }}'; imageModalOpen = true" class="w-full h-20 object-cover rounded-lg cursor-zoom-in hover:opacity-80 transition shadow-sm border border-slate-100 dark:border-slate-700">
                                            @else
                                                <div class="w-full h-20 bg-slate-100 dark:bg-slate-700/50 rounded-lg flex items-center justify-center text-xs text-slate-400 dark:text-slate-500 font-bold border border-slate-200 dark:border-slate-700">Kosong</div>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase mb-1">Sesudah</p>
                                            @if($item->after_image)
                                                <img src="{{ asset('storage/' . $item->after_image) }}" @click="imageModalSrc = '{{ asset('storage/' . $item->after_image) }}'; imageModalOpen = true" class="w-full h-20 object-cover rounded-lg cursor-zoom-in hover:opacity-80 transition shadow-sm border border-slate-100 dark:border-slate-700">
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
                <img @click="imageModalOpen = false" :src="imageModalSrc" class="max-h-[90vh] w-auto max-w-full rounded-2xl shadow-2xl border-4 border-white/10 object-contain cursor-zoom-out">
            </div>
        </div>

        <div id="deleteModal" class="fixed inset-0 bg-slate-900/60 dark:bg-slate-900/80 backdrop-blur-sm z-50 hidden items-center justify-center transition-opacity">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-sm p-6 transform transition-transform scale-100 border dark:border-slate-700">
                <div class="text-center">
                    <div class="w-16 h-16 rounded-full bg-rose-100 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 flex items-center justify-center text-3xl mx-auto mb-4 border-4 border-white dark:border-slate-800 shadow-sm">
                        ⚠️
                    </div>
                    <h3 class="text-lg font-black text-slate-800 dark:text-white mb-2">Hapus Laporan?</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mb-6">Tindakan ini tidak bisa dibatalkan. Semua foto di dalam laporan ini juga akan terhapus dari server.</p>
                    <div class="flex gap-3">
                        <button type="button" onclick="closeDeleteModal()" class="flex-1 px-4 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-bold rounded-xl transition">
                            Batal
                        </button>
                        <form id="deleteForm" method="POST" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl transition shadow-sm">
                                Ya, Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openDeleteModal(url) {
            document.getElementById('deleteForm').action = url;
            const modal = document.getElementById('deleteModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeDeleteModal() {
            const modal = document.getElementById('deleteModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        document.addEventListener('DOMContentLoaded', function() {
            const hotelSelector = document.getElementById('hotel-selector');
            if (hotelSelector) {
                hotelSelector.addEventListener('change', function() {
                    const selectedHotel = this.value;
                    window.location.href = "{{ route('admin.dashboard') }}?hotel=" + selectedHotel;
                });
            }

            const filterForm = document.getElementById('filterForm');
            const filterDate = document.getElementById('filterDate');
            const filterDept = document.getElementById('filterDept');
            const filterStatus = document.getElementById('filterStatus');

            const autoSubmitForm = () => {
                if (filterForm) {
                    filterForm.submit();
                }
            };

            if (filterDate) filterDate.addEventListener('change', autoSubmitForm);
            if (filterDept) filterDept.addEventListener('change', autoSubmitForm);
            if (filterStatus) filterStatus.addEventListener('change', autoSubmitForm);

            var themeToggleDarkIcon = document.getElementById('theme-toggle-dark-icon-mobile');
            var themeToggleLightIcon = document.getElementById('theme-toggle-light-icon-mobile');
            var themeToggleBtn = document.getElementById('theme-toggle-mobile');

            if (themeToggleDarkIcon && themeToggleLightIcon && themeToggleBtn) {
                if (localStorage.getItem('color-theme') === 'dark' || (!('color-theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    themeToggleLightIcon.classList.remove('hidden');
                } else {
                    themeToggleDarkIcon.classList.remove('hidden');
                }

                themeToggleBtn.addEventListener('click', function() {
                    themeToggleDarkIcon.classList.toggle('hidden');
                    themeToggleLightIcon.classList.toggle('hidden');

                    if (localStorage.getItem('color-theme')) {
                        if (localStorage.getItem('color-theme') === 'light') {
                            document.documentElement.classList.add('dark');
                            localStorage.setItem('color-theme', 'dark');
                        } else {
                            document.documentElement.classList.remove('dark');
                            localStorage.setItem('color-theme', 'light');
                        }
                    } else {
                        if (document.documentElement.classList.contains('dark')) {
                            document.documentElement.classList.remove('dark');
                            localStorage.setItem('color-theme', 'light');
                        } else {
                            document.documentElement.classList.add('dark');
                            localStorage.setItem('color-theme', 'dark');
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>
