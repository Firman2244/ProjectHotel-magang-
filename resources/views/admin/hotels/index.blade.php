<x-app-layout>
    <style>
        [x-cloak] { display: none !important; }
    </style>

    <div class="min-h-screen bg-sky-950/5 dark:bg-slate-900 flex flex-col md:flex-row relative transition-colors duration-300" x-data="{ sidebarOpen: false }">
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
                    <span class="font-extrabold text-xl text-blue-700 dark:text-sky-400 tracking-tight">Data Hotel</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Login as: <span class="text-sky-700 dark:text-sky-400 font-black">{{ Auth::user()->name }}</span></span>
                </div>
            </header>

            <div class="p-4 md:p-8 space-y-6">
                <div class="md:hidden flex items-center justify-between bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-sm border border-sky-100 dark:border-slate-700 transition-colors duration-300">
                    <span class="font-extrabold text-sm text-blue-700 dark:text-sky-400 tracking-tight">Data Hotel</span>
                </div>

                @if(session('success'))
                    <div class="bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-400 px-5 py-4 rounded-2xl shadow-sm font-bold text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-black text-slate-800 dark:text-white">Daftar Cabang Hotel</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">Kelola informasi cabang hotel yang terdaftar di dalam sistem.</p>
                    </div>
                    <a href="{{ route('admin.hotels.create') }}" class="inline-flex items-center gap-2 bg-sky-600 hover:bg-sky-700 text-white font-bold py-3 px-5 rounded-xl shadow-md transition text-sm">
                        <span>➕</span> Tambah Hotel
                    </a>
                </div>

                <div class="bg-sky-50/40 dark:bg-slate-800/90 backdrop-blur-md rounded-2xl shadow-sm border border-sky-200/60 dark:border-slate-700 overflow-hidden transition-colors duration-300">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-sky-100/40 dark:bg-slate-700/50 border-b border-sky-200/60 dark:border-slate-600">
                                    <th class="px-6 py-4 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama Hotel</th>
                                    <th class="px-6 py-4 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">Alamat</th>
                                    <th class="px-6 py-4 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nomor Telepon</th>
                                    <th class="px-6 py-4 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-sky-200/50 dark:divide-slate-700">
                                @forelse($hotels as $hotel)
                                    <tr class="hover:bg-sky-100/30 dark:hover:bg-slate-700/50 transition">
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
                                                <a href="{{ route('admin.hotels.edit', $hotel->id) }}" class="inline-flex items-center px-3.5 py-1.5 bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-amber-900/50 font-bold rounded-xl transition border border-amber-200 dark:border-amber-800 text-xs">
                                                    Edit
                                                </a>
                                                <form action="{{ route('admin.hotels.destroy', $hotel->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus hotel ini?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center px-3.5 py-1.5 bg-rose-50 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/50 font-bold rounded-xl transition border border-rose-200 dark:border-rose-800 text-xs">
                                                        Hapus
                                                    </button>
                                                </form>
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
        </div>
    </div>
</x-app-layout>
