<x-app-layout>
    <style>
        [x-cloak] { display: none !important; }
    </style>

    <x-slot name="header">
        <h2 class="font-extrabold text-2xl text-slate-800 dark:text-white leading-tight">{{ __('Dashboard Karyawan') }}</h2>
    </x-slot>

    <div class="py-12 bg-sky-50/60 dark:bg-slate-900 min-h-screen transition-colors duration-300" x-data="{ openReportModal: null, imageModalOpen: false, imageModalSrc: '', showSuccessPopup: {{ session('success') ? 'true' : 'false' }}, showErrorPopup: {{ $errors->any() ? 'true' : 'false' }} }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-md p-8 rounded-2xl shadow-sm border border-sky-100 dark:border-slate-700 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 rounded-2xl overflow-hidden bg-sky-600 flex items-center justify-center text-white text-2xl font-black shadow-md border-2 border-white dark:border-slate-700 flex-shrink-0">
                        @if(!empty($user->avatar))
                            <img src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        @endif
                    </div>
                    <div>
                        <p class="text-xs font-bold text-sky-600 dark:text-sky-400 uppercase tracking-wider">Selamat Datang Kembali,</p>
                        <h1 class="text-2xl font-black text-slate-800 dark:text-white mt-0.5">{{ $user->name }}</h1>
                        <p class="text-sm font-semibold text-sky-700 dark:text-sky-300 mt-0.5">Role: {{ ucfirst($user->role) }} | Departemen: {{ $user->department }}</p>
                    </div>
                </div>
                <div>
                    @if(!$todayReportCompleted && !$todayReportPlanned)
                        <a href="{{ route('reports.create') }}" class="inline-flex items-center bg-sky-600 hover:bg-sky-700 text-white font-extrabold py-3.5 px-6 rounded-xl shadow-lg shadow-sky-600/20 transition duration-150">+ Buat Laporan Harian</a>
                    @elseif($todayReportPlanned)
                        <a href="{{ route('reports.show', $todayReportPlanned->id) }}" class="inline-flex items-center bg-amber-600 hover:bg-amber-700 text-white font-extrabold py-3.5 px-6 rounded-xl shadow-lg shadow-amber-600/20 transition duration-150">Lanjutkan Laporan Shift</a>
                    @else
                        <span class="inline-flex items-center bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 font-extrabold py-3.5 px-6 rounded-xl border border-emerald-200 dark:border-emerald-800">✓ Shift Hari Ini Selesai</span>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-md p-6 rounded-2xl shadow-sm border border-sky-100 dark:border-slate-700 flex items-center gap-4">
                    <div class="w-12 h-12 bg-sky-50 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 rounded-xl flex items-center justify-center text-xl font-bold border border-sky-100 dark:border-sky-800">📋</div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Laporan</p>
                        <p class="text-2xl font-black text-slate-800 dark:text-white">{{ $totalReports }}</p>
                    </div>
                </div>

                <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-md p-6 rounded-2xl shadow-sm border border-sky-100 dark:border-slate-700 flex items-center gap-4">
                    <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 rounded-xl flex items-center justify-center text-xl font-bold border border-indigo-100 dark:border-indigo-800">🕒</div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Shift Aktif</p>
                        <p class="text-lg font-black text-indigo-700 dark:text-indigo-400">{{ $shiftName }}</p>
                    </div>
                </div>

                <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-md p-6 rounded-2xl shadow-sm border border-sky-100 dark:border-slate-700 flex items-center gap-4">
                    <div class="w-12 h-12 bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-xl flex items-center justify-center text-xl font-bold border border-amber-100 dark:border-amber-800">⏳</div>
                    <div>
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Mulai Isi Todo</p>
                        <p class="text-lg font-black text-slate-800 dark:text-white">{{ $todoDeadline }} WIB</p>
                    </div>
                </div>

                @if($todayReportCompleted)
                    <div class="bg-emerald-50/90 dark:bg-emerald-900/20 backdrop-blur-md p-6 rounded-2xl shadow-sm border border-emerald-200 dark:border-emerald-800 flex flex-col justify-center items-center text-center relative overflow-hidden">
                        <p class="text-[10px] font-black text-emerald-600 dark:text-emerald-500 uppercase tracking-wider mb-1">Skor Shift Hari Ini</p>
                        <p class="text-3xl font-black text-emerald-800 dark:text-emerald-400 leading-none">{{ $todayReport->total_score ?? 0 }}</p>
                        <p class="text-[10px] font-bold text-emerald-600 dark:text-emerald-500 mt-1">Selesai & Aman 🎉</p>
                    </div>
                @else
                    <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-md p-6 rounded-2xl shadow-sm border border-sky-100 dark:border-slate-700 flex flex-col justify-center items-center text-center relative overflow-hidden">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-1">Batas Akhir Submit</p>
                        <p class="text-2xl font-black text-rose-600 dark:text-rose-400 leading-none">{{ $submitDeadlineTime }}</p>
                        <div class="mt-1.5 text-[10px] font-bold text-slate-600 dark:text-slate-300 bg-sky-50 dark:bg-slate-700 border border-sky-100 dark:border-slate-600 px-2 py-0.5 rounded flex items-center gap-1">
                            <span>Sisa:</span> <span id="countdown-timer" class="font-black text-rose-600 dark:text-rose-400">--:--:--</span>
                        </div>
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-md p-6 rounded-2xl shadow-sm border border-sky-100 dark:border-slate-700">
                        <form method="GET" action="{{ route('dashboard') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
                            <div>
                                <label class="block text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Tanggal Spesifik</label>
                                <input type="date" name="date" value="{{ request('date') }}" class="w-full border-sky-200 dark:border-slate-600 bg-sky-50/40 dark:bg-slate-700 text-slate-800 dark:text-white font-semibold rounded-xl shadow-sm py-2 px-3 text-sm focus:border-sky-500 focus:ring-sky-500">
                            </div>
                            <div>
                                <label class="block text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Bulan</label>
                                <select name="month" class="w-full border-sky-200 dark:border-slate-600 bg-sky-50/40 dark:bg-slate-700 text-slate-800 dark:text-white font-semibold rounded-xl shadow-sm py-2 px-3 text-sm focus:border-sky-500 focus:ring-sky-500">
                                    <option value="">Semua Bulan</option>
                                    @for($i=1; $i<=12; $i++)
                                        <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $i, 10)) }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Tahun</label>
                                <select name="year" class="w-full border-sky-200 dark:border-slate-600 bg-sky-50/40 dark:bg-slate-700 text-slate-800 dark:text-white font-semibold rounded-xl shadow-sm py-2 px-3 text-sm focus:border-sky-500 focus:ring-sky-500">
                                    <option value="">Semua Tahun</option>
                                    @foreach($years as $yr)
                                        <option value="{{ $yr }}" {{ request('year') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="flex gap-2">
                                <button type="submit" class="flex-1 bg-sky-600 hover:bg-sky-700 text-white font-bold py-2 px-4 rounded-xl shadow-md transition text-sm">Filter</button>
                                <a href="{{ route('dashboard') }}" class="bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-bold py-2 px-4 rounded-xl transition text-sm flex items-center justify-center">Reset</a>
                            </div>
                        </form>
                    </div>

                    <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-md p-6 rounded-2xl shadow-sm border border-sky-100 dark:border-slate-700">
                        <div class="border-b border-slate-100 dark:border-slate-700 pb-4 mb-5 flex items-center gap-3">
                            <div class="w-10 h-10 bg-sky-50 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 rounded-xl flex items-center justify-center text-lg font-bold border border-sky-100 dark:border-sky-800">📋</div>
                            <div>
                                <h3 class="text-lg font-black text-slate-800 dark:text-white">Form Laporan & Catatan</h3>
                                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kirim Catatan & Bukti Foto Ke Admin</p>
                            </div>
                        </div>

                        <form action="{{ route('notes.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                            @csrf
                            <div>
                                <label class="block text-xs font-black text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-2">Kategori Laporan</label>
                                <select name="category" class="w-full border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 text-slate-800 dark:text-white font-bold rounded-xl shadow-sm py-2 px-3 text-sm focus:border-sky-500 focus:ring-sky-500 transition" required>
                                    <option value="" disabled selected>-- Pilih Kategori --</option>
                                    <option value="Kerusakan">🛠️ Laporan Kerusakan (Engineering)</option>
                                    <option value="Temuan Barang">📦 Temuan Barang (Lost & Found)</option>
                                    <option value="Kendala Tamu">🗣️ Kendala Tamu (Guest Complaint)</option>
                                    <option value="Operasional">📝 Catatan Operasional Shift</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-black text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-2">Judul Laporan</label>
                                <input type="text" name="title" class="w-full border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 text-slate-800 dark:text-white font-semibold rounded-xl shadow-sm py-2 px-3 text-sm focus:border-sky-500 focus:ring-sky-500 transition" placeholder="Contoh: Sanyo Rusak / Kunci Hilang" required>
                            </div>

                            <div>
                                <label class="block text-xs font-black text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-2">Detail Catatan</label>
                                <textarea name="message" rows="3" class="w-full border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 text-slate-800 dark:text-white font-semibold rounded-xl shadow-sm py-2 px-4 text-sm focus:border-sky-500 focus:ring-sky-500 transition" placeholder="Tuliskan detail laporan atau catatan secara lengkap..." required></textarea>
                            </div>

                            <div>
                                <label class="block text-xs font-black text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-2">Bukti Foto (Opsional)</label>
                                <input type="file" name="image" accept="image/*" class="w-full border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-semibold rounded-xl shadow-sm py-1.5 px-3 text-sm focus:border-sky-500 focus:ring-sky-500 transition file:mr-4 file:py-1 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-sky-100 dark:file:bg-slate-600 file:text-sky-700 dark:file:text-sky-300 hover:file:bg-sky-200 dark:hover:file:bg-slate-500">
                            </div>

                            <div class="flex justify-end pt-2">
                                <button type="submit" class="bg-sky-600 hover:bg-sky-700 text-white font-bold py-2 px-6 rounded-xl shadow-md transition text-sm">Kirim Laporan</button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-gradient-to-br from-sky-500 to-indigo-600 dark:from-sky-700 dark:to-indigo-900 rounded-2xl shadow-md border border-sky-400 dark:border-sky-800 p-6 text-white relative overflow-hidden">
                        <div class="relative z-10 flex items-center justify-between">
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-wider text-sky-100 mb-1">Total Poin Saya</p>
                                <p class="text-4xl font-black">{{ $myTotalPoints }} <span class="text-sm font-bold text-sky-200">Pts</span></p>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] font-black uppercase tracking-wider text-sky-100 mb-1">Peringkat</p>
                                <p class="text-3xl font-black">#{{ $myRank }}</p>
                            </div>
                        </div>
                        <div class="absolute -right-4 -bottom-4 opacity-10 text-8xl">⭐</div>
                    </div>

                    <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-md rounded-2xl shadow-sm border border-sky-100 dark:border-slate-700 overflow-hidden flex flex-col">
                        <div class="p-4 border-b border-sky-100 dark:border-slate-700 bg-sky-50/50 dark:bg-slate-800/50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <h3 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-wider flex-shrink-0">🏆 Leaderboard</h3>
                            <form method="GET" action="{{ route('dashboard') }}" class="flex gap-2">
                                @foreach(request()->except(['lb_month', 'lb_dept']) as $key => $value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endforeach
                                <select name="lb_dept" onchange="this.form.submit()" class="border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 text-[10px] font-bold rounded-lg shadow-sm py-1.5 px-2 focus:ring-sky-500 transition">
                                    <option value="">Semua Dept</option>
                                    @foreach($lbDepartments as $dept)
                                        <option value="{{ $dept }}" {{ request('lb_dept', $lbDept) == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                                    @endforeach
                                </select>
                                <select name="lb_month" onchange="this.form.submit()" class="border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 text-[10px] font-bold rounded-lg shadow-sm py-1.5 px-2 focus:ring-sky-500 transition">
                                    @for($i=1; $i<=12; $i++)
                                        <option value="{{ $i }}" {{ request('lb_month', $lbMonth) == $i ? 'selected' : '' }}>{{ date('M', mktime(0, 0, 0, $i, 10)) }}</option>
                                    @endfor
                                </select>
                            </form>
                        </div>
                        <div class="p-5 flex-1 flex flex-col gap-4">
                            @forelse($leaderboard as $index => $leader)
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center font-black {{ $index == 0 ? 'bg-amber-100 text-amber-600 dark:bg-amber-900/40' : ($index == 1 ? 'bg-slate-100 text-slate-500 dark:bg-slate-700' : ($index == 2 ? 'bg-orange-100 text-orange-600 dark:bg-orange-900/40' : 'bg-sky-50 text-sky-600 dark:bg-slate-800')) }}">
                                        #{{ $index + 1 }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-bold text-slate-800 dark:text-slate-200 truncate">{{ $leader->user->name }}</p>
                                        <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider truncate">{{ $leader->user->department }}</p>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-sm font-black text-sky-600 dark:text-sky-400">{{ $leader->total_points }}</span>
                                        <span class="text-[10px] text-slate-400 block">Pts</span>
                                    </div>
                                </div>
                                @if(!$loop->last) <div class="border-b border-slate-100 dark:border-slate-700"></div> @endif
                            @empty
                                <div class="text-center py-6 text-slate-400 text-sm font-medium">Belum ada data bulan ini.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>

            <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-md rounded-2xl shadow-sm border border-sky-100 dark:border-slate-700 overflow-hidden">
                <div class="p-6 border-b border-sky-100 dark:border-slate-700 flex justify-between items-center bg-sky-50/30 dark:bg-slate-800">
                    <h3 class="text-lg font-black text-slate-800 dark:text-white">Riwayat Laporan</h3>
                    <span class="text-xs font-bold text-sky-600 dark:text-sky-400 uppercase tracking-wider">Total Ditemukan: {{ count($reports) }}</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-sky-50/60 dark:bg-slate-700/50 border-b border-sky-100 dark:border-slate-600">
                                <th class="px-6 py-4 text-xs font-black text-slate-400 dark:text-slate-400 uppercase tracking-wider">Waktu Submit</th>
                                <th class="px-6 py-4 text-xs font-black text-slate-400 dark:text-slate-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-xs font-black text-slate-400 dark:text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-sky-100 dark:divide-slate-700">
                            @forelse($reports as $report)
                                <tr class="hover:bg-sky-50/40 dark:hover:bg-slate-700/50 transition">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ \Carbon\Carbon::parse($report->created_at)->timezone('Asia/Jakarta')->format('Y-m-d') }}</div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400 font-medium">Jam: {{ \Carbon\Carbon::parse($report->created_at)->timezone('Asia/Jakarta')->format('H:i') }} WIB</div>
                                        @if($report->is_late)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400 mt-1 uppercase border border-rose-200 dark:border-rose-800">Terlambat</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 mt-1 uppercase border border-emerald-200 dark:border-emerald-800">Tepat Waktu</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full {{ $report->status == 'completed' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800' }}">
                                            {{ ucfirst($report->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button type="button" @click="openReportModal = {{ $report->id }}" class="inline-flex items-center px-4 py-2 bg-sky-50 dark:bg-slate-700 text-sky-700 dark:text-sky-300 hover:bg-sky-100 dark:hover:bg-slate-600 font-bold rounded-xl transition border border-sky-200 dark:border-slate-600">Lihat Detail</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center text-slate-400 dark:text-slate-500 font-medium">Tidak ada riwayat laporan yang sesuai dengan filter.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        @foreach($reports as $r)
            @php
                $baseS = 0;
                $totStd = $r->items->where('is_additional', 0)->count();
                $totPend = $r->items->where('is_additional', 0)->where('status', 'pending')->count();
                $totComp = $r->items->where('is_additional', 0)->where('status', 'completed')->count();
                $valDenom = $totStd - $totPend;
                if ($valDenom > 0) { $baseS = ($totComp / $valDenom) * 100; } elseif ($valDenom === 0 && $totStd > 0) { $baseS = 100; }

                $bonusS = $r->items->where('is_additional', 1)->where('status', 'completed')->count() * 10;
                $penaltyLate = $r->is_late ? 15 : 0;
                $penaltySubmit = $r->is_late_submit ? 15 : 0;
                $totalPenalty = $penaltyLate + $penaltySubmit;
            @endphp
            <div x-cloak x-show="openReportModal === {{ $r->id }}" class="fixed inset-0 z-[80] flex items-center justify-center">
                <div x-show="openReportModal === {{ $r->id }}" x-transition class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm" @click="openReportModal = null"></div>

                <div x-show="openReportModal === {{ $r->id }}" x-transition class="relative z-[90] bg-white dark:bg-slate-800 rounded-3xl shadow-2xl w-full max-w-5xl mx-4 max-h-[90vh] flex flex-col overflow-hidden border border-slate-200 dark:border-slate-700">
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

                        @if($r->status == 'completed')
                        <div class="mb-6 bg-sky-50/50 dark:bg-slate-800 border border-sky-100 dark:border-slate-700 rounded-xl p-4 shadow-sm">
                            <h4 class="text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3">Rincian Skor Performa Anda</h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="bg-white dark:bg-slate-900/50 p-3 rounded-lg border border-slate-200 dark:border-slate-700">
                                    <span class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 mb-1">SKOR DASAR (SOP)</span>
                                    <span class="text-lg font-black text-slate-800 dark:text-white" id="base-score-display-{{ $r->id }}">{{ round($baseS) }}</span>
                                </div>
                                <div class="bg-emerald-50 dark:bg-emerald-900/20 p-3 rounded-lg border border-emerald-100 dark:border-emerald-800/30">
                                    <span class="block text-[10px] font-bold text-emerald-600 dark:text-emerald-400 mb-1">BONUS (EXTRA TASKS)</span>
                                    <span class="text-lg font-black text-emerald-700 dark:text-emerald-300" id="bonus-score-display-{{ $r->id }}">+{{ $bonusS }}</span>
                                </div>
                                <div class="bg-rose-50 dark:bg-rose-900/20 p-3 rounded-lg border border-rose-100 dark:border-rose-800/30">
                                    <span class="block text-[10px] font-bold text-rose-600 dark:text-rose-400 mb-1">PINALTI TERLAMBAT</span>
                                    <span class="text-lg font-black text-rose-700 dark:text-rose-300" id="penalty-score-display-{{ $r->id }}">{{ $totalPenalty == 0 ? '0' : '-'.$totalPenalty }}</span>
                                </div>
                                <div class="bg-sky-100 dark:bg-sky-900/40 p-3 rounded-lg border border-sky-200 dark:border-sky-800">
                                    <span class="block text-[10px] font-bold text-sky-700 dark:text-sky-300 mb-1">TOTAL SKOR AKHIR</span>
                                    <span class="text-xl font-black text-sky-800 dark:text-sky-400" id="total-score-display-{{ $r->id }}">{{ $r->total_score ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="flex justify-between items-center mb-4">
                            <h4 class="font-black text-slate-800 dark:text-white">Daftar Pekerjaan</h4>
                            <span class="inline-block bg-white dark:bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm text-xs font-bold text-slate-600 dark:text-slate-400">
                                📅 {{ \Carbon\Carbon::parse($r->created_at)->translatedFormat('d M Y') }} &nbsp;|&nbsp; ⏰ {{ \Carbon\Carbon::parse($r->created_at)->format('H:i') }} WIB
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($r->items as $item)
                                <div id="item-block-{{ $item->id }}" class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200/60 dark:border-slate-700 p-4 flex flex-col justify-between hover:border-sky-300 dark:hover:border-sky-600 transition duration-300 relative">

                                    <div class="mt-2">
                                        <div class="flex items-start justify-between mb-2 gap-2">
                                            <h5 class="text-sm font-bold text-slate-800 dark:text-white leading-tight">
                                                {{ $item->task ? $item->task->name : ($item->task_name ?? 'Tugas Tambahan') }}
                                            </h5>
                                            <div class="flex items-center gap-1 flex-shrink-0">
                                                @if($item->status == 'completed')
                                                    <span class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-[10px] font-black rounded uppercase">Selesai</span>
                                                @else
                                                    <span class="px-2 py-0.5 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 text-[10px] font-black rounded uppercase">Pending</span>
                                                @endif
                                                @if($item->is_additional)
                                                    <span class="px-2 py-0.5 bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-400 text-[10px] font-black rounded uppercase">Extra</span>
                                                @endif
                                            </div>
                                        </div>
                                        @if($item->notes)
                                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium bg-slate-50 dark:bg-slate-900/50 p-2 rounded-lg mb-3 border border-slate-100 dark:border-slate-700">{{ $item->notes }}</p>
                                        @endif
                                        @if($item->obstacle_note)
                                            <p class="text-xs text-rose-600 dark:text-rose-400 font-medium bg-rose-50 dark:bg-rose-900/30 p-2 rounded-lg mb-3 border border-rose-100 dark:border-rose-800/30">Kendala: {{ $item->obstacle_note }}</p>
                                        @endif
                                    </div>

                                    <div class="flex gap-2 mt-auto pt-3 border-t border-slate-100 dark:border-slate-700">
                                        <div class="flex-1">
                                            <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase mb-1">Sebelum</p>
                                            @if($item->before_image)
                                                <img src="{{ asset('storage/' . $item->before_image) }}" @click="imageModalSrc = '{{ asset('storage/' . $item->before_image) }}'; imageModalOpen = true" class="w-full h-20 object-cover rounded-lg cursor-zoom-in hover:opacity-80 transition shadow-sm border border-slate-100 dark:border-slate-700" loading="lazy">
                                            @else
                                                <div class="w-full h-20 bg-slate-100 dark:bg-slate-700/50 rounded-lg flex items-center justify-center text-xs text-slate-400 dark:text-slate-500 font-bold border border-slate-200 dark:border-slate-700">Kosong</div>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase mb-1">Sesudah</p>
                                            @if($item->after_image)
                                                <img src="{{ asset('storage/' . $item->after_image) }}" @click="imageModalSrc = '{{ asset('storage/' . $item->after_image) }}'; imageModalOpen = true" class="w-full h-20 object-cover rounded-lg cursor-zoom-in hover:opacity-80 transition shadow-sm border border-slate-100 dark:border-slate-700" loading="lazy">
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
                <img @click="imageModalOpen = false" :src="imageModalSrc" class="max-h-[90vh] w-auto max-w-full rounded-2xl shadow-2xl border-4 border-white/10 object-contain cursor-zoom-out" title="Klik untuk menutup" loading="lazy">
            </div>
        </div>

        <div x-cloak x-show="showSuccessPopup" class="fixed inset-0 z-[100] flex items-center justify-center">
            <div x-show="showSuccessPopup" x-transition class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showSuccessPopup = false"></div>
            <div x-show="showSuccessPopup" x-transition class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl w-full max-w-sm p-6 relative z-[110] text-center border border-slate-100 dark:border-slate-700">
                <div class="w-16 h-16 rounded-full bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 flex items-center justify-center text-3xl mx-auto mb-4 shadow-inner">✅</div>
                <h3 class="text-xl font-black text-slate-800 dark:text-white mb-2">Berhasil!</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mb-6">{{ session('success') }}</p>
                <button @click="showSuccessPopup = false" class="w-full px-4 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-2xl transition shadow-lg shadow-emerald-600/30">Tutup</button>
            </div>
        </div>

        <div x-cloak x-show="showErrorPopup" class="fixed inset-0 z-[100] flex items-center justify-center">
            <div x-show="showErrorPopup" x-transition class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showErrorPopup = false"></div>
            <div x-show="showErrorPopup" x-transition class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl w-full max-w-sm p-6 relative z-[110] text-center border border-slate-100 dark:border-slate-700">
                <div class="w-16 h-16 rounded-full bg-rose-100 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 flex items-center justify-center text-3xl mx-auto mb-4 shadow-inner">⚠️</div>
                <h3 class="text-xl font-black text-slate-800 dark:text-white mb-2">Gagal Mengirim!</h3>
                <div class="text-sm text-rose-600 dark:text-rose-400 font-medium mb-6 text-left bg-rose-50 dark:bg-rose-900/20 p-4 rounded-xl border border-rose-100 dark:border-rose-800/50">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button @click="showErrorPopup = false" class="w-full px-4 py-3 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-2xl transition shadow-lg shadow-rose-600/30">Perbaiki Form</button>
            </div>
        </div>

    </div>

    <script>
        const deadlineTimeStr = "{{ $submitDeadlineTime ?? '15:30' }}";

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
                timerEl.classList.add('text-rose-700', 'dark:text-rose-400', 'animate-pulse');
                return;
            }

            const hours = String(Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, '0');
            const minutes = String(Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
            const seconds = String(Math.floor((diff % (1000 * 60)) / 1000)).padStart(2, '0');

            timerEl.textContent = `${hours}:${minutes}:${seconds}`;
        }

        setInterval(updateCountdown, 1000);
        updateCountdown();
    </script>
</x-app-layout>
