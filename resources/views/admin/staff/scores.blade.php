<x-app-layout>
    <div class="min-h-screen bg-slate-50/50 dark:bg-slate-900 flex flex-col md:flex-row relative transition-colors duration-300" x-data="{ sidebarOpen: false }">
        <div class="fixed inset-y-0 left-0 z-50 w-64 h-screen bg-white dark:bg-slate-800 shadow-2xl md:shadow-none transform transition-transform duration-300 ease-in-out flex-shrink-0 md:fixed border-r dark:border-slate-700">
            <x-admin-sidebar />
        </div>

        <div class="flex-1 flex flex-col min-w-0 md:ml-64">
            <header class="bg-white/80 dark:bg-slate-900/50 backdrop-blur-md border-b border-slate-200 dark:border-slate-700 h-16 flex items-center justify-between px-6 shadow-sm sticky top-0 z-20">
                <span class="font-extrabold text-xl text-slate-800 dark:text-white tracking-tight">Leaderboard & Akumulasi Skor Staf</span>
            </header>

            <div class="p-4 md:p-8 space-y-6">
                <div class="bg-white dark:bg-slate-800 p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 transition-colors duration-300">
                    <form method="GET" action="{{ route('admin.staff.scores') }}" class="flex flex-wrap gap-3 items-end">
                        <input type="hidden" name="hotel" value="{{ request('hotel', 'wahyu') }}">

                        <div>
                            <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Periode Bulan</label>
                            <select name="month" class="w-40 text-sm font-semibold border-slate-200 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-white focus:border-sky-500 focus:ring-sky-500 transition-colors duration-300">
                                @foreach($months as $num => $name)
                                    <option value="{{ $num }}" {{ $selectedMonth == $num ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Tahun</label>
                            <select name="year" class="w-32 text-sm font-semibold border-slate-200 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-white focus:border-sky-500 focus:ring-sky-500 transition-colors duration-300">
                                @foreach($years as $yr)
                                    <option value="{{ $yr }}" {{ $selectedYear == $yr ? 'selected' : '' }}>{{ $yr }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex gap-2">
                            <button type="submit" class="bg-sky-600 hover:bg-sky-700 text-white text-sm font-bold py-2.5 px-6 rounded-xl transition shadow-sm">Tampilkan</button>
                        </div>
                    </form>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex justify-between items-center">
                        <h3 class="text-sm font-black text-slate-800 dark:text-white">Peringkat Kinerja Musiman ({{ $months[(int)$selectedMonth] }} {{ $selectedYear }})</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="bg-slate-50 dark:bg-slate-700/50 text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-600">
                                    <th class="px-5 py-3 font-bold uppercase tracking-wider text-[11px] text-center">Peringkat</th>
                                    <th class="px-5 py-3 font-bold uppercase tracking-wider text-[11px]">Nama Staf</th>
                                    <th class="px-5 py-3 font-bold uppercase tracking-wider text-[11px]">Cabang Hotel</th>
                                    <th class="px-5 py-3 font-bold uppercase tracking-wider text-[11px]">Departemen</th>
                                    <th class="px-5 py-3 font-bold uppercase tracking-wider text-[11px] text-center">Shift Terhitung</th>
                                    <th class="px-5 py-3 font-bold uppercase tracking-wider text-[11px] text-center">Total Skor</th>
                                    <th class="px-5 py-3 font-bold uppercase tracking-wider text-[11px] text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @php
                                    $startOfMonth = \Carbon\Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfMonth()->format('Y-m-d');
                                    $endOfMonth = \Carbon\Carbon::createFromDate($selectedYear, $selectedMonth, 1)->endOfMonth()->format('Y-m-d');
                                @endphp

                                @forelse($staffs as $index => $staff)
                                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30 transition">
                                        <td class="px-5 py-3 font-black text-center text-lg">
                                            @if($index === 0) 🥇
                                            @elseif($index === 1) 🥈
                                            @elseif($index === 2) 🥉
                                            @else <span class="text-sm text-slate-500">{{ $index + 1 }}</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3 font-bold text-slate-800 dark:text-slate-200">{{ $staff['name'] }}</td>
                                        <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $staff['branch'] }}</td>
                                        <td class="px-5 py-3 text-slate-600 dark:text-slate-400">{{ $staff['department'] }}</td>
                                        <td class="px-5 py-3 font-semibold text-slate-700 dark:text-slate-300 text-center">{{ $staff['total_shift'] }}</td>
                                        <td class="px-5 py-3 font-black text-center">
                                            @php $score = $staff['total_score']; @endphp

                                            @if($index === 0)
                                                <span class="text-amber-500 flex items-center justify-center gap-1">{{ $score }} 🌟</span>
                                            @elseif($index === 1)
                                                <span class="text-slate-400 flex items-center justify-center gap-1">{{ $score }} ✨</span>
                                            @elseif($index === 2)
                                                <span class="text-amber-700 flex items-center justify-center gap-1">{{ $score }} 💫</span>
                                            @else
                                                <span class="text-sky-600 dark:text-sky-400">{{ $score }}</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3 text-center">
                                            <a href="{{ route('admin.reports.summary', ['hotel' => $hotelSlug, 'staff_id' => $staff['id'], 'start_date' => $startOfMonth, 'end_date' => $endOfMonth]) }}" class="inline-flex items-center justify-center bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-400 border border-sky-200 dark:border-sky-800 hover:bg-sky-200 dark:hover:bg-sky-800/50 font-bold py-1.5 px-3 rounded-lg transition text-xs shadow-sm">
                                                Lihat Histori
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-5 py-8 text-center text-slate-500 font-medium">Belum ada data skor staf pada periode ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
