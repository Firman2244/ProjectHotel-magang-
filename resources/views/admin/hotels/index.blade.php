<x-app-layout>
    <div x-data="{ deleteModalOpen: false, deleteActionUrl: '', typedConfirmation: '', deleteStep: 1 }">

        <header class="bg-white/80 dark:bg-slate-900/50 backdrop-blur-md border-b border-slate-200 dark:border-slate-700 h-16 hidden md:flex items-center justify-between px-6 shadow-sm sticky top-0 z-20">
            <div class="flex items-center">
                <span class="font-extrabold text-xl text-slate-800 dark:text-white tracking-tight">Data Hotel</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Login as: <span class="text-sky-700 dark:text-sky-400 font-black">{{ Auth::user()->name }}</span></span>
            </div>
        </header>

        <div class="p-4 md:p-8 space-y-6">
            <div class="md:hidden flex items-center justify-between bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                <span class="font-extrabold text-sm text-slate-800 dark:text-white tracking-tight">Data Hotel</span>
            </div>

            @if(session('success'))
                <div class="bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-400 px-5 py-4 rounded-2xl shadow-sm font-bold text-sm">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="bg-rose-50 dark:bg-rose-900/30 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-400 px-5 py-4 rounded-2xl shadow-sm font-bold text-sm">
                    {{ $errors->first() }}
                </div>
            @endif

            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-black text-slate-800 dark:text-white">Daftar Cabang Hotel</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">Kelola informasi cabang hotel yang terdaftar di dalam sistem.</p>
                </div>
                <a href="{{ route('admin.hotels.create') }}" class="inline-flex items-center gap-2 bg-sky-600 hover:bg-sky-700 text-white font-bold py-3 px-5 rounded-xl shadow-md text-sm">
                    <span>➕</span> Tambah Hotel
                </a>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-700/50 border-b border-slate-200 dark:border-slate-600">
                                <th class="px-6 py-4 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama Hotel</th>
                                <th class="px-6 py-4 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">Alamat</th>
                                <th class="px-6 py-4 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nomor Telepon</th>
                                <th class="px-6 py-4 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @forelse($hotels as $hotel)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $hotel->name }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-slate-600 dark:text-slate-400 line-clamp-2">{{ $hotel->address ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-sky-700 dark:text-sky-400">{{ $hotel->phone_number ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('admin.hotels.edit', $hotel->id) }}" class="inline-flex items-center px-3.5 py-1.5 bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-amber-900/50 font-bold rounded-xl border border-amber-200 dark:border-amber-800 text-xs">
                                                Edit
                                            </a>
                                            <button type="button" @click="deleteActionUrl = '{{ route('admin.hotels.destroy', $hotel->id) }}'; typedConfirmation = ''; deleteStep = 1; deleteModalOpen = true" class="inline-flex items-center px-3.5 py-1.5 bg-rose-50 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/50 font-bold rounded-xl border border-rose-200 dark:border-rose-800 text-xs">
                                                Hapus
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400 font-medium">
                                        Belum ada data hotel. Silakan tambahkan hotel baru.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div x-cloak x-show="deleteModalOpen" class="fixed inset-0 z-[80] flex items-center justify-center p-4">
            <div x-show="deleteModalOpen" class="absolute inset-0 bg-slate-900/60 dark:bg-slate-900/80 backdrop-blur-sm" @click="deleteModalOpen = false"></div>

            <div x-show="deleteModalOpen" class="relative z-[90] bg-white dark:bg-slate-800 rounded-3xl shadow-2xl w-full max-w-md p-6 text-center border border-slate-100 dark:border-slate-700">

                <div x-show="deleteStep === 1">
                    <div class="w-16 h-16 rounded-2xl bg-rose-100 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 flex items-center justify-center text-3xl mx-auto mb-4 shadow-inner">
                        ⚠️
                    </div>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white mb-2">Hapus Cabang Hotel?</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mb-3">
                        Untuk konfirmasi, silakan ketik <code class="bg-slate-100 dark:bg-slate-700 px-1.5 py-0.5 rounded text-rose-600 dark:text-rose-400 font-bold select-all">HAPUS_HOTEL_DENGAN_SADAR</code> di bawah ini:
                    </p>
                    <input type="text" x-model="typedConfirmation" placeholder="Ketik konfirmasi di sini..." class="w-full mb-5 border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-white font-mono font-semibold rounded-xl shadow-sm py-2 px-3 text-xs focus:border-rose-500 focus:ring-rose-500 text-center tracking-tight">

                    <div class="flex gap-3">
                        <button type="button" @click="deleteModalOpen = false" class="flex-1 px-4 py-3 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-bold rounded-2xl text-sm">
                            Batal
                        </button>
                        <button type="button"
                                :disabled="typedConfirmation !== 'HAPUS_HOTEL_DENGAN_SADAR'"
                                @click="deleteStep = 2"
                                :class="typedConfirmation === 'HAPUS_HOTEL_DENGAN_SADAR' ? 'bg-rose-600 hover:bg-rose-700 text-white shadow-lg shadow-rose-600/30 cursor-pointer' : 'bg-slate-300 dark:bg-slate-700 text-slate-400 dark:text-slate-500 cursor-not-allowed'"
                                class="flex-1 px-4 py-3 font-bold rounded-2xl text-sm">
                            Konfirmasi
                        </button>
                    </div>
                </div>

                <div x-show="deleteStep === 2" x-cloak>
                    <div class="w-16 h-16 rounded-2xl bg-rose-500/20 text-rose-600 dark:text-rose-400 flex items-center justify-center text-3xl mx-auto mb-4 shadow-inner animate-pulse">
                        🚨
                    </div>
                    <h3 class="text-xl font-black text-slate-800 dark:text-white mb-2">Peringatan Terakhir!</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mb-6">
                        Apakah Anda benar-benar yakin ingin menghapus data hotel ini secara permanen dari server? Tindakan ini tidak dapat diurungkan dan berisiko mempengaruhi data staf/laporan terkait.
                    </p>

                    <div class="flex gap-3">
                        <button type="button" @click="deleteStep = 1; typedConfirmation = ''" class="flex-1 px-4 py-3 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-bold rounded-2xl text-sm">
                            Kembali
                        </button>
                        <form :action="deleteActionUrl" method="POST" class="flex-1">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full px-4 py-3 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-2xl shadow-lg shadow-rose-600/30 text-sm">
                                Ya, Hapus Permanen
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
