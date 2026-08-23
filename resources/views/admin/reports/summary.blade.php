<x-app-layout>
    <style>
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

    <div x-data="{ openReportModal: null, imageModalOpen: false, imageModalSrc: '', focusMode: false, direction: 'next', tooltip: { show: false, x: 0, y: 0, base: 0, bonus: 0, penaltyLate: 0, penaltySubmit: 0, total: 0 } }">

        <header class="bg-white/80 dark:bg-slate-900/50 backdrop-blur-md border-b border-slate-200 dark:border-slate-700 h-16 hidden md:flex items-center justify-between px-6 shadow-sm sticky top-0 z-20">
            <span class="font-extrabold text-xl text-slate-800 dark:text-white tracking-tight">Rangkuman Laporan</span>
            <button type="button" onclick="document.getElementById('exportModal').classList.remove('hidden')" class="bg-slate-900 dark:bg-slate-700 text-white font-bold text-xs px-5 py-2.5 rounded-lg hover:bg-slate-800 dark:hover:bg-slate-600 transition inline-flex items-center gap-2 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                Export Excel
            </button>
        </header>

        <div class="p-4 md:p-8 space-y-6">
            <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                <form method="GET" action="{{ route('admin.reports.summary') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-3 items-end"
                      x-data='{
                          dept: "{{ request('department', $department) }}",
                          staffId: "{{ request('staff_id', $staffId) }}",
                          allStaff: @json($staffList),
                          get filteredStaff() {
                              if (!this.dept) return this.allStaff;
                              return this.allStaff.filter(s => s.department === this.dept);
                          }
                      }'>
                    <input type="hidden" name="hotel" value="{{ $currentHotel ? $currentHotel->id : '' }}">

                    <div>
                        <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Mulai Tanggal</label>
                        <input type="date" name="start_date" value="{{ request('start_date', $startDate) }}" class="w-full text-sm font-semibold border-slate-200 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-white focus:border-sky-500 focus:ring-sky-500">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                        <input type="date" name="end_date" value="{{ request('end_date', $endDate) }}" class="w-full text-sm font-semibold border-slate-200 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-white focus:border-sky-500 focus:ring-sky-500">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Departemen</label>
                        <select name="department" x-model="dept" @change="staffId = ''" class="w-full text-sm font-semibold border-slate-200 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-white focus:border-sky-500 focus:ring-sky-500">
                            <option value="">Semua Dept</option>
                            @foreach($availableDepartments as $d)
                                <option value="{{ $d }}">{{ $d }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Pilih Staf</label>
                        <select name="staff_id" x-model="staffId" class="w-full text-sm font-semibold border-slate-200 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-white focus:border-sky-500 focus:ring-sky-500">
                            <option value="">Semua Staf</option>
                            <template x-for="staff in filteredStaff" :key="staff.id">
                                <option :value="staff.id" x-text="staff.name"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Shift</label>
                        <select name="shift_id" class="w-full text-sm font-semibold border-slate-200 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-white focus:border-sky-500 focus:ring-sky-500">
                            <option value="">Semua Shift</option>
                            <option value="1" {{ request('shift_id', $shiftId) == '1' ? 'selected' : '' }}>Shift 1</option>
                            <option value="2" {{ request('shift_id', $shiftId) == '2' ? 'selected' : '' }}>Shift 2</option>
                            <option value="3" {{ request('shift_id', $shiftId) == '3' ? 'selected' : '' }}>Shift 3</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-sky-600 hover:bg-sky-700 text-white text-sm font-bold py-2.5 px-3 rounded-xl">Filter</button>
                        <a href="{{ route('admin.reports.summary', ['hotel' => request('hotel')]) }}" class="px-3 py-2.5 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-600 font-bold text-sm border border-slate-200 dark:border-slate-600 flex items-center justify-center">Reset</a>
                    </div>
                </form>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="space-y-4 lg:col-span-1">
                    <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 flex justify-between items-center border-l-4 border-l-sky-500">
                        <div>
                            <p class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Laporan Masuk</p>
                            <p class="text-3xl font-black text-slate-800 dark:text-white mt-1">{{ $totalKaryawanMasuk }} <span class="text-sm font-bold text-slate-400 dark:text-slate-500">Shift</span></p>
                        </div>
                        <div class="w-12 h-12 bg-sky-50 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 flex justify-between items-center border-l-4 border-l-emerald-500">
                        <div>
                            <p class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tepat Waktu</p>
                            <p class="text-3xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $laporanTepatWaktu }} <span class="text-sm font-bold text-emerald-400 dark:text-emerald-500">Shift</span></p>
                        </div>
                        <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 flex justify-between items-center border-l-4 border-l-rose-500">
                        <div>
                            <p class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">Terlambat</p>
                            <p class="text-3xl font-black text-rose-600 dark:text-rose-400 mt-1">{{ $laporanTerlambat }} <span class="text-sm font-bold text-rose-400 dark:text-rose-500">Shift</span></p>
                        </div>
                        <div class="w-12 h-12 bg-rose-50 dark:bg-rose-900/30 text-rose-600 dark:text-rose-400 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 flex justify-between items-center border-l-4 border-l-amber-500">
                        <div>
                            <p class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">Total Tugas Tambahan</p>
                            <p class="text-3xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ $totalTugasTambahan }} <span class="text-sm font-bold text-amber-400 dark:text-amber-500">Tugas</span></p>
                        </div>
                        <div class="w-12 h-12 bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 lg:col-span-2 flex flex-col">
                    <h3 class="text-sm font-black text-slate-800 dark:text-white mb-4">Grafik Trend Kepatuhan Waktu Harian</h3>
                    <div class="relative flex-1 w-full min-h-[250px]" wire:ignore>
                        <canvas id="complianceChart"></canvas>
                    </div>
                </div>
            </div>

            <div :class="focusMode ? 'fixed inset-0 z-[70] bg-slate-50 dark:bg-slate-900 p-4 lg:p-8 overflow-y-auto' : ''" x-cloak>
                <div :class="focusMode ? 'min-h-full' : ''" class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <h3 class="text-base font-black text-slate-800 dark:text-white">Detail Rekapitulasi Data</h3>
                            <span class="inline-flex items-center justify-center px-3 py-1 rounded-full bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-400 text-[11px] font-black tracking-wider border border-sky-200 dark:border-sky-800 shadow-sm">
                                {{ $reports->total() }} DATA DITEMUKAN
                            </span>
                        </div>
                        <button @click="focusMode = !focusMode" class="text-slate-400 hover:text-sky-600 dark:hover:text-sky-400 transition" title="Toggle Focus Mode">
                            <svg x-show="!focusMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                            <svg x-show="focusMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 14h4v4m0-4l-5 5m11-1h4v-4m0 4l-5-5M4 10h4V6m0 4l-5-5m11 5l-5-5m5 5h-4V6"></path></svg>
                        </button>
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
                                    @php $sc = $calcShiftScore($r); $scoreVal = $r->total_score ?? 0; @endphp
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition">
                                        <td class="px-5 py-3 font-semibold text-slate-700 dark:text-slate-300 whitespace-nowrap">{{ \Carbon\Carbon::parse($r->report_date)->timezone('Asia/Jakarta')->format('d/m/Y') }}</td>
                                        <td class="px-5 py-3 font-bold text-slate-800 dark:text-slate-200 whitespace-nowrap">{{ $r->user->name }}</td>
                                        <td class="px-5 py-3 text-slate-600 dark:text-slate-400 whitespace-nowrap">{{ $r->user->department }} (S{{ $r->shift_id }})</td>
                                        <td class="px-5 py-3 text-slate-600 dark:text-slate-400 font-mono text-xs">{{ \Carbon\Carbon::parse($r->created_at)->timezone('Asia/Jakarta')->format('H:i') }}</td>
                                        <td class="px-5 py-3 text-slate-600 dark:text-slate-400 font-mono text-xs">{{ $r->status == 'completed' ? \Carbon\Carbon::parse($r->updated_at)->timezone('Asia/Jakarta')->format('H:i') : '-' }}</td>
                                        <td class="px-5 py-3">
                                            <div class="flex gap-1 flex-wrap">
                                                @if(!$r->is_late && !$r->is_late_submit)
                                                    <span class="text-emerald-600 dark:text-emerald-400 font-semibold bg-emerald-50 dark:bg-emerald-900/30 px-2.5 py-1 rounded-md text-[10px] whitespace-nowrap border border-emerald-200 dark:border-emerald-800">Tepat Waktu</span>
                                                @endif
                                                @if($r->is_late)
                                                    <span class="text-amber-600 dark:text-amber-400 font-semibold bg-amber-50 dark:bg-amber-900/30 px-2.5 py-1 rounded-md text-[10px] whitespace-nowrap border border-amber-200 dark:border-amber-800">Telat Datang</span>
                                                @endif
                                                @if($r->is_late_submit)
                                                    <span class="text-rose-600 dark:text-rose-400 font-semibold bg-rose-50 dark:bg-rose-900/30 px-2.5 py-1 rounded-md text-[10px] whitespace-nowrap border border-rose-200 dark:border-rose-800">Telat Pulang</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-5 py-3 font-semibold text-slate-700 dark:text-slate-300 text-center">{{ $r->items->count() }}</td>

                                        <!-- KOLOM SKOR YANG AKAN DI-UPDATE JS -->
                                        <td class="px-5 py-3 font-bold text-center relative">
                                            <div class="inline-block cursor-help"
                                                 id="table-score-wrapper-{{ $r->id }}"
                                                 data-base="{{ round($sc['base']) }}"
                                                 data-bonus="{{ $sc['bonus'] }}"
                                                 data-total="{{ $scoreVal }}"
                                                 @mouseenter="tooltip.show = true; tooltip.x = $event.clientX; tooltip.y = $event.clientY;
                                                              tooltip.base = $el.dataset.base; tooltip.bonus = $el.dataset.bonus;
                                                              tooltip.penaltyLate = {{ $sc['penaltyLate'] }}; tooltip.penaltySubmit = {{ $sc['penaltySubmit'] }};
                                                              tooltip.total = $el.dataset.total"
                                                 @mousemove="tooltip.x = $event.clientX; tooltip.y = $event.clientY"
                                                 @mouseleave="tooltip.show = false">
                                                <span id="table-score-text-{{ $r->id }}" class="{{ $scoreVal > 100 ? 'text-amber-500 dark:text-amber-400 flex items-center justify-center gap-1' : ($scoreVal == 100 ? 'text-emerald-600 dark:text-emerald-400' : ($scoreVal >= 80 ? 'text-sky-600 dark:text-sky-400' : 'text-rose-600 dark:text-rose-400')) }}">
                                                    {{ $scoreVal }}
                                                    @if($scoreVal > 100)
                                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                                    @endif
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-5 py-3 text-center">
                                            <button type="button" @click="direction = 'next'; openReportModal = {{ $r->id }}" class="inline-block bg-white dark:bg-slate-700 text-sky-600 dark:text-sky-400 border border-sky-200 dark:border-slate-600 hover:bg-sky-50 dark:hover:bg-slate-600 font-semibold py-1.5 px-4 rounded-lg transition text-xs shadow-sm">Detail</button>
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

        @php
            $reportModels = isset($reports) && method_exists($reports, 'getCollection') ? $reports->getCollection() : collect($reports ?? []);
            $reportIds = $reportModels->pluck('id')->toArray();
        @endphp

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
                    <button @click.stop="direction = 'prev'; openReportModal = {{ $prevId }}" class="absolute left-2 sm:left-6 top-1/2 -translate-y-1/2 z-[100] w-10 h-10 sm:w-14 sm:h-14 flex items-center justify-center rounded-full bg-slate-800/40 hover:bg-slate-800/80 text-white transition backdrop-blur-sm border border-white/10 shadow-2xl focus:outline-none">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                @endif

                @if($nextId)
                    <button @click.stop="direction = 'next'; openReportModal = {{ $nextId }}" class="absolute right-2 sm:right-6 top-1/2 -translate-y-1/2 z-[100] w-10 h-10 sm:w-14 sm:h-14 flex items-center justify-center rounded-full bg-slate-800/40 hover:bg-slate-800/80 text-white transition backdrop-blur-sm border border-white/10 shadow-2xl focus:outline-none">
                        <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                    </button>
                @endif

                <div x-show="openReportModal === {{ $r->id }}"
                     :data-dir="direction"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 dir-start"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-x-0"
                     x-transition:leave-end="opacity-0 dir-end"
                     class="modal-box transform relative z-[90] bg-white dark:bg-slate-800 rounded-3xl shadow-2xl w-full max-w-5xl mx-auto my-4 sm:mx-24 max-h-[90vh] flex flex-col overflow-hidden border border-slate-200 dark:border-slate-700">

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
                                <button type="button" onclick="verifyAllTasks({{ $r->id }})" class="bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 font-bold py-1.5 px-3 rounded-lg text-xs border border-emerald-200 dark:border-emerald-800 shadow-sm transition">✅ ACC Semua</button>
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
                        <input type="hidden" name="hotel" value="{{ $currentHotel ? $currentHotel->id : '' }}">
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
                            <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold py-2.5 px-4 rounded-xl transition inline-flex items-center justify-center gap-2">📥 Download</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <template x-teleport="body">
            <div x-show="tooltip.show" x-cloak
                 class="fixed z-[200] w-48 bg-slate-800 dark:bg-slate-700 text-white text-[10px] rounded-lg shadow-xl p-3 pointer-events-none text-left"
                 :style="`top:${tooltip.y - 10}px; left:${tooltip.x}px; transform: translate(-50%, -100%);`">
                <div class="flex justify-between mb-1"><span class="text-slate-400">Skor Dasar:</span> <span class="font-bold" x-text="tooltip.base"></span></div>
                <div class="flex justify-between mb-1"><span class="text-slate-400">Bonus Tugas:</span> <span class="font-bold text-emerald-400" x-text="'+' + tooltip.bonus"></span></div>
                <template x-if="tooltip.penaltyLate < 0">
                    <div class="flex justify-between mb-1"><span class="text-slate-400">Pinalti Datang:</span> <span class="font-bold text-rose-400" x-text="tooltip.penaltyLate"></span></div>
                </template>
                <template x-if="tooltip.penaltySubmit < 0">
                    <div class="flex justify-between mb-1"><span class="text-slate-400">Pinalti Laporan:</span> <span class="font-bold text-rose-400" x-text="tooltip.penaltySubmit"></span></div>
                </template>
                <div class="border-t border-slate-600 mt-2 pt-1 flex justify-between"><span class="font-bold">Total Final:</span> <span class="font-bold" x-text="tooltip.total"></span></div>
            </div>
        </template>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js" data-navigate-once></script>
    <script>
        // VARIABLE GLOBAL CHART.JS
        window.complianceChartInstance = null;

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
                    const reportScoreVal = data.new_score !== undefined ? data.new_score : data.newTotalScore;
                    const reportBaseVal = data.base_score !== undefined ? data.base_score : Math.round(data.newBaseScore);
                    const reportBonusVal = data.bonus_score !== undefined ? data.bonus_score : 0;
                    const reportPenaltyVal = data.penalty !== undefined ? data.penalty : 0;

                    // Update UI di Modal
                    const scoreDisplay = document.getElementById('total-score-display-' + reportId);
                    const baseDisplay = document.getElementById('base-score-display-' + reportId);
                    const bonusDisplay = document.getElementById('bonus-score-display-' + reportId);
                    const penaltyDisplay = document.getElementById('penalty-score-display-' + reportId);

                    if (scoreDisplay) scoreDisplay.innerText = reportScoreVal;
                    if (baseDisplay) baseDisplay.innerText = reportBaseVal;
                    if (bonusDisplay) bonusDisplay.innerText = '+' + reportBonusVal;
                    if (penaltyDisplay) penaltyDisplay.innerText = reportPenaltyVal == 0 ? '0' : '-' + reportPenaltyVal;

                    const badge = document.getElementById('status-badge-' + itemId);
                    if (badge) {
                        badge.className = 'px-2 py-0.5 text-[10px] font-black rounded uppercase border ' +
                            (status === 'verified' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-800' :
                            (status === 'rejected' ? 'bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-800' : 'bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-400 border-sky-200 dark:border-sky-800'));
                        badge.innerText = status === 'verified' ? 'DI-ACC' : (status === 'rejected' ? 'DITOLAK' : 'SELESAI');
                    }

                    // UPDATE UI DI TABEL UTAMA SINKRONISASI
                    const tableWrapper = document.getElementById('table-score-wrapper-' + reportId);
                    const tableText = document.getElementById('table-score-text-' + reportId);

                    if (tableWrapper && tableText) {
                        tableWrapper.dataset.base = reportBaseVal;
                        tableWrapper.dataset.bonus = reportBonusVal;
                        tableWrapper.dataset.total = reportScoreVal;

                        if (reportScoreVal > 100) {
                            tableText.className = 'text-amber-500 dark:text-amber-400 flex items-center justify-center gap-1';
                            tableText.innerHTML = reportScoreVal + ' <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>';
                        } else if (reportScoreVal == 100) {
                            tableText.className = 'text-emerald-600 dark:text-emerald-400';
                            tableText.innerHTML = reportScoreVal;
                        } else if (reportScoreVal >= 80) {
                            tableText.className = 'text-sky-600 dark:text-sky-400';
                            tableText.innerHTML = reportScoreVal;
                        } else {
                            tableText.className = 'text-rose-600 dark:text-rose-400';
                            tableText.innerHTML = reportScoreVal;
                        }
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

        function sortTable(n) {
            let table = document.getElementById("reportTable"), rows, switching = true, i, x, y, shouldSwitch, dir = "asc", switchcount = 0;
            if(!table) return;
            while (switching) {
                switching = false; rows = table.rows;
                for (i = 1; i < (rows.length - 1); i++) {
                    shouldSwitch = false;
                    x = rows[i].getElementsByTagName("TD")[n]; y = rows[i + 1].getElementsByTagName("TD")[n];
                    let xVal = x.innerHTML.toLowerCase().replace(/(<([^>]+)>)/gi, ""), yVal = y.innerHTML.toLowerCase().replace(/(<([^>]+)>)/gi, "");
                    if (!isNaN(xVal) && !isNaN(yVal)) { xVal = parseFloat(xVal); yVal = parseFloat(yVal); }
                    if (dir == "asc") { if (xVal > yVal) { shouldSwitch = true; break; } } else if (dir == "desc") { if (xVal < yVal) { shouldSwitch = true; break; } }
                }
                if (shouldSwitch) {
                    rows[i].parentNode.insertBefore(rows[i + 1], rows[i]); switching = true; switchcount++;
                } else {
                    if (switchcount == 0 && dir == "asc") { dir = "desc"; switching = true; }
                }
            }
        }

        function initSummaryPage() {
            if (typeof Chart === 'undefined') {
                setTimeout(initSummaryPage, 50);
                return;
            }

            const ctxElement = document.getElementById('complianceChart');
            if (!ctxElement) return;

            if (window.complianceChartInstance) {
                window.complianceChartInstance.destroy();
            }

            const context = ctxElement.getContext('2d');
            const isDark = document.documentElement.classList.contains('dark');
            Chart.defaults.color = isDark ? '#94a3b8' : '#64748b';
            Chart.defaults.borderColor = isDark ? 'rgba(148, 163, 184, 0.1)' : 'rgba(148, 163, 184, 0.2)';

            const labels = @json($chartDates ?? []);
            const dataTepat = @json($chartTepat ?? []);
            const dataTelat = @json($chartTelat ?? []);

            const gradientTepat = context.createLinearGradient(0, 0, 0, 300);
            gradientTepat.addColorStop(0, 'rgba(5, 150, 105, 0.3)');
            gradientTepat.addColorStop(1, 'rgba(5, 150, 105, 0.0)');

            const gradientTelat = context.createLinearGradient(0, 0, 0, 300);
            gradientTelat.addColorStop(0, 'rgba(225, 29, 72, 0.3)');
            gradientTelat.addColorStop(1, 'rgba(225, 29, 72, 0.0)');

            window.complianceChartInstance = new Chart(context, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        { label: 'Tepat Waktu', data: dataTepat, borderColor: '#059669', backgroundColor: gradientTepat, fill: true, tension: 0.4, borderWidth: 2, pointRadius: 4, pointBackgroundColor: isDark ? '#1e293b' : '#ffffff', pointBorderColor: '#059669'},
                        { label: 'Terlambat', data: dataTelat, borderColor: '#e11d48', backgroundColor: gradientTelat, fill: true, tension: 0.4, borderWidth: 2, pointRadius: 4, pointBackgroundColor: isDark ? '#1e293b' : '#ffffff', pointBorderColor: '#e11d48'}
                    ]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { position: 'top', align: 'end' } },
                    scales: { x: { grid: { display: false } }, y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                }
            });
        }

        document.addEventListener('livewire:navigated', initSummaryPage);
        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            initSummaryPage();
        }
    </script>
</x-app-layout>
