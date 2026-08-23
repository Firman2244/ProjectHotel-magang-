<x-app-layout>
    <div x-data="{ showDeleteModal: false, deleteUrl: '', deleteName: '' }">

        <header class="bg-white/80 dark:bg-slate-900/50 backdrop-blur-md border-b border-slate-200 dark:border-slate-700 h-16 hidden md:flex items-center justify-between px-6 shadow-sm sticky top-0 z-20">
            <span class="font-extrabold text-xl text-slate-800 dark:text-white tracking-tight">Manajemen Staf</span>
        </header>

        <div class="p-4 md:p-8 space-y-6">
            <div class="md:hidden flex items-center justify-between bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                <span class="font-extrabold text-sm text-slate-800 dark:text-white tracking-tight">Manajemen Staf</span>
            </div>

            @if(session('success'))
                <div class="bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-400 px-5 py-4 rounded-2xl shadow-sm font-bold text-sm">{{ session('success') }}</div>
            @endif

            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-xl font-black text-slate-800 dark:text-white">Daftar Akun Staf</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">Kelola data karyawan, penempatan hotel, dan shift kerja.</p>
                </div>
                <a href="{{ route('admin.staff.create') }}" class="inline-flex items-center gap-2 bg-sky-600 hover:bg-sky-700 text-white font-bold py-3 px-5 rounded-xl shadow-md text-sm"><span>➕</span> Tambah Staf Baru</a>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-700/50 border-b border-slate-200 dark:border-slate-600">
                                <th class="px-6 py-4 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nama & Email</th>
                                <th class="px-6 py-4 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">Hotel Penempatan</th>
                                <th class="px-6 py-4 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider">Departemen / Shift</th>
                                <th class="px-6 py-4 text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @forelse($staffs as $staff)
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/30">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ $staff->name }}</div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400">{{ $staff->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-sky-700 dark:text-sky-400">{{ $staff->branch ? $staff->branch->name : 'Belum Ditentukan' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ $staff->department }}</div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400">Shift ID: {{ $staff->shift_id }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end gap-2">
                                            <a href="{{ route('admin.staff.edit', $staff->id) }}" class="inline-flex items-center px-3.5 py-1.5 bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-amber-900/50 font-bold rounded-xl border border-amber-200 dark:border-amber-800 text-xs">Edit</a>
                                            <button type="button" @click="showDeleteModal = true; deleteUrl = '{{ route('admin.staff.destroy', $staff->id) }}'; deleteName = '{{ addslashes($staff->name) }}'" class="inline-flex items-center px-3.5 py-1.5 bg-rose-50 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/50 font-bold rounded-xl border border-rose-200 dark:border-rose-800 text-xs">Hapus</button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400 font-medium">Belum ada akun staf terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div x-cloak x-show="showDeleteModal" class="fixed inset-0 z-[200] flex items-center justify-center p-4">
            <div x-show="showDeleteModal" class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm" @click="showDeleteModal = false"></div>
            <div x-show="showDeleteModal" class="relative z-[210] bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden border border-slate-200 dark:border-slate-700 p-6 flex flex-col items-center text-center">

                <div class="w-16 h-16 bg-rose-100 dark:bg-rose-900/30 rounded-2xl flex items-center justify-center text-3xl mb-4 shadow-inner border border-rose-200 dark:border-rose-800">⚠️</div>
                <h3 class="text-xl font-black text-slate-800 dark:text-white mb-2">Hapus Akun Staf?</h3>
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mb-6">Anda yakin ingin menghapus <strong x-text="deleteName" class="text-slate-700 dark:text-slate-300"></strong>? Data laporan lama mereka akan tetap tersimpan dengan aman.</p>

                <form :action="deleteUrl" method="POST" class="w-full flex gap-3">
                    @csrf
                    @method('DELETE')
                    <button type="button" @click="showDeleteModal = false" class="flex-1 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-bold py-3 px-4 rounded-xl shadow-sm border border-slate-200 dark:border-slate-600 focus:outline-none">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 bg-rose-600 hover:bg-rose-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-rose-600/30 focus:outline-none">
                        Ya, Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
