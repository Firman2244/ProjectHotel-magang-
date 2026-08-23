<x-app-layout>
    <style>
        .staff-selected {
            border-color: #0ea5e9 !important;
            background-color: #f0f9ff !important;
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.2) !important;
            transform: scale(1.02);
            z-index: 10;
        }
        .dark .staff-selected {
            background-color: #0c4a6e !important;
            border-color: #38bdf8 !important;
            box-shadow: 0 0 0 4px rgba(56, 189, 248, 0.2) !important;
        }
        #list-unassigned .ds-checkbox {
            display: none !important;
        }

        #list-shift-1 .staff-card { border-color: rgba(14, 165, 233, 0.4); box-shadow: 0 4px 15px -3px rgba(14, 165, 233, 0.2); }
        .dark #list-shift-1 .staff-card { border-color: rgba(14, 165, 233, 0.6); box-shadow: 0 4px 15px -3px rgba(14, 165, 233, 0.3); }
        #list-shift-1 .staff-card .dept-label { color: #0ea5e9; }
        .dark #list-shift-1 .staff-card .dept-label { color: #38bdf8; }

        #list-shift-2 .staff-card { border-color: rgba(245, 158, 11, 0.4); box-shadow: 0 4px 15px -3px rgba(245, 158, 11, 0.2); }
        .dark #list-shift-2 .staff-card { border-color: rgba(245, 158, 11, 0.6); box-shadow: 0 4px 15px -3px rgba(245, 158, 11, 0.3); }
        #list-shift-2 .staff-card .dept-label { color: #f59e0b; }
        .dark #list-shift-2 .staff-card .dept-label { color: #fbbf24; }

        #list-shift-3 .staff-card { border-color: rgba(99, 102, 241, 0.4); box-shadow: 0 4px 15px -3px rgba(99, 102, 241, 0.2); }
        .dark #list-shift-3 .staff-card { border-color: rgba(99, 102, 241, 0.6); box-shadow: 0 4px 15px -3px rgba(99, 102, 241, 0.3); }
        #list-shift-3 .staff-card .dept-label { color: #6366f1; }
        .dark #list-shift-3 .staff-card .dept-label { color: #818cf8; }

        #list-unassigned .staff-card { border-color: rgba(203, 213, 225, 0.8); }
        .dark #list-unassigned .staff-card { border-color: rgba(51, 65, 85, 0.8); }
        #list-unassigned .staff-card .dept-label { color: #64748b; }
        .dark #list-unassigned .staff-card .dept-label { color: #94a3b8; }
    </style>

    <div x-data="{ configModalOpen: false }">
        <header class="bg-white/80 dark:bg-slate-900/50 backdrop-blur-md border-b border-slate-200 dark:border-slate-700 h-16 hidden md:flex items-center justify-between px-6 shadow-sm sticky top-0 z-20">
            <div class="flex items-center">
                <span class="font-extrabold text-xl text-slate-800 dark:text-white tracking-tight">Pengaturan Shift Harian</span>
            </div>
            <div class="flex items-center gap-4">
                <button @click="configModalOpen = true" class="bg-slate-800 hover:bg-slate-900 dark:bg-sky-600 dark:hover:bg-sky-500 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-md flex items-center gap-2">
                    ⚙️ Atur Jam Operasional
                </button>
            </div>
        </header>

        <div id="bulk-action-panel" class="hidden fixed bottom-6 left-1/2 md:left-[calc(50%+8rem)] transform -translate-x-1/2 z-50 bg-slate-900/90 backdrop-blur-md text-white px-6 py-4 rounded-2xl shadow-2xl border border-slate-700 items-center gap-4 w-[90%] md:w-auto justify-between">
            <div>
                <p class="text-xs text-slate-400 font-bold uppercase tracking-wider mb-0.5">Bulk Action</p>
                <p class="font-black text-sm md:text-base"><span id="selected-count" class="text-sky-400">0</span> Staf Terpilih</p>
            </div>
            <button type="button" onclick="submitDoubleShift()" class="bg-sky-600 hover:bg-sky-500 text-white font-bold py-2 px-5 rounded-xl text-xs md:text-sm shadow-md flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path></svg>
                Cetak Tiket Lembur
            </button>
        </div>

        <div class="p-4 md:p-8 space-y-6 pb-24">
            @if(session('success'))
                <div class="bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 p-4 rounded-xl font-bold mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="md:hidden flex items-center justify-between bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                <span class="font-extrabold text-sm text-sky-700 dark:text-sky-400 tracking-tight">Pengaturan Shift</span>
                <button @click="configModalOpen = true" class="bg-slate-800 dark:bg-sky-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold shadow-md">⚙️ Atur Jam</button>
            </div>

            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h2 class="text-xl font-black text-slate-800 dark:text-white">Assign & Lembur</h2>
                    <div class="flex items-center gap-3 mt-1 flex-wrap">
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">Geser kartu untuk pindah shift utama, atau centang kotak untuk cetak tiket Lembur.</p>
                    </div>
                </div>
                <form method="GET" action="" class="w-full md:w-64">
                    <input type="hidden" name="hotel" value="{{ request('hotel') }}">
                    <select name="department" onchange="this.form.submit()" class="w-full border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-800 dark:text-white font-semibold rounded-xl shadow-sm py-2.5 px-3 text-sm focus:border-sky-500 focus:ring-sky-500 cursor-pointer outline-none">
                        <option value="">Semua Departemen</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                        @endforeach
                    </select>
                </form>
            </div>

            <div class="flex flex-col lg:flex-row gap-6 items-start" id="shift-board">
                <div class="w-full lg:w-1/4 bg-white dark:bg-slate-800 rounded-2xl p-4 border border-slate-200 dark:border-slate-700 flex flex-col h-[700px] shadow-sm shift-target-zone" data-target-id="">
                    <div class="flex items-center justify-between mb-4 px-2 cursor-pointer">
                        <h3 class="font-black text-slate-700 dark:text-slate-300 text-sm uppercase tracking-wider pointer-events-none">Belum Set</h3>
                        <div class="flex items-center gap-2">
                            <span class="bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-black px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-600 pointer-events-none" id="count-unassigned">{{ $staffUnassigned->count() }}</span>
                        </div>
                    </div>
                    <div id="list-unassigned" data-shift-id="" class="shift-container flex-1 overflow-y-auto space-y-3 bg-slate-50 dark:bg-slate-900/50 p-3 rounded-xl border-2 border-dashed border-slate-200 dark:border-slate-600 min-h-[100px]">
                        @foreach($staffUnassigned as $staff)
                            @php $hasDS = $staff->can_double_shift && $staff->double_shift_date === $today; @endphp
                            <div class="relative bg-white dark:bg-slate-800 border-2 p-3.5 rounded-xl cursor-grab active:cursor-grabbing staff-card" data-user-id="{{ $staff->id }}">
                                <input type="checkbox" value="{{ $staff->id }}" class="ds-checkbox absolute top-3 right-3 w-4 h-4 text-sky-600 rounded border-slate-300 focus:ring-sky-500 cursor-pointer z-10 bg-white dark:bg-slate-700 dark:border-slate-500">
                                <div class="pr-6 pointer-events-none">
                                    <p class="font-bold text-slate-800 dark:text-slate-200 text-sm">{{ $staff->name }}</p>
                                    <p class="text-[10px] font-black uppercase mt-0.5 dept-label">{{ $staff->department }}</p>
                                    @if($hasDS)
                                        <span class="lembur-badge inline-block mt-2 px-2 py-0.5 bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400 text-[9px] font-black rounded-md border border-amber-200 dark:border-amber-800">⭐ LEMBUR AKTIF</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="w-full lg:w-3/4 grid grid-cols-1 md:grid-cols-3 gap-6 h-[700px]">
                    @foreach($shifts as $shiftConfig)
                        @php
                            $staffList = $shiftConfig->id == 1 ? $staffShift1 : ($shiftConfig->id == 2 ? $staffShift2 : $staffShift3);

                            $containerClasses = match($shiftConfig->id) {
                                1 => 'bg-sky-50 dark:bg-sky-900/20 border-sky-100 dark:border-sky-800',
                                2 => 'bg-amber-50 dark:bg-amber-900/20 border-amber-100 dark:border-amber-800',
                                default => 'bg-indigo-50 dark:bg-indigo-900/20 border-indigo-100 dark:border-indigo-800',
                            };
                            $textClasses = match($shiftConfig->id) {
                                1 => 'text-sky-800 dark:text-sky-400',
                                2 => 'text-amber-800 dark:text-amber-400',
                                default => 'text-indigo-800 dark:text-indigo-400',
                            };
                            $subTextClasses = match($shiftConfig->id) {
                                1 => 'text-sky-600 dark:text-sky-500',
                                2 => 'text-amber-600 dark:text-amber-500',
                                default => 'text-indigo-600 dark:text-indigo-500',
                            };
                            $btnClasses = match($shiftConfig->id) {
                                1 => 'bg-sky-200 dark:bg-sky-800 text-sky-800 dark:text-sky-300 hover:bg-sky-300 dark:hover:bg-sky-700',
                                2 => 'bg-amber-200 dark:bg-amber-800 text-amber-800 dark:text-amber-300 hover:bg-amber-300 dark:hover:bg-amber-700',
                                default => 'bg-indigo-200 dark:bg-indigo-800 text-indigo-800 dark:text-indigo-300 hover:bg-indigo-300 dark:hover:bg-indigo-700',
                            };
                            $badgeClasses = match($shiftConfig->id) {
                                1 => 'bg-sky-200 dark:bg-sky-800 text-sky-800 dark:text-sky-300 border-sky-300 dark:border-sky-700',
                                2 => 'bg-amber-200 dark:bg-amber-800 text-amber-800 dark:text-amber-300 border-amber-300 dark:border-amber-700',
                                default => 'bg-indigo-200 dark:bg-indigo-800 text-indigo-800 dark:text-indigo-300 border-indigo-300 dark:border-indigo-700',
                            };
                            $listContainerClasses = match($shiftConfig->id) {
                                1 => 'border-sky-200 dark:border-sky-700/50 bg-white/60 dark:bg-slate-900/40',
                                2 => 'border-amber-200 dark:border-amber-700/50 bg-white/60 dark:bg-slate-900/40',
                                default => 'border-indigo-200 dark:border-indigo-700/50 bg-white/60 dark:bg-slate-900/40',
                            };
                        @endphp

                        <div class="rounded-2xl p-4 border flex flex-col h-full shadow-sm shift-target-zone {{ $containerClasses }}" data-target-id="{{ $shiftConfig->id }}">
                            <div class="flex items-center justify-between mb-4 px-2 cursor-pointer">
                                <div class="pointer-events-none">
                                    <h3 class="font-black text-sm uppercase tracking-wider {{ $textClasses }}">{{ $shiftConfig->name }}</h3>
                                    <p class="text-[10px] font-bold mt-0.5 {{ $subTextClasses }}">
                                        {{ \Carbon\Carbon::parse($shiftConfig->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($shiftConfig->end_time)->format('H:i') }}
                                    </p>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" class="btn-select-column text-[9px] font-bold px-2 py-1 rounded {{ $btnClasses }}" data-target="list-shift-{{ $shiftConfig->id }}">☑️ Semua</button>
                                    <span class="text-xs font-black px-2.5 py-1 rounded-lg border pointer-events-none {{ $badgeClasses }}" id="count-shift-{{ $shiftConfig->id }}">{{ $staffList->count() }}</span>
                                </div>
                            </div>
                            <div id="list-shift-{{ $shiftConfig->id }}" data-shift-id="{{ $shiftConfig->id }}" class="shift-container flex-1 overflow-y-auto space-y-3 p-3 rounded-xl border-2 border-dashed min-h-[100px] {{ $listContainerClasses }}">
                                @foreach($staffList as $staff)
                                    @php $hasDS = $staff->can_double_shift && $staff->double_shift_date === $today; @endphp
                                    <div class="relative bg-white dark:bg-slate-800 border-2 p-3.5 rounded-xl cursor-grab active:cursor-grabbing staff-card" data-user-id="{{ $staff->id }}">
                                        <input type="checkbox" value="{{ $staff->id }}" class="ds-checkbox absolute top-3 right-3 w-4 h-4 text-sky-600 rounded border-slate-300 focus:ring-sky-500 cursor-pointer z-10 bg-white dark:bg-slate-700 dark:border-slate-500">
                                        <div class="pr-6 pointer-events-none">
                                            <p class="font-bold text-slate-800 dark:text-slate-200 text-sm">{{ $staff->name }}</p>
                                            <p class="text-[10px] font-black uppercase mt-0.5 dept-label">{{ $staff->department }}</p>
                                            @if($hasDS)
                                                <span class="lembur-badge inline-block mt-2 px-2 py-0.5 bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400 text-[9px] font-black rounded-md border border-amber-200 dark:border-amber-800">⭐ LEMBUR AKTIF</span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div x-cloak x-show="configModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center">
            <div x-show="configModalOpen" class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm" @click="configModalOpen = false"></div>

            <div x-show="configModalOpen"
                 class="relative z-[110] bg-white dark:bg-slate-800 rounded-3xl shadow-2xl w-full max-w-3xl mx-4 overflow-hidden border border-slate-200 dark:border-slate-700 flex flex-col max-h-[90vh]">

                <div class="p-6 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 flex justify-between items-center flex-shrink-0">
                    <div>
                        <h3 class="text-xl font-black text-slate-800 dark:text-white">Konfigurasi Jam Operasional</h3>
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-wider">Atur Jam Masuk, Pulang, dan Batas Laporan</p>
                    </div>
                    <button @click="configModalOpen = false" class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400 hover:bg-rose-100 hover:text-rose-600 dark:hover:bg-rose-900/50 dark:hover:text-rose-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <div class="p-6 overflow-y-auto bg-slate-50/50 dark:bg-slate-900 flex-1">
                    <form id="shiftConfigForm" action="{{ route('admin.shifts.config') }}" method="POST">
                        @csrf
                        <div class="space-y-6">
                            @foreach($shifts as $index => $shift)
                                <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm">
                                    <h4 class="font-black text-slate-800 dark:text-white mb-4 text-lg border-b border-slate-100 dark:border-slate-700 pb-2">{{ $shift->name }}</h4>
                                    <input type="hidden" name="shifts[{{ $index }}][id]" value="{{ $shift->id }}">

                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Jam Mulai Shift</label>
                                            <input type="time" name="shifts[{{ $index }}][start_time]" value="{{ \Carbon\Carbon::parse($shift->start_time)->format('H:i') }}" class="w-full border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 text-slate-800 dark:text-white font-bold rounded-xl shadow-sm py-2 px-3 text-sm focus:border-sky-500 focus:ring-sky-500" required>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase mb-1">Jam Selesai Shift</label>
                                            <input type="time" name="shifts[{{ $index }}][end_time]" value="{{ \Carbon\Carbon::parse($shift->end_time)->format('H:i') }}" class="w-full border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 text-slate-800 dark:text-white font-bold rounded-xl shadow-sm py-2 px-3 text-sm focus:border-sky-500 focus:ring-sky-500" required>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-black text-rose-500 dark:text-rose-400 uppercase mb-1">Batas Submit Todo</label>
                                            <input type="time" name="shifts[{{ $index }}][deadline_time]" value="{{ \Carbon\Carbon::parse($shift->deadline_time)->format('H:i') }}" class="w-full border-rose-300 dark:border-rose-800/50 bg-rose-50/50 dark:bg-rose-900/20 text-rose-800 dark:text-rose-200 font-bold rounded-xl shadow-sm py-2 px-3 text-sm focus:border-rose-500 focus:ring-rose-500" required>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </form>
                </div>

                <div class="p-6 border-t border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800 flex justify-end gap-3 flex-shrink-0">
                    <button type="button" @click="configModalOpen = false" class="px-5 py-2.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-bold rounded-xl shadow-sm border border-slate-200 dark:border-slate-600">Batal</button>
                    <button type="button" onclick="document.getElementById('shiftConfigForm').submit()" class="px-5 py-2.5 bg-sky-600 hover:bg-sky-700 text-white font-bold rounded-xl shadow-lg shadow-sky-600/30">Simpan Perubahan</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js" defer></script>
    <script>
        function initShiftManagement() {
            window.selectedForDoubleShift = [];
            window.activeCard = null;

            const bulkPanel = document.getElementById('bulk-action-panel');
            const selectedCount = document.getElementById('selected-count');
            const shiftBoard = document.getElementById('shift-board');

            if(!shiftBoard) return;

            const newBoard = shiftBoard.cloneNode(true);
            shiftBoard.parentNode.replaceChild(newBoard, shiftBoard);

            function toggleBulkPanel() {
                if(window.selectedForDoubleShift.length > 0) {
                    bulkPanel.classList.remove('hidden');
                    bulkPanel.classList.add('flex');
                    selectedCount.innerText = window.selectedForDoubleShift.length;
                } else {
                    bulkPanel.classList.add('hidden');
                    bulkPanel.classList.remove('flex');
                }
            }

            newBoard.addEventListener('click', function(e) {
                if(e.target.classList.contains('btn-select-column')) {
                    e.stopPropagation();
                    const targetId = e.target.getAttribute('data-target');
                    const container = document.getElementById(targetId);
                    if(!container) return;

                    const checkboxes = container.querySelectorAll('.ds-checkbox');
                    let allChecked = true;
                    checkboxes.forEach(cb => { if(!cb.checked) allChecked = false; });

                    checkboxes.forEach(cb => {
                        cb.checked = !allChecked;
                        const id = parseInt(cb.value);
                        if (!allChecked) {
                            if(!window.selectedForDoubleShift.includes(id)) window.selectedForDoubleShift.push(id);
                        } else {
                            window.selectedForDoubleShift = window.selectedForDoubleShift.filter(item => item !== id);
                        }
                    });

                    e.target.innerText = allChecked ? '☑️ Semua' : '◻️ Batal';
                    toggleBulkPanel();
                    return;
                }

                if(e.target.classList.contains('ds-checkbox')) return;

                const clickedCard = e.target.closest('.staff-card');
                if (clickedCard) {
                    if (window.activeCard === clickedCard) {
                        window.activeCard.classList.remove('staff-selected');
                        window.activeCard = null;
                    } else {
                        if (window.activeCard) window.activeCard.classList.remove('staff-selected');
                        window.activeCard = clickedCard;
                        window.activeCard.classList.add('staff-selected');
                    }
                    return;
                }

                const targetZone = e.target.closest('.shift-target-zone');
                if (targetZone && window.activeCard) {
                    const targetContainer = targetZone.querySelector('.shift-container');
                    const currentContainer = window.activeCard.closest('.shift-container');

                    if (targetContainer !== currentContainer) {
                        targetContainer.appendChild(window.activeCard);
                        const userId = window.activeCard.getAttribute('data-user-id');
                        const shiftId = targetContainer.getAttribute('data-shift-id');

                        const badge = window.activeCard.querySelector('.lembur-badge');
                        if(badge) badge.remove();

                        const cb = window.activeCard.querySelector('.ds-checkbox');
                        if(cb && cb.checked) {
                            cb.checked = false;
                            window.selectedForDoubleShift = window.selectedForDoubleShift.filter(id => id !== parseInt(cb.value));
                            toggleBulkPanel();
                        }

                        updateCounts();
                        saveShiftChange(userId, shiftId);
                    }
                    window.activeCard.classList.remove('staff-selected');
                    window.activeCard = null;
                }
            });

            newBoard.addEventListener('change', function(e) {
                if(e.target.classList.contains('ds-checkbox')) {
                    const id = parseInt(e.target.value);
                    if(e.target.checked) {
                        if(!window.selectedForDoubleShift.includes(id)) window.selectedForDoubleShift.push(id);
                    } else {
                        window.selectedForDoubleShift = window.selectedForDoubleShift.filter(item => item !== id);
                    }
                    toggleBulkPanel();
                }
            });

            window.submitDoubleShift = function() {
                if(window.selectedForDoubleShift.length === 0) return;
                fetch('{{ route('admin.shifts.grant_double_shift') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ user_ids: window.selectedForDoubleShift })
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        window.location.reload();
                    } else {
                        alert('Gagal memberikan otorisasi.');
                    }
                }).catch(error => alert('Terjadi kesalahan jaringan saat otorisasi.'));
            };

            function updateCounts() {
                document.getElementById('count-unassigned').innerText = document.getElementById('list-unassigned').children.length;
                document.getElementById('count-shift-1').innerText = document.getElementById('list-shift-1').children.length;
                document.getElementById('count-shift-2').innerText = document.getElementById('list-shift-2').children.length;
                document.getElementById('count-shift-3').innerText = document.getElementById('list-shift-3').children.length;
            }

            function saveShiftChange(userId, shiftId) {
                fetch('{{ route('admin.shifts.update') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ user_id: userId, shift_id: shiftId ? shiftId : null })
                }).catch(error => alert('Terjadi kesalahan jaringan.'));
            }

            if (typeof Sortable !== 'undefined') {
                const containers = document.querySelectorAll('.shift-container');
                containers.forEach(container => {
                    new Sortable(container, {
                        group: 'shared',
                        animation: 150,
                        ghostClass: 'opacity-50',
                        filter: '.ds-checkbox, .btn-select-column',
                        preventOnFilter: false,
                        onEnd: function (evt) {
                            if (window.activeCard) {
                                window.activeCard.classList.remove('staff-selected');
                                window.activeCard = null;
                            }
                            const itemEl = evt.item;
                            const userId = itemEl.getAttribute('data-user-id');
                            const shiftId = evt.to.getAttribute('data-shift-id');

                            if (evt.to !== evt.from) {
                                const badge = itemEl.querySelector('.lembur-badge');
                                if(badge) badge.remove();

                                const cb = itemEl.querySelector('.ds-checkbox');
                                if(cb && cb.checked) {
                                    cb.checked = false;
                                    window.selectedForDoubleShift = window.selectedForDoubleShift.filter(id => id !== parseInt(cb.value));
                                    toggleBulkPanel();
                                }
                            }
                            updateCounts();
                            saveShiftChange(userId, shiftId);
                        }
                    });
                });
            } else {
                setTimeout(initShiftManagement, 500);
            }
        }

        document.addEventListener('livewire:navigated', initShiftManagement);
        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            initShiftManagement();
        }
    </script>
</x-app-layout>
