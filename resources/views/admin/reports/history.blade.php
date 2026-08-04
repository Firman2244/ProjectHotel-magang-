<x-app-layout>
    <div class="min-h-screen bg-slate-50 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto space-y-6">

            <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-black text-slate-800 tracking-tight">Riwayat Laporan</h1>
                    <p class="text-sm font-medium text-slate-500 mt-1">Rekam jejak performa shift harian Anda</p>
                </div>
                <a href="{{ route('dashboard') }}" class="bg-slate-900 hover:bg-slate-800 text-white font-bold py-2 px-4 rounded-xl transition text-sm shadow-sm">
                    ⬅️ Kembali ke Dashboard
                </a>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-200">
                                <th class="py-4 px-6 text-xs font-black text-slate-500 uppercase tracking-wider">Tanggal</th>
                                <th class="py-4 px-6 text-xs font-black text-slate-500 uppercase tracking-wider">Shift</th>
                                <th class="py-4 px-6 text-xs font-black text-slate-500 uppercase tracking-wider">Kehadiran</th>
                                <th class="py-4 px-6 text-xs font-black text-slate-500 uppercase tracking-wider">Status Laporan</th>
                                <th class="py-4 px-6 text-xs font-black text-slate-500 uppercase tracking-wider text-right">Skor</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($reports as $report)
                                <tr class="hover:bg-slate-50/50 transition">
                                    <td class="py-4 px-6 text-sm font-bold text-slate-800">
                                        {{ \Carbon\Carbon::parse($report->report_date)->format('d M Y') }}
                                    </td>
                                    <td class="py-4 px-6 text-sm font-semibold text-slate-600">
                                        Shift {{ $report->shift_id }}
                                    </td>
                                    <td class="py-4 px-6 text-sm">
                                        @if($report->is_late || $report->is_late_submit)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-100">Terlambat</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">Tepat Waktu</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-sm">
                                        @if($report->status == 'completed')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-100">Disubmit</span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-100">Berjalan</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6 text-right">
                                        <span class="text-lg font-black text-sky-600">{{ $report->total_score ?? 0 }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-8 text-center text-sm font-medium text-slate-500">Belum ada riwayat laporan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-slate-200">
                    {{ $reports->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
