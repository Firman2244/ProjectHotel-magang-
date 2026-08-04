<x-app-layout>
    <style>
        [x-cloak] { display: none !important; }
    </style>

    <div class="min-h-screen bg-sky-950/5 dark:bg-slate-900 flex flex-col md:flex-row relative transition-colors duration-300" x-data="{ sidebarOpen: false, imageModalOpen: false, imageModalSrc: '' }">
        <div class="md:hidden sticky top-0 z-40 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 shadow-sm flex items-center justify-between h-16 px-4 transition-colors duration-300">
            <h1 class="font-black text-xl text-slate-800 dark:text-white">Admin Panel</h1>
            <button type="button" @click="sidebarOpen = !sidebarOpen" class="p-2 rounded-xl text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </div>

        <div x-cloak :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'" class="fixed inset-y-0 left-0 z-50 w-64 h-screen bg-white dark:bg-slate-800 shadow-2xl md:shadow-none transform transition-transform duration-300 ease-in-out flex-shrink-0 md:fixed">
            <x-admin-sidebar />
        </div>

        <div x-cloak x-show="sidebarOpen" x-transition.opacity @click="sidebarOpen = false" class="fixed inset-0 bg-black/50 z-40 md:hidden"></div>

        <div class="flex-1 flex flex-col min-w-0 md:ml-64 overflow-y-auto relative">

            <header class="bg-sky-50/30 dark:bg-slate-900/50 backdrop-blur-md border-b border-sky-100 dark:border-slate-700 h-16 hidden md:flex items-center justify-between px-6 shadow-sm transition-colors duration-300">
                <div class="flex items-center">
                    <span class="font-extrabold text-xl text-blue-700 dark:text-sky-400 tracking-tight">Manajemen Storage</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Login as: <span class="text-sky-700 dark:text-sky-400 font-black">{{ Auth::user()->name }}</span></span>
                </div>
            </header>

            <div class="p-4 md:p-8 space-y-6">

                <div class="flex justify-between items-center mb-6">
                    <h2 class="font-black text-3xl text-slate-800 dark:text-white">Manajemen Storage (Gambar)</h2>
                </div>

                @if(session('success'))
                    <div class="bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 px-5 py-4 rounded-2xl relative shadow-sm font-bold">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-1 space-y-6">
                        <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700 text-center transition-colors duration-300">
                            <div class="w-20 h-20 bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl shadow-inner">💾</div>
                            <p class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Total Penggunaan</p>
                            <p class="text-4xl font-black text-slate-800 dark:text-white">{{ $totalSizeMb }} <span class="text-lg text-slate-400 dark:text-slate-500">MB</span></p>
                            <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mt-2">Dari {{ count($images) }} gambar tersimpan</p>
                        </div>

                        <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700 space-y-5 transition-colors duration-300">
                            <h3 class="font-black text-slate-800 dark:text-white text-lg flex items-center gap-2">⚙️ Hapus Otomatis</h3>

                            <div class="p-4 rounded-2xl border {{ $autoDeleteDays > 0 ? 'bg-sky-50 dark:bg-sky-900/30 border-sky-200 dark:border-sky-800 text-sky-900 dark:text-sky-300' : 'bg-slate-50 dark:bg-slate-700/50 border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300' }}">
                                <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">Status Berjalan:</p>
                                @if($autoDeleteDays > 0)
                                    <p class="text-sm font-bold">🧹 Gambar otomatis dihapus setiap <span class="text-sky-700 dark:text-sky-400 font-black underline">{{ $autoDeleteDays }} hari</span>.</p>
                                @else
                                    <p class="text-sm font-bold">💤 Fitur hapus otomatis saat ini sedang <span class="text-slate-700 dark:text-slate-400 font-black">Nonaktif</span> (0).</p>
                                @endif
                            </div>

                            <form action="{{ route('admin.storage.settings') }}" method="POST">
                                @csrf
                                <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Ubah Batas Hari:</label>
                                <div class="flex gap-2">
                                    <input type="number" name="auto_delete_days" value="" placeholder="Ketik angka..." class="w-full bg-white dark:bg-slate-700 border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white rounded-xl focus:ring-sky-500 focus:border-sky-500 font-bold placeholder-slate-400 dark:placeholder-slate-500" min="0" required>
                                    <span class="flex items-center px-3 bg-slate-100 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl font-bold text-slate-500 dark:text-slate-400">Hari</span>
                                </div>
                                <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-2 mb-4">Set angka ke <b>0</b> jika fitur hapus otomatis ingin dimatikan.</p>
                                <button type="submit" class="w-full bg-slate-800 hover:bg-slate-900 dark:bg-slate-700 dark:hover:bg-slate-600 text-white font-bold py-2.5 px-4 rounded-xl transition">Simpan Pengaturan</button>
                            </form>
                        </div>

                        <div class="bg-rose-50 dark:bg-rose-900/10 p-6 rounded-3xl shadow-sm border border-rose-200 dark:border-rose-800 text-center transition-colors duration-300">
                            <h3 class="font-black text-rose-800 dark:text-rose-400 text-lg mb-2">⚠️ Zona Bahaya</h3>
                            <p class="text-xs text-rose-600 dark:text-rose-500 mb-4">Hapus seluruh gambar laporan & catatan saat ini secara permanen untuk mengosongkan memori.</p>
                            <button type="button" onclick="openStorageDeleteModal()" class="w-full bg-rose-600 hover:bg-rose-700 text-white font-bold py-3 px-4 rounded-xl shadow-md transition">Hapus Semua Gambar Sekarang</button>
                        </div>
                    </div>

                    <div class="md:col-span-2 bg-white dark:bg-slate-800 p-6 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700 transition-colors duration-300">
                        <h3 class="font-black text-slate-800 dark:text-white text-lg mb-6 flex items-center gap-2">🖼️ Preview Galeri Gambar</h3>

                        @if(count($images) > 0)
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 h-[650px] overflow-y-auto pr-2">
                                @foreach($images as $img)
                                    <div @click="imageModalSrc = '{{ $img['url'] }}'; imageModalOpen = true" class="relative group rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-600 bg-slate-950/5 dark:bg-slate-900 flex items-center justify-center h-36 p-2 cursor-zoom-in">
                                        <img src="{{ $img['url'] }}" class="max-h-full max-w-full object-contain rounded-lg transition duration-300 group-hover:scale-105" loading="lazy">
                                        <div class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover:opacity-100 transition duration-300 flex flex-col items-center justify-center text-white text-xs p-2 text-center">
                                            <span class="font-bold mb-1">🔍 Perbesar</span>
                                            <span class="font-bold text-[11px]">{{ $img['size'] }} KB</span>
                                            <span class="text-[10px] text-slate-300">{{ date('d M Y', $img['last_modified']) }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center h-64 text-slate-400 dark:text-slate-500">
                                <span class="text-4xl mb-3">📭</span>
                                <p class="font-bold">Storage kosong, tidak ada gambar tersimpan.</p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div x-cloak x-show="imageModalOpen" class="fixed inset-0 z-[60] flex items-center justify-center">
        <div x-show="imageModalOpen" x-transition.opacity class="absolute inset-0 bg-slate-900/90 backdrop-blur-sm" @click="imageModalOpen = false"></div>

        <div x-show="imageModalOpen"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 scale-90"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-90"
             class="relative z-[70] max-w-5xl w-full mx-4 flex flex-col items-center">

            <img @click="imageModalOpen = false" :src="imageModalSrc" class="max-h-[85vh] w-auto max-w-full rounded-2xl shadow-2xl border-4 border-white/10 object-contain bg-black/50 cursor-zoom-out" title="Klik untuk menutup">
        </div>
    </div>

    <div id="storageDeleteModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden items-center justify-center transition-opacity">
        <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl w-full max-w-sm p-6 transform transition-transform scale-100 border border-slate-100 dark:border-slate-700">
            <div class="text-center">
                <div class="w-16 h-16 rounded-2xl bg-rose-100 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 flex items-center justify-center text-3xl mx-auto mb-4 shadow-inner">
                    ⚠️
                </div>
                <h3 class="text-xl font-black text-slate-800 dark:text-white mb-2">Hapus Semua Gambar?</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mb-6">Tindakan ini tidak bisa dibatalkan. Seluruh gambar di server akan hilang selamanya.</p>
                <div class="flex gap-3">
                    <button type="button" onclick="closeStorageDeleteModal()" class="flex-1 px-4 py-3 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-bold rounded-2xl transition">
                        Batal
                    </button>
                    <form action="{{ route('admin.storage.clear') }}" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-3 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-2xl transition shadow-lg shadow-rose-600/30">
                            Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openStorageDeleteModal() {
            const modal = document.getElementById('storageDeleteModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeStorageDeleteModal() {
            const modal = document.getElementById('storageDeleteModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
</x-app-layout>
