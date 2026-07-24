<x-app-layout>
    <div class="min-h-screen bg-sky-950/5 flex">
        <x-admin-sidebar />

        <div class="flex-1 flex flex-col min-w-0 overflow-y-auto">
            <header class="bg-sky-50/30 backdrop-blur-md border-b border-sky-100 h-16 flex items-center justify-between px-6 shadow-sm">
                <span class="font-extrabold text-xl text-blue-700 tracking-tight">Edit Akun Staf</span>
            </header>

            <div class="p-8 max-w-3xl space-y-6">
                <a href="{{ route('admin.staff.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-sky-700 hover:text-sky-900 transition">⬅ Kembali ke Manajemen Staf</a>

                <div class="bg-white/90 backdrop-blur-md rounded-2xl shadow-sm border border-sky-200/60 p-6">
                    <form action="{{ route('admin.staff.update', $staff->id) }}" method="POST" class="space-y-5">
                        @csrf
                        @method('PUT')

                        <div>
                            <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', $staff->name) }}" required class="w-full border-sky-200 bg-white text-slate-800 font-semibold rounded-xl shadow-sm py-2.5 px-3 text-sm focus:border-sky-500 focus:ring-sky-500">
                            @error('name') <span class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">Email Login</label>
                            <input type="email" name="email" value="{{ old('email', $staff->email) }}" required class="w-full border-sky-200 bg-white text-slate-800 font-semibold rounded-xl shadow-sm py-2.5 px-3 text-sm focus:border-sky-500 focus:ring-sky-500">
                            @error('email') <span class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">Password Baru <span class="text-slate-400 font-normal">(Opsional)</span></label>
                                <input type="password" name="password" class="w-full border-sky-200 bg-white text-slate-800 font-semibold rounded-xl shadow-sm py-2.5 px-3 text-sm focus:border-sky-500 focus:ring-sky-500" placeholder="Kosongkan jika tidak diubah">
                                @error('password') <span class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">Konfirmasi Password Baru</label>
                                <input type="password" name="password_confirmation" class="w-full border-sky-200 bg-white text-slate-800 font-semibold rounded-xl shadow-sm py-2.5 px-3 text-sm focus:border-sky-500 focus:ring-sky-500" placeholder="Kosongkan jika tidak diubah">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">Penempatan Hotel</label>
                            <select name="hotel_id" required class="w-full border-sky-200 bg-white text-slate-800 font-semibold rounded-xl shadow-sm py-2.5 px-3 text-sm focus:border-sky-500 focus:ring-sky-500 cursor-pointer">
                                <option value="">-- Pilih Cabang Hotel --</option>
                                @foreach($hotels as $hotel)
                                    <option value="{{ $hotel->id }}" {{ old('hotel_id', $staff->hotel_id) == $hotel->id ? 'selected' : '' }}>{{ $hotel->name }}</option>
                                @endforeach
                            </select>
                            @error('hotel_id') <span class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</span> @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">Departemen</label>
                                <select name="department" required class="w-full border-sky-200 bg-white text-slate-800 font-semibold rounded-xl shadow-sm py-2.5 px-3 text-sm focus:border-sky-500 focus:ring-sky-500 cursor-pointer">
                                    <option value="">-- Pilih Departemen --</option>
                                    @foreach(\App\Models\Task::select('department')->distinct()->pluck('department') as $dept)
                                        <option value="{{ $dept }}" {{ old('department', $staff->department) == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                                    @endforeach
                                </select>
                                @error('department') <span class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-black text-slate-600 uppercase tracking-wider mb-2">Shift Kerja</label>
                                <select name="shift_id" required class="w-full border-sky-200 bg-white text-slate-800 font-semibold rounded-xl shadow-sm py-2.5 px-3 text-sm focus:border-sky-500 focus:ring-sky-500 cursor-pointer">
                                    <option value="">-- Pilih Shift --</option>
                                    <option value="1" {{ old('shift_id', $staff->shift_id) == '1' ? 'selected' : '' }}>Shift 1 (Pagi)</option>
                                    <option value="2" {{ old('shift_id', $staff->shift_id) == '2' ? 'selected' : '' }}>Shift 2 (Siang)</option>
                                    <option value="3" {{ old('shift_id', $staff->shift_id) == '3' ? 'selected' : '' }}>Shift 3 (Malam)</option>
                                </select>
                                @error('shift_id') <span class="text-xs text-rose-600 font-bold mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div class="pt-4 flex gap-3">
                            <button type="submit" class="bg-sky-600 hover:bg-sky-700 text-white font-bold py-2.5 px-6 rounded-xl shadow-md transition text-sm">Perbarui Akun Staf</button>
                            <a href="{{ route('admin.staff.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2.5 px-6 rounded-xl transition text-sm">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
