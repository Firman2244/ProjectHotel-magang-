<x-app-layout>
    <div class="min-h-screen bg-sky-950/5 flex">
        <x-admin-sidebar />

        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
            <header class="bg-sky-50/30 backdrop-blur-md border-b border-sky-100 h-16 flex items-center justify-between px-6 shadow-sm">
                <span class="font-extrabold text-xl text-blue-700 tracking-tight">Master Tugas (SOP)</span>
                <span class="text-xs font-bold text-slate-500">Login as: <span class="text-sky-700 font-black">{{ Auth::user()->name }}</span></span>
            </header>

            <div class="p-8 space-y-6">
                @if(session('success'))
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl shadow-sm font-bold text-sm">{{ session('success') }}</div>
                @endif

                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                    <div>
                        <h2 class="text-xl font-black text-slate-800">Daftar Pekerjaan Harian</h2>
                        <p class="text-xs text-slate-500 font-medium mt-1">Kelola daftar SOP tugas harian berdasarkan departemen.</p>
                    </div>
                    <div class="flex items-center gap-3 w-full md:w-auto">
                        <form method="GET" action="{{ route('admin.tasks.index') }}" class="flex-1 md:w-48">
                            <select name="department" onchange="this.form.submit()" class="w-full border-sky-200 bg-white/80 text-slate-800 font-semibold rounded-xl shadow-sm py-3 px-3 text-sm focus:border-sky-500 focus:ring-sky-500 cursor-pointer">
                                <option value="">Semua Departemen</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                                @endforeach
                            </select>
                        </form>
                        <a href="{{ route('admin.tasks.create') }}" class="inline-flex items-center gap-2 bg-sky-600 hover:bg-sky-700 text-white font-bold py-3 px-5 rounded-xl shadow-md transition text-sm whitespace-nowrap"><span>➕</span> Tambah Tugas</a>
                    </div>
                </div>

                <div class="bg-sky-50/40 backdrop-blur-md rounded-2xl shadow-sm border border-sky-200/60 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-sky-100/40 border-b border-sky-200/60">
                                    <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-wider w-16">No</th>
                                    <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-wider">Nama Pekerjaan (SOP)</th>
                                    <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-wider">Departemen</th>
                                    <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-wider text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-sky-200/50">
                                @forelse($tasks as $index => $task)
                                    <tr class="hover:bg-sky-100/30 transition">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-500">{{ $index + 1 }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-slate-800">{{ $task->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 py-1 inline-flex text-[10px] leading-5 font-black uppercase rounded-full bg-sky-100 text-sky-700 border border-sky-200">
                                                {{ $task->department }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end gap-2">
                                                <a href="{{ route('admin.tasks.edit', $task->id) }}" class="inline-flex items-center px-3.5 py-1.5 bg-amber-50 text-amber-700 hover:bg-amber-100 font-bold rounded-xl transition border border-amber-200 text-xs">Edit</a>
                                                <form action="{{ route('admin.tasks.destroy', $task->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus tugas ini dari SOP?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center px-3.5 py-1.5 bg-rose-50 text-rose-700 hover:bg-rose-100 font-bold rounded-xl transition border border-rose-200 text-xs">Hapus</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-slate-500 font-medium">Belum ada daftar tugas terdaftar.</td>
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
