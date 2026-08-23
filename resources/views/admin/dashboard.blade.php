<x-app-layout>
    <style>
        .tooltip-container:hover .tooltip-content { display: block; }
        .modal-box.dir-start[data-dir="next"] { --tw-translate-x: 4rem; }
        .modal-box.dir-start[data-dir="prev"] { --tw-translate-x: -4rem; }
        .modal-box.dir-end[data-dir="next"] { --tw-translate-x: -4rem; }
        .modal-box.dir-end[data-dir="prev"] { --tw-translate-x: 4rem; }
    </style>

    @php
        $calcShiftScore = function ($r) {
            $totStd = $r->items->where('is_additional', 0)->count();
            $totPend = $r->items->where('is_additional', 0)->where('status', 'pending')->count();
            $totComp = $r->items->where('is_additional', 0)->whereIn('status', ['completed', 'verified'])->count();
            $valDenom = $totStd - $totPend;

            $baseS = $valDenom > 0
                ? ($totComp / $valDenom) * 100
                : ($totStd > 0 && $valDenom === 0 ? 100 : 0);

            $bonusS = $r->items->where('is_additional', 1)->whereIn('status', ['completed', 'verified'])->count() * 10;
            $penaltyLate = $r->is_late ? -15 : 0;
            $penaltySubmit = $r->is_late_submit ? -15 : 0;

            return [
                'base' => $baseS,
                'bonus' => $bonusS,
                'penaltyLate' => $penaltyLate,
                'penaltySubmit' => $penaltySubmit,
                'penaltyTotal' => $penaltyLate + $penaltySubmit,
            ];
        };
    @endphp

    <div x-data="{ openReportModal: null, direction: 'next', imageModalOpen: false, imageModalSrc: '' }">
        <header class="bg-sky-50/30 dark:bg-slate-900/50 backdrop-blur-md border-b border-sky-100 dark:border-slate-700 h-16 hidden md:flex items-center justify-between px-6 shadow-sm">
            <div class="flex items-center">
                <span class="font-extrabold text-xl text-slate-800 dark:text-white tracking-tight">Laporan Harian</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Login as: <span class="text-sky-700 dark:text-sky-400 font-black">{{ Auth::user()->name }}</span></span>
            </div>
        </header>

        <div class="p-4 md:p-8 space-y-6">
            <div class="md:hidden flex items-center justify-between bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-sm border border-sky-100 dark:border-slate-700">
                <span class="font-extrabold text-sm text-slate-800 dark:text-white tracking-tight">Laporan Harian</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-sky-50/40 dark:bg-slate-800/90 backdrop-blur-md p-6 rounded-2xl shadow-sm border border-sky-200/60 dark:border-slate-700 flex items-center gap-4">
                    <div class="w-12 h-12 bg-sky-100/70 dark:bg-sky-900/40 text-sky-700 dark:text-sky-400 rounded-xl flex items-center justify-center text-xl font-bold border border-sky-200 dark:border-sky-800">👥</div>
                    <div>
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Staf Terdaftar</p>
                        <p class="text-2xl font-black text-slate-800 dark:text-white">{{ $totalStaff ?? 0 }} Orang</p>
                    </div>
                </div>

                <div class="bg-sky-50/40 dark:bg-slate-800/90 backdrop-blur-md p-6 rounded-2xl shadow-sm border border-sky-200/60 dark:border-slate-700 flex items-center gap-4">
                    <div class="w-12 h-12 bg-emerald-100/70 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 rounded-xl flex items-center justify-center text-xl font-bold border border-emerald-200 dark:border-emerald-800">✅</div>
                    <div>
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Sudah Submit Hari Ini</p>
                        <p class="text-2xl font-black text-emerald-800 dark:text-emerald-400">{{ $submittedCount ?? 0 }} Laporan</p>
                    </div>
                </div>

                <div class="bg-sky-50/40 dark:bg-slate-800/90 backdrop-blur-md p-6 rounded-2xl shadow-sm border border-sky-200/60 dark:border-slate-700 flex items-center gap-4">
                    <div class="w-12 h-12 bg-rose-100/70 dark:bg-rose-900/40 text-rose-700 dark:text-rose-400 rounded-xl flex items-center justify-center text-xl font-bold border border-rose-200 dark:border-rose-800">⚠️</div>
                    <div>
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tercatat Terlambat</p>
                        <p class="text-2xl font-black text-rose-700 dark:text-rose-400">{{ $lateCount ?? 0 }} Laporan</p>
                    </div>
                </div>
            </div>

            <div class="bg-sky-50/40 dark:bg-slate-800/90 backdrop-blur-md p-6 rounded-2xl shadow-sm border border-sky-200/60 dark:border-slate-700">
                <form id="filterForm" method="GET" action="{{ route('admin.dashboard') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                    <input type="hidden" name="hotel" value="{{ request('hotel') }}">
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
                        <a href="{{ route('admin.dashboard', ['hotel' => request('hotel')]) }}" class="w-full bg-white/80 dark:bg-slate-700 hover:bg-white dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-bold py-2.5 px-4 rounded-xl text-sm flex items-center justify-center border border-sky-200 dark:border-slate-600 shadow-sm">Reset Filter</a>
                    </div>
                </form>
            </div>

            <div class="bg-sky-50/40 dark:bg-slate-800/90 backdrop-blur-md rounded-2xl shadow-sm border border-sky-200/60 dark:border-slate-700 overflow-hidden">
                <div class="p-6 border-b border-sky-200/60 dark:border-slate-700 flex justify-between items-center bg-sky-100/30 dark:bg-slate-800">
                    <div class="flex items-center gap-3">
                        <h3 class="text-lg font-black text-slate-800 dark:text-white">Dokumentasi Laporan Masuk</h3>
                        @php
                            $activeHotelObj = \App\Models\Hotel::find(request('hotel'));
                            $hotelNameDisplay = $activeHotelObj ? $activeHotelObj->name : 'SEMUA CABANG';
                        @endphp
                        <span class="px-2.5 py-0.5 rounded-lg bg-sky-100 dark:bg-sky-900/40 text-sky-700 dark:text-sky-400 text-xs font-black uppercase tracking-wider border border-sky-200 dark:border-sky-800 shadow-sm">
                            {{ strtoupper($hotelNameDisplay) }}
                        </span>
                    </div>
                    <span class="text-xs font-bold text-sky-700 dark:text-sky-400 uppercase tracking-wider hidden sm:block">Total Ditampilkan: {{ count($reports ?? []) }}</span>
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
                                <tr class="hover:bg-sky-100/30 dark:hover:bg-slate-700/50">
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
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black bg-orange-100 dark:bg-orange-900/30 text-orange-800 dark:text-orange-400 mt-1 uppercase border border-orange-200 dark:border-orange-800">Late Apply</span>
                                        @endif
                                        @if($report->is_late_submit)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black bg-rose-100 dark:bg-rose-900/30 text-rose-800 dark:text-rose-400 mt-1 uppercase border border-rose-200 dark:border-rose-800">Late Submit</span>
                                        @endif
                                        @if(!$report->is_late && !$report->is_late_submit)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-400 mt-1 uppercase border border-emerald-200 dark:border-emerald-800">Tepat Waktu</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full {{ $report->status == 'completed' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-800 dark:text-amber-400 border border-amber-200 dark:border-amber-800' }}">
                                            {{ ucfirst($report->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end gap-2">
                                            <button type="button" @click="direction = 'next'; openReportModal = {{ $report->id }}" class="inline-flex items-center px-4 py-2 bg-white/80 dark:bg-slate-700 text-sky-700 dark:text-sky-300 hover:bg-sky-50 dark:hover:bg-slate-600 font-bold rounded-xl border border-sky-200 dark:border-slate-600 shadow-sm">Periksa Laporan</button>
                                            <button type="button" onclick="openDeleteModal('{{ route('reports.destroy', $report->id) }}')" class="inline-flex items-center px-4 py-2 bg-rose-50 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/50 font-bold rounded-xl border border-rose-200 dark:border-rose-800 shadow-sm">Hapus</button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400 font-medium">Tidak ada laporan masuk untuk hotel ini pada tanggal atau filter yang dipilih.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-sky-200/60 dark:border-slate-700">
                    {{ isset($reports) && method_exists($reports, 'links') ? $reports->links() : '' }}
                </div>
            </div>
        </div>

        @php
            $reportModels = isset($reports) && method_exists($reports, 'getCollection') ? $reports->getCollection() : collect($reports ?? []);
            $reportIds = $reportModels->pluck('id')->toArray();
        @endphp

        <!-- MODAL LOOP: Gak pake <template x-if> biar perubahannya permanen nempel di DOM -->
        @foreach($reportModels as $r)
            @php
                $sc = $calcShiftScore($r);
                $currentIndex = $loop->index;
                $prevId = $currentIndex > 0 ? $reportIds[$currentIndex - 1] : null;
                $nextId = $currentIndex < count($reportIds) - 1 ? $reportIds[$currentIndex + 1] : null;
            @endphp
            <div x-cloak x-show="openReportModal === {{ $r->id }}" class="fixed inset-0 z-[80] flex items-center justify-center">

                <div x-show="openReportModal === {{ $r->id }}" class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm" @click="openReportModal = null"></div>

                @if($prevId)
                    <button @click.stop="direction = 'prev'; openReportModal = {{ $prevId }}" class="absolute left-2 sm:left-6 top-1/2 -translate-y-1/2 z-[100] w-10 h-10 sm:w-14 sm:h-14 flex items-center justify-center rounded-full bg-slate-800/40 hover:bg-slate-800/80 text-white backdrop-blur-sm border border-white/10 shadow-2xl focus:outline-none">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                @endif
                @if($nextId)
                    <button @click.stop="direction = 'next'; openReportModal = {{ $nextId }}" class="absolute right-2 sm:right-6 top-1/2 -translate-y-1/2 z-[100] w-10 h-10 sm:w-14 sm:h-14 flex items-center justify-center rounded-full bg-slate-800/40 hover:bg-slate-800/80 text-white backdrop-blur-sm border border-white/10 shadow-2xl focus:outline-none">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                @endif

                <div x-show="openReportModal === {{ $r->id }}" :data-dir="direction" class="modal-box transform relative z-[90] bg-white dark:bg-slate-800 rounded-3xl shadow-2xl w-full max-w-5xl mx-auto my-4 sm:mx-24 max-h-[90vh] flex flex-col overflow-hidden border border-slate-200 dark:border-slate-700">

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
                                <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 mt-1">{{ $r->user->department }} | Shift {{ $r->shift_id }} | Laporan {{ $currentIndex + 1 }} dari {{ count($reportIds) }}</p>
                            </div>
                        </div>
                        <div class="text-right flex flex-col items-end gap-2">
                            <button @click="openReportModal = null" class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400 hover:bg-rose-100 hover:text-rose-600 dark:hover:bg-rose-900/50 dark:hover:text-rose-400 transition" title="Tutup">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                            <span class="px-3 py-1 rounded-md text-[10px] font-black uppercase tracking-wider {{ $r->status == 'completed' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800' }}">
                                Status: {{ ucfirst($r->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="p-6 overflow-y-auto bg-slate-50/50 dark:bg-slate-900 flex-1">
                        <div class="mb-6 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4 shadow-sm">
                            <h4 class="text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3">Ringkasan Performa Shift</h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="bg-slate-50 dark:bg-slate-900/50 p-3 rounded-lg border border-slate-100 dark:border-slate-700">
                                    <span class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 mb-1">SKOR DASAR (SOP)</span>
                                    <span class="text-lg font-black text-slate-800 dark:text-white" id="base-score-display-{{ $r->id }}">{{ round($sc['base']) }}</span>
                                </div>
                                <div class="bg-emerald-50 dark:bg-emerald-900/20 p-3 rounded-lg border border-emerald-100 dark:border-emerald-800/30">
                                    <span class="block text-[10px] font-bold text-emerald-600 dark:text-emerald-400 mb-1">BONUS (EXTRA TASKS)</span>
                                    <span class="text-lg font-black text-emerald-700 dark:text-emerald-300" id="bonus-score-display-{{ $r->id }}">+{{ $sc['bonus'] }}</span>
                                </div>
                                <div class="bg-rose-50 dark:bg-rose-900/20 p-3 rounded-lg border border-rose-100 dark:border-rose-800/30">
                                    <span class="block text-[10px] font-bold text-rose-600 dark:text-rose-400 mb-1">PINALTI KETERLAMBATAN</span>
                                    <span class="text-lg font-black text-rose-700 dark:text-rose-300" id="penalty-score-display-{{ $r->id }}">{{ $sc['penaltyTotal'] == 0 ? '0' : $sc['penaltyTotal'] }}</span>
                                </div>
                                <div class="bg-sky-50 dark:bg-sky-900/20 p-3 rounded-lg border border-sky-100 dark:border-sky-800/30">
                                    <span class="block text-[10px] font-bold text-sky-600 dark:text-sky-400 mb-1">TOTAL SKOR AKHIR</span>
                                    <span class="text-lg font-black text-sky-700 dark:text-sky-300" id="total-score-display-{{ $r->id }}">{{ $r->total_score ?? 0 }}</span>
                                </div>
                            </div>
                            <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700 flex gap-2 flex-wrap">
                                @if(!$r->is_late && !$r->is_late_submit)
                                    <span class="text-emerald-600 dark:text-emerald-400 font-semibold bg-emerald-50 dark:bg-emerald-900/30 px-3 py-1.5 rounded-md text-xs border border-emerald-200 dark:border-emerald-800">✅ Datang & Pulang Tepat Waktu</span>
                                @endif
                                @if($r->is_late)
                                    <span class="text-amber-600 dark:text-amber-400 font-semibold bg-amber-50 dark:bg-amber-900/30 px-3 py-1.5 rounded-md text-xs border border-amber-200 dark:border-amber-800">⚠️ Terlambat Datang / Submit Todo</span>
                                @endif
                                @if($r->is_late_submit)
                                    <span class="text-rose-600 dark:text-rose-400 font-semibold bg-rose-50 dark:bg-rose-900/30 px-3 py-1.5 rounded-md text-xs border border-rose-200 dark:border-rose-800">🚨 Terlambat Laporan Pulang</span>
                                @endif
                            </div>
                        </div>

                        <div class="flex justify-between items-center mb-4">
                            <h4 class="font-black text-slate-800 dark:text-white">Daftar Pekerjaan</h4>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="verifyAllTasks({{ $r->id }})" class="bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 font-bold py-1.5 px-3 rounded-lg text-xs transition border border-emerald-200 dark:border-emerald-800 shadow-sm">✅ ACC Semua</button>
                                <span class="inline-block bg-white dark:bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm text-xs font-bold text-slate-600 dark:text-slate-400">
                                    📅 {{ \Carbon\Carbon::parse($r->created_at)->timezone('Asia/Jakarta')->translatedFormat('d M Y') }} &nbsp;|&nbsp; ⏰ {{ \Carbon\Carbon::parse($r->created_at)->timezone('Asia/Jakarta')->format('H:i') }} WIB
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($r->items as $item)
                                <div id="item-block-{{ $item->id }}" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200/60 dark:border-slate-700 p-4 flex flex-col justify-between hover:border-sky-300 dark:hover:border-sky-600 transition duration-300 relative">

                                    <div class="absolute top-2 right-2 flex gap-1">
                                        <button type="button" onclick="updateTaskStatus({{ $item->id }}, {{ $r->id }}, 'verified')" class="bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 font-bold py-1 px-3 rounded-lg text-[10px] transition border border-emerald-200 dark:border-emerald-800 shadow-sm">✅ ACC</button>
                                        <button type="button" onclick="updateTaskStatus({{ $item->id }}, {{ $r->id }}, 'rejected')" class="bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/60 font-bold py-1 px-3 rounded-lg text-[10px] transition border border-rose-200 dark:border-rose-800 shadow-sm">❌ Tolak</button>
                                    </div>

                                    <div class="mt-8">
                                        <div class="flex items-start justify-between mb-2 gap-2">
                                            <h5 class="text-sm font-bold text-slate-800 dark:text-white leading-tight pr-6">
                                                {{ $item->task ? $item->task->name : ($item->task_name ?? 'Tugas Tambahan') }}
                                            </h5>
                                            <div class="flex items-center gap-1 flex-shrink-0">
                                                @if($item->status == 'verified')
                                                    <span id="status-badge-{{ $item->id }}" class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-[10px] font-black rounded uppercase border border-emerald-200 dark:border-emerald-800">DI-ACC</span>
                                                @elseif($item->status == 'rejected')
                                                    <span id="status-badge-{{ $item->id }}" class="px-2 py-0.5 bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400 text-[10px] font-black rounded uppercase border border-rose-200 dark:border-rose-800">DITOLAK</span>
                                                @elseif($item->status == 'completed')
                                                    <span id="status-badge-{{ $item->id }}" class="px-2 py-0.5 bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-400 text-[10px] font-black rounded uppercase border border-sky-200 dark:border-sky-800">SELESAI</span>
                                                @else
                                                    <span id="status-badge-{{ $item->id }}" class="px-2 py-0.5 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 text-[10px] font-black rounded uppercase border border-amber-200 dark:border-amber-800">PENDING</span>
                                                @endif

                                                @if($item->is_additional)
                                                    <span class="px-2 py-0.5 bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-400 text-[10px] font-black rounded uppercase">Extra</span>
                                                @endif
                                            </div>
                                        </div>
                                        @if($item->notes)
                                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium bg-slate-50 dark:bg-slate-900/50 p-2 rounded-lg mb-2 mt-2 border border-slate-100 dark:border-slate-700">{{ $item->notes }}</p>
                                        @endif
                                        @if($item->obstacle_note)
                                            <p class="text-xs text-rose-600 dark:text-rose-400 font-medium bg-rose-50 dark:bg-rose-900/30 p-2 rounded-lg mb-3 mt-2 border border-rose-100 dark:border-rose-800/30">Kendala: {{ $item->obstacle_note }}</p>
                                        @endif
                                    </div>

                                    <div class="flex gap-2 mt-auto pt-3 border-t border-slate-100 dark:border-slate-700">
                                        <div class="flex-1">
                                            <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase mb-1">Sebelum</p>
                                            @if($item->before_image)
                                                <img src="{{ asset('storage/' . $item->before_image) }}" @click="imageModalSrc = '{{ asset('storage/' . $item->before_image) }}'; imageModalOpen = true" class="w-full h-24 object-cover rounded-lg cursor-zoom-in hover:opacity-80 transition shadow-sm border border-slate-100 dark:border-slate-700" loading="lazy">
                                            @else
                                                <div class="w-full h-24 bg-slate-100 dark:bg-slate-700/50 rounded-lg flex items-center justify-center text-xs text-slate-400 dark:text-slate-500 font-bold border border-slate-200 dark:border-slate-700">Kosong</div>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase mb-1">Sesudah</p>
                                            @if($item->after_image)
                                                <img src="{{ asset('storage/' . $item->after_image) }}" @click="imageModalSrc = '{{ asset('storage/' . $item->after_image) }}'; imageModalOpen = true" class="w-full h-24 object-cover rounded-lg cursor-zoom-in hover:opacity-80 transition shadow-sm border border-slate-100 dark:border-slate-700" loading="lazy">
                                            @else
                                                <div class="w-full h-24 bg-slate-100 dark:bg-slate-700/50 rounded-lg flex items-center justify-center text-xs text-slate-400 dark:text-slate-500 font-bold border border-slate-200 dark:border-slate-700">Kosong</div>
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

        <div x-cloak x-show="imageModalOpen" class="fixed inset-0 z-[120] flex items-center justify-center p-4">
            <div x-show="imageModalOpen" class="absolute inset-0 bg-slate-900/90 backdrop-blur-sm" @click="imageModalOpen = false"></div>
            <div x-show="imageModalOpen" class="relative z-[130] max-w-5xl w-full flex flex-col items-center">
                <img @click="imageModalOpen = false" :src="imageModalSrc" class="max-h-[85vh] w-auto max-w-full rounded-2xl shadow-2xl border border-white/10 object-contain bg-black/50 cursor-zoom-out" title="Klik untuk menutup">
            </div>
        </div>

        <div id="deleteModal" class="fixed inset-0 bg-slate-900/60 dark:bg-slate-900/80 backdrop-blur-sm z-50 hidden items-center justify-center">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-sm p-6 transform border dark:border-slate-700">
                <div class="text-center">
                    <div class="w-16 h-16 rounded-full bg-rose-100 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 flex items-center justify-center text-3xl mx-auto mb-4 border-4 border-white dark:border-slate-800 shadow-sm">⚠️</div>
                    <h3 class="text-lg font-black text-slate-800 dark:text-white mb-2">Hapus Laporan?</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mb-6">Tindakan ini tidak bisa dibatalkan.</p>
                    <div class="flex gap-3">
                        <button type="button" onclick="closeDeleteModal()" class="flex-1 px-4 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-bold rounded-xl">Batal</button>
                        <form id="deleteForm" method="POST" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-4 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl shadow-sm">Ya, Hapus</button>
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

        const filterForm = document.getElementById('filterForm');
        const filterDate = document.getElementById('filterDate');
        const filterDept = document.getElementById('filterDept');
        const filterStatus = document.getElementById('filterStatus');

        const autoSubmitForm = () => { if (filterForm) filterForm.submit(); };

        if (filterDate) filterDate.addEventListener('change', autoSubmitForm);
        if (filterDept) filterDept.addEventListener('change', autoSubmitForm);
        if (filterStatus) filterStatus.addEventListener('change', autoSubmitForm);

        // INI FUNGSI JS YANG DIAMBIL 100% PERSIS DARI SUMMARY LU.
        // GAK GW TAMBAH ATAU KURANGIN NAMA VARIABELNYA BIAR GA MATI LAGI.
        window.updateTaskStatus = function(itemId, reportId, status) {
            fetch(`/admin/report-items/${itemId}/status`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ status: status })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    const scoreDisplay = document.getElementById('total-score-display-' + reportId);
                    const baseDisplay = document.getElementById('base-score-display-' + reportId);
                    const bonusDisplay = document.getElementById('bonus-score-display-' + reportId);
                    const penaltyDisplay = document.getElementById('penalty-score-display-' + reportId);

                    if (scoreDisplay) scoreDisplay.innerText = data.new_score;
                    if (baseDisplay) baseDisplay.innerText = data.base_score;
                    if (bonusDisplay) bonusDisplay.innerText = '+' + data.bonus_score;
                    if (penaltyDisplay) penaltyDisplay.innerText = data.penalty == 0 ? '0' : '-' + data.penalty;

                    const badge = document.getElementById('status-badge-' + itemId);
                    if (badge) {
                        badge.className = 'px-2 py-0.5 text-[10px] font-black rounded uppercase border ' +
                            (status === 'verified' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800' :
                            (status === 'rejected' ? 'bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-800' : 'bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-400 border-sky-200 dark:border-sky-800'));
                        badge.innerText = status === 'verified' ? 'DI-ACC' : (status === 'rejected' ? 'DITOLAK' : 'SELESAI');
                    }
                } else {
                    alert('Gagal update status: ' + data.message);
                }
            })
            .catch(error => {
                alert('Terjadi kesalahan jaringan.');
            });
        };

        window.verifyAllTasks = function(reportId) {
            if(!confirm('Anda yakin ingin memverifikasi (ACC) semua pekerjaan yang masih pending di laporan ini?')) return;

            fetch(`/admin/reports/${reportId}/verify-all`, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                }
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    window.location.reload();
                } else {
                    alert('Gagal memverifikasi: ' + data.message);
                }
            })
            .catch(err => alert('Kesalahan jaringan.'));
        };
    </script>
</x-app-layout>
