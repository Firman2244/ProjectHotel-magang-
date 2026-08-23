<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl sm:text-2xl text-slate-800 dark:text-white leading-tight">
            {{ __('Detail Laporan') }}
        </h2>
    </x-slot>

    @if(session('success'))
        <div id="alert-success" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm transition-opacity">
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 max-w-sm w-full mx-4 shadow-2xl border border-slate-100 dark:border-slate-700 text-center transform transition-all">
                <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl shadow-inner border border-emerald-200 dark:border-emerald-800">✅</div>
                <h3 class="text-xl font-black text-slate-800 dark:text-white mb-2">Berhasil!</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mb-6">{{ session('success') }}</p>
                <button onclick="document.getElementById('alert-success').remove()" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-md shadow-emerald-600/20 transition border border-emerald-700">Tutup</button>
            </div>
        </div>
    @endif

    @if(session('error') || $errors->any())
        <div id="alert-error" class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm transition-opacity">
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 max-w-sm w-full mx-4 shadow-2xl border border-slate-100 dark:border-slate-700 text-center transform transition-all">
                <div class="w-16 h-16 bg-rose-100 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl shadow-inner border border-rose-200 dark:border-rose-800">⚠️</div>
                <h3 class="text-xl font-black text-slate-800 dark:text-white mb-2">Gagal!</h3>
                <div class="text-sm text-rose-600 dark:text-rose-400 font-medium mb-6 text-left bg-rose-50 dark:bg-rose-900/20 p-4 rounded-xl border border-rose-100 dark:border-rose-800/50">
                    @if(session('error'))
                        {{ session('error') }}
                    @else
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>
                <button onclick="document.getElementById('alert-error').remove()" class="w-full bg-rose-600 hover:bg-rose-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-md shadow-rose-600/20 transition border border-rose-700">Perbaiki</button>
            </div>
        </div>
    @endif

    <div class="py-12 bg-sky-50/60 dark:bg-slate-900 min-h-screen transition-colors duration-300">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(Auth::user()->role === 'admin')
                <div class="bg-slate-800 rounded-2xl shadow-lg p-4 flex flex-col sm:flex-row justify-between items-center gap-4 border border-slate-700">
                    <div class="text-sky-400 font-black text-sm flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                        MODE PENGECEKAN ADMIN
                        <span class="bg-slate-700 text-slate-300 px-3 py-1 rounded text-xs ml-3 font-bold border border-slate-600">
                            Laporan {{ $currentIndex }} dari {{ $totalReports }}
                        </span>
                    </div>
                    <div class="flex gap-3 w-full sm:w-auto">
                        @if(isset($prevReport))
                            <a href="{{ route('reports.show', $prevReport->id) }}" class="flex-1 sm:flex-none text-center bg-slate-700 hover:bg-slate-600 text-white font-bold py-2.5 px-5 rounded-xl transition">
                                ⬅ Sebelumnya
                            </a>
                        @else
                            <button disabled class="flex-1 sm:flex-none text-center bg-slate-800 text-slate-500 border border-slate-700 font-bold py-2.5 px-5 rounded-xl cursor-not-allowed">
                                ⬅ Mentok
                            </button>
                        @endif

                        @if(isset($nextReport))
                            <a href="{{ route('reports.show', $nextReport->id) }}" class="flex-1 sm:flex-none text-center bg-sky-500 hover:bg-sky-400 text-white font-bold py-2.5 px-5 rounded-xl shadow-md transition">
                                Selanjutnya ➡
                            </a>
                        @else
                            <button disabled class="flex-1 sm:flex-none text-center bg-slate-800 text-slate-500 border border-slate-700 font-bold py-2.5 px-5 rounded-xl cursor-not-allowed">
                                Mentok ➡
                            </button>
                        @endif
                    </div>
                </div>
            @endif

            <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-md shadow-sm sm:rounded-2xl border border-sky-100 dark:border-slate-700 p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 transition-colors duration-300">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-sky-100 dark:bg-sky-900/30 rounded-xl flex items-center justify-center text-sky-700 dark:text-sky-400 font-bold text-xl overflow-hidden shadow-sm border border-sky-200 dark:border-sky-800">
                        @if(!empty($report->user->avatar))
                            <img src="{{ asset('storage/' . $report->user->avatar) }}" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr($report->user->name, 0, 1)) }}
                        @endif
                    </div>
                    <div>
                        <p class="text-xs font-bold text-sky-600 dark:text-sky-400 uppercase tracking-wider">Karyawan</p>
                        <p class="font-black text-xl text-slate-800 dark:text-white mt-0.5">{{ $report->user->name }}</p>
                    </div>
                </div>
                <div class="text-left md:text-right">
                    <p class="text-xs font-bold text-sky-600 dark:text-sky-400 uppercase tracking-wider">Departemen | Shift</p>
                    <p class="font-black text-xl text-sky-700 dark:text-sky-300 mt-0.5">{{ $report->user->department }} | Shift {{ $report->shift_id }}</p>
                </div>
            </div>

            <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-md shadow-md sm:rounded-2xl p-8 border border-sky-100 dark:border-slate-700 transition-colors duration-300">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 border-b border-sky-100 dark:border-slate-700 pb-4 gap-4">
                    <div class="flex items-center gap-4">
                        <a href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : route('dashboard') }}" class="inline-flex items-center bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-bold py-2.5 px-4 rounded-xl transition border border-slate-200 dark:border-slate-600 text-sm">
                            &#8592; Kembali
                        </a>
                        <h3 class="text-xl font-black text-slate-800 dark:text-white hidden sm:block">Daftar Pekerjaan</h3>
                    </div>

                    <div class="flex items-center gap-4">
                        <span class="px-3.5 py-1.5 rounded-full text-xs font-black uppercase tracking-wider {{ $report->status == 'completed' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800' : 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-800' }}">
                            Status: {{ $report->status }}
                        </span>

                        @if(Auth::user()->role === 'staff' && Auth::id() == $report->user_id && $report->status === 'planned')
                            <form id="delete-report-form" action="{{ route('reports.destroy', $report->id) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="openDeleteModal()" class="bg-rose-50 dark:bg-rose-900/30 hover:bg-rose-100 dark:hover:bg-rose-900/60 text-rose-600 dark:text-rose-400 font-bold py-2 px-4 rounded-xl transition text-sm border border-rose-200 dark:border-rose-800/50">
                                    Hapus Laporan
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                @if(Auth::user()->role === 'staff' && Auth::id() == $report->user_id && $report->status === 'planned')
                    <form id="final-report-form" action="{{ route('reports.updateFinal', $report->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div id="master-task-container" class="space-y-6">
                            @foreach($report->items as $index => $item)
                                <div id="item-block-{{ $item->id }}" class="border {{ $item->is_additional ? 'border-amber-300 dark:border-amber-700/50 bg-amber-50/40 dark:bg-amber-900/10' : 'border-sky-200 dark:border-slate-700 bg-sky-50/40 dark:bg-slate-700/30' }} p-6 rounded-2xl relative shadow-sm hover:shadow transition additional-item-block">

                                    @if($item->is_additional)
                                        <button type="button" onclick="deleteExistingItem({{ $item->id }})" class="absolute top-4 right-4 bg-rose-50 dark:bg-rose-900/30 border border-rose-200 dark:border-rose-800/50 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/60 font-bold py-1.5 px-3 rounded-lg text-xs z-10 transition">Hapus</button>
                                    @endif

                                    <div class="mb-4 {{ $item->is_additional ? 'pr-20' : '' }}">
                                        <span class="font-black text-lg text-slate-800 dark:text-white flex items-center gap-2">
                                            <span class="{{ $item->is_additional ? 'text-amber-600 dark:text-amber-400' : 'text-sky-600 dark:text-sky-400' }}">{{ $index + 1 }}.</span>
                                            <!-- FIX DEAD COLUMN: Pakai custom_task_name -->
                                            {{ $item->is_additional ? ($item->custom_task_name ?? str_replace('Tugas Tambahan: ', '', explode(' - ', $item->notes)[0])) : ($item->task->name ?? 'Tugas') }}
                                            @if($item->is_additional)
                                                <span class="px-2 py-0.5 bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400 text-[10px] font-black rounded uppercase ml-2 border border-amber-200 dark:border-amber-800">Extra</span>
                                            @endif
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <label class="block text-xs font-black text-slate-500 dark:text-slate-400 uppercase mb-1">Status Pengerjaan</label>
                                            <select name="items[{{ $item->id }}][status]" class="w-full rounded-lg border-slate-300 dark:border-slate-600 bg-white dark:bg-slate-800 text-slate-800 dark:text-white text-sm font-semibold status-select transition" data-id="{{ $item->id }}">
                                                <option value="completed" {{ $item->status == 'completed' ? 'selected' : '' }}>✅ Selesai</option>
                                                <option value="pending" {{ $item->status == 'pending' ? 'selected' : '' }}>⏳ Kendala / Pending</option>
                                            </select>
                                        </div>
                                        <div class="obstacle-div {{ $item->status == 'pending' ? '' : 'hidden' }}" id="obstacle-{{ $item->id }}">
                                            <label class="block text-xs font-black text-rose-500 dark:text-rose-400 uppercase mb-1">Alasan Kendala</label>
                                            <input type="text" name="items[{{ $item->id }}][obstacle_note]" value="{{ $item->obstacle_note }}" class="w-full rounded-lg border-rose-300 dark:border-rose-800/50 bg-rose-50 dark:bg-rose-900/20 text-slate-800 dark:text-white text-sm placeholder-rose-300 dark:placeholder-rose-500/50 transition obstacle-input" placeholder="Jelaskan alasan pending...">
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <p class="block text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Foto Before</p>
                                            <div class="relative flex flex-col items-center justify-center w-full h-64 border-2 border-dashed {{ $item->is_additional ? 'border-amber-200 dark:border-amber-700/50 hover:bg-amber-50/50 dark:hover:bg-amber-900/20' : 'border-sky-200 dark:border-slate-600 hover:bg-sky-50/50 dark:hover:bg-slate-700/50' }} rounded-2xl cursor-pointer bg-white dark:bg-slate-800 overflow-hidden group transition upload-container" onclick="openUploadModal(this)">
                                                <div class="upload-placeholder flex-col items-center justify-center pt-5 pb-6 {{ $item->before_image ? 'hidden' : 'flex' }}">
                                                    <span class="text-4xl {{ $item->is_additional ? 'text-amber-400 dark:text-amber-500 group-hover:text-amber-600' : 'text-sky-400 dark:text-sky-500 group-hover:text-sky-600 dark:group-hover:text-sky-400' }} font-bold">+</span>
                                                    <p class="text-xs text-slate-500 dark:text-slate-400 font-bold mt-2">Tap Upload Foto</p>
                                                </div>
                                                <img class="preview-img absolute inset-0 w-full h-full object-contain bg-slate-900 {{ $item->before_image ? '' : 'hidden' }}" src="{{ $item->before_image ? asset('storage/' . $item->before_image) : '' }}" alt="Preview">
                                                <div class="preview-overlay absolute top-3 left-3 bg-slate-900/80 backdrop-blur-sm text-white text-[10px] font-black px-2.5 py-1 rounded-lg shadow border border-slate-700 {{ $item->before_image ? '' : 'hidden' }}">BEFORE</div>
                                                <input type="file" name="items[{{ $item->id }}][before_image]" class="hidden image-input" accept="image/*">
                                            </div>
                                        </div>

                                        <div>
                                            <p class="block text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Foto After</p>
                                            <div class="relative flex flex-col items-center justify-center w-full h-64 border-2 border-dashed {{ $item->is_additional ? 'border-amber-200 dark:border-amber-700/50 hover:bg-amber-50/50 dark:hover:bg-amber-900/20' : 'border-sky-200 dark:border-slate-600 hover:bg-sky-50/50 dark:hover:bg-slate-700/50' }} rounded-2xl cursor-pointer bg-white dark:bg-slate-800 overflow-hidden group transition upload-container" onclick="openUploadModal(this)">
                                                <div class="upload-placeholder flex-col items-center justify-center pt-5 pb-6 {{ $item->after_image ? 'hidden' : 'flex' }}">
                                                    <span class="text-4xl {{ $item->is_additional ? 'text-amber-400 dark:text-amber-500 group-hover:text-amber-600' : 'text-sky-400 dark:text-sky-500 group-hover:text-sky-600 dark:group-hover:text-sky-400' }} font-bold">+</span>
                                                    <p class="text-xs text-slate-500 dark:text-slate-400 font-bold mt-2">Tap Upload Foto</p>
                                                </div>
                                                <img class="preview-img absolute inset-0 w-full h-full object-contain bg-slate-900 {{ $item->after_image ? '' : 'hidden' }}" src="{{ $item->after_image ? asset('storage/' . $item->after_image) : '' }}" alt="Preview">
                                                <div class="preview-overlay absolute top-3 right-3 bg-emerald-600 dark:bg-emerald-700 text-white text-[10px] font-black px-2.5 py-1 rounded-lg shadow border border-emerald-500 {{ $item->after_image ? '' : 'hidden' }}">AFTER</div>
                                                <input type="file" name="items[{{ $item->id }}][after_image]" class="hidden image-input" accept="image/*">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-6">
                                        <label class="block text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Catatan Tugas</label>
                                        <input type="text" name="items[{{ $item->id }}][notes]" value="{{ old('items.'.$item->id.'.notes', $item->notes) }}" class="block w-full {{ $item->is_additional ? 'border-amber-200 dark:border-amber-700/50 focus:border-amber-500 dark:focus:border-amber-400 focus:ring-amber-500' : 'border-sky-200 dark:border-slate-600 focus:border-sky-500 dark:focus:border-sky-400 focus:ring-sky-500' }} bg-white dark:bg-slate-800 text-slate-800 dark:text-white font-medium rounded-xl shadow-sm py-3 px-4 transition">
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-8 border-t border-sky-100 dark:border-slate-700 pt-6">
                            <button type="button" id="btn-add-additional-task" class="w-full sm:w-auto bg-sky-50 dark:bg-slate-800 hover:bg-sky-100 dark:hover:bg-slate-700 text-sky-700 dark:text-sky-400 font-bold py-3.5 px-6 rounded-xl border border-sky-200 dark:border-slate-600 shadow-sm transition">
                                + Input Tugas Tambahan (Manual)
                            </button>
                        </div>

                        <div class="mt-8 flex flex-col sm:flex-row justify-between items-center bg-sky-50/80 dark:bg-slate-800/80 p-6 rounded-2xl border border-sky-200 dark:border-slate-700 gap-4 transition-colors duration-300">
                            <a href="{{ route('dashboard') }}" class="w-full sm:w-auto text-center bg-slate-200 dark:bg-slate-700 hover:bg-slate-300 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-bold py-3.5 px-6 rounded-xl transition shadow-sm border border-slate-300 dark:border-slate-600">
                                Kembali ke Dashboard
                            </a>
                            <div class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                                <button type="submit" name="save_action" value="draft" class="w-full sm:w-auto bg-amber-50 dark:bg-amber-900/30 hover:bg-amber-100 dark:hover:bg-amber-900/50 text-amber-700 dark:text-amber-400 font-bold py-4 px-6 rounded-xl border border-amber-200 dark:border-amber-800/50 shadow-sm transition text-base text-center">
                                    Simpan Draft
                                </button>
                                <button type="button" id="btn-submit-final" class="w-full sm:w-auto bg-sky-600 hover:bg-sky-700 text-white font-black py-4 px-8 rounded-xl transition shadow-lg shadow-sky-600/20 text-base text-center border border-sky-700">
                                    Kirim Laporan Akhir Shift
                                </button>
                            </div>
                        </div>
                    </form>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mt-6">
                        @foreach($report->items as $item)
                            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm border border-slate-200/60 dark:border-slate-700 p-4 flex flex-col justify-between hover:border-sky-300 dark:hover:border-sky-600 transition duration-300">
                                <div>
                                    <div class="flex items-start justify-between mb-2">
                                        <h4 class="text-sm font-bold text-slate-800 dark:text-white leading-tight">
                                            {{ $item->task ? $item->task->name : ($item->custom_task_name ?? 'Tugas Tambahan') }}
                                        </h4>
                                        @if($item->is_additional)
                                            <span class="px-2 py-0.5 bg-sky-100 dark:bg-sky-900/30 text-sky-700 dark:text-sky-400 text-[10px] font-black rounded uppercase ml-2 flex-shrink-0 border border-sky-200 dark:border-sky-800">Extra</span>
                                        @endif
                                    </div>
                                    @if($item->notes)
                                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium bg-slate-50 dark:bg-slate-900/50 p-2 rounded-lg mb-3 border border-slate-100 dark:border-slate-700">{{ $item->notes }}</p>
                                    @endif
                                </div>

                                <div class="flex gap-2 mt-auto pt-3 border-t border-slate-100 dark:border-slate-700">
                                    <div class="flex-1">
                                        <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase mb-1">Sebelum</p>
                                        @if($item->before_image)
                                            <img src="{{ asset('storage/' . $item->before_image) }}" onclick="openLightbox(this.src)" class="w-full h-24 object-cover rounded-lg cursor-pointer hover:opacity-80 transition shadow-sm border border-slate-100 dark:border-slate-600">
                                        @else
                                            <div class="w-full h-24 bg-slate-100 dark:bg-slate-700/50 rounded-lg flex items-center justify-center text-xs text-slate-400 dark:text-slate-500 font-bold border border-slate-200 dark:border-slate-600">Kosong</div>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase mb-1">Sesudah</p>
                                        @if($item->after_image)
                                            <img src="{{ asset('storage/' . $item->after_image) }}" onclick="openLightbox(this.src)" class="w-full h-24 object-cover rounded-lg cursor-pointer hover:opacity-80 transition shadow-sm border border-slate-100 dark:border-slate-600">
                                        @else
                                            <div class="w-full h-24 bg-slate-100 dark:bg-slate-700/50 rounded-lg flex items-center justify-center text-xs text-slate-400 dark:text-slate-500 font-bold border border-slate-200 dark:border-slate-600">Kosong</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if(Auth::user()->role === 'staff' && Auth::id() == $report->user_id && $report->status === 'planned')
        <div id="upload-choice-modal" class="fixed inset-0 z-[110] items-center justify-center bg-slate-900/60 backdrop-blur-sm hidden transition-opacity opacity-0">
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-6 max-w-sm w-full mx-4 shadow-2xl border border-sky-100 dark:border-slate-700 text-center transform transition-all duration-300">
                <div class="w-16 h-16 bg-sky-100 dark:bg-sky-900/40 text-sky-600 dark:text-sky-400 rounded-2xl flex items-center justify-center mx-auto mb-4 text-3xl shadow-inner border border-sky-200 dark:border-sky-800">📸</div>
                <h3 class="text-xl font-black text-slate-800 dark:text-white mb-2">Pilih Sumber Foto</h3>
                <p class="text-slate-500 dark:text-slate-400 mb-6 font-medium text-sm">Mau ambil foto langsung atau dari galeri?</p>
                <div class="flex flex-col gap-3">
                    <button type="button" id="btn-choose-camera" class="w-full bg-sky-600 hover:bg-sky-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-md shadow-sky-600/20 transition flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        Buka Kamera
                    </button>
                    <button type="button" id="btn-choose-gallery" class="w-full bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-bold py-3.5 px-4 rounded-xl transition flex items-center justify-center gap-2 border border-slate-200 dark:border-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Pilih dari Galeri
                    </button>
                    <button type="button" onclick="closeUploadModal()" class="w-full bg-transparent hover:bg-rose-50 dark:hover:bg-rose-900/30 text-rose-600 dark:text-rose-400 font-bold py-3 px-4 rounded-xl transition mt-2">Batal</button>
                </div>
            </div>
        </div>

        <div id="custom-confirm-modal" class="fixed inset-0 z-50 items-center justify-center bg-slate-900/60 backdrop-blur-sm hidden transition-opacity opacity-0">
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 max-w-md w-full mx-4 shadow-2xl border border-sky-100 dark:border-slate-700 text-center transform transition-all duration-300">
                <div class="w-16 h-16 bg-sky-100 dark:bg-sky-900/40 text-sky-600 dark:text-sky-400 rounded-2xl flex items-center justify-center mx-auto mb-4 text-3xl shadow-inner border border-sky-200 dark:border-sky-800">🚀</div>
                <h3 class="text-xl font-black text-slate-800 dark:text-white mb-2">Kirim Laporan Akhir Shift?</h3>
                <p class="text-slate-500 dark:text-slate-400 mb-6 font-medium text-sm">Pastikan semua foto After sudah diunggah. Data tidak dapat diubah setelah disubmit.</p>
                <div class="flex space-x-3">
                    <button type="button" id="modal-btn-cancel" class="flex-1 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-bold py-3.5 px-4 rounded-xl transition border border-slate-200 dark:border-slate-600">Batal</button>
                    <button type="button" id="modal-btn-confirm" class="flex-1 bg-sky-600 hover:bg-sky-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-md shadow-sky-600/20 transition border border-sky-700">Ya, Kirim</button>
                </div>
            </div>
        </div>

        <div id="custom-delete-modal" class="fixed inset-0 z-[120] items-center justify-center bg-slate-900/60 backdrop-blur-sm hidden transition-opacity opacity-0">
            <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 max-w-md w-full mx-4 shadow-2xl border border-rose-100 dark:border-slate-700 text-center transform transition-all duration-300">
                <div class="w-16 h-16 bg-rose-100 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 rounded-2xl flex items-center justify-center mx-auto mb-4 text-3xl shadow-inner border border-rose-200 dark:border-rose-800">🗑️</div>
                <h3 class="text-xl font-black text-slate-800 dark:text-white mb-2">Hapus Laporan?</h3>
                <p class="text-slate-500 dark:text-slate-400 mb-6 font-medium text-sm">Apakah Anda yakin ingin menghapus laporan ini? Seluruh data yang belum disubmit akan hilang secara permanen.</p>
                <div class="flex space-x-3">
                    <button type="button" onclick="closeDeleteModal()" class="flex-1 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-bold py-3.5 px-4 rounded-xl transition-colors duration-200 border border-slate-200 dark:border-slate-600 shadow-sm">
                        Batal
                    </button>
                    <button type="button" onclick="document.getElementById('delete-report-form').submit()" class="flex-1 bg-rose-600 hover:bg-rose-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-md shadow-rose-600/20 transition-colors duration-200 border border-rose-700">
                        Ya, Hapus
                    </button>
                </div>
            </div>
        </div>

        <script>
            window.addEventListener("pageshow", function (event) {
                if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
                    window.location.reload();
                }
            });

            window.openDeleteModal = function() {
                const modal = document.getElementById('custom-delete-modal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                setTimeout(() => modal.classList.remove('opacity-0'), 10);
            };

            window.closeDeleteModal = function() {
                const modal = document.getElementById('custom-delete-modal');
                modal.classList.add('opacity-0');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }, 300);
            };

            window.deleteExistingItem = function(itemId) {
                if (!confirm('Yakin ingin menghapus tugas tambahan ini secara permanen?')) return;

                fetch(`/report-items/${itemId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        const block = document.getElementById('item-block-' + itemId);
                        if (block) block.remove();
                    } else {
                        alert('Gagal menghapus tugas tambahan.');
                    }
                })
                .catch(error => {
                    alert('Terjadi kesalahan jaringan.');
                });
            };

            let currentFileInput = null;
            const uploadModal = document.getElementById('upload-choice-modal');

            window.openUploadModal = function(element) {
                currentFileInput = element.querySelector('.image-input');
                uploadModal.classList.remove('hidden');
                uploadModal.classList.add('flex');
                setTimeout(() => uploadModal.classList.add('opacity-100'), 10);
            };

            window.closeUploadModal = function() {
                uploadModal.classList.remove('opacity-100');
                setTimeout(() => {
                    uploadModal.classList.add('hidden');
                    uploadModal.classList.remove('flex');
                    currentFileInput = null;
                }, 300);
            };

            document.getElementById('btn-choose-camera')?.addEventListener('click', () => {
                if(currentFileInput) {
                    currentFileInput.setAttribute('capture', 'environment');
                    currentFileInput.click();
                }
                closeUploadModal();
            });

            document.getElementById('btn-choose-gallery')?.addEventListener('click', () => {
                if(currentFileInput) {
                    currentFileInput.removeAttribute('capture');
                    currentFileInput.click();
                }
                closeUploadModal();
            });

            document.addEventListener('DOMContentLoaded', () => {
                let newTaskCounter = 0;
                const container = document.getElementById('master-task-container');
                const btnSubmitFinal = document.getElementById('btn-submit-final');
                const confirmModal = document.getElementById('custom-confirm-modal');
                const finalReportForm = document.getElementById('final-report-form');
                const objectUrls = [];

                document.addEventListener('change', (e) => {
                    if (e.target.classList.contains('image-input')) {
                        const file = e.target.files[0];
                        if (file) {
                            const uploadContainer = e.target.closest('.upload-container');
                            const placeholderElement = uploadContainer.querySelector('.upload-placeholder p');
                            const originalText = placeholderElement.innerText;

                            // Tampilkan status loading biar staf gak bingung pas nge-freeze sebentar
                            placeholderElement.innerText = "⏳ Mengompres foto...";
                            uploadContainer.classList.add('animate-pulse');

                            // Jeda 50ms biar browser sempat merender tulisan "Mengompres foto..."
                            setTimeout(() => {
                                const objectUrl = URL.createObjectURL(file);
                                objectUrls.push(objectUrl);
                                const img = new Image();
                                img.src = objectUrl;

                                img.onload = () => {
                                    const canvas = document.createElement('canvas');
                                    const MAX_WIDTH = 1000;
                                    let width = img.width;
                                    let height = img.height;

                                    if (width > MAX_WIDTH) {
                                        height = Math.round((height * MAX_WIDTH) / width);
                                        width = MAX_WIDTH;
                                    }

                                    canvas.width = width;
                                    canvas.height = height;
                                    const ctx = canvas.getContext('2d');
                                    ctx.drawImage(img, 0, 0, width, height);

                                    canvas.toBlob((blob) => {
                                        const dataTransfer = new DataTransfer();
                                        dataTransfer.items.add(new File([blob], file.name, { type: 'image/jpeg' }));
                                        e.target.files = dataTransfer.files;

                                        const compressedUrl = URL.createObjectURL(blob);
                                        objectUrls.push(compressedUrl);

                                        uploadContainer.querySelector('.preview-img').src = compressedUrl;
                                        uploadContainer.querySelector('.preview-img').classList.remove('hidden');
                                        uploadContainer.querySelector('.preview-overlay').classList.remove('hidden');
                                        uploadContainer.querySelector('.upload-placeholder').classList.add('hidden');

                                        // Balikin state semula
                                        placeholderElement.innerText = originalText;
                                        uploadContainer.classList.remove('animate-pulse');
                                    }, 'image/jpeg', 0.8);
                                };
                            }, 50);
                        }
                    }
                });

                window.addEventListener('beforeunload', () => {
                    objectUrls.forEach(url => URL.revokeObjectURL(url));
                });

                document.querySelectorAll('.status-select').forEach(select => {
                    select.addEventListener('change', function() {
                        const obstacleDiv = document.getElementById(`obstacle-${this.dataset.id}`);
                        const inputField = obstacleDiv.querySelector('.obstacle-input');

                        if (this.value === 'pending') {
                            obstacleDiv.classList.remove('hidden');
                        } else {
                            obstacleDiv.classList.add('hidden');
                            if (inputField) inputField.value = '';
                        }
                    });
                });

                document.getElementById('btn-add-additional-task')?.addEventListener('click', () => {
                    container.insertAdjacentHTML('beforeend', `
                        <div class="border border-amber-300 dark:border-amber-700/50 p-6 rounded-2xl bg-amber-50/40 dark:bg-amber-900/10 relative shadow-sm hover:shadow transition mt-6 additional-item-block">
                            <button type="button" class="absolute top-4 right-4 bg-rose-50 dark:bg-rose-900/30 border border-rose-200 dark:border-rose-800/50 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/60 font-bold py-1.5 px-3 rounded-lg text-xs z-10 btn-remove-additional transition">Hapus</button>

                            <div class="mb-4 pr-24">
                                <label class="block text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Nama Tugas Tambahan</label>
                                <!-- FIX DEAD COLUMN: Pakai custom_task_name -->
                                <input type="text" name="new_items[${newTaskCounter}][custom_task_name]" class="block w-full border-amber-200 dark:border-amber-700/50 bg-white dark:bg-slate-800 text-slate-800 dark:text-white font-medium focus:border-amber-500 dark:focus:border-amber-400 focus:ring-amber-500 rounded-xl shadow-sm py-3 px-4 transition" required>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <p class="block text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Foto Before</p>
                                    <div class="relative flex flex-col items-center justify-center w-full h-64 border-2 border-dashed border-amber-200 dark:border-amber-700/50 rounded-2xl cursor-pointer bg-white dark:bg-slate-800 hover:bg-amber-50/50 dark:hover:bg-amber-900/20 overflow-hidden group transition upload-container" onclick="openUploadModal(this)">
                                        <div class="upload-placeholder flex flex-col items-center justify-center pt-5 pb-6">
                                            <span class="text-4xl text-amber-400 dark:text-amber-500 font-bold group-hover:text-amber-600">+</span>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 font-bold mt-2 group-hover:text-amber-600">Tap Upload Foto</p>
                                        </div>
                                        <img class="preview-img absolute inset-0 w-full h-full object-contain bg-slate-900 hidden" src="" alt="Preview">
                                        <div class="preview-overlay absolute top-3 left-3 bg-slate-900/80 backdrop-blur-sm text-white text-[10px] font-black px-2.5 py-1 rounded-lg shadow hidden border border-slate-700">BEFORE</div>
                                        <input type="file" name="new_items[${newTaskCounter}][before_image]" class="hidden image-input" accept="image/*">
                                    </div>
                                </div>
                                <div>
                                    <p class="block text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Foto After</p>
                                    <div class="relative flex flex-col items-center justify-center w-full h-64 border-2 border-dashed border-amber-200 dark:border-amber-700/50 rounded-2xl cursor-pointer bg-white dark:bg-slate-800 hover:bg-amber-50/50 dark:hover:bg-amber-900/20 overflow-hidden group transition upload-container" onclick="openUploadModal(this)">
                                        <div class="upload-placeholder flex flex-col items-center justify-center pt-5 pb-6">
                                            <span class="text-4xl text-amber-400 dark:text-amber-500 font-bold group-hover:text-amber-600">+</span>
                                            <p class="text-xs text-slate-500 dark:text-slate-400 font-bold mt-2 group-hover:text-amber-600">Tap Upload Foto</p>
                                        </div>
                                        <img class="preview-img absolute inset-0 w-full h-full object-contain bg-slate-900 hidden" src="" alt="Preview">
                                        <div class="preview-overlay absolute top-3 right-3 bg-emerald-600 dark:bg-emerald-700 text-white text-[10px] font-black px-2.5 py-1 rounded-lg shadow hidden border border-emerald-500">AFTER</div>
                                        <input type="file" name="new_items[${newTaskCounter}][after_image]" class="hidden image-input" accept="image/*">
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6">
                                <label class="block text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Catatan Tugas Tambahan</label>
                                <input type="text" name="new_items[${newTaskCounter}][notes]" class="block w-full border-amber-200 dark:border-amber-700/50 bg-white dark:bg-slate-800 text-slate-800 dark:text-white font-medium focus:border-amber-500 dark:focus:border-amber-400 focus:ring-amber-500 rounded-xl shadow-sm py-3 px-4 transition">
                            </div>
                        </div>
                    `);
                    newTaskCounter++;
                });

                container.addEventListener('click', (e) => {
                    if (e.target.classList.contains('btn-remove-additional')) e.target.closest('.additional-item-block').remove();
                });

                const toggleConfirmModal = (show) => {
                    if (show) {
                        confirmModal.classList.remove('hidden');
                        confirmModal.classList.add('flex');
                        setTimeout(() => confirmModal.classList.add('opacity-100'), 10);
                    } else {
                        confirmModal.classList.remove('opacity-100');
                        setTimeout(() => {
                            confirmModal.classList.add('hidden');
                            confirmModal.classList.remove('flex');
                        }, 300);
                    }
                };

                btnSubmitFinal?.addEventListener('click', () => toggleConfirmModal(true));
                document.getElementById('modal-btn-cancel')?.addEventListener('click', () => toggleConfirmModal(false));

                document.getElementById('modal-btn-confirm')?.addEventListener('click', () => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'save_action';
                    input.value = 'final';
                    finalReportForm.appendChild(input);
                    finalReportForm.submit();
                });
            });
        </script>
    @else
        <div id="lightboxModal" class="fixed inset-0 bg-slate-900/95 z-[100] items-center justify-center p-4 cursor-zoom-out transition-opacity duration-300 opacity-0 hidden" onclick="closeLightbox()">
            <img id="lightboxImage" src="" class="max-w-full max-h-[90vh] object-contain rounded-xl shadow-2xl border-2 border-slate-700">
            <button class="absolute top-6 right-6 text-white bg-slate-800 hover:bg-rose-600 rounded-full w-12 h-12 flex items-center justify-center font-bold text-2xl shadow-lg transition-colors duration-200 border border-slate-600">&times;</button>
        </div>

        <script>
            function openLightbox(src) {
                document.getElementById('lightboxImage').src = src;
                const modal = document.getElementById('lightboxModal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                setTimeout(() => modal.classList.remove('opacity-0'), 10);
                document.body.style.overflow = 'hidden';
            }

            function closeLightbox() {
                const modal = document.getElementById('lightboxModal');
                modal.classList.add('opacity-0');
                setTimeout(() => {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    document.body.style.overflow = 'auto';
                }, 300);
            }
        </script>
    @endif
</x-app-layout>
