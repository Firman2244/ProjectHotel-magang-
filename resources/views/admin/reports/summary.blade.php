<x-app-layout>
    <div class="min-h-screen bg-slate-50/50 flex flex-col md:flex-row">
        <x-admin-sidebar />

        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
            <header class="bg-white/80 backdrop-blur-md border-b border-slate-200 h-16 flex items-center justify-between px-6 shadow-sm sticky top-0 z-10">
                <span class="font-extrabold text-xl text-slate-800 tracking-tight">Data Historis Laporan</span>
                <button class="bg-emerald-50 text-emerald-600 border border-emerald-200 font-bold text-xs px-4 py-2 rounded-lg hover:bg-emerald-100 transition" onclick="alert('Fitur Export Excel akan diaktifkan setelah Fase Testing lapangan selesai.')">
                    📥 Export Excel
                </button>
            </header>

            <div class="p-6 md:p-8 space-y-6">
                <!-- A. FITUR FILTER WAKTU & SPESIFIK -->
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                    <form method="GET" action="{{ route('admin.reports.summary') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">Mulai Tanggal</label>
                            <input type="date" name="start_date" value="{{ $startDate }}" class="w-full text-sm font-semibold border-slate-200 rounded-xl bg-slate-50 focus:border-sky-500 focus:ring-sky-500">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                            <input type="date" name="end_date" value="{{ $endDate }}" class="w-full text-sm font-semibold border-slate-200 rounded-xl bg-slate-50 focus:border-sky-500 focus:ring-sky-500">
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">Cabang Hotel</label>
                            <select name="hotel_id" class="w-full text-sm font-semibold border-slate-200 rounded-xl bg-slate-50 focus:border-sky-500 focus:ring-sky-500">
                                <option value="">Semua Cabang</option>
                                @foreach($hotels as $hotel)
                                    <option value="{{ $hotel->id }}" {{ $hotelId == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-1">Departemen</label>
                            <select name="department" class="w-full text-sm font-semibold border-slate-200 rounded-xl bg-slate-50 focus:border-sky-500 focus:ring-sky-500">
                                <option value="">Semua Dept</option>
                                <option value="Housekeeping" {{ $department == 'Housekeeping' ? 'selected' : '' }}>Housekeeping</option>
                                <option value="Engineering" {{ $department == 'Engineering' ? 'selected' : '' }}>Engineering</option>
                                <option value="Front Office" {{ $department == 'Front Office' ? 'selected' : '' }}>Front Office</option>
                            </select>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="flex-1 bg-slate-800 hover:bg-slate-900 text-white text-sm font-bold py-2.5 px-4 rounded-xl transition">Terapkan</button>
                            <a href="{{ route('admin.reports.summary') }}" class="px-4 py-2.5 bg-slate-100 text-slate-600 rounded-xl hover:bg-slate-200 font-bold text-sm border border-slate-200 flex items-center justify-center">Reset</a>
                        </div>
                    </form>
                </div>

                <!-- B. MATRIKS KINERJA & GRAFIK -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Cards -->
                    <div class="space-y-4 lg:col-span-1">
                        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex justify-between items-center border-l-4 border-l-sky-500">
                            <div>
                                <p class="text-xs font-black text-slate-500 uppercase tracking-wider">Total Laporan Masuk</p>
                                <p class="text-3xl font-black text-slate-800 mt-1">{{ $totalKaryawanMasuk }} <span class="text-sm font-bold text-slate-400">Shift</span></p>
                            </div>
                            <div class="w-12 h-12 bg-sky-50 text-sky-600 rounded-full flex items-center justify-center text-xl">📋</div>
                        </div>

                        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex justify-between items-center border-l-4 border-l-emerald-500">
                            <div>
                                <p class="text-xs font-black text-slate-500 uppercase tracking-wider">Tepat Waktu</p>
                                <p class="text-3xl font-black text-emerald-600 mt-1">{{ $laporanTepatWaktu }} <span class="text-sm font-bold text-emerald-300">Shift</span></p>
                            </div>
                            <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center text-xl">✅</div>
                        </div>

                        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 flex justify-between items-center border-l-4 border-l-rose-500">
                            <div>
                                <p class="text-xs font-black text-slate-500 uppercase tracking-wider">Terlambat</p>
                                <p class="text-3xl font-black text-rose-600 mt-1">{{ $laporanTerlambat }} <span class="text-sm font-bold text-rose-300">Shift</span></p>
                            </div>
                            <div class="w-12 h-12 bg-rose-50 text-rose-600 rounded-full flex items-center justify-center text-xl">⚠️</div>
                        </div>

                        <div class="bg-amber-50 p-5 rounded-2xl shadow-sm border border-amber-200 flex justify-between items-center">
                            <div>
                                <p class="text-xs font-black text-amber-700 uppercase tracking-wider">Anomali: Tugas Ekstra</p>
                                <p class="text-3xl font-black text-amber-800 mt-1">{{ $totalTugasTambahan }} <span class="text-sm font-bold text-amber-600/70">Tugas</span></p>
                            </div>
                            <div class="w-12 h-12 bg-white text-amber-600 rounded-full flex items-center justify-center text-xl border border-amber-100 shadow-sm">🚀</div>
                        </div>
                    </div>

                    <!-- Chart -->
                    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 lg:col-span-2">
                        <h3 class="text-sm font-black text-slate-800 mb-4">Grafik Trend Kepatuhan Waktu Harian</h3>
                        <div class="relative h-64 w-full">
                            <canvas id="complianceChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- C. TABEL REKAP PADAT -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
                        <h3 class="text-sm font-black text-slate-800">Detail Rekapitulasi Data</h3>
                        <span class="text-[10px] bg-slate-200 text-slate-600 px-2 py-1 rounded font-bold uppercase">Padat</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-xs">
                            <thead>
                                <tr class="bg-slate-100 text-slate-500">
                                    <th class="px-4 py-2 font-black uppercase tracking-wider border-b border-slate-200">Tgl Laporan</th>
                                    <th class="px-4 py-2 font-black uppercase tracking-wider border-b border-slate-200">Nama Staf</th>
                                    <th class="px-4 py-2 font-black uppercase tracking-wider border-b border-slate-200">Dept | Shift</th>
                                    <th class="px-4 py-2 font-black uppercase tracking-wider border-b border-slate-200">Jam Datang</th>
                                    <th class="px-4 py-2 font-black uppercase tracking-wider border-b border-slate-200">Jam Selesai</th>
                                    <th class="px-4 py-2 font-black uppercase tracking-wider border-b border-slate-200">Status Waktu</th>
                                    <th class="px-4 py-2 font-black uppercase tracking-wider border-b border-slate-200">Total Tugas</th>
                                    <th class="px-4 py-2 font-black uppercase tracking-wider border-b border-slate-200 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($reports as $r)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="px-4 py-2.5 font-bold text-slate-700">{{ \Carbon\Carbon::parse($r->report_date)->format('d/m/Y') }}</td>
                                        <td class="px-4 py-2.5 font-bold text-slate-800">{{ $r->user->name }}</td>
                                        <td class="px-4 py-2.5 text-slate-600">{{ $r->user->department }} (S{{ $r->shift_id }})</td>
                                        <td class="px-4 py-2.5 text-slate-600 font-mono">{{ \Carbon\Carbon::parse($r->created_at)->format('H:i') }}</td>
                                        <td class="px-4 py-2.5 text-slate-600 font-mono">{{ $r->status == 'completed' ? \Carbon\Carbon::parse($r->updated_at)->format('H:i') : '-' }}</td>
                                        <td class="px-4 py-2.5">
                                            @if($r->is_late || $r->is_late_submit)
                                                <span class="text-rose-600 font-bold bg-rose-50 px-2 py-0.5 rounded">Terlambat</span>
                                            @else
                                                <span class="text-emerald-600 font-bold bg-emerald-50 px-2 py-0.5 rounded">Tepat Waktu</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-2.5 font-bold text-slate-700 text-center">{{ $r->items->count() }}</td>
                                        <td class="px-4 py-2.5 text-center">
                                            <a href="{{ route('reports.show', $r->id) }}" class="inline-block bg-sky-50 text-sky-700 border border-sky-200 hover:bg-sky-100 font-bold py-1 px-3 rounded transition" target="_blank">Detail</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-4 py-8 text-center text-slate-500 font-medium">Tidak ada data pada rentang waktu/filter ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Script Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('complianceChart').getContext('2d');

            // Mengambil data dari PHP ke JavaScript
            const labels = @json($chartDates);
            const dataTepat = @json($chartTepat);
            const dataTelat = @json($chartTelat);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Tepat Waktu',
                            data: dataTepat,
                            borderColor: '#059669', // emerald-600
                            backgroundColor: '#059669',
                            tension: 0.3,
                            borderWidth: 3,
                            pointRadius: 4
                        },
                        {
                            label: 'Terlambat',
                            data: dataTelat,
                            borderColor: '#e11d48', // rose-600
                            backgroundColor: '#e11d48',
                            tension: 0.3,
                            borderWidth: 3,
                            pointRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom' }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { stepSize: 1 }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
