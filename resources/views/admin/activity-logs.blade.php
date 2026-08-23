<x-app-layout>
    <header class="bg-white/80 dark:bg-slate-900/50 backdrop-blur-md border-b border-slate-200 dark:border-slate-700 h-16 hidden md:flex items-center justify-between px-6 shadow-sm sticky top-0 z-20">
        <div class="flex items-center">
            <span class="font-extrabold text-xl text-slate-800 dark:text-white tracking-tight">Activity Log</span>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Login as: <span class="text-sky-700 dark:text-sky-400 font-black">{{ Auth::user()->name }}</span></span>
        </div>
    </header>

    <div class="p-4 md:p-8 space-y-6">
        <div class="md:hidden flex items-center justify-between bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
            <span class="font-extrabold text-sm text-slate-800 dark:text-white tracking-tight">Activity Log</span>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-400 px-5 py-4 rounded-2xl shadow-sm font-bold text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-xl font-black text-slate-800 dark:text-white">Riwayat Sistem</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">Pemantauan seluruh aktivitas aksi yang terjadi di dalam aplikasi.</p>
            </div>
            <button type="button" onclick="openLogDeleteModal()" class="inline-flex items-center gap-2 bg-rose-50 dark:bg-rose-900/30 hover:bg-rose-100 dark:hover:bg-rose-900/50 text-rose-700 dark:text-rose-400 font-bold py-2.5 px-5 rounded-xl text-sm border border-rose-200 dark:border-rose-800 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> Kosongkan Log
            </button>
        </div>

        <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-md p-5 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
            <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
                <input type="hidden" name="hotel" value="{{ request('hotel') }}">
                <div>
                    <label class="block text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Pilih Tanggal</label>
                    <input type="date" name="date" value="{{ request('date') }}" class="w-full border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-white font-semibold rounded-xl shadow-sm py-2 px-3 text-sm focus:border-sky-500 focus:ring-sky-500">
                </div>
                <div>
                    <label class="block text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Jenis Aksi</label>
                    <select name="action" class="w-full border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-white font-semibold rounded-xl shadow-sm py-2 px-3 text-sm focus:border-sky-500 focus:ring-sky-500">
                        <option value="">Semua Aksi</option>
                        <option value="CREATE_REPORT" {{ request('action') == 'CREATE_REPORT' ? 'selected' : '' }}>CREATE_REPORT</option>
                        <option value="SUBMIT_REPORT" {{ request('action') == 'SUBMIT_REPORT' ? 'selected' : '' }}>SUBMIT_REPORT</option>
                        <option value="DELETE_REPORT" {{ request('action') == 'DELETE_REPORT' ? 'selected' : '' }}>DELETE_REPORT</option>
                        <option value="CLEAR_LOGS" {{ request('action') == 'CLEAR_LOGS' ? 'selected' : '' }}>CLEAR_LOGS</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-sky-600 hover:bg-sky-700 text-white font-bold py-2 px-4 rounded-xl shadow-sm text-sm">Filter</button>
                    <a href="{{ route('admin.activity-logs.index', ['hotel' => request('hotel')]) }}" class="bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-bold py-2 px-4 rounded-xl text-sm flex items-center justify-center border border-slate-200 dark:border-slate-600">Reset</a>
                </div>
            </form>
        </div>

        <div class="bg-white dark:bg-slate-800 shadow-sm rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-700/50 border-b border-slate-200 dark:border-slate-600">
                            <th class="px-5 py-4 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">Waktu</th>
                            <th class="px-5 py-4 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">User</th>
                            <th class="px-5 py-4 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                            <th class="px-5 py-4 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">Deskripsi</th>
                            <th class="px-5 py-4 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30">
                            <td class="px-5 py-4 whitespace-nowrap">
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $log->created_at->format('d M Y') }}</p>
                                <p class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ $log->created_at->format('H:i:s') }} WIB</p>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <p class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $log->user->name ?? 'Sistem/Dihapus' }}</p>
                                <p class="text-xs font-black text-sky-600 dark:text-sky-400 uppercase">{{ $log->user->role ?? '-' }}</p>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-black bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800 uppercase tracking-wider">{{ $log->action }}</span>
                            </td>
                            <td class="px-5 py-4">
                                <p class="text-sm font-medium text-slate-700 dark:text-slate-300 {{ $log->action === 'CLEAR_LOGS' ? 'text-rose-600 dark:text-rose-400 font-bold' : '' }}">{{ $log->description }}</p>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <p class="text-xs font-mono font-bold text-slate-500 dark:text-slate-400">{{ $log->ip_address ?? '-' }}</p>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-5 py-8 text-center text-slate-500 dark:text-slate-400 font-medium">Belum ada catatan aktivitas sistem.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800">
                {{ $logs->links() }}
            </div>
        </div>
    </div>

    <div id="logDeleteModal" class="fixed inset-0 bg-slate-900/60 dark:bg-slate-900/80 backdrop-blur-sm z-[100] hidden items-center justify-center">
        <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl w-full max-w-sm p-6 relative z-[110] text-center border border-slate-100 dark:border-slate-700">
            <div class="w-16 h-16 rounded-2xl bg-rose-100 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 flex items-center justify-center text-3xl mx-auto mb-4 shadow-inner border border-rose-200 dark:border-rose-800">⚠️</div>
            <h3 class="text-xl font-black text-slate-800 dark:text-white mb-2">Kosongkan Log Keamanan?</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mb-6">Tindakan ini tidak bisa dibatalkan. Seluruh riwayat sistem akan terhapus, namun tindakan penghapusan ini akan tetap tercatat.</p>
            <div class="flex gap-3">
                <button type="button" onclick="closeLogDeleteModal()" class="flex-1 px-4 py-3 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-bold rounded-2xl text-sm border border-slate-200 dark:border-slate-600">Batal</button>
                <form action="{{ route('admin.activity-logs.clear') }}" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full px-4 py-3 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-2xl shadow-lg shadow-rose-600/30 text-sm">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openLogDeleteModal() {
            const modal = document.getElementById('logDeleteModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeLogDeleteModal() {
            const modal = document.getElementById('logDeleteModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
</x-app-layout>
