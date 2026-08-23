<x-app-layout>
    <div class="p-4 md:p-8 space-y-6">
        <div class="flex items-center mb-4">
            <a href="{{ route('admin.tasks.index') }}" class="text-sky-600 dark:text-sky-400 hover:text-sky-700 dark:hover:text-sky-300 font-bold text-sm flex items-center gap-2">
                ⬅️ Kembali ke Master Tugas
            </a>
        </div>

        <div class="bg-white dark:bg-slate-800 p-6 md:p-8 rounded-3xl shadow-sm border border-slate-200 dark:border-slate-700 max-w-3xl">
            <h2 class="text-2xl font-black text-slate-800 dark:text-white mb-6">Tambah SOP Tugas Baru</h2>

            <form action="{{ route('admin.tasks.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label class="block text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Nama Pekerjaan (SOP)</label>
                    <input type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Membersihkan area Lobby" class="w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white rounded-xl focus:ring-sky-500 focus:border-sky-500 font-semibold text-sm px-4 py-3" required>
                    @error('name') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Departemen Tugas</label>
                    <select name="department" class="w-full bg-slate-50 dark:bg-slate-900 border-slate-200 dark:border-slate-700 text-slate-800 dark:text-white rounded-xl focus:ring-sky-500 focus:border-sky-500 font-semibold text-sm px-4 py-3" required>
                        <option value="">-- Pilih Departemen --</option>
                        @foreach(['Front Office', 'Housekeeping', 'Engineering', 'Food & Beverage', 'Security', 'Human Resources', 'Accounting', 'Sales & Marketing'] as $dept)
                            <option value="{{ $dept }}" {{ old('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                        @endforeach
                    </select>
                    @error('department') <span class="text-rose-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex gap-3 pt-4 border-t border-slate-100 dark:border-slate-700">
                    <button type="submit" class="bg-sky-600 hover:bg-sky-700 text-white font-bold py-3 px-6 rounded-xl shadow-lg shadow-sky-600/30 text-sm">Simpan Tugas</button>
                    <a href="{{ route('admin.tasks.index') }}" class="bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-300 font-bold py-3 px-6 rounded-xl text-sm flex items-center justify-center">Batal</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
