<x-app-layout>
    <div class="min-h-screen bg-sky-950/5 flex">

        <!-- SIDEBAR KIRI ADMIN -->
        <aside class="w-64 bg-sky-100/85 backdrop-blur-md text-slate-700 flex flex-col justify-between hidden md:flex flex-shrink-0 shadow-sm border-r border-sky-200/60">
            <div>
                <!-- Logo / Brand Hotel -->
                <div class="p-6 border-b border-sky-200/60 flex items-center gap-3">
                    <div class="w-10 h-10 bg-sky-600 text-white rounded-xl flex items-center justify-center font-black text-xl shadow-sm">
                        H
                    </div>
                    <div>
                        <h1 class="font-black text-slate-800 text-base tracking-wide">Hotel Management</h1>
                        <p class="text-[10px] text-sky-700 font-bold uppercase tracking-widest">System Admin</p>
                    </div>
                </div>

                <!-- PILIHAN HOTEL -->
                <div class="px-4 py-3 bg-sky-200/40 border-b border-sky-200/60">
                    <label class="block text-[10px] font-black text-sky-800 uppercase tracking-wider mb-1.5">Pilih Hotel Aktif</label>
                    <select id="hotel-selector" class="w-full bg-white/90 border border-sky-300 text-slate-800 font-bold rounded-xl text-xs py-2 px-3 focus:border-sky-500 focus:ring-sky-500 cursor-pointer shadow-sm">
                        <option value="wahyu" {{ ($currentHotel ?? 'wahyu') == 'wahyu' ? 'selected' : '' }}>Hotel Wahyu</option>
                        <option value="nirwana" {{ ($currentHotel ?? 'wahyu') == 'nirwana' ? 'selected' : '' }}>Hotel Nirwana</option>
                    </select>
                </div>

                <!-- MENU UTAMA -->
                <div class="px-4 py-6 space-y-1.5">
                    <p class="px-3 text-[10px] font-black text-sky-800/70 uppercase tracking-wider mb-2">Main Menu</p>

                    <a href="{{ route('admin.dashboard', ['hotel' => $currentHotel ?? 'wahyu']) }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-sky-600 text-white font-bold text-sm shadow-sm">
                        <span>📊</span> Dashboard
                    </a>
                    <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-sky-200/60 text-slate-600 hover:text-slate-900 font-semibold text-sm transition">
                        <span>🏨</span> Data Hotel
                    </a>
                    <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-sky-200/60 text-slate-600 hover:text-slate-900 font-semibold text-sm transition">
                        <span>👥</span> Manajemen Staf
                    </a>
                    <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-sky-200/60 text-slate-600 hover:text-slate-900 font-semibold text-sm transition">
                        <span>⏱️</span> Pengaturan Shift
                    </a>
                    <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-sky-200/60 text-slate-600 hover:text-slate-900 font-semibold text-sm transition">
                        <span>📋</span> Master Tugas (SOP)
                    </a>
                    <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-sky-200/60 text-slate-600 hover:text-slate-900 font-semibold text-sm transition">
                        <span>📈</span> Rangkuman Laporan
                    </a>
                </div>
            </div>

            <!-- FOOTER SIDEBAR: LOGOUT -->
            <div class="p-4 border-t border-sky-200/60 bg-sky-200/20">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center justify-center gap-2 bg-rose-500/10 hover:bg-rose-500/20 text-rose-600 font-bold py-3 px-4 rounded-xl transition border border-rose-500/20 text-xs">
                        <span>🚪</span> Log Out System
                    </button>
                </form>
            </div>
        </aside>

        <!-- KONTEN UTAMA DASHBOARD -->
        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">

            <!-- HEADER ATAS (Bersih & Rapat ke Kiri) -->
            <header class="bg-sky-50/30 backdrop-blur-md border-b border-sky-100 h-16 flex items-center justify-between px-6 shadow-sm">
                <div class="flex items-center">
                    <span class="font-extrabold text-xl text-blue-700 tracking-tight">Laporan Harian</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs font-bold text-slate-500">Login as: <span class="text-sky-700 font-black">{{ Auth::user()->name }}</span></span>
                </div>
            </header>

            <div class="p-8 space-y-6">

                <!-- STATISTIK KARTU -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-sky-50/40 backdrop-blur-md p-6 rounded-2xl shadow-sm border border-sky-200/60 flex items-center gap-4">
                        <div class="w-12 h-12 bg-sky-100/70 text-sky-700 rounded-xl flex items-center justify-center text-xl font-bold border border-sky-200">
                            👥
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Staf Terdaftar</p>
                            <p class="text-2xl font-black text-slate-800">{{ $totalStaff }} Orang</p>
                        </div>
                    </div>

                    <div class="bg-sky-50/40 backdrop-blur-md p-6 rounded-2xl shadow-sm border border-sky-200/60 flex items-center gap-4">
                        <div class="w-12 h-12 bg-emerald-100/70 text-emerald-700 rounded-xl flex items-center justify-center text-xl font-bold border border-emerald-200">
                            ✅
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Sudah Submit Hari Ini</p>
                            <p class="text-2xl font-black text-emerald-800">{{ $submittedCount }} Laporan</p>
                        </div>
                    </div>

                    <div class="bg-sky-50/40 backdrop-blur-md p-6 rounded-2xl shadow-sm border border-sky-200/60 flex items-center gap-4">
                        <div class="w-12 h-12 bg-rose-100/70 text-rose-700 rounded-xl flex items-center justify-center text-xl font-bold border border-rose-200">
                            ⚠️
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Tercatat Terlambat</p>
                            <p class="text-2xl font-black text-rose-700">{{ $lateCount }} Laporan</p>
                        </div>
                    </div>
                </div>

                <!-- PANEL FILTER MONITORING -->
                <div class="bg-sky-50/40 backdrop-blur-md p-6 rounded-2xl shadow-sm border border-sky-200/60">
                    <form method="GET" action="{{ route('admin.dashboard') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                        <input type="hidden" name="hotel" value="{{ $currentHotel ?? 'wahyu' }}">

                        <div>
                            <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">Tanggal Laporan</label>
                            <input type="date" name="date" value="{{ request('date', date('Y-m-d')) }}" class="w-full border-sky-200 bg-white/80 text-slate-800 font-semibold rounded-xl shadow-sm py-2.5 px-3 text-sm focus:border-sky-500 focus:ring-sky-500">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">Departemen</label>
                            <select name="department" class="w-full border-sky-200 bg-white/80 text-slate-800 font-semibold rounded-xl shadow-sm py-2.5 px-3 text-sm focus:border-sky-500 focus:ring-sky-500">
                                <option value="">Semua Departemen</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">Status</label>
                            <select name="status" class="w-full border-sky-200 bg-white/80 text-slate-800 font-semibold rounded-xl shadow-sm py-2.5 px-3 text-sm focus:border-sky-500 focus:ring-sky-500">
                                <option value="">Semua Status</option>
                                <option value="planned" {{ request('status') == 'planned' ? 'selected' : '' }}>Planned (Belum Submit)</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed (Selesai)</option>
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 bg-sky-600 hover:bg-sky-700 text-white font-bold py-2.5 px-4 rounded-xl shadow-md transition text-sm">
                                Filter
                            </button>
                            <a href="{{ route('admin.dashboard', ['hotel' => $currentHotel ?? 'wahyu']) }}" class="bg-white/80 hover:bg-white text-slate-700 font-bold py-2.5 px-4 rounded-xl transition text-sm flex items-center justify-center border border-sky-200" title="Reset Filter">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>

                <!-- TABEL MONITORING LAPORAN STAF -->
                <div class="bg-sky-50/40 backdrop-blur-md rounded-2xl shadow-sm border border-sky-200/60 overflow-hidden">
                    <div class="p-6 border-b border-sky-200/60 flex justify-between items-center bg-sky-100/30">
                        <h3 class="text-lg font-black text-slate-800">
                            Dokumentasi Laporan Masuk — <span class="text-sky-700 uppercase">{{ $currentHotel ?? 'wahyu' }}</span>
                        </h3>
                        <span class="text-xs font-bold text-sky-700 uppercase tracking-wider">Total Ditampilkan: {{ count($reports) }}</span>
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
                                @forelse($reports as $report)
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
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black bg-rose-100 text-rose-800 mt-1 uppercase border border-rose-200">
                                                    Terlambat
                                                </span>
                                            @else
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
                                            <a href="{{ route('reports.show', $report->id) }}" class="inline-flex items-center px-4 py-2 bg-white/80 text-sky-700 hover:bg-sky-50 font-bold rounded-xl transition border border-sky-200 shadow-sm">
                                                Periksa Laporan
                                            </a>
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

    <!-- Script Interaktif untuk Pergantian Hotel -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const hotelSelector = document.getElementById('hotel-selector');
            if (hotelSelector) {
                hotelSelector.addEventListener('change', function() {
                    const selectedHotel = this.value;
                    window.location.href = "{{ route('admin.dashboard') }}?hotel=" + selectedHotel;
                });
            }
        });
    </script>
</x-app-layout>
