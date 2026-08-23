<x-app-layout>
    <style>
        [x-cloak] { display: none !important; }

        /* Custom Scrollbar Super Elegan */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #cbd5e1; /* slate-300 */
            border-radius: 10px;
        }
        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background-color: #475569; /* slate-600 */
        }
    </style>

    <x-slot name="header">
        <h2 class="font-extrabold text-2xl text-slate-800 dark:text-white leading-tight">{{ __('Dashboard Karyawan') }}</h2>
    </x-slot>

    <div class="py-12 bg-sky-50/60 dark:bg-slate-900 min-h-screen" x-data="{ openReportModal: null, resolveTaskModal: null, imageModalOpen: false, imageModalSrc: '', showSuccessPopup: {{ session('success') ? 'true' : 'false' }}, showErrorPopup: {{ session('error') || $errors->any() ? 'true' : 'false' }} }">

        @if(session('success'))
            <div x-cloak x-show="showSuccessPopup" class="fixed inset-0 z-[200] flex items-center justify-center p-4">
                <div x-show="showSuccessPopup" class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm" @click="showSuccessPopup = false"></div>
                <div x-show="showSuccessPopup" class="relative z-[210] bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden border border-slate-200 dark:border-slate-700 p-6 flex flex-col items-center text-center">
                    <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-900/30 rounded-2xl flex items-center justify-center text-3xl mb-4 shadow-inner border border-emerald-200 dark:border-emerald-800">✅</div>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white mb-2">Berhasil!</h3>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-6">{{ session('success') }}</p>
                    <button @click="showSuccessPopup = false" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-emerald-600/30 focus:outline-none">Tutup</button>
                </div>
            </div>
        @endif

        @if(session('error') || $errors->any())
            <div x-cloak x-show="showErrorPopup" class="fixed inset-0 z-[200] flex items-center justify-center p-4">
                <div x-show="showErrorPopup" class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm" @click="showErrorPopup = false"></div>
                <div x-show="showErrorPopup" class="relative z-[210] bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden border border-slate-200 dark:border-slate-700 p-6 flex flex-col items-center text-center">
                    <div class="w-16 h-16 bg-rose-100 dark:bg-rose-900/30 rounded-2xl flex items-center justify-center text-3xl mb-4 shadow-inner border border-rose-200 dark:border-rose-800">⚠️</div>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white mb-2">Perhatian!</h3>
                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-6">{{ session('error') ?? $errors->first() }}</p>
                    <button @click="showErrorPopup = false" class="w-full bg-rose-600 hover:bg-rose-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-rose-600/30 focus:outline-none">Tutup</button>
                </div>
            </div>
        @endif

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if($hasDoubleShiftPermit)
            <div class="bg-gradient-to-r from-amber-500 to-orange-500 rounded-2xl p-5 shadow-lg border border-amber-400 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 relative overflow-hidden">
                <div class="relative z-10 flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center text-2xl animate-bounce">🎟️</div>
                    <div class="text-white">
                        <h3 class="text-lg font-black uppercase tracking-wider">Tiket Lembur Tersedia!</h3>
                        <p class="text-sm font-medium opacity-90">Admin telah menugaskan Anda untuk lembur hari ini.</p>
                    </div>
                </div>
                @if($baseReportCompleted)
                <a href="{{ route('reports.create') }}" class="relative z-10 bg-white text-orange-600 hover:bg-slate-50 font-black py-2.5 px-6 rounded-xl shadow-md text-center w-full md:w-auto">🔥 Ambil Shift Lembur</a>
                @else
                <div class="relative z-10 bg-white/20 text-white font-bold py-2.5 px-4 rounded-xl border border-white/30 text-sm text-center w-full md:w-auto">Selesaikan Shift Utama Dulu</div>
                @endif
                <div class="absolute -right-4 -bottom-4 opacity-10 text-6xl transform rotate-12 pointer-events-none">⭐</div>
            </div>
            @endif

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
                <div class="w-full md:w-auto">
                    @if($todayReportPlanned)
                        <a href="{{ route('reports.show', $todayReportPlanned->id) }}" class="flex md:inline-flex justify-center items-center bg-amber-600 hover:bg-amber-700 text-white font-extrabold py-3.5 px-6 rounded-xl shadow-lg shadow-amber-600/20 w-full md:w-auto">Lanjutkan Laporan Shift</a>
                    @elseif($hasDoubleShiftPermit && $baseReportCompleted)
                        <a href="{{ route('reports.create') }}" class="flex md:inline-flex justify-center items-center bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white font-extrabold py-3.5 px-6 rounded-xl shadow-lg shadow-amber-500/30 border border-amber-400 w-full md:w-auto">🔥 Buat Laporan Lembur</a>
                    @elseif(!$todayReportCompleted)
                        <a href="{{ route('reports.create') }}" class="flex md:inline-flex justify-center items-center bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-extrabold py-3.5 px-6 rounded-xl shadow-lg shadow-emerald-500/30 border border-emerald-400 w-full md:w-auto">+ Buat Laporan Harian</a>
                    @else
                        <span class="flex md:inline-flex justify-center items-center bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 font-extrabold py-3.5 px-6 rounded-xl border border-emerald-200 dark:border-emerald-800 w-full md:w-auto">✓ Shift Hari Ini Selesai</span>
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
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Durasi Shift & Batas Laporan</p>
                        <p class="text-lg font-black text-slate-800 dark:text-white">{{ $todoDeadline }} WIB</p>
                    </div>
                </div>

                @if($todayReportCompleted)
                    <div class="bg-emerald-50/90 dark:bg-emerald-900/20 backdrop-blur-md p-6 rounded-2xl shadow-sm border border-emerald-200 dark:border-emerald-800 flex flex-col justify-center items-center text-center relative overflow-hidden">
                        <p class="text-[10px] font-black text-emerald-600 dark:text-emerald-500 uppercase tracking-wider mb-1">Skor Shift Terakhir</p>
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

            @if($user->department === 'Engineering')
                <div class="bg-rose-50/90 dark:bg-slate-800/90 backdrop-blur-md border border-rose-200 dark:border-rose-900/50 rounded-2xl shadow-md overflow-hidden">
                    <div class="p-6 border-b border-rose-200 dark:border-rose-900/50 bg-rose-100/50 dark:bg-rose-900/20 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-rose-600 text-white rounded-xl flex items-center justify-center text-lg shadow-sm animate-pulse">🔧</div>
                            <div>
                                <h3 class="text-lg font-black text-slate-800 dark:text-white tracking-tight">Work Order: Kerusakan Masuk</h3>
                                <p class="text-[10px] font-bold text-rose-600 dark:text-rose-400 uppercase tracking-wider">Perbaikan Menunggu Eksekusi</p>
                            </div>
                        </div>
                        <span class="bg-rose-600 text-white px-3 py-1 rounded-full text-xs font-black shadow-md">{{ count($engineeringTasks) }} Tugas Terbuka</span>
                    </div>

                    <div class="p-6 overflow-y-auto max-h-[500px] custom-scrollbar">
                        @if(count($engineeringTasks) > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($engineeringTasks as $task)
                                    <div class="bg-white dark:bg-slate-900 rounded-xl p-5 border {{ $task->status == 'open' ? 'border-rose-200 dark:border-rose-900 shadow-sm shadow-rose-100/50' : 'border-amber-200 dark:border-amber-900 opacity-70' }} relative">
                                        <div class="flex justify-between items-start mb-3">
                                            <div class="pr-4">
                                                <h4 class="font-bold text-slate-800 dark:text-white text-sm leading-tight">{{ $task->title }}</h4>
                                                <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase mt-1">Dilaporkan oleh: {{ $task->user->name ?? 'Anonim' }}</p>
                                            </div>
                                            @if($task->status == 'open')
                                                <span class="bg-rose-100 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 px-2 py-0.5 rounded text-[10px] font-black uppercase border border-rose-200 dark:border-rose-800 flex-shrink-0">OPEN</span>
                                            @else
                                                <span class="bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 px-2 py-0.5 rounded text-[10px] font-black uppercase border border-amber-200 dark:border-amber-800 flex-shrink-0">Tunggu ACC</span>
                                            @endif
                                        </div>

                                        <p class="text-xs text-slate-600 dark:text-slate-300 font-medium bg-slate-50 dark:bg-slate-800 p-3 rounded-lg border border-slate-100 dark:border-slate-700 mb-4">{{ $task->message }}</p>

                                        @if($task->image)
                                            <div class="mb-4">
                                                <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase mb-1">Foto Bukti Keluhan:</p>
                                                <img src="{{ asset('storage/' . $task->image) }}" @click="imageModalSrc = '{{ asset('storage/' . $task->image) }}'; imageModalOpen = true" class="w-full h-32 object-cover rounded-xl cursor-zoom-in hover:opacity-80 border border-slate-200 dark:border-slate-700" loading="lazy">
                                            </div>
                                        @endif

                                        <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center">
                                            <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500">📅 {{ \Carbon\Carbon::parse($task->created_at)->translatedFormat('d M Y, H:i') }}</span>
                                            @if($task->status == 'open')
                                                <button @click="resolveTaskModal = {{ $task->id }}" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-xl text-xs font-bold shadow-md shadow-emerald-600/20">Selesaikan Perbaikan</button>
                                            @else
                                                <button disabled class="bg-slate-200 dark:bg-slate-700 text-slate-400 dark:text-slate-500 px-4 py-2 rounded-xl text-xs font-bold cursor-not-allowed">Sedang Di-Review Admin</button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-10 bg-white/50 dark:bg-slate-900/50 rounded-xl border border-dashed border-slate-300 dark:border-slate-700">
                                <div class="text-4xl mb-3 opacity-50">🏖️</div>
                                <h4 class="font-bold text-slate-600 dark:text-slate-300">Tidak ada kerusakan yang dilaporkan</h4>
                                <p class="text-xs text-slate-500 mt-1">Semua mesin dan fasilitas berjalan dengan baik.</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-md rounded-2xl shadow-sm border border-sky-100 dark:border-slate-700 overflow-hidden flex flex-col">
                        <div class="p-6 border-b border-sky-100 dark:border-slate-700 flex flex-col xl:flex-row justify-between xl:items-center gap-4 bg-sky-50/30 dark:bg-slate-800">
                            <div>
                                <h3 class="text-lg font-black text-slate-800 dark:text-white">Riwayat Laporan</h3>
                                <span class="text-[10px] font-bold text-sky-600 dark:text-sky-400 uppercase tracking-wider">Total Ditemukan: {{ count($reports) }}</span>
                            </div>

                            <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap md:flex-nowrap gap-2 items-center w-full xl:w-auto">
                                <input type="date" name="date" value="{{ request('date') }}" class="w-full md:w-auto border-sky-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white font-semibold rounded-lg shadow-sm py-1.5 px-3 text-xs focus:border-sky-500 focus:ring-sky-500">

                                <select name="month" class="w-full md:w-auto border-sky-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white font-semibold rounded-lg shadow-sm py-1.5 px-3 text-xs focus:border-sky-500 focus:ring-sky-500">
                                    <option value="">Semua Bulan</option>
                                    @for($i=1; $i<=12; $i++)
                                        <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>{{ date('M', mktime(0, 0, 0, $i, 10)) }}</option>
                                    @endfor
                                </select>

                                <select name="year" class="w-full md:w-auto border-sky-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white font-semibold rounded-lg shadow-sm py-1.5 px-3 text-xs focus:border-sky-500 focus:ring-sky-500">
                                    <option value="">Semua Tahun</option>
                                    @foreach($years as $yr)
                                        <option value="{{ $yr }}" {{ request('year') == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                                    @endforeach
                                </select>

                                <button type="submit" class="bg-sky-600 hover:bg-sky-700 text-white font-bold py-1.5 px-3 rounded-lg shadow-md text-xs flex-shrink-0">Filter</button>
                                <a href="{{ route('dashboard') }}" class="bg-slate-100 dark:bg-slate-600 hover:bg-slate-200 dark:hover:bg-slate-500 text-slate-700 dark:text-slate-300 font-bold py-1.5 px-3 rounded-lg text-xs flex-shrink-0">Reset</a>
                            </form>
                        </div>

                        <!-- TABEL RIWAYAT LAPORAN FIX SCROLL -->
                        <div class="overflow-x-auto max-h-[400px] overflow-y-auto relative custom-scrollbar">
                            <table class="w-full text-left border-collapse relative">
                                <thead class="sticky top-0 z-10 shadow-sm">
                                    <tr class="bg-white dark:bg-slate-800 border-b border-sky-200 dark:border-slate-700">
                                        <th class="px-6 py-4 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">Waktu Submit</th>
                                        <th class="px-6 py-4 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-4 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-sky-100 dark:divide-slate-700">
                                    @forelse($reports as $report)
                                        <tr class="hover:bg-sky-50/40 dark:hover:bg-slate-700/50">
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
                                                @if($report->status == 'completed')
                                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">Selesai</span>
                                                @elseif($report->status == 'planned')
                                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-400 border border-sky-200 dark:border-sky-800">Todo Aktif</span>
                                                @else
                                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-bold rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800">Tertunda / Draft</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <button type="button" @click="openReportModal = {{ $report->id }}" class="inline-flex items-center px-4 py-2 bg-sky-50 dark:bg-slate-700 text-sky-700 dark:text-sky-300 hover:bg-sky-100 dark:hover:bg-slate-600 font-bold rounded-xl border border-sky-200 dark:border-slate-600">Lihat Detail</button>
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

                    <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-md rounded-2xl shadow-sm border border-sky-100 dark:border-slate-700 overflow-hidden">
                        <div class="border-b border-slate-100 dark:border-slate-700 p-6 flex items-center justify-between bg-sky-50/30 dark:bg-slate-800">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400 rounded-xl flex items-center justify-center text-lg font-bold border border-amber-100 dark:border-amber-800">🪙</div>
                                <div>
                                    <h3 class="text-lg font-black text-slate-800 dark:text-white">Riwayat Perolehan Skor</h3>
                                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Transparansi Skor</p>
                                </div>
                            </div>
                        </div>

                        <!-- TABEL RIWAYAT SKOR FIX SCROLL -->
                        <div class="overflow-x-auto max-h-[400px] overflow-y-auto relative custom-scrollbar">
                            <table class="w-full text-left border-collapse relative">
                                <thead class="sticky top-0 z-10 shadow-sm">
                                    <tr class="bg-white dark:bg-slate-800 border-b border-sky-200 dark:border-slate-700">
                                        <th class="px-4 py-3 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">Waktu</th>
                                        <th class="px-4 py-3 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">Tipe</th>
                                        <th class="px-4 py-3 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">Keterangan</th>
                                        <th class="px-4 py-3 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider text-right">Nominal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-sky-100 dark:divide-slate-700">
                                    @forelse($pointHistories ?? [] as $history)
                                        <tr class="hover:bg-sky-50/40 dark:hover:bg-slate-700/50">
                                            <td class="px-4 py-3 whitespace-nowrap text-xs font-bold text-slate-600 dark:text-slate-300">
                                                {{ \Carbon\Carbon::parse($history->created_at)->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <span class="px-2.5 py-1 inline-flex text-[10px] font-black rounded-md bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-400 border border-sky-200 dark:border-sky-800 uppercase">
                                                    {{ $history->type }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-xs font-semibold text-slate-800 dark:text-slate-200">{{ $history->description }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-black text-emerald-600 dark:text-emerald-400">+{{ $history->points }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-8 text-center text-slate-400 dark:text-slate-500 font-medium text-xs">Belum ada riwayat pemasukan poin.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-md p-6 rounded-2xl shadow-sm border border-sky-100 dark:border-slate-700">
                        <div class="border-b border-slate-100 dark:border-slate-700 pb-4 mb-5 flex items-center gap-3">
                            <div class="w-10 h-10 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 rounded-xl flex items-center justify-center text-lg font-bold border border-slate-200 dark:border-slate-600">🛠️</div>
                            <div>
                                <h3 class="text-lg font-black text-slate-800 dark:text-white">Laporan Kejadian & Catatan</h3>
                                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Kirim Catatan Insidental / Kerusakan</p>
                            </div>
                        </div>

                        <form action="{{ route('notes.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-black text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-2">Kategori Laporan</label>
                                    <select name="category" class="w-full border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 text-slate-800 dark:text-white font-bold rounded-xl shadow-sm py-2 px-3 text-sm focus:border-sky-500 focus:ring-sky-500" required>
                                        <option value="" disabled selected>-- Pilih Kategori --</option>
                                        <option value="Kerusakan">🛠️ Kerusakan (Engineering)</option>
                                        <option value="Temuan Barang">📦 Temuan (Lost & Found)</option>
                                        <option value="Kendala Tamu">🗣️ Komplain Tamu</option>
                                        <option value="Operasional">📝 Catatan Lainnya</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-black text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-2">Judul Ringkas</label>
                                    <input type="text" name="title" class="w-full border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 text-slate-800 dark:text-white font-semibold rounded-xl shadow-sm py-2 px-3 text-sm focus:border-sky-500 focus:ring-sky-500" placeholder="Sanyo Rusak / Kunci Hilang" required>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-black text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-2">Detail Kejadian</label>
                                <textarea name="message" rows="3" class="w-full border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 text-slate-800 dark:text-white font-semibold rounded-xl shadow-sm py-2 px-4 text-sm focus:border-sky-500 focus:ring-sky-500" placeholder="Jelaskan secara lengkap kejadiannya..." required></textarea>
                            </div>

                            <div>
                                <label class="block text-xs font-black text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-2">Bukti Foto (Opsional)</label>
                                <input type="file" name="image" accept="image/*" class="w-full border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-semibold rounded-xl shadow-sm py-1.5 px-3 text-sm focus:border-sky-500 focus:ring-sky-500 file:mr-4 file:py-1 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-sky-100 dark:file:bg-slate-600 file:text-sky-700 dark:file:text-sky-300">
                            </div>

                            <div class="flex justify-end pt-2">
                                <button type="submit" class="bg-slate-800 hover:bg-slate-900 dark:bg-sky-600 dark:hover:bg-sky-700 text-white font-bold py-2 px-6 rounded-xl shadow-md text-sm">Kirim Laporan Kejadian</button>
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
                                <p class="text-3xl font-black">{{ $myRank ? '#'.$myRank : '-' }}</p>
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
                                <select name="lb_dept" onchange="this.form.submit()" class="border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 text-[10px] font-bold rounded-lg shadow-sm py-1.5 px-2 focus:ring-sky-500">
                                    <option value="">Semua Dept</option>
                                    @foreach($lbDepartments as $dept)
                                        <option value="{{ $dept }}" {{ request('lb_dept', $lbDept) == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                                    @endforeach
                                </select>
                                <select name="lb_month" onchange="this.form.submit()" class="border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 text-[10px] font-bold rounded-lg shadow-sm py-1.5 px-2 focus:ring-sky-500">
                                    @for($i=1; $i<=12; $i++)
                                        <option value="{{ $i }}" {{ request('lb_month', $lbMonth) == $i ? 'selected' : '' }}>{{ date('M', mktime(0, 0, 0, $i, 10)) }}</option>
                                    @endfor
                                </select>
                            </form>
                        </div>

                        <!-- LIST LEADERBOARD FIX SCROLL -->
                        <div class="p-5 flex-1 flex flex-col gap-4 overflow-y-auto max-h-[400px] custom-scrollbar">
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

        </div>

        @if(isset($engineeringTasks) && count($engineeringTasks) > 0)
            @foreach($engineeringTasks as $task)
                <template x-if="resolveTaskModal === {{ $task->id }}">
                    <div x-cloak x-show="resolveTaskModal === {{ $task->id }}" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                        <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm" @click="resolveTaskModal = null"></div>

                        <div class="relative z-[110] bg-white dark:bg-slate-800 rounded-3xl shadow-2xl w-full max-w-lg overflow-hidden border border-slate-200 dark:border-slate-700 max-h-[90vh] flex flex-col">
                            <div class="p-6 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 flex justify-between items-center flex-shrink-0">
                                <div>
                                    <h3 class="text-xl font-black text-slate-800 dark:text-white">Selesaikan Perbaikan</h3>
                                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-wider">Unggah Bukti & Pilih Anggota Tim</p>
                                </div>
                                <button type="button" @click="resolveTaskModal = null" class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400 hover:bg-rose-100 hover:text-rose-600 dark:hover:bg-rose-900/50 dark:hover:text-rose-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>

                            <form action="{{ route('notes.resolve', $task->id) }}" method="POST" enctype="multipart/form-data" class="p-6 overflow-y-auto space-y-5 flex-1">
                                @csrf
                                <div>
                                    <label class="block text-xs font-black text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-2">Foto Hasil Perbaikan (Bisa Pilih Banyak) <span class="text-rose-500">*</span></label>
                                    <input type="file" name="resolved_images[]" accept="image/*" multiple class="w-full border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 text-slate-600 dark:text-slate-300 font-semibold rounded-xl shadow-sm py-2 px-3 text-sm focus:border-emerald-500 focus:ring-emerald-500 file:mr-4 file:py-1 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-emerald-100 dark:file:bg-emerald-900/40 file:text-emerald-700 dark:file:text-emerald-400" required>
                                    <p class="text-[10px] text-slate-400 mt-1 font-medium">Tahan tombol Ctrl / Shift di keyboard saat memilih foto untuk mengunggah lebih dari satu.</p>
                                </div>

                                <div>
                                    <label class="block text-xs font-black text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-2">Rekan Yang Ikut Membantu (Opsional)</label>
                                    @php
                                        $filteredStaff = isset($engineeringStaff) ? $engineeringStaff->filter(fn($s) => $s->id !== Auth::id())->values()->all() : [];
                                    @endphp
                                    <div x-data="{
                                        open: false,
                                        selected: [],
                                        staff: {{ json_encode($filteredStaff) }},
                                        toggle(id) {
                                            if (this.selected.includes(id)) {
                                                this.selected = this.selected.filter(s => s !== id);
                                            } else {
                                                this.selected.push(id);
                                            }
                                        },
                                        getStaff(id) {
                                            return this.staff.find(s => s.id === id);
                                        }
                                    }" class="relative">
                                        <div class="flex flex-wrap gap-2 mb-2" x-show="selected.length > 0">
                                            <template x-for="id in selected" :key="id">
                                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 text-[10px] font-black rounded-full border border-emerald-200 dark:border-emerald-800 shadow-sm">
                                                    <span x-text="getStaff(id).name"></span>
                                                    <button type="button" @click="toggle(id)" class="w-4 h-4 flex items-center justify-center rounded-full bg-emerald-200/50 dark:bg-emerald-800/50 hover:bg-rose-500 hover:text-white focus:outline-none">✕</button>
                                                    <input type="hidden" name="helpers[]" :value="id">
                                                </span>
                                            </template>
                                        </div>

                                        <button type="button" @click="open = !open" @click.outside="open = false" class="flex items-center gap-2 px-4 py-2.5 text-xs font-bold text-slate-600 dark:text-slate-300 bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600 rounded-xl hover:bg-slate-100 dark:hover:bg-slate-600 w-full justify-between focus:outline-none" :disabled="staff.length === 0" :class="staff.length === 0 ? 'opacity-50 cursor-not-allowed' : ''">
                                            <span x-text="staff.length === 0 ? 'Tidak ada rekan teknisi lain' : '+ Tambahkan rekan yang membantu'"></span>
                                            <svg x-show="staff.length > 0" class="w-4 h-4 text-slate-400" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </button>

                                        <div x-show="open" class="absolute z-20 w-full mt-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-xl shadow-xl max-h-48 overflow-y-auto py-1">
                                            <template x-for="person in staff" :key="person.id">
                                                <button type="button" @click="toggle(person.id)" class="w-full text-left px-4 py-2.5 text-xs font-bold flex items-center justify-between" :class="selected.includes(person.id) ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700'">
                                                    <span x-text="person.name"></span>
                                                    <div class="w-4 h-4 rounded-full border flex items-center justify-center" :class="selected.includes(person.id) ? 'border-emerald-500 bg-emerald-500 text-white' : 'border-slate-300 dark:border-slate-500'">
                                                        <svg x-show="selected.includes(person.id)" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                                    </div>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-black text-slate-600 dark:text-slate-300 uppercase tracking-wider mb-2">Catatan Teknisi (Opsional)</label>
                                    <textarea name="resolved_note" rows="2" class="w-full border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-700 text-slate-800 dark:text-white font-semibold rounded-xl shadow-sm py-2 px-4 text-sm focus:border-emerald-500 focus:ring-emerald-500" placeholder="Contoh: Kabel dinamo putus, sudah disambung..."></textarea>
                                </div>

                                <div class="flex gap-3 pt-2">
                                    <button type="button" @click="resolveTaskModal = null" class="flex-1 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 text-slate-700 dark:text-slate-200 font-bold py-3 px-4 rounded-xl shadow-sm border border-slate-200 dark:border-slate-600">Batal</button>
                                    <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-emerald-600/30">Kirim Bukti Selesai</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </template>
            @endforeach
        @endif

        @foreach($reports as $r)
            @php
                $scoreBreakdown = $r->status === 'completed' ? $r->scoreBreakdown() : null;
            @endphp
            <div x-cloak x-show="openReportModal === {{ $r->id }}" class="fixed inset-0 z-[80] flex items-center justify-center p-4">
                <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm" @click="openReportModal = null"></div>

                <div class="relative z-[90] bg-white dark:bg-slate-800 rounded-3xl shadow-2xl w-full max-w-5xl max-h-[90vh] flex flex-col overflow-hidden border border-slate-200 dark:border-slate-700">
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
                            <button @click="openReportModal = null" class="w-8 h-8 flex items-center justify-center rounded-full bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400 hover:bg-rose-100 hover:text-rose-600 dark:hover:bg-rose-900/50 dark:hover:text-rose-400" title="Tutup">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                            <span class="px-3 py-1 rounded-md text-[10px] font-black uppercase tracking-wider {{ $r->status == 'completed' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800' }}">
                                Status: {{ $r->status == 'completed' ? 'Selesai' : 'Tertunda' }}
                            </span>
                        </div>
                    </div>

                    <div class="p-6 overflow-y-auto bg-slate-50/50 dark:bg-slate-900 flex-1">
                        @if($scoreBreakdown)
                        <div class="mb-6 bg-sky-50/50 dark:bg-slate-800 border border-sky-100 dark:border-slate-700 rounded-xl p-4 shadow-sm">
                            <h4 class="text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3">Rincian Skor Performa Anda</h4>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div class="bg-white dark:bg-slate-900/50 p-3 rounded-lg border border-slate-200 dark:border-slate-700">
                                    <span class="block text-[10px] font-bold text-slate-500 dark:text-slate-400 mb-1">SKOR DASAR (SOP)</span>
                                    <span class="text-lg font-black text-slate-800 dark:text-white">{{ $scoreBreakdown['base_score'] }}</span>
                                </div>
                                <div class="bg-emerald-50 dark:bg-emerald-900/20 p-3 rounded-lg border border-emerald-100 dark:border-emerald-800/30">
                                    <span class="block text-[10px] font-bold text-emerald-600 dark:text-emerald-400 mb-1">BONUS (EXTRA TASKS)</span>
                                    <span class="text-lg font-black text-emerald-700 dark:text-emerald-300">+{{ $scoreBreakdown['bonus_score'] }}</span>
                                </div>
                                <div class="bg-rose-50 dark:bg-rose-900/20 p-3 rounded-lg border border-rose-100 dark:border-rose-800/30">
                                    <span class="block text-[10px] font-bold text-rose-600 dark:text-rose-400 mb-1">PINALTI TERLAMBAT</span>
                                    <span class="text-lg font-black text-rose-700 dark:text-rose-300">{{ $scoreBreakdown['penalty'] == 0 ? '0' : '-'.$scoreBreakdown['penalty'] }}</span>
                                </div>
                                <div class="bg-sky-100 dark:bg-sky-900/40 p-3 rounded-lg border border-sky-200 dark:border-sky-800">
                                    <span class="block text-[10px] font-bold text-sky-700 dark:text-sky-300 mb-1">TOTAL SKOR AKHIR</span>
                                    <span class="text-xl font-black text-sky-800 dark:text-sky-400">{{ $r->total_score ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="flex justify-between items-center mb-4">
                            <h4 class="font-black text-slate-800 dark:text-white">Daftar Pekerjaan</h4>
                            <span class="inline-block bg-white dark:bg-slate-800 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 shadow-sm text-xs font-bold text-slate-600 dark:text-slate-400">
                                📅 {{ \Carbon\Carbon::parse($r->created_at)->timezone('Asia/Jakarta')->translatedFormat('d M Y') }} &nbsp;|&nbsp; ⏰ {{ \Carbon\Carbon::parse($r->created_at)->timezone('Asia/Jakarta')->format('H:i') }} WIB
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($r->items as $item)
                                <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200/60 dark:border-slate-700 p-4 flex flex-col justify-between relative">
                                    <div class="mt-2">
                                        <div class="flex items-start justify-between mb-2 gap-2">
                                            <h5 class="text-sm font-bold text-slate-800 dark:text-white leading-tight">
                                                {{ $item->task ? $item->task->name : ($item->task_name ?? 'Tugas Tambahan') }}
                                            </h5>
                                            <div class="flex items-center gap-1 flex-shrink-0">
                                                @if($item->status == 'verified')
                                                    <span class="px-2 py-0.5 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-[10px] font-black rounded uppercase">Di-ACC</span>
                                                @elseif($item->status == 'rejected')
                                                    <span class="px-2 py-0.5 bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400 text-[10px] font-black rounded uppercase">Ditolak</span>
                                                @elseif($item->status == 'completed')
                                                    <span class="px-2 py-0.5 bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-400 text-[10px] font-black rounded uppercase">Selesai</span>
                                                @else
                                                    <span class="px-2 py-0.5 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 text-[10px] font-black rounded uppercase">Pending</span>
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
                                                <img src="{{ asset('storage/' . $item->before_image) }}" @click="imageModalSrc = '{{ asset('storage/' . $item->before_image) }}'; imageModalOpen = true" class="w-full h-20 object-cover rounded-lg cursor-zoom-in hover:opacity-80 shadow-sm border border-slate-100 dark:border-slate-700" loading="lazy">
                                            @else
                                                <div class="w-full h-20 bg-slate-100 dark:bg-slate-700/50 rounded-lg flex items-center justify-center text-xs text-slate-400 dark:text-slate-500 font-bold border border-slate-200 dark:border-slate-700">Kosong</div>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase mb-1">Sesudah</p>
                                            @if($item->after_image)
                                                <img src="{{ asset('storage/' . $item->after_image) }}" @click="imageModalSrc = '{{ asset('storage/' . $item->after_image) }}'; imageModalOpen = true" class="w-full h-20 object-cover rounded-lg cursor-zoom-in hover:opacity-80 shadow-sm border border-slate-100 dark:border-slate-700" loading="lazy">
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

        <div x-cloak x-show="imageModalOpen" class="fixed inset-0 z-[120] flex items-center justify-center p-4">
            <div x-show="imageModalOpen" class="absolute inset-0 bg-slate-900/90 backdrop-blur-sm" @click="imageModalOpen = false"></div>
            <img :src="imageModalSrc" class="relative z-[130] max-h-[90vh] max-w-full rounded-2xl object-contain shadow-2xl cursor-zoom-out" @click="imageModalOpen = false" title="Klik untuk menutup" loading="lazy">
        </div>

    </div>

    <script>
        const deadlineTimeStr = "{{ $submitDeadlineTime ?? '15:30' }}";
        const serverOffset = Date.now() - ({{ \Carbon\Carbon::now('Asia/Jakarta')->valueOf() }});

        function updateCountdown() {
            const timerEl = document.getElementById('countdown-timer');
            if (!timerEl) return;

            const nowWib = new Date(Date.now() - serverOffset);
            const [targetHours, targetMinutes] = deadlineTimeStr.split(':').map(Number);
            const targetDate = new Date(nowWib);
            targetDate.setHours(targetHours, targetMinutes, 0, 0);

            let diff = targetDate - nowWib;

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
