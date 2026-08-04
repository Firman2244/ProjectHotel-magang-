<x-app-layout>
    <style>
        [x-cloak] { display: none !important; }
    </style>

    <div class="min-h-screen bg-sky-950/5 dark:bg-slate-900 flex flex-col md:flex-row relative transition-colors duration-300" x-data="{ sidebarOpen: false, imageModalOpen: false, imageModalSrc: '', deleteModalOpen: false, deleteActionUrl: '' }">
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
                    <span class="font-extrabold text-xl text-blue-700 dark:text-sky-400 tracking-tight">Laporan & Catatan Staf</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Login as: <span class="text-sky-700 dark:text-sky-400 font-black">{{ Auth::user()->name }}</span></span>
                </div>
            </header>

            <div class="p-4 md:p-8 space-y-6">
                <h2 class="font-black text-2xl text-slate-800 dark:text-white">Daftar Laporan & Catatan Masuk</h2>

                @if(session('success'))
                    <div class="bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-400 p-4 rounded-xl font-bold">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
                    @foreach($notes as $note)
                        <div class="p-6 rounded-2xl border transition-all duration-300 flex flex-col justify-between {{ $note->is_read ? 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700 opacity-70' : 'bg-gradient-to-br from-amber-50/60 to-sky-50/40 dark:from-amber-900/20 dark:to-sky-900/20 border-sky-300 dark:border-sky-700 shadow-md ring-4 ring-sky-50 dark:ring-sky-900/30' }}">
                            <div>
                                <div class="flex justify-between items-start mb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 {{ $note->is_read ? 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400' : 'bg-sky-600 dark:bg-sky-500 text-white shadow-sm' }} rounded-xl flex items-center justify-center font-black transition-colors duration-300">
                                            {{ strtoupper(substr($note->user->name ?? 'S', 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="font-black {{ $note->is_read ? 'text-slate-800 dark:text-slate-200' : 'text-slate-900 dark:text-white' }} block">{{ $note->user->name ?? 'Staf' }}</span>
                                            <span class="text-xs {{ $note->is_read ? 'text-slate-500 dark:text-slate-400' : 'text-sky-700 dark:text-sky-400 font-bold' }} block">{{ $note->created_at->format('d M Y, H:i') }}</span>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @if(!$note->is_read)
                                            <span class="bg-sky-600 text-white text-[10px] px-3 py-1.5 rounded-lg font-black uppercase tracking-wider animate-pulse shadow-sm">Baru</span>
                                        @endif
                                        <button type="button" @click="deleteActionUrl = '{{ route('admin.notes.destroy', $note->id) }}'; deleteModalOpen = true" class="p-2 text-slate-400 dark:text-slate-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/30 rounded-xl transition" title="Hapus Laporan">
                                            🗑️
                                        </button>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <h4 class="font-black text-lg {{ $note->is_read ? 'text-slate-800 dark:text-slate-200' : 'text-sky-950 dark:text-sky-100' }} mb-2">{{ $note->title }}</h4>
                                    <p class="{{ $note->is_read ? 'text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-900/50 border-slate-100 dark:border-slate-700' : 'text-slate-800 dark:text-slate-200 bg-white/80 dark:bg-slate-800/80 border-sky-200 dark:border-sky-800 font-medium' }} text-sm p-4 rounded-xl border">{{ $note->message }}</p>
                                </div>

                                @if($note->image)
                                    <div class="mb-4">
                                        <p class="text-xs font-bold {{ $note->is_read ? 'text-slate-400 dark:text-slate-500' : 'text-sky-600 dark:text-sky-400' }} uppercase tracking-wider mb-2">Lampiran Foto:</p>
                                        <button type="button" @click.prevent="imageModalSrc = '{{ asset('storage/' . $note->image) }}'; imageModalOpen = true" class="w-full text-left focus:outline-none block rounded-xl overflow-hidden border {{ $note->is_read ? 'border-slate-200 dark:border-slate-700' : 'border-sky-300 dark:border-sky-700' }} bg-slate-950/5 dark:bg-slate-900 hover:opacity-90 transition cursor-zoom-in relative group flex items-center justify-center p-2">
                                            <img src="{{ asset('storage/' . $note->image) }}" alt="Bukti Foto" class="max-h-48 w-auto object-contain rounded-lg">
                                            <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition duration-300 flex items-center justify-center">
                                                <span class="bg-white text-slate-800 text-xs font-black px-3 py-1.5 rounded-lg shadow-sm">🔍 Perbesar Foto</span>
                                            </div>
                                        </button>
                                    </div>
                                @endif
                            </div>

                            @if(!$note->is_read)
                                <form action="{{ route('admin.notes.read', $note->id) }}" method="POST" class="mt-4 pt-4 border-t border-sky-200/50 dark:border-sky-800/50">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="w-full bg-white dark:bg-slate-800 hover:bg-sky-50 dark:hover:bg-slate-700 text-sky-700 dark:text-sky-400 font-black py-3 px-4 rounded-xl border border-sky-200 dark:border-sky-800 transition shadow-sm">Tandai Sudah Dibaca</button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>

                @if($notes->isEmpty())
                    <div class="bg-white dark:bg-slate-800 p-10 rounded-3xl border border-slate-200 dark:border-slate-700 text-center transition-colors duration-300">
                        <span class="text-5xl block mb-4">✨</span>
                        <h3 class="font-black text-xl text-slate-800 dark:text-white">Tidak ada laporan masuk</h3>
                        <p class="text-slate-500 dark:text-slate-400 font-medium">Belum ada catatan atau laporan baru dari staf.</p>
                    </div>
                @endif
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

        <div x-cloak x-show="deleteModalOpen" class="fixed inset-0 z-[60] flex items-center justify-center">
            <div x-show="deleteModalOpen" x-transition.opacity class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="deleteModalOpen = false"></div>

            <div x-show="deleteModalOpen"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-90"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-90"
                 class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl w-full max-w-sm p-6 relative z-[70] text-center border border-slate-100 dark:border-slate-700">
                <div class="w-16 h-16 rounded-2xl bg-rose-100 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 flex items-center justify-center text-3xl mx-auto mb-4 shadow-inner">
                    🗑️
                </div>
                <h3 class="text-xl font-black text-slate-800 dark:text-white mb-2">Hapus Laporan?</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mb-6">Laporan dan foto lampirannya akan dihapus permanen dari sistem.</p>

                <div class="flex gap-3">
                    <button type="button" @click="deleteModalOpen = false" class="flex-1 px-4 py-3 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-bold rounded-2xl transition">
                        Batal
                    </button>
                    <form :action="deleteActionUrl" method="POST" class="flex-1">
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
</x-app-layout>
