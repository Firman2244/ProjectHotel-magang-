<x-app-layout>
    <div>
        <header class="bg-white/80 dark:bg-slate-900/50 backdrop-blur-md border-b border-slate-200 dark:border-slate-700 h-16 hidden md:flex items-center justify-between px-6 shadow-sm sticky top-0 z-20">
            <span class="font-extrabold text-xl text-slate-800 dark:text-white tracking-tight">Histori Poin Staf</span>
        </header>

        <div class="p-4 md:p-8 space-y-6">
            <div class="bg-white dark:bg-slate-800 p-6 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-xl font-black text-slate-800 dark:text-white">Riwayat Perolehan Poin: {{ $staff->name }}</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">Departemen: {{ $staff->department }} | Cabang: {{ $staff->branch->name ?? '-' }}</p>
                </div>
                <a href="{{ route('admin.staff.scores') }}" class="inline-flex items-center gap-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-bold py-2.5 px-4 rounded-xl text-sm border border-slate-200 dark:border-slate-600">
                    Kembali ke Leaderboard
                </a>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-700/50 text-slate-500 dark:text-slate-400 border-b border-slate-200 dark:border-slate-600">
                                <th class="px-5 py-3 font-bold uppercase tracking-wider text-[11px]">Waktu</th>
                                <th class="px-5 py-3 font-bold uppercase tracking-wider text-[11px]">Tipe</th>
                                <th class="px-5 py-3 font-bold uppercase tracking-wider text-[11px]">Keterangan</th>
                                <th class="px-5 py-3 font-bold uppercase tracking-wider text-[11px] text-right">Nominal Poin</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @forelse($histories as $history)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30">
                                    <td class="px-5 py-3 font-semibold text-slate-700 dark:text-slate-300 whitespace-nowrap">{{ $history->created_at->format('d M Y, H:i') }} WIB</td>
                                    <td class="px-5 py-3 whitespace-nowrap">
                                        <span class="px-2.5 py-1 rounded-md text-[10px] font-black uppercase bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-400 border border-sky-200 dark:border-sky-800">
                                            {{ $history->type }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3 font-medium text-slate-600 dark:text-slate-400">{{ $history->description }}</td>
                                    <td class="px-5 py-3 font-black text-right text-emerald-600 dark:text-emerald-400 whitespace-nowrap">+{{ number_format($history->points, 2) }} Pts</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-8 text-center text-slate-500 dark:text-slate-400 font-medium">Belum ada riwayat perolehan poin untuk staf ini.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
