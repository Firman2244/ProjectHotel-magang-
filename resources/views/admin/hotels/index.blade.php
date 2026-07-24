<x-app-layout>
    <div class="min-h-screen bg-sky-950/5 flex">
        <x-admin-sidebar />

        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
            <header class="bg-sky-50/30 backdrop-blur-md border-b border-sky-100 h-16 flex items-center justify-between px-6 shadow-sm">
                <div class="flex items-center">
                    <span class="font-extrabold text-xl text-blue-700 tracking-tight">Data Hotel</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs font-bold text-slate-500">Login as: <span class="text-sky-700 font-black">{{ Auth::user()->name }}</span></span>
                </div>
            </header>

            <div class="p-8 space-y-6">
                @if(session('success'))
                    <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-2xl shadow-sm font-bold text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-black text-slate-800">Daftar Cabang Hotel</h2>
                        <p class="text-xs text-slate-500 font-medium mt-1">Kelola informasi cabang hotel yang terdaftar di dalam sistem.</p>
                    </div>
                    <a href="{{ route('admin.hotels.create') }}" class="inline-flex items-center gap-2 bg-sky-600 hover:bg-sky-700 text-white font-bold py-3 px-5 rounded-xl shadow-md transition text-sm">
                        <span>➕</span> Tambah Hotel
                    </a>
                </div>

                <div class="bg-sky-50/40 backdrop-blur-md rounded-2xl shadow-sm border border-sky-200/60 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-sky-100/40 border-b border-sky-200/60">
                                    <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-wider">Nama Hotel</th>
                                    <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-wider">Alamat</th>
                                    <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-wider">Nomor Telepon</th>
                                    <th class="px-6 py-4 text-xs font-black text-slate-500 uppercase tracking-wider text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-sky-200/50">
                                @forelse($hotels as $hotel)
                                    <tr class="hover:bg-sky-100/30 transition">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-slate-800">{{ $hotel->name }}</div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm font-medium text-slate-600 line-clamp-2">{{ $hotel->address ?? '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-semibold text-sky-700">{{ $hotel->phone_number ?? '-' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end gap-2">
                                                <a href="{{ route('admin.hotels.edit', $hotel->id) }}" class="inline-flex items-center px-3.5 py-1.5 bg-amber-50 text-amber-700 hover:bg-amber-100 font-bold rounded-xl transition border border-amber-200 text-xs">
                                                    Edit
                                                </a>
                                                <form action="{{ route('admin.hotels.destroy', $hotel->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus hotel ini?');" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="inline-flex items-center px-3.5 py-1.5 bg-rose-50 text-rose-700 hover:bg-rose-100 font-bold rounded-xl transition border border-rose-200 text-xs">
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-slate-500 font-medium">
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
