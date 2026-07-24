<x-app-layout>
    <div class="min-h-screen bg-sky-950/5 flex">
        <x-admin-sidebar />

        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
            <header class="bg-sky-50/30 backdrop-blur-md border-b border-sky-100 h-16 flex items-center justify-between px-6 shadow-sm">
                <span class="font-extrabold text-xl text-blue-700 tracking-tight">Pengaturan Shift Harian</span>
                <span class="text-xs font-bold text-slate-500">Login as: <span class="text-sky-700 font-black">{{ Auth::user()->name }}</span></span>
            </header>

            <div class="p-8 space-y-6">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 class="text-xl font-black text-slate-800">Assign & Geser Jadwal</h2>
                        <p class="text-xs text-slate-500 font-medium mt-1">Sistem otomatis menyimpan perubahan saat nama dilepaskan di kolom tujuan.</p>
                    </div>
                    <form method="GET" action="" class="w-full md:w-64">
                        <select name="department" onchange="this.form.submit()" class="w-full border-sky-200 bg-white text-slate-800 font-semibold rounded-xl shadow-sm py-2.5 px-3 text-sm focus:border-sky-500 focus:ring-sky-500 cursor-pointer outline-none">
                            <option value="">Semua Departemen</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <div class="flex flex-col lg:flex-row gap-6 items-start">

                    <div class="w-full lg:w-1/4 bg-slate-100 rounded-2xl p-4 border border-slate-200 flex flex-col h-[700px] shadow-inner">
                        <div class="flex items-center justify-between mb-4 px-2">
                            <h3 class="font-black text-slate-700 text-sm uppercase tracking-wider">Belum Set</h3>
                            <span class="bg-slate-200 text-slate-600 text-xs font-black px-2.5 py-1 rounded-lg border border-slate-300" id="count-unassigned">{{ $staffUnassigned->count() }}</span>
                        </div>
                        <div id="list-unassigned" data-shift-id="" class="shift-container flex-1 overflow-y-auto space-y-3 bg-white/40 p-3 rounded-xl border-2 border-dashed border-slate-300 min-h-[100px]">
                            @foreach($staffUnassigned as $staff)
                                <div class="bg-white border-2 border-slate-200 p-3.5 rounded-xl shadow-sm cursor-grab active:cursor-grabbing hover:border-sky-400 hover:shadow-md transition" data-user-id="{{ $staff->id }}">
                                    <p class="font-bold text-slate-800 text-sm">{{ $staff->name }}</p>
                                    <p class="text-[10px] font-black text-slate-500 uppercase mt-0.5">{{ $staff->department }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="w-full lg:w-3/4 grid grid-cols-1 md:grid-cols-3 gap-6 h-[700px]">

                        <div class="bg-sky-50 rounded-2xl p-4 border border-sky-100 flex flex-col h-full shadow-inner">
                            <div class="flex items-center justify-between mb-4 px-2">
                                <div>
                                    <h3 class="font-black text-sky-800 text-sm uppercase tracking-wider">Shift 1 (Pagi)</h3>
                                    <p class="text-[10px] font-bold text-sky-600 mt-0.5">07:00 - 16:00</p>
                                </div>
                                <span class="bg-sky-200 text-sky-800 text-xs font-black px-2.5 py-1 rounded-lg border border-sky-300" id="count-shift-1">{{ $staffShift1->count() }}</span>
                            </div>
                            <div id="list-shift-1" data-shift-id="1" class="shift-container flex-1 overflow-y-auto space-y-3 bg-white/40 p-3 rounded-xl border-2 border-dashed border-sky-200 min-h-[100px]">
                                @foreach($staffShift1 as $staff)
                                    <div class="bg-white border-2 border-sky-100 p-3.5 rounded-xl shadow-sm cursor-grab active:cursor-grabbing hover:border-sky-400 hover:shadow-md transition" data-user-id="{{ $staff->id }}">
                                        <p class="font-bold text-slate-800 text-sm">{{ $staff->name }}</p>
                                        <p class="text-[10px] font-black text-sky-600 uppercase mt-0.5">{{ $staff->department }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="bg-amber-50 rounded-2xl p-4 border border-amber-100 flex flex-col h-full shadow-inner">
                            <div class="flex items-center justify-between mb-4 px-2">
                                <div>
                                    <h3 class="font-black text-amber-800 text-sm uppercase tracking-wider">Shift 2 (Siang)</h3>
                                    <p class="text-[10px] font-bold text-amber-600 mt-0.5">13:00 - 22:00</p>
                                </div>
                                <span class="bg-amber-200 text-amber-800 text-xs font-black px-2.5 py-1 rounded-lg border border-amber-300" id="count-shift-2">{{ $staffShift2->count() }}</span>
                            </div>
                            <div id="list-shift-2" data-shift-id="2" class="shift-container flex-1 overflow-y-auto space-y-3 bg-white/40 p-3 rounded-xl border-2 border-dashed border-amber-200 min-h-[100px]">
                                @foreach($staffShift2 as $staff)
                                    <div class="bg-white border-2 border-amber-100 p-3.5 rounded-xl shadow-sm cursor-grab active:cursor-grabbing hover:border-amber-400 hover:shadow-md transition" data-user-id="{{ $staff->id }}">
                                        <p class="font-bold text-slate-800 text-sm">{{ $staff->name }}</p>
                                        <p class="text-[10px] font-black text-amber-600 uppercase mt-0.5">{{ $staff->department }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="bg-indigo-50 rounded-2xl p-4 border border-indigo-100 flex flex-col h-full shadow-inner">
                            <div class="flex items-center justify-between mb-4 px-2">
                                <div>
                                    <h3 class="font-black text-indigo-800 text-sm uppercase tracking-wider">Shift 3 (Malam)</h3>
                                    <p class="text-[10px] font-bold text-indigo-600 mt-0.5">22:00 - 07:00</p>
                                </div>
                                <span class="bg-indigo-200 text-indigo-800 text-xs font-black px-2.5 py-1 rounded-lg border border-indigo-300" id="count-shift-3">{{ $staffShift3->count() }}</span>
                            </div>
                            <div id="list-shift-3" data-shift-id="3" class="shift-container flex-1 overflow-y-auto space-y-3 bg-white/40 p-3 rounded-xl border-2 border-dashed border-indigo-200 min-h-[100px]">
                                @foreach($staffShift3 as $staff)
                                    <div class="bg-white border-2 border-indigo-100 p-3.5 rounded-xl shadow-sm cursor-grab active:cursor-grabbing hover:border-indigo-400 hover:shadow-md transition" data-user-id="{{ $staff->id }}">
                                        <p class="font-bold text-slate-800 text-sm">{{ $staff->name }}</p>
                                        <p class="text-[10px] font-black text-indigo-600 uppercase mt-0.5">{{ $staff->department }}</p>
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
            const containers = document.querySelectorAll('.shift-container');

            containers.forEach(container => {
                new Sortable(container, {
                    group: 'shared',
                    animation: 150,
                    ghostClass: 'opacity-50',
                    onEnd: function (evt) {
                        const itemEl = evt.item;
                        const userId = itemEl.getAttribute('data-user-id');
                        const toList = evt.to;
                        const shiftId = toList.getAttribute('data-shift-id');

                        document.getElementById('count-unassigned').innerText = document.getElementById('list-unassigned').children.length;
                        document.getElementById('count-shift-1').innerText = document.getElementById('list-shift-1').children.length;
                        document.getElementById('count-shift-2').innerText = document.getElementById('list-shift-2').children.length;
                        document.getElementById('count-shift-3').innerText = document.getElementById('list-shift-3').children.length;

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
                });
            });
        });
    </script>
</x-app-layout>
