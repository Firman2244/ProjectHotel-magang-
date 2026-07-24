<x-app-layout>
    <div class="min-h-screen bg-sky-950/5 flex">
        <x-admin-sidebar />

        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
            <header class="bg-sky-50/30 backdrop-blur-md border-b border-sky-100 h-16 flex items-center justify-between px-6 shadow-sm">
                <span class="font-extrabold text-xl text-blue-700 tracking-tight">Tambah Tugas Baru</span>
            </header>

            <div class="p-8 max-w-3xl space-y-6">
                <a href="{{ route('admin.tasks.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-sky-700 hover:text-sky-900 transition">⬅ Kembali ke Master Tugas</a>

                <div class="bg-white/90 backdrop-blur-md rounded-2xl shadow-sm border border-sky-200/60 p-6">
                    <form action="{{ route('admin.tasks.store') }}" method="POST" class="space-y-5">
                        @csrf
                        <div>
                            <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">Nama Pekerjaan (SOP)</label>
                            <input type="text" name="name" value="{{ old('name') }}" required class="w-full border-sky-200 bg-white text-slate-800 font-semibold rounded-xl shadow-sm py-2.5 px-3 text-sm focus:border-sky-500 focus:ring-sky-500" placeholder="Contoh: Bersihkan Jendela Lobby">
                            @error('name') <span class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">Departemen</label>
                            <select name="department" required class="w-full border-sky-200 bg-white text-slate-800 font-semibold rounded-xl shadow-sm py-2.5 px-3 text-sm focus:border-sky-500 focus:ring-sky-500 cursor-pointer">
                                <option value="">-- Pilih Departemen --</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept }}" {{ old('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                                @endforeach
                            </select>
                            @error('department') <span class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="pt-4 flex gap-3">
                            <button type="submit" class="bg-sky-600 hover:bg-sky-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-md transition text-sm">Simpan Tugas</button>
                            <a href="{{ route('admin.tasks.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2.5 px-6 rounded-xl transition text-sm">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
