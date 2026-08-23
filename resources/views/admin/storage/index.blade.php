<x-app-layout>
    <div x-data="{ imageModalOpen: false, imageModalSrc: '' }">

        <header class="bg-white/80 dark:bg-slate-900/50 backdrop-blur-md border-b border-slate-200 dark:border-slate-700 h-16 hidden md:flex items-center justify-between px-6 shadow-sm sticky top-0 z-20">
            <div class="flex items-center">
                <span class="font-extrabold text-xl text-slate-800 dark:text-white tracking-tight">Manajemen Storage</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Login as: <span class="text-sky-700 dark:text-sky-400 font-black">{{ Auth::user()->name }}</span></span>
            </div>
        </header>

        <div class="p-4 md:p-8 space-y-6">
            <div class="md:hidden flex items-center justify-between bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                <span class="font-extrabold text-sm text-slate-800 dark:text-white tracking-tight">Manajemen Storage</span>
            </div>

            <div class="flex justify-between items-center mb-2">
                <div>
                    <h2 class="text-xl font-black text-slate-800 dark:text-white">Penyimpanan Media</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">Pantau dan kelola kapasitas file gambar pelaporan sistem.</p>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-400 px-5 py-4 rounded-2xl shadow-sm font-bold text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Kolom Kiri: Info & Pengaturan -->
                <div class="md:col-span-1 space-y-6">
                    <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700 text-center">
                        <div class="w-20 h-20 bg-sky-100 dark:bg-sky-900/30 text-sky-600 dark:text-sky-400 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl shadow-inner border border-sky-200 dark:border-sky-800">💾</div>
                        <p class="text-sm font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">Total Penggunaan</p>
                        <p class="text-4xl font-black text-slate-800 dark:text-white">{{ $totalSizeMb ?? '0' }} <span class="text-lg text-slate-400 dark:text-slate-500">MB</span></p>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mt-2">Dari {{ count($images ?? []) }} gambar tersimpan</p>
                    </div>

                    <div class="bg-white dark:bg-slate-800 p-6 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700 space-y-5">
                        <h3 class="font-black text-slate-800 dark:text-white text-lg flex items-center gap-2">⚙️ Hapus Otomatis</h3>

                        <div class="p-4 rounded-2xl border {{ isset($autoDeleteDays) && $autoDeleteDays > 0 ? 'bg-sky-50 dark:bg-sky-900/30 border-sky-200 dark:border-sky-800 text-sky-900 dark:text-sky-300' : 'bg-slate-50 dark:bg-slate-700/50 border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300' }}">
                            <p class="text-[11px] font-extrabold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-1">Status Berjalan:</p>
                            @if(isset($autoDeleteDays) && $autoDeleteDays > 0)
                                <p class="text-sm font-bold">🧹 Otomatis dihapus tiap <span class="text-sky-700 dark:text-sky-400 font-black underline">{{ $autoDeleteDays }} hari</span>.</p>
                            @else
                                <p class="text-sm font-bold">💤 Fitur hapus otomatis <span class="text-slate-700 dark:text-slate-400 font-black">Nonaktif</span> (0).</p>
                            @endif
                        </div>

                        <form action="{{ route('admin.storage.settings') }}" method="POST">
                            @csrf
                            <label class="block text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Ubah Batas Hari:</label>
                            <div class="flex gap-2">
                                <input type="number" name="auto_delete_days" value="{{ $autoDeleteDays ?? 0 }}" placeholder="0" class="w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white rounded-xl focus:ring-sky-500 focus:border-sky-500 font-bold placeholder-slate-400 text-sm px-4 py-2" min="0" required>
                                <span class="flex items-center px-4 bg-slate-100 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl font-bold text-slate-500 dark:text-slate-400 text-sm">Hari</span>
                            </div>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-2 mb-4">Set angka ke <b>0</b> untuk mematikan.</p>
                            <button type="submit" class="w-full bg-slate-800 hover:bg-slate-900 dark:bg-sky-600 dark:hover:bg-sky-500 text-white font-bold py-2.5 px-4 rounded-xl shadow-sm text-sm">Simpan Pengaturan</button>
                        </form>
                    </div>

                    <div class="bg-rose-50 dark:bg-rose-900/10 p-6 rounded-3xl shadow-sm border border-rose-200 dark:border-rose-800 text-center">
                        <h3 class="font-black text-rose-800 dark:text-rose-400 text-lg mb-2">⚠️ Zona Bahaya</h3>
                        <p class="text-xs text-rose-600 dark:text-rose-500 mb-4 font-medium">Hapus seluruh gambar saat ini secara permanen untuk mengosongkan server.</p>
                        <button type="button" onclick="openStorageDeleteModal()" class="w-full bg-rose-600 hover:bg-rose-700 text-white font-bold py-3 px-4 rounded-xl shadow-md text-sm">Kosongkan Storage</button>
                    </div>
                </div>

                <!-- Kolom Kanan: Galeri -->
                <div class="md:col-span-2 bg-white dark:bg-slate-800 p-6 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700 flex flex-col">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="font-black text-slate-800 dark:text-white text-lg flex items-center gap-2">🖼️ Galeri Media</h3>
                        <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Total: {{ count($images ?? []) }}</span>
                    </div>

                    @if(isset($images) && count($images) > 0)
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 content-start flex-1 overflow-y-auto pr-2 max-h-[700px]">
                            @foreach($images as $img)
                                <div @click="imageModalSrc = '{{ $img['url'] }}'; imageModalOpen = true" class="relative group rounded-2xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-900 flex items-center justify-center h-40 p-2 cursor-zoom-in">
                                    <img src="{{ $img['url'] }}" class="max-h-full max-w-full object-contain rounded-lg transition-transform duration-300 group-hover:scale-105" loading="lazy">
                                    <div class="absolute inset-0 bg-slate-900/70 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col items-center justify-center text-white text-xs p-2 text-center backdrop-blur-[2px]">
                                        <span class="text-2xl mb-1 drop-shadow-md">🔍</span>
                                        <span class="font-bold text-[11px]">{{ $img['size'] }} KB</span>
                                        <span class="text-[10px] text-slate-300 font-medium mt-0.5">{{ date('d M Y', $img['last_modified']) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center flex-1 min-h-[400px] text-slate-400 dark:text-slate-500 bg-slate-50 dark:bg-slate-900/50 rounded-2xl border border-dashed border-slate-200 dark:border-slate-700">
                            <span class="text-5xl mb-4 opacity-50">📭</span>
                            <p class="font-bold text-sm">Storage kosong, tidak ada gambar tersimpan.</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>

        <!-- Modal Lihat Gambar -->
        <div x-cloak x-show="imageModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div x-show="imageModalOpen" class="absolute inset-0 bg-slate-900/90 backdrop-blur-sm" @click="imageModalOpen = false"></div>
            <div x-show="imageModalOpen" class="relative z-[110] max-w-5xl w-full flex flex-col items-center">
                <img @click="imageModalOpen = false" :src="imageModalSrc" class="max-h-[85vh] w-auto max-w-full rounded-2xl shadow-2xl border border-white/10 object-contain bg-black/50 cursor-zoom-out" title="Klik untuk menutup">
            </div>
        </div>

        <!-- Modal Hapus Semua -->
        <div id="storageDeleteModal" class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-[100] hidden items-center justify-center">
            <div class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl w-full max-w-sm p-6 relative z-[110] text-center border border-slate-100 dark:border-slate-700">
                <div class="w-16 h-16 rounded-2xl bg-rose-100 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 flex items-center justify-center text-3xl mx-auto mb-4 shadow-inner border border-rose-200 dark:border-rose-800">
                    ⚠️
                </div>
                <h3 class="text-xl font-black text-slate-800 dark:text-white mb-2">Hapus Semua Media?</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mb-6">Tindakan ini tidak bisa dibatalkan. Seluruh gambar laporan & catatan di server akan hilang permanen.</p>
                <div class="flex gap-3">
                    <button type="button" onclick="closeStorageDeleteModal()" class="flex-1 px-4 py-3 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-bold rounded-2xl text-sm border border-slate-200 dark:border-slate-600">
                        Batal
                    </button>
                    <form action="{{ route('admin.storage.clear') }}" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-3 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-2xl shadow-lg shadow-rose-600/30 text-sm">
                            Ya, Kosongkan
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
