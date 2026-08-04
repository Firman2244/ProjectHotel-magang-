<x-app-layout>
    <style>
        [x-cloak] { display: none !important; }
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
    </style>

    <div class="min-h-screen bg-sky-950/5 dark:bg-slate-900 flex flex-col md:flex-row relative transition-colors duration-300" x-data="{ sidebarOpen: false }">
        <div class="md:hidden sticky top-0 z-40 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between h-16 px-4 transition-colors duration-300">
            <h1 class="font-black text-xl text-slate-800 dark:text-white">Admin Panel</h1>
            <button type="button" @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <div x-cloak :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'" class="fixed inset-y-0 left-0 z-50 w-64 h-screen bg-white dark:bg-slate-800 shadow-2xl md:shadow-none transform transition-transform duration-300 ease-in-out flex-shrink-0 md:fixed">
            <x-admin-sidebar />
        </div>

        <div x-cloak x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-40 md:hidden"></div>

        <div class="flex-1 flex flex-col min-w-0 md:ml-64 overflow-y-auto relative">
            <header class="bg-sky-50/30 dark:bg-slate-900/50 backdrop-blur-md border-b border-sky-100 dark:border-slate-700 h-16 hidden md:flex items-center justify-between px-6 shadow-sm transition-colors duration-300">
                <span class="font-extrabold text-xl text-blue-700 dark:text-sky-400 tracking-tight">Pengaturan Shift Harian</span>
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Login as: <span class="text-sky-700 dark:text-sky-400 font-black">{{ Auth::user()->name }}</span></span>
            </header>

            <div class="p-4 md:p-8 space-y-6">
                <div class="md:hidden flex items-center justify-between bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-sm border border-sky-100 dark:border-slate-700 transition-colors duration-300">
                    <span class="font-extrabold text-sm text-blue-700 dark:text-sky-400 tracking-tight">Pengaturan Shift Harian</span>
                </div>

                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 class="text-xl font-black text-slate-800 dark:text-white">Assign & Geser Jadwal</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">Tekan nama staf, lalu tekan kotak shift tujuan untuk memindahkan (khusus HP), atau geser langsung.</p>
                    </div>
                    <form method="GET" action="" class="w-full md:w-64">
                        <select name="department" onchange="this.form.submit()" class="w-full border-sky-200 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-800 dark:text-white font-semibold rounded-xl shadow-sm py-2.5 px-3 text-sm focus:border-sky-500 focus:ring-sky-500 cursor-pointer outline-none transition-colors duration-300">
                            <option value="">Semua Departemen</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <div class="flex flex-col lg:flex-row gap-6 items-start" id="shift-board">
                    <div class="w-full lg:w-1/4 bg-slate-100 dark:bg-slate-800/50 rounded-2xl p-4 border border-slate-200 dark:border-slate-700 flex flex-col h-[700px] shadow-inner shift-target-zone transition-colors duration-300" data-target-id="">
                        <div class="flex items-center justify-between mb-4 px-2 cursor-pointer">
                            <h3 class="font-black text-slate-700 dark:text-slate-300 text-sm uppercase tracking-wider pointer-events-none">Belum Set</h3>
                            <span class="bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-black px-2.5 py-1 rounded-lg border border-slate-300 dark:border-slate-600 pointer-events-none" id="count-unassigned">{{ $staffUnassigned->count() }}</span>
                        </div>
                        <div id="list-unassigned" data-shift-id="" class="shift-container flex-1 overflow-y-auto space-y-3 bg-white/40 dark:bg-slate-900/50 p-3 rounded-xl border-2 border-dashed border-slate-300 dark:border-slate-600 min-h-[100px]">
                            @foreach($staffUnassigned as $staff)
                                <div class="bg-white dark:bg-slate-800 border-2 border-slate-200 dark:border-slate-600 p-3.5 rounded-xl shadow-sm cursor-grab active:cursor-grabbing hover:border-sky-400 dark:hover:border-sky-500 hover:shadow-md transition staff-card" data-user-id="{{ $staff->id }}">
                                    <p class="font-bold text-slate-800 dark:text-slate-200 text-sm pointer-events-none">{{ $staff->name }}</p>
                                    <p class="text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase mt-0.5 pointer-events-none">{{ $staff->department }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="w-full lg:w-3/4 grid grid-cols-1 md:grid-cols-3 gap-6 h-[700px]">
                        <div class="bg-sky-50 dark:bg-sky-900/20 rounded-2xl p-4 border border-sky-100 dark:border-sky-800 flex flex-col h-full shadow-inner shift-target-zone transition-colors duration-300" data-target-id="1">
                            <div class="flex items-center justify-between mb-4 px-2 cursor-pointer">
                                <div class="pointer-events-none">
                                    <h3 class="font-black text-sky-800 dark:text-sky-400 text-sm uppercase tracking-wider">Shift 1 (Pagi)</h3>
                                    <p class="text-[10px] font-bold text-sky-600 dark:text-sky-500 mt-0.5">07:00 - 16:00</p>
                                </div>
                                <span class="bg-sky-200 dark:bg-sky-800 text-sky-800 dark:text-sky-300 text-xs font-black px-2.5 py-1 rounded-lg border border-sky-300 dark:border-sky-700 pointer-events-none" id="count-shift-1">{{ $staffShift1->count() }}</span>
                            </div>
                            <div id="list-shift-1" data-shift-id="1" class="shift-container flex-1 overflow-y-auto space-y-3 bg-white/40 dark:bg-slate-900/50 p-3 rounded-xl border-2 border-dashed border-sky-200 dark:border-sky-700/50 min-h-[100px]">
                                @foreach($staffShift1 as $staff)
                                    <div class="bg-white dark:bg-slate-800 border-2 border-sky-100 dark:border-slate-600 p-3.5 rounded-xl shadow-sm cursor-grab active:cursor-grabbing hover:border-sky-400 dark:hover:border-sky-500 hover:shadow-md transition staff-card" data-user-id="{{ $staff->id }}">
                                        <p class="font-bold text-slate-800 dark:text-slate-200 text-sm pointer-events-none">{{ $staff->name }}</p>
                                        <p class="text-[10px] font-black text-sky-600 dark:text-sky-400 uppercase mt-0.5 pointer-events-none">{{ $staff->department }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="bg-amber-50 dark:bg-amber-900/20 rounded-2xl p-4 border border-amber-100 dark:border-amber-800 flex flex-col h-full shadow-inner shift-target-zone transition-colors duration-300" data-target-id="2">
                            <div class="flex items-center justify-between mb-4 px-2 cursor-pointer">
                                <div class="pointer-events-none">
                                    <h3 class="font-black text-amber-800 dark:text-amber-400 text-sm uppercase tracking-wider">Shift 2 (Siang)</h3>
                                    <p class="text-[10px] font-bold text-amber-600 dark:text-amber-500 mt-0.5">13:00 - 22:00</p>
                                </div>
                                <span class="bg-amber-200 dark:bg-amber-800 text-amber-800 dark:text-amber-300 text-xs font-black px-2.5 py-1 rounded-lg border border-amber-300 dark:border-amber-700 pointer-events-none" id="count-shift-2">{{ $staffShift2->count() }}</span>
                            </div>
                            <div id="list-shift-2" data-shift-id="2" class="shift-container flex-1 overflow-y-auto space-y-3 bg-white/40 dark:bg-slate-900/50 p-3 rounded-xl border-2 border-dashed border-amber-200 dark:border-amber-700/50 min-h-[100px]">
                                @foreach($staffShift2 as $staff)
                                    <div class="bg-white dark:bg-slate-800 border-2 border-amber-100 dark:border-slate-600 p-3.5 rounded-xl shadow-sm cursor-grab active:cursor-grabbing hover:border-amber-400 dark:hover:border-amber-500 hover:shadow-md transition staff-card" data-user-id="{{ $staff->id }}">
                                        <p class="font-bold text-slate-800 dark:text-slate-200 text-sm pointer-events-none">{{ $staff->name }}</p>
                                        <p class="text-[10px] font-black text-amber-600 dark:text-amber-500 uppercase mt-0.5 pointer-events-none">{{ $staff->department }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-2xl p-4 border border-indigo-100 dark:border-indigo-800 flex flex-col h-full shadow-inner shift-target-zone transition-colors duration-300" data-target-id="3">
                            <div class="flex items-center justify-between mb-4 px-2 cursor-pointer">
                                <div class="pointer-events-none">
                                    <h3 class="font-black text-indigo-800 dark:text-indigo-400 text-sm uppercase tracking-wider">Shift 3 (Malam)</h3>
                                    <p class="text-[10px] font-bold text-indigo-600 dark:text-indigo-500 mt-0.5">22:00 - 07:00</p>
                                </div>
                                <span class="bg-indigo-200 dark:bg-indigo-800 text-indigo-800 dark:text-indigo-300 text-xs font-black px-2.5 py-1 rounded-lg border border-indigo-300 dark:border-indigo-700 pointer-events-none" id="count-shift-3">{{ $staffShift3->count() }}</span>
                            </div>
                            <div id="list-shift-3" data-shift-id="3" class="shift-container flex-1 overflow-y-auto space-y-3 bg-white/40 dark:bg-slate-900/50 p-3 rounded-xl border-2 border-dashed border-indigo-200 dark:border-indigo-700/50 min-h-[100px]">
                                @foreach($staffShift3 as $staff)
                                    <div class="bg-white dark:bg-slate-800 border-2 border-indigo-100 dark:border-slate-600 p-3.5 rounded-xl shadow-sm cursor-grab active:cursor-grabbing hover:border-indigo-400 dark:hover:border-indigo-500 hover:shadow-md transition staff-card" data-user-id="{{ $staff->id }}">
                                        <p class="font-bold text-slate-800 dark:text-slate-200 text-sm pointer-events-none">{{ $staff->name }}</p>
                                        <p class="text-[10px] font-black text-indigo-600 dark:text-indigo-400 uppercase mt-0.5 pointer-events-none">{{ $staff->department }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let activeCard = null;

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
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        user_id: userId,
                        shift_id: shiftId ? shiftId : null
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if(!data.success) {
                        alert('Koneksi terputus, gagal memindahkan shift.');
                    }
                })
                .catch(error => {
                    alert('Terjadi kesalahan jaringan.');
                });
            }

            const containers = document.querySelectorAll('.shift-container');
            containers.forEach(container => {
                new Sortable(container, {
                    group: 'shared',
                    animation: 150,
                    ghostClass: 'opacity-50',
                    onEnd: function (evt) {
                        if (activeCard) {
                            activeCard.classList.remove('staff-selected');
                            activeCard = null;
                        }

                        const itemEl = evt.item;
                        const userId = itemEl.getAttribute('data-user-id');
                        const shiftId = evt.to.getAttribute('data-shift-id');

                        updateCounts();
                        saveShiftChange(userId, shiftId);
                    }
                });
            });

            document.getElementById('shift-board').addEventListener('click', function(e) {
                const clickedCard = e.target.closest('.staff-card');

                if (clickedCard) {
                    if (activeCard === clickedCard) {
                        activeCard.classList.remove('staff-selected');
                        activeCard = null;
                    } else {
                        if (activeCard) {
                            activeCard.classList.remove('staff-selected');
                        }
                        activeCard = clickedCard;
                        activeCard.classList.add('staff-selected');
                    }
                    return;
                }

                const targetZone = e.target.closest('.shift-target-zone');

                if (targetZone && activeCard) {
                    const targetContainer = targetZone.querySelector('.shift-container');
                    const currentContainer = activeCard.closest('.shift-container');

                    if (targetContainer !== currentContainer) {
                        targetContainer.appendChild(activeCard);

                        const userId = activeCard.getAttribute('data-user-id');
                        const shiftId = targetContainer.getAttribute('data-shift-id');

                        updateCounts();
                        saveShiftChange(userId, shiftId);
                    }

                    activeCard.classList.remove('staff-selected');
                    activeCard = null;
                }
            });
        });
    </script>
</x-app-layout>
