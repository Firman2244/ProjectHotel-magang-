<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-2xl text-slate-800 leading-tight">
            {{ __('Dashboard Karyawan') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-sky-50/60 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4 rounded-2xl relative shadow-sm" role="alert">
                    <span class="block sm:inline font-bold">{{ session('success') }}</span>
                </div>
            @endif

            <!-- KARTU PROFIL USER -->
            <div class="bg-white/90 backdrop-blur-md p-8 rounded-2xl shadow-sm border border-sky-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 rounded-2xl overflow-hidden bg-sky-600 flex items-center justify-center text-white text-2xl font-black shadow-md border-2 border-white flex-shrink-0">
                        @if(!empty($user->avatar))
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        @endif
                    </div>
                    <div>
                        <p class="text-xs font-bold text-sky-600 uppercase tracking-wider">Selamat Datang Kembali,</p>
                        <h1 class="text-2xl font-black text-slate-800 mt-0.5">{{ $user->name }}</h1>
                        <p class="text-sm font-semibold text-sky-700 mt-0.5">Role: {{ ucfirst($user->role) }} | Departemen: {{ $user->department }}</p>
                    </div>
                </div>
                <div>
                    @if(!$todayReportCompleted && !$todayReportPlanned)
                        <a href="{{ route('reports.create') }}" class="inline-flex items-center bg-sky-600 hover:bg-sky-700 text-white font-extrabold py-3.5 px-6 rounded-xl shadow-lg shadow-sky-600/20 transition duration-150">
                            + Buat Laporan Harian
                        </a>
                    @elseif($todayReportPlanned)
                        <a href="{{ route('reports.show', $todayReportPlanned->id) }}" class="inline-flex items-center bg-amber-600 hover:bg-amber-700 text-white font-extrabold py-3.5 px-6 rounded-xl shadow-lg shadow-amber-600/20 transition duration-150">
                            Lanjutkan Laporan Shift
                        </a>
                    @else
                        <span class="inline-flex items-center bg-emerald-100 text-emerald-700 font-extrabold py-3.5 px-6 rounded-xl border border-emerald-200">
                            ✓ Shift Hari Ini Selesai
                        </span>
                    @endif
                </div>
            </div>

            <!-- KOTAK INFORMASI STATISTIK & STATUS SHIFT -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white/90 backdrop-blur-md p-6 rounded-2xl shadow-sm border border-sky-100 flex items-center gap-4">
                    <div class="w-12 h-12 bg-sky-50 text-sky-600 rounded-xl flex items-center justify-center text-xl font-bold border border-sky-100">
                        📋
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Laporan</p>
                        <p class="text-2xl font-black text-slate-800">{{ $totalReports }}</p>
                    </div>
                </div>

                <div class="bg-white/90 backdrop-blur-md p-6 rounded-2xl shadow-sm border border-sky-100 flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-xl font-bold border border-indigo-100">
                        🕒
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Shift Aktif</p>
                        <p class="text-lg font-black text-indigo-700">{{ $shiftName }}</p>
                    </div>
                </div>

                <div class="bg-white/90 backdrop-blur-md p-6 rounded-2xl shadow-sm border border-sky-100 flex items-center gap-4">
                    <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-xl font-bold border border-amber-100">
                        ⏳
                    </div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Mulai Isi Todo</p>
                        <p class="text-lg font-black text-slate-800">{{ $todoDeadline }} WIB</p>
                    </div>
                </div>

                <!-- KARTU DINAMIS: COUNTDOWN ATAU STATUS SELESAI -->
                @if($todayReportCompleted)
                    <div class="bg-emerald-50/90 backdrop-blur-md p-6 rounded-2xl shadow-sm border border-emerald-200 flex flex-col justify-center items-center text-center relative overflow-hidden">
                        <p class="text-xs font-extrabold text-emerald-600 uppercase tracking-wider mb-1">Status Shift</p>
                        <p class="text-xl font-black text-emerald-800">Selesai & Aman 🎉</p>
                        <p class="text-[11px] font-bold text-emerald-600 mt-1">Terima kasih atas kerja kerasmu!</p>
                    </div>
                @else
                    <div class="bg-white/90 backdrop-blur-md p-6 rounded-2xl shadow-sm border border-sky-100 flex flex-col justify-center items-center text-center relative overflow-hidden">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Batas Akhir Submit</p>
                        <p class="text-2xl font-black text-rose-600">{{ $submitDeadlineTime }} WIB</p>
                        <div class="mt-2 text-xs font-bold text-slate-600 bg-sky-50 border border-sky-100 px-3 py-1 rounded-full flex items-center gap-1.5">
                            <span>Sisa:</span> <span id="countdown-timer" class="font-black text-rose-600">--:--:--</span>
                        </div>
                    </div>
                @endif
            </div>

            <!-- PANEL FILTER LAPORAN -->
            <div class="bg-white/90 backdrop-blur-md p-6 rounded-2xl shadow-sm border border-sky-100">
                <form method="GET" action="{{ route('dashboard') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Tanggal Spesifik</label>
                        <input type="date" name="date" value="{{ request('date') }}" class="w-full border-sky-200 bg-sky-50/40 text-slate-800 font-semibold rounded-xl shadow-sm py-2.5 px-3 text-sm focus:border-sky-500 focus:ring-sky-500">
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Bulan</label>
                        <select name="month" class="w-full border-sky-200 bg-sky-50/40 text-slate-800 font-semibold rounded-xl shadow-sm py-2.5 px-3 text-sm focus:border-sky-500 focus:ring-sky-500">
                            <option value="">Semua Bulan</option>
                            <option value="1" {{ request('month') == 1 ? 'selected' : '' }}>Januari</option>
                            <option value="2" {{ request('month') == 2 ? 'selected' : '' }}>Februari</option>
                            <option value="3" {{ request('month') == 3 ? 'selected' : '' }}>Maret</option>
                            <option value="4" {{ request('month') == 4 ? 'selected' : '' }}>April</option>
                            <option value="5" {{ request('month') == 5 ? 'selected' : '' }}>Mei</option>
                            <option value="6" {{ request('month') == 6 ? 'selected' : '' }}>Juni</option>
                            <option value="7" {{ request('month') == 7 ? 'selected' : '' }}>Juli</option>
                            <option value="8" {{ request('month') == 8 ? 'selected' : '' }}>Agustus</option>
                            <option value="9" {{ request('month') == 9 ? 'selected' : '' }}>September</option>
                            <option value="10" {{ request('month') == 10 ? 'selected' : '' }}>Oktober</option>
                            <option value="11" {{ request('month') == 11 ? 'selected' : '' }}>November</option>
                            <option value="12" {{ request('month') == 12 ? 'selected' : '' }}>Desember</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Tahun</label>
                        <select name="year" class="w-full border-sky-200 bg-sky-50/40 text-slate-800 font-semibold rounded-xl shadow-sm py-2.5 px-3 text-sm focus:border-sky-500 focus:ring-sky-500">
                            <option value="">Semua Tahun</option>
                            @foreach($years as $yr)
                                <option value="{{ $yr }}" {{ request('year') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-sky-600 hover:bg-sky-700 text-white font-bold py-2.5 px-4 rounded-xl shadow-md transition text-sm">
                            Filter
                        </button>
                        <a href="{{ route('dashboard') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2.5 px-4 rounded-xl transition text-sm flex items-center justify-center" title="Reset Filter">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- TABEL RIWAYAT LAPORAN -->
            <div class="bg-white/90 backdrop-blur-md rounded-2xl shadow-sm border border-sky-100 overflow-hidden">
                <div class="p-6 border-b border-sky-100 flex justify-between items-center bg-sky-50/30">
                    <h3 class="text-lg font-black text-slate-800">Riwayat Laporan</h3>
                    <span class="text-xs font-bold text-sky-600 uppercase tracking-wider">Total Ditemukan: {{ count($reports) }}</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-sky-50/60 border-b border-sky-100">
                                <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-wider">Waktu Submit</th>
                                <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-xs font-black text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-sky-100">
                            @forelse($reports as $report)
                                <tr class="hover:bg-sky-50/40 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-slate-800">{{ \Carbon\Carbon::parse($report->created_at)->timezone('Asia/Jakarta')->format('Y-m-d') }}</div>
                                        <div class="text-xs text-slate-500 font-medium">Jam: {{ \Carbon\Carbon::parse($report->created_at)->timezone('Asia/Jakarta')->format('H:i') }} WIB</div>
                                        @if($report->is_late)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black bg-rose-100 text-rose-700 mt-1 uppercase border border-rose-200">
                                                Terlambat
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black bg-emerald-100 text-emerald-700 mt-1 uppercase border border-emerald-200">
                                                Tepat Waktu
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full {{ $report->status == 'completed' ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-amber-100 text-amber-700 border border-amber-200' }}">
                                            {{ ucfirst($report->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('reports.show', $report->id) }}" class="inline-flex items-center px-4 py-2 bg-sky-50 text-sky-700 hover:bg-sky-100 font-bold rounded-xl transition border border-sky-200">
                                            Lihat Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center text-slate-400 font-medium">
                                        Tidak ada riwayat laporan yang sesuai dengan filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    <script>
        // Logika Countdown Timer menuju Batas Akhir Submit
        const deadlineTimeStr = "{{ $submitDeadlineTime ?? '15:30' }}"; // contoh: "15:30"

        function updateCountdown() {
            const timerEl = document.getElementById('countdown-timer');
            if (!timerEl) return;

            const now = new Date();
            const [targetHours, targetMinutes] = deadlineTimeStr.split(':').map(Number);

            const targetDate = new Date();
            targetDate.setHours(targetHours, targetMinutes, 0, 0);

            let diff = targetDate - now;

            if (diff <= 0) {
                timerEl.textContent = "Waktu Habis (Terlambat)";
                timerEl.classList.add('text-rose-700', 'animate-pulse');
                return;
            }

            const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60 * 60 / 60));
            const seconds = Math.floor((diff % (1000 * 60)) / 1000);

            timerEl.textContent = `${String(hours).padStart(2, '0')}:${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        }

        setInterval(updateCountdown, 1000);
        updateCountdown();
    </script>
</x-app-layout>
