<x-app-layout>
    <div class="p-4 md:p-8 space-y-6">
        <header class="bg-white/80 dark:bg-slate-900/50 backdrop-blur-md border-b border-slate-200 dark:border-slate-700 h-16 hidden md:flex items-center justify-between px-6 shadow-sm sticky top-0 z-20 -mx-4 md:-mx-8 -mt-4 md:-mt-8 mb-6">
            <div class="flex items-center">
                <span class="font-extrabold text-xl text-slate-800 dark:text-white tracking-tight">Kategori Beban Kerja</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Login as: <span class="text-sky-700 dark:text-sky-400 font-black">{{ Auth::user()->name }}</span></span>
            </div>
        </header>

        <div>
            <h2 class="text-xl font-black text-slate-800 dark:text-white">Rekap Pekerjaan per Kategori Beban Kerja</h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">
                Gunakan filter di bawah untuk menampilkan data. Klik pada kotak pekerjaan untuk melompat langsung ke detail laporannya.
            </p>
        </div>

        <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
            <form method="GET" action="{{ route('admin.workload.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-4 items-end">
                <input type="hidden" name="hotel" value="{{ $currentHotel ? $currentHotel->id : '' }}">
                <input type="hidden" name="filter_applied" value="1">

                <div class="lg:col-span-1">
                    <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Mulai Tanggal</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="w-full text-sm font-semibold border-slate-200 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-white focus:border-sky-500 focus:ring-sky-500">
                </div>

                <div class="lg:col-span-1">
                    <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="w-full text-sm font-semibold border-slate-200 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-white focus:border-sky-500 focus:ring-sky-500">
                </div>

                <div class="lg:col-span-1">
                    <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Pilih Kategori <span class="text-rose-500">*</span></label>
                    <select name="difficulty" required class="w-full text-sm font-semibold border-slate-200 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-white focus:border-sky-500 focus:ring-sky-500">
                        <option value="" disabled {{ empty($difficulty) ? 'selected' : '' }}>-- Wajib Pilih --</option>
                        <option value="semua" {{ $difficulty == 'semua' ? 'selected' : '' }}>Semua Kategori</option>
                        <option value="ringan" {{ $difficulty == 'ringan' ? 'selected' : '' }}>Ringan 🟢</option>
                        <option value="sedang" {{ $difficulty == 'sedang' ? 'selected' : '' }}>Sedang 🟠</option>
                        <option value="berat" {{ $difficulty == 'berat' ? 'selected' : '' }}>Berat 🔴</option>
                    </select>
                </div>

                <div class="lg:col-span-1">
                    <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Tampilkan per Baris</label>
                    <select name="per_page" class="w-full text-sm font-semibold border-slate-200 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-white focus:border-sky-500 focus:ring-sky-500">
                        <option value="12" {{ $perPage == 12 ? 'selected' : '' }}>12 Data</option>
                        <option value="24" {{ $perPage == 24 ? 'selected' : '' }}>24 Data</option>
                        <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 Data</option>
                        <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100 Data</option>
                    </select>
                </div>

                <div class="flex gap-2 lg:col-span-2">
                    <button type="submit" class="flex-1 bg-sky-600 hover:bg-sky-700 text-white text-sm font-bold py-2.5 px-3 rounded-xl transition shadow-sm">🔍 Tampilkan</button>
                    <a href="{{ route('admin.workload.index', ['hotel' => $hotelId]) }}" class="px-4 py-2.5 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-xl hover:bg-slate-200 dark:hover:bg-slate-600 font-bold text-sm border border-slate-200 dark:border-slate-600 flex items-center justify-center transition">Reset</a>
                </div>
            </form>
        </div>

        <!-- Tampilan Konten Dinamis -->
        @if(!$filterApplied || empty($difficulty))
            <!-- STATE 1: KOSONG (Belum Filter) -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-12 flex flex-col items-center justify-center text-center">
                <div class="w-20 h-20 bg-sky-50 dark:bg-sky-900/30 rounded-full flex items-center justify-center text-4xl mb-4 border-4 border-sky-100 dark:border-slate-700">🔎</div>
                <h3 class="text-xl font-black text-slate-800 dark:text-white mb-2">Tentukan Parameter Filter</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm max-w-md">Untuk menjaga performa sistem, silakan pilih kategori beban kerja (Ringan/Sedang/Berat) dan rentang tanggal di atas, lalu klik "Tampilkan".</p>
            </div>
        @elseif($items->isEmpty())
            <!-- STATE 2: FILTERED TAPI TIDAK ADA DATA -->
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 p-12 flex flex-col items-center justify-center text-center">
                <div class="w-20 h-20 bg-slate-50 dark:bg-slate-900/50 rounded-full flex items-center justify-center text-4xl mb-4 border-4 border-slate-100 dark:border-slate-700">📭</div>
                <h3 class="text-xl font-black text-slate-800 dark:text-white mb-2">Data Tidak Ditemukan</h3>
                <p class="text-slate-500 dark:text-slate-400 text-sm max-w-md">Tidak ada pekerjaan dengan kategori <strong>{{ strtoupper($difficulty) }}</strong> pada rentang tanggal yang dipilih.</p>
            </div>
        @else
            <!-- STATE 3: ADA DATA (GRID PAGINATION) -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($items as $item)
                    @php
                        $uiStyles = [
                            'berat' => ['emoji' => '🔴', 'border' => 'border-rose-200 dark:border-rose-800', 'bg' => 'hover:bg-rose-50/50 dark:hover:bg-rose-900/20', 'chip' => 'bg-rose-100 dark:bg-rose-900/50 text-rose-700 dark:text-rose-400'],
                            'sedang' => ['emoji' => '🟠', 'border' => 'border-amber-200 dark:border-amber-800', 'bg' => 'hover:bg-amber-50/50 dark:hover:bg-amber-900/20', 'chip' => 'bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-400'],
                            'ringan' => ['emoji' => '🟢', 'border' => 'border-emerald-200 dark:border-emerald-800', 'bg' => 'hover:bg-emerald-50/50 dark:hover:bg-emerald-900/20', 'chip' => 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-400'],
                        ][$item->difficulty] ?? ['emoji' => '⚪', 'border' => 'border-slate-200', 'bg' => 'hover:bg-slate-50', 'chip' => 'bg-slate-100 text-slate-700'];

                        // Generate URL untuk redirect ke Rangkuman Laporan, langsung kefilter ke staf & tanggal tersebut
                        $reportUrl = route('admin.reports.summary', [
                            'hotel' => $hotelId,
                            'start_date' => $item->report->report_date,
                            'end_date' => $item->report->report_date,
                            'staff_id' => $item->report->user_id,
                            'department' => $item->report->user->department
                        ]);
                    @endphp

                    <a href="{{ $reportUrl }}" title="Klik untuk melihat detail laporan ini" class="block bg-white dark:bg-slate-800 rounded-2xl shadow-sm border {{ $uiStyles['border'] }} p-5 {{ $uiStyles['bg'] }} transition-all duration-300 hover:shadow-md hover:-translate-y-1 group relative">
                        <!-- Icon Arah -->
                        <div class="absolute top-4 right-4 text-slate-300 dark:text-slate-600 group-hover:text-sky-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                        </div>

                        <div class="mb-3 pr-8">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-[10px] font-black uppercase tracking-wider {{ $uiStyles['chip'] }} border {{ $uiStyles['border'] }}">
                                {{ $uiStyles['emoji'] }} Kategori {{ $item->difficulty }}
                            </span>
                        </div>

                        <p class="text-sm font-bold text-slate-800 dark:text-white leading-tight mb-4">
                            {{ $item->task ? $item->task->name : ($item->custom_task_name ?? 'Tugas Tambahan') }}
                        </p>

                        <div class="pt-4 border-t border-slate-100 dark:border-slate-700/50 space-y-1.5">
                            <div class="flex items-center gap-2 text-xs font-semibold text-slate-600 dark:text-slate-300">
                                <span class="w-4 h-4 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center text-[8px]">👤</span>
                                <span class="truncate">{{ $item->report && $item->report->user ? $item->report->user->name : '-' }}</span>
                            </div>
                            <div class="flex items-center justify-between text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase">
                                <span>{{ $item->report && $item->report->user ? $item->report->user->department : '' }}</span>
                                <span>📅 {{ $item->report ? \Carbon\Carbon::parse($item->report->report_date)->format('d/m/Y') : '-' }}</span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <!-- Bagian Navigasi Pagination Laravel -->
            <div class="mt-8 bg-white dark:bg-slate-800 p-4 rounded-xl shadow-sm border border-slate-200 dark:border-slate-700">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
