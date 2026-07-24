<x-app-layout>
    <div class="py-12 bg-sky-50/60 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(Auth::user()->role === 'admin')
                <div class="bg-slate-800 rounded-2xl shadow-lg p-4 flex flex-col sm:flex-row justify-between items-center gap-4">
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

            <div class="bg-white/90 backdrop-blur-md shadow-sm sm:rounded-2xl border border-sky-100 p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 bg-sky-100 rounded-xl flex items-center justify-center text-sky-700 font-bold text-xl overflow-hidden shadow-sm">
                        @if(!empty($report->user->avatar))
                            <img src="{{ asset('storage/' . $report->user->avatar) }}" class="w-full h-full object-cover">
                        @else
                            {{ strtoupper(substr($report->user->name, 0, 1)) }}
                        @endif
                    </div>
                    <div>
                        <p class="text-xs font-bold text-sky-600 uppercase tracking-wider">Karyawan</p>
                        <p class="font-black text-xl text-slate-800 mt-0.5">{{ $report->user->name }}</p>
                    </div>
                </div>
                <div class="text-left md:text-right">
                    <p class="text-xs font-bold text-sky-600 uppercase tracking-wider">Departemen | Shift</p>
                    <p class="font-black text-xl text-sky-700 mt-0.5">{{ $report->user->department }} | Shift {{ $report->shift_id }}</p>
                </div>
            </div>

            @if(session('error'))
                <div class="bg-rose-50 border border-rose-200 text-rose-700 px-5 py-4 rounded-2xl relative shadow-sm font-bold" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white/90 backdrop-blur-md shadow-md sm:rounded-2xl p-8 border border-sky-100">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 border-b border-sky-100 pb-4 gap-4">
                    <div class="flex items-center gap-4">
                        <a href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard') : route('dashboard') }}" class="inline-flex items-center bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2.5 px-4 rounded-xl transition border border-slate-200 text-sm">
                            &#8592; Kembali
                        </a>
                        <h3 class="text-xl font-black text-slate-800 hidden sm:block">Daftar Pekerjaan</h3>
                    </div>

                    <div class="flex items-center gap-4">
                        <span class="px-3.5 py-1.5 rounded-full text-xs font-black uppercase tracking-wider {{ $report->status == 'completed' ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-amber-100 text-amber-700 border border-amber-200' }}">
                            Status: {{ $report->status }}
                        </span>

                        @if($report->status == 'planned' && Auth::user()->role !== 'admin')
                            <form action="{{ route('reports.destroy', $report->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan ini? Seluruh data yang belum disubmit akan hilang secara permanen.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold py-2 px-4 rounded-xl transition text-sm border border-rose-200">
                                    Hapus Laporan
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                @if($report->status == 'planned')
                    <form id="final-report-form" action="{{ route('reports.updateFinal', $report->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div id="master-task-container" class="space-y-6">
                            @foreach($report->items as $index => $item)
                                <div class="border border-sky-200 p-6 rounded-2xl bg-sky-50/40 relative shadow-sm hover:shadow transition">
                                    <div class="mb-4">
                                        <span class="font-black text-lg text-slate-800 flex items-center gap-2">
                                            <span class="text-sky-600">{{ $index + 1 }}.</span> {{ $item->is_additional ? ($item->task_name ?? 'Tugas Tambahan') : ($item->task->name ?? 'Tugas') }}
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <div>
                                            <p class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Foto Before</p>
                                            <label class="relative flex flex-col items-center justify-center w-full h-64 border-2 border-dashed border-sky-200 rounded-2xl cursor-pointer bg-white hover:bg-sky-50/50 overflow-hidden group transition">
                                                <div class="upload-placeholder flex-col items-center justify-center pt-5 pb-6 {{ $item->before_image ? 'hidden' : 'flex' }}">
                                                    <span class="text-4xl text-sky-400 font-bold group-hover:text-sky-600">+</span>
                                                    <p class="text-xs text-slate-500 font-bold mt-2 group-hover:text-sky-600">Tap Upload Foto</p>
                                                </div>
                                                <img class="preview-img absolute inset-0 w-full h-full object-contain bg-slate-900 {{ $item->before_image ? '' : 'hidden' }}" src="{{ $item->before_image ? asset('storage/' . $item->before_image) : '' }}" alt="Preview">
                                                <div class="preview-overlay absolute top-3 left-3 bg-slate-900/80 backdrop-blur-sm text-white text-[10px] font-black px-2.5 py-1 rounded-lg shadow {{ $item->before_image ? '' : 'hidden' }}">BEFORE</div>
                                                <input type="file" name="items[{{ $item->id }}][before_image]" class="hidden image-input" accept="image/*">
                                            </label>
                                        </div>

                                        <div>
                                            <p class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Foto After</p>
                                            <label class="relative flex flex-col items-center justify-center w-full h-64 border-2 border-dashed border-sky-200 rounded-2xl cursor-pointer bg-white hover:bg-sky-50/50 overflow-hidden group transition">
                                                <div class="upload-placeholder flex-col items-center justify-center pt-5 pb-6 {{ $item->after_image ? 'hidden' : 'flex' }}">
                                                    <span class="text-4xl text-sky-400 font-bold group-hover:text-sky-600">+</span>
                                                    <p class="text-xs text-slate-500 font-bold mt-2 group-hover:text-sky-600">Tap Upload Foto</p>
                                                </div>
                                                <img class="preview-img absolute inset-0 w-full h-full object-contain bg-slate-900 {{ $item->after_image ? '' : 'hidden' }}" src="{{ $item->after_image ? asset('storage/' . $item->after_image) : '' }}" alt="Preview">
                                                <div class="preview-overlay absolute top-3 right-3 bg-emerald-600 text-white text-[10px] font-black px-2.5 py-1 rounded-lg shadow {{ $item->after_image ? '' : 'hidden' }}">AFTER</div>
                                                <input type="file" name="items[{{ $item->id }}][after_image]" class="hidden image-input" accept="image/*">
                                            </label>
                                        </div>
                                    </div>

                                    <div class="mt-6">
                                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Catatan Tugas</label>
                                        <input type="text" name="items[{{ $item->id }}][notes]" value="{{ old('items.'.$item->id.'.notes', $item->notes) }}" class="block w-full border-sky-200 bg-white text-slate-800 font-medium focus:border-sky-500 focus:ring-sky-500 rounded-xl shadow-sm py-3 px-4">
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-8 border-t border-sky-100 pt-6">
                            <button type="button" id="btn-add-additional-task" class="w-full sm:w-auto bg-sky-50 hover:bg-sky-100 text-sky-700 font-bold py-3.5 px-6 rounded-xl border border-sky-200 shadow-sm transition">
                                + Input Tugas Tambahan (Manual)
                            </button>
                        </div>

                        <div class="mt-8 flex flex-col sm:flex-row justify-between items-center bg-sky-50/80 p-6 rounded-2xl border border-sky-200 gap-4">
                            <a href="{{ route('dashboard') }}" class="w-full sm:w-auto text-center bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-3.5 px-6 rounded-xl transition shadow-sm">
                                Kembali ke Dashboard
                            </a>
                            <button type="button" id="btn-submit-final" class="w-full sm:w-auto bg-sky-600 hover:bg-sky-700 text-white font-black py-4 px-8 rounded-xl transition shadow-lg shadow-sky-600/20 text-base">
                                Kirim Laporan Akhir Shift
                            </button>
                        </div>
                    </form>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mt-6">
                        @foreach($report->items as $item)
                            <div class="bg-white rounded-xl shadow-sm border border-slate-200/60 p-4 flex flex-col justify-between hover:border-sky-300 transition">
                                <div>
                                    <div class="flex items-start justify-between mb-2">
                                        <h4 class="text-sm font-bold text-slate-800 leading-tight">
                                            {{ $item->task ? $item->task->name : ($item->task_name ?? 'Tugas Tambahan') }}
                                        </h4>
                                        @if($item->is_additional)
                                            <span class="px-2 py-0.5 bg-sky-100 text-sky-700 text-[10px] font-black rounded uppercase ml-2 flex-shrink-0">Extra</span>
                                        @endif
                                    </div>
                                    @if($item->notes)
                                        <p class="text-xs text-slate-500 font-medium bg-slate-50 p-2 rounded-lg mb-3 border border-slate-100">{{ $item->notes }}</p>
                                    @endif
                                </div>

                                <div class="flex gap-2 mt-auto pt-3 border-t border-slate-100">
                                    <div class="flex-1">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Sebelum</p>
                                        @if($item->before_image)
                                            <img src="{{ asset('storage/' . $item->before_image) }}" onclick="openLightbox(this.src)" class="w-full h-24 object-cover rounded-lg cursor-pointer hover:opacity-80 transition shadow-sm border border-slate-100">
                                        @else
                                            <div class="w-full h-24 bg-slate-100 rounded-lg flex items-center justify-center text-xs text-slate-400 font-bold border border-slate-200">Kosong</div>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase mb-1">Sesudah</p>
                                        @if($item->after_image)
                                            <img src="{{ asset('storage/' . $item->after_image) }}" onclick="openLightbox(this.src)" class="w-full h-24 object-cover rounded-lg cursor-pointer hover:opacity-80 transition shadow-sm border border-slate-100">
                                        @else
                                            <div class="w-full h-24 bg-slate-100 rounded-lg flex items-center justify-center text-xs text-slate-400 font-bold border border-slate-200">Kosong</div>
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

    @if($report->status == 'planned')
        <div id="custom-confirm-modal" class="fixed inset-0 z-50 items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden">
            <div class="bg-white rounded-3xl p-8 max-w-md w-full mx-4 shadow-2xl border border-sky-100 text-center transform transition-all">
                <div class="w-16 h-16 bg-sky-100 text-sky-600 rounded-2xl flex items-center justify-center mx-auto mb-4 text-3xl shadow-inner">
                    🚀
                </div>
                <h3 class="text-xl font-black text-slate-800 mb-2">Kirim Laporan Akhir Shift?</h3>
                <p class="text-slate-500 mb-6 font-medium text-sm">Pastikan semua foto After sudah diunggah. Data tidak dapat diubah setelah disubmit.</p>
                <div class="flex space-x-3">
                    <button type="button" id="modal-btn-cancel" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-3.5 px-4 rounded-xl transition">
                        Batal
                    </button>
                    <button type="button" id="modal-btn-confirm" class="flex-1 bg-sky-600 hover:bg-sky-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-md shadow-sky-600/20 transition">
                        Ya, Kirim
                    </button>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let newTaskCounter = 0;
                const btnAddAdditional = document.getElementById('btn-add-additional-task');
                const container = document.getElementById('master-task-container');
                const btnSubmitFinal = document.getElementById('btn-submit-final');
                const finalReportForm = document.getElementById('final-report-form');
                const confirmModal = document.getElementById('custom-confirm-modal');
                const modalBtnCancel = document.getElementById('modal-btn-cancel');
                const modalBtnConfirm = document.getElementById('modal-btn-confirm');

                document.addEventListener('change', function(e) {
                    if (e.target.classList.contains('image-input')) {
                        const file = e.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            const label = e.target.closest('label');
                            const previewImg = label.querySelector('.preview-img');
                            const placeholder = label.querySelector('.upload-placeholder');
                            const overlay = label.querySelector('.preview-overlay');

                            reader.onload = function(e) {
                                previewImg.src = e.target.result;
                                previewImg.classList.remove('hidden');
                                overlay.classList.remove('hidden');
                                placeholder.classList.add('hidden');
                            }
                            reader.readAsDataURL(file);
                        }
                    }
                });

                if (btnAddAdditional) {
                    btnAddAdditional.addEventListener('click', function() {
                        const html = `
                            <div class="border border-amber-300 p-6 rounded-2xl bg-amber-50/40 relative shadow-sm hover:shadow transition mt-6 additional-item-block">
                                <button type="button" class="absolute top-4 right-4 bg-rose-50 border border-rose-200 text-rose-600 hover:bg-rose-100 font-bold py-1.5 px-3 rounded-lg text-xs z-10 btn-remove-additional transition">Hapus</button>

                                <div class="mb-4 pr-24">
                                    <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Nama Tugas Tambahan</label>
                                    <input type="text" name="new_items[${newTaskCounter}][task_name]" class="block w-full border-amber-200 bg-white text-slate-800 font-medium focus:border-amber-500 focus:ring-amber-500 rounded-xl shadow-sm py-3 px-4" required>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <p class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Foto Before</p>
                                        <label class="relative flex flex-col items-center justify-center w-full h-64 border-2 border-dashed border-amber-200 rounded-2xl cursor-pointer bg-white hover:bg-amber-50/50 overflow-hidden group transition">
                                            <div class="upload-placeholder flex flex-col items-center justify-center pt-5 pb-6">
                                                <span class="text-4xl text-amber-400 font-bold group-hover:text-amber-600">+</span>
                                                <p class="text-xs text-slate-500 font-bold mt-2 group-hover:text-amber-600">Tap Upload Foto</p>
                                            </div>
                                            <img class="preview-img absolute inset-0 w-full h-full object-contain bg-slate-900 hidden" src="" alt="Preview">
                                            <div class="preview-overlay absolute top-3 left-3 bg-slate-900/80 backdrop-blur-sm text-white text-[10px] font-black px-2.5 py-1 rounded-lg shadow hidden">BEFORE</div>
                                            <input type="file" name="new_items[${newTaskCounter}][before_image]" class="hidden image-input" accept="image/*">
                                        </label>
                                    </div>
                                    <div>
                                        <p class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Foto After</p>
                                        <label class="relative flex flex-col items-center justify-center w-full h-64 border-2 border-dashed border-amber-200 rounded-2xl cursor-pointer bg-white hover:bg-amber-50/50 overflow-hidden group transition">
                                            <div class="upload-placeholder flex flex-col items-center justify-center pt-5 pb-6">
                                                <span class="text-4xl text-amber-400 font-bold group-hover:text-amber-600">+</span>
                                                <p class="text-xs text-slate-500 font-bold mt-2 group-hover:text-amber-600">Tap Upload Foto</p>
                                            </div>
                                            <img class="preview-img absolute inset-0 w-full h-full object-contain bg-slate-900 hidden" src="" alt="Preview">
                                            <div class="preview-overlay absolute top-3 right-3 bg-emerald-600 text-white text-[10px] font-black px-2.5 py-1 rounded-lg shadow hidden">AFTER</div>
                                            <input type="file" name="new_items[${newTaskCounter}][after_image]" class="hidden image-input" accept="image/*">
                                        </label>
                                    </div>
                                </div>

                                <div class="mt-6">
                                    <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Catatan Tugas Tambahan</label>
                                    <input type="text" name="new_items[${newTaskCounter}][notes]" class="block w-full border-amber-200 bg-white text-slate-800 font-medium focus:border-amber-500 focus:ring-amber-500 rounded-xl shadow-sm py-3 px-4">
                                </div>
                            </div>
                        `;
                        container.insertAdjacentHTML('beforeend', html);
                        newTaskCounter++;
                    });
                }

                container.addEventListener('click', function(e) {
                    if (e.target.classList.contains('btn-remove-additional')) {
                        e.target.closest('.additional-item-block').remove();
                    }
                });

                if (btnSubmitFinal) {
                    btnSubmitFinal.addEventListener('click', function() {
                        confirmModal.classList.remove('hidden');
                        confirmModal.classList.add('flex');
                    });
                }

                if (modalBtnCancel) {
                    modalBtnCancel.addEventListener('click', function() {
                        confirmModal.classList.add('hidden');
                        confirmModal.classList.remove('flex');
                    });
                }

                if (modalBtnConfirm) {
                    modalBtnConfirm.addEventListener('click', function() {
                        finalReportForm.submit();
                    });
                }
            });
        </script>
    @else
        <div id="lightboxModal" class="fixed inset-0 bg-slate-900/90 z-[100] hidden items-center justify-center p-4 cursor-zoom-out transition-opacity" onclick="closeLightbox()">
            <img id="lightboxImage" src="" class="max-w-full max-h-[90vh] object-contain rounded-lg shadow-2xl">
            <button class="absolute top-6 right-6 text-white bg-slate-800/50 hover:bg-rose-600 rounded-full w-10 h-10 flex items-center justify-center font-bold text-xl backdrop-blur-sm transition">
                ×
            </button>
        </div>

        <script>
            function openLightbox(src) {
                document.getElementById('lightboxImage').src = src;
                const modal = document.getElementById('lightboxModal');
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            }

            function closeLightbox() {
                const modal = document.getElementById('lightboxModal');
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = 'auto';
            }
        </script>
    @endif
</x-app-layout>
