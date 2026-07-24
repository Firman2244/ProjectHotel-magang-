<x-app-layout>
    <div class="min-h-screen bg-sky-950/5 flex">
        <x-admin-sidebar />

        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto relative">
            <header class="bg-sky-50/30 backdrop-blur-md border-b border-sky-100 h-16 flex items-center justify-between px-6 shadow-sm">
                <div class="flex items-center">
                    <span class="font-extrabold text-xl text-blue-700 tracking-tight">Laporan Harian</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs font-bold text-slate-500">Login as: <span class="text-sky-700 font-black">{{ Auth::user()->name }}</span></span>
                </div>
            </header>

            <div class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-sky-50/40 backdrop-blur-md p-6 rounded-2xl shadow-sm border border-sky-200/60 flex items-center gap-4">
                        <div class="w-12 h-12 bg-sky-100/70 text-sky-700 rounded-xl flex items-center justify-center text-xl font-bold border border-sky-200">
                            👥
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Staf Terdaftar</p>
                            <p class="text-2xl font-black text-slate-800">{{ $totalStaff ?? 0 }} Orang</p>
                        </div>
                    </div>

                    <div class="bg-sky-50/40 backdrop-blur-md p-6 rounded-2xl shadow-sm border border-sky-200/60 flex items-center gap-4">
                        <div class="w-12 h-12 bg-emerald-100/70 text-emerald-700 rounded-xl flex items-center justify-center text-xl font-bold border border-emerald-200">
                            ✅
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Sudah Submit Hari Ini</p>
                            <p class="text-2xl font-black text-emerald-800">{{ $submittedCount ?? 0 }} Laporan</p>
                        </div>
                    </div>

                    <div class="bg-sky-50/40 backdrop-blur-md p-6 rounded-2xl shadow-sm border border-sky-200/60 flex items-center gap-4">
                        <div class="w-12 h-12 bg-rose-100/70 text-rose-700 rounded-xl flex items-center justify-center text-xl font-bold border border-rose-200">
                            ⚠️
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tercatat Terlambat</p>
                            <p class="text-2xl font-black text-rose-700">{{ $lateCount ?? 0 }} Laporan</p>
                        </div>
                    </div>
                </div>

                <div class="bg-sky-50/40 backdrop-blur-md p-6 rounded-2xl shadow-sm border border-sky-200/60">
                    <form id="filterForm" method="GET" action="{{ route('admin.dashboard') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                        <input type="hidden" name="hotel" value="{{ $currentHotel ?? 'wahyu' }}">

                        <div>
                            <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">Tanggal Laporan</label>
                            <input type="date" id="filterDate" name="date" value="{{ request('date', date('Y-m-d')) }}" class="w-full border-sky-200 bg-white/80 text-slate-800 font-semibold rounded-xl shadow-sm py-2.5 px-3 text-sm focus:border-sky-500 focus:ring-sky-500 cursor-pointer">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">Departemen</label>
                            <select id="filterDept" name="department" class="w-full border-sky-200 bg-white/80 text-slate-800 font-semibold rounded-xl shadow-sm py-2.5 px-3 text-sm focus:border-sky-500 focus:ring-sky-500 cursor-pointer">
                                <option value="">Semua Departemen</option>
                                @foreach($departments ?? [] as $dept)
                                    <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">Status</label>
                            <select id="filterStatus" name="status" class="w-full border-sky-200 bg-white/80 text-slate-800 font-semibold rounded-xl shadow-sm py-2.5 px-3 text-sm focus:border-sky-500 focus:ring-sky-500 cursor-pointer">
                                <option value="">Semua Status</option>
                                <option value="planned" {{ request('status') == 'planned' ? 'selected' : '' }}>Planned (Belum Submit)</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed (Selesai)</option>
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('admin.dashboard', ['hotel' => $currentHotel ?? 'wahyu']) }}" class="w-full bg-white/80 hover:bg-white text-slate-700 font-bold py-2.5 px-4 rounded-xl transition text-sm flex items-center justify-center border border-sky-200 shadow-sm" title="Reset Filter">
                                Reset Filter
                            </a>
                        </div>
                    </form>
                </div>

                <div class="bg-sky-50/40 backdrop-blur-md rounded-2xl shadow-sm border border-sky-200/60 overflow-hidden">
                    <div class="p-6 border-b border-sky-200/60 flex justify-between items-center bg-sky-100/30">
                        <h3 class="text-lg font-black text-slate-800">
                            Dokumentasi Laporan Masuk — <span class="text-sky-700 uppercase">{{ $currentHotel ?? 'wahyu' }}</span>
                        </h3>
                        <span class="text-xs font-bold text-sky-700 uppercase tracking-wider">Total Ditampilkan: {{ count($reports ?? []) }}</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-sky-100/40 border-b border-sky-200/60">
                                    <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-wider">Nama Karyawan</th>
                                    <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-wider">Departemen / Shift</th>
                                    <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-wider">Waktu Submit</th>
                                    <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-wider text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-sky-200/50">
                                @forelse($reports ?? [] as $report)
                                    <tr class="hover:bg-sky-100/30 transition">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded-xl overflow-hidden bg-sky-700 flex items-center justify-center text-white font-bold flex-shrink-0 text-sm shadow-sm">
                                                    @if(!empty($report->user->avatar))
                                                        <img src="{{ asset('storage/' . $report->user->avatar) }}" class="w-full h-full object-cover">
                                                    @else
                                                        {{ strtoupper(substr($report->user->name, 0, 1)) }}
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="text-sm font-bold text-slate-800">{{ $report->user->name }}</div>
                                                    <div class="text-xs text-slate-500 font-medium">ID: #{{ $report->user->id }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-slate-700">{{ $report->user->department }}</div>
                                            <div class="text-xs text-sky-700 font-semibold">Shift ID: {{ $report->shift_id }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-slate-800">{{ \Carbon\Carbon::parse($report->created_at)->timezone('Asia/Jakarta')->format('Y-m-d H:i') }} WIB</div>

                                            @if($report->is_late)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black bg-orange-100 text-orange-800 mt-1 uppercase border border-orange-200">
                                                    Late Apply
                                                </span>
                                            @endif

                                            @if($report->is_late_submit)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black bg-rose-100 text-rose-800 mt-1 uppercase border border-rose-200">
                                                    Late Submit
                                                </span>
                                            @endif

                                            @if(!$report->is_late && !$report->is_late_submit)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black bg-emerald-100 text-emerald-800 mt-1 uppercase border border-emerald-200">
                                                    Tepat Waktu
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full {{ $report->status == 'completed' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-amber-100 text-amber-800 border border-amber-200' }}">
                                                {{ ucfirst($report->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end gap-2">
                                                <a href="{{ route('reports.show', $report->id) }}" class="inline-flex items-center px-4 py-2 bg-white/80 text-sky-700 hover:bg-sky-50 font-bold rounded-xl transition border border-sky-200 shadow-sm">
                                                    Periksa Laporan
                                                </a>
                                                <button type="button" onclick="openDeleteModal('{{ route('reports.destroy', $report->id) }}')" class="inline-flex items-center px-4 py-2 bg-rose-50 text-rose-700 hover:bg-rose-100 font-bold rounded-xl transition border border-rose-200 shadow-sm">
                                                    Hapus
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-slate-500 font-medium">
                                            Tidak ada laporan masuk untuk hotel ini pada tanggal atau filter yang dipilih.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="deleteModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden items-center justify-center transition-opacity">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm p-6 transform transition-transform scale-100">
            <div class="text-center">
                <div class="w-16 h-16 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center text-3xl mx-auto mb-4 border-4 border-white shadow-sm">
                    ⚠️
                </div>
                <h3 class="text-lg font-black text-slate-800 mb-2">Hapus Laporan?</h3>
                <p class="text-sm text-slate-500 font-medium mb-6">Tindakan ini tidak bisa dibatalkan. Semua foto di dalam laporan ini juga akan terhapus dari server.</p>
                <div class="flex gap-3">
                    <button type="button" onclick="closeDeleteModal()" class="flex-1 px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
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
        });
    </script>
</x-app-layout>
