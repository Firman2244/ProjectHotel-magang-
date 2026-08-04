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
                <span class="font-extrabold text-xl text-blue-700 dark:text-sky-400 tracking-tight">Master Tugas (SOP)</span>
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Login as: <span class="text-sky-700 dark:text-sky-400 font-black">{{ Auth::user()->name }}</span></span>
            </header>

            <div class="p-4 md:p-8 space-y-6">
                <div class="md:hidden flex items-center justify-between bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-sm border border-sky-100 dark:border-slate-700 transition-colors duration-300">
                    <span class="font-extrabold text-sm text-blue-700 dark:text-sky-400 tracking-tight">Master Tugas (SOP)</span>
                </div>

                @if(session('success'))
                    <div class="bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-400 px-5 py-4 rounded-2xl shadow-sm font-bold text-sm">{{ session('success') }}</div>
                @endif

                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 class="text-xl font-black text-slate-800 dark:text-white">Daftar Pekerjaan Harian</h2>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">Kelola daftar SOP tugas harian berdasarkan departemen.</p>
                    </div>
                    <div class="flex items-center gap-3 w-full md:w-auto">
                        <form method="GET" action="{{ route('admin.tasks.index') }}" class="flex-1 md:w-48">
                            <select name="department" onchange="this.form.submit()" class="w-full border-sky-200 dark:border-slate-600 bg-white/80 dark:bg-slate-800 text-slate-800 dark:text-white font-semibold rounded-xl shadow-sm py-3 px-3 text-sm focus:border-sky-500 focus:ring-sky-500 cursor-pointer transition-colors duration-300">
                                <option value="">Semua Departemen</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                                @endforeach
                            </select>
                        </form>
                        <a href="{{ route('admin.tasks.create') }}" class="inline-flex items-center gap-2 bg-sky-600 hover:bg-sky-700 text-white font-bold py-3 px-5 rounded-xl shadow-md transition text-sm whitespace-nowrap"><span>➕</span> Tambah Tugas</a>
                    </div>
                </div>

                <div class="bg-sky-50/40 dark:bg-slate-800/90 backdrop-blur-md rounded-2xl shadow-sm border border-sky-200/60 dark:border-slate-700 overflow-hidden transition-colors duration-300">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-sky-100/40 dark:bg-slate-700/50 border-b border-sky-200/60 dark:border-slate-600">
                                    <th class="px-6 py-4 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider w-16">No</th>
                                    <th class="px-6 py-4 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama Pekerjaan (SOP)</th>
                                    <th class="px-6 py-4 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">Departemen</th>
                                    <th class="px-6 py-4 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-sky-200/50 dark:divide-slate-700">
                                @forelse($tasks as $index => $task)
                                    <tr class="hover:bg-sky-100/30 dark:hover:bg-slate-700/50 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-500 dark:text-slate-400">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $task->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 inline-flex text-[10px] leading-5 font-black uppercase rounded-full bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-400 border border-sky-200 dark:border-sky-800">
                                                {{ $task->department }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end gap-2">
                                                <a href="{{ route('admin.tasks.edit', $task->id) }}" class="inline-flex items-center px-3.5 py-1.5 bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-amber-900/50 font-bold rounded-xl transition border border-amber-200 dark:border-amber-800 text-xs">Edit</a>
                                                <form action="{{ route('admin.tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus tugas ini dari SOP?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center px-3.5 py-1.5 bg-rose-50 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/50 font-bold rounded-xl transition border border-rose-200 dark:border-rose-800 text-xs">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400 font-medium">Belum ada daftar tugas terdaftar.</td>
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
