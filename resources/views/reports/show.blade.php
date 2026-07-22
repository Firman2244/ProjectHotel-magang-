<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-2xl text-slate-800 leading-tight">
            {{ __('Detail Laporan & Upload Foto') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-sky-50/60 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white/90 backdrop-blur-md shadow-sm sm:rounded-2xl border border-sky-100 p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                <div>
                    <p class="text-xs font-bold text-sky-600 uppercase tracking-wider">Karyawan</p>
                    <p class="font-black text-xl text-slate-800 mt-0.5">{{ $report->user->name }}</p>
                </div>
                <div class="text-left md:text-right">
                    <p class="text-xs font-bold text-sky-600 uppercase tracking-wider">Departemen</p>
                    <p class="font-black text-xl text-sky-700 mt-0.5">{{ $report->user->department }}</p>
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
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-2.5 px-4 rounded-xl transition border border-slate-200 text-sm">
                            &#8592; Kembali
                        </a>
                        <h3 class="text-xl font-black text-slate-800">Daftar Pekerjaan</h3>
                    </div>

                    <div class="flex items-center gap-4">
                        <span class="px-3.5 py-1.5 rounded-full text-xs font-black uppercase tracking-wider {{ $report->status == 'completed' ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-amber-100 text-amber-700 border border-amber-200' }}">
                            Status: {{ $report->status }}
                        </span>

                        @if($report->status == 'planned')
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
                @endif

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
                                    @if($report->status == 'planned')
                                        <label class="relative flex flex-col items-center justify-center w-full h-64 border-2 border-dashed border-sky-200 rounded-2xl cursor-pointer bg-white hover:bg-sky-50/50 overflow-hidden group transition">
                                            <div class="upload-placeholder flex-col items-center justify-center pt-5 pb-6 {{ $item->before_image ? 'hidden' : 'flex' }}">
                                                <span class="text-4xl text-sky-400 font-bold group-hover:text-sky-600">+</span>
                                                <p class="text-xs text-slate-500 font-bold mt-2 group-hover:text-sky-600">Tap Upload Foto</p>
                                            </div>
                                            <img class="preview-img absolute inset-0 w-full h-full object-contain bg-slate-900 {{ $item->before_image ? '' : 'hidden' }}" src="{{ $item->before_image ? asset('storage/' . $item->before_image) : '' }}" alt="Preview">
                                            <div class="preview-overlay absolute top-3 left-3 bg-slate-900/80 backdrop-blur-sm text-white text-[10px] font-black px-2.5 py-1 rounded-lg shadow {{ $item->before_image ? '' : 'hidden' }}">BEFORE</div>
                                            <input type="file" name="items[{{ $item->id }}][before_image]" class="hidden image-input" accept="image/*" capture="environment">
                                        </label>
                                    @else
                                        @if($item->before_image)
                                            <a href="{{ asset('storage/' . $item->before_image) }}" target="_blank" class="block w-full h-64 border border-sky-200 rounded-2xl overflow-hidden bg-slate-900 relative group cursor-pointer shadow-sm" title="Klik untuk lihat ukuran penuh">
                                                <img src="{{ asset('storage/' . $item->before_image) }}" class="w-full h-full object-contain" alt="Before">
                                            </a>
                                        @else
                                            <div class="w-full h-64 border border-dashed border-sky-200 rounded-2xl flex items-center justify-center bg-sky-50/30 text-slate-400 font-medium text-sm">Belum ada foto</div>
                                        @endif
                                    @endif
                                </div>

                                <div>
                                    <p class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Foto After</p>
                                    @if($report->status == 'planned')
                                        <label class="relative flex flex-col items-center justify-center w-full h-64 border-2 border-dashed border-sky-200 rounded-2xl cursor-pointer bg-white hover:bg-sky-50/50 overflow-hidden group transition">
                                            <div class="upload-placeholder flex-col items-center justify-center pt-5 pb-6 {{ $item->after_image ? 'hidden' : 'flex' }}">
                                                <span class="text-4xl text-sky-400 font-bold group-hover:text-sky-600">+</span>
                                                <p class="text-xs text-slate-500 font-bold mt-2 group-hover:text-sky-600">Tap Upload Foto</p>
                                            </div>
                                            <img class="preview-img absolute inset-0 w-full h-full object-contain bg-slate-900 {{ $item->after_image ? '' : 'hidden' }}" src="{{ $item->after_image ? asset('storage/' . $item->after_image) : '' }}" alt="Preview">
                                            <div class="preview-overlay absolute top-3 right-3 bg-emerald-600 text-white text-[10px] font-black px-2.5 py-1 rounded-lg shadow {{ $item->after_image ? '' : 'hidden' }}">AFTER</div>
                                            <input type="file" name="items[{{ $item->id }}][after_image]" class="hidden image-input" accept="image/*" capture="environment">
                                        </label>
                                    @else
                                        @if($item->after_image)
                                            <a href="{{ asset('storage/' . $item->after_image) }}" target="_blank" class="block w-full h-64 border border-sky-200 rounded-2xl overflow-hidden bg-slate-900 relative group cursor-pointer shadow-sm" title="Klik untuk lihat ukuran penuh">
                                                <img src="{{ asset('storage/' . $item->after_image) }}" class="w-full h-full object-contain" alt="After">
                                            </a>
                                        @else
                                            <div class="w-full h-64 border border-dashed border-sky-200 rounded-2xl flex items-center justify-center bg-sky-50/30 text-slate-400 font-medium text-sm">Belum ada foto</div>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            <div class="mt-6">
                                <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Catatan Tugas</label>
                                @if($report->status == 'planned')
                                    <input type="text" name="items[{{ $item->id }}][notes]" value="{{ old('items.'.$item->id.'.notes', $item->notes) }}" class="block w-full border-sky-200 bg-white text-slate-800 font-medium focus:border-sky-500 focus:ring-sky-500 rounded-xl shadow-sm py-3 px-4" placeholder="Ketik catatan tambahan jika ada...">
                                @else
                                    <div class="w-full border border-sky-200 rounded-xl bg-white py-3 px-4 text-slate-800 min-h-[50px] font-medium text-sm flex items-center">
                                        {{ $item->notes ?: '-' }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($report->status == 'planned')
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
                    <div class="mt-8 flex justify-start">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold py-3 px-6 rounded-xl transition shadow-sm">
                            Kembali ke Dashboard
                        </a>
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- MODAL KONFIRMASI AKHIR SHIFT -->
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
                                <input type="text" name="new_items[${newTaskCounter}][task_name]" class="block w-full border-amber-200 bg-white text-slate-800 font-medium focus:border-amber-500 focus:ring-amber-500 rounded-xl shadow-sm py-3 px-4" placeholder="Ketik nama tugas di sini..." required>
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
                                        <input type="file" name="new_items[${newTaskCounter}][before_image]" class="hidden image-input" accept="image/*" capture="environment">
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
                                        <input type="file" name="new_items[${newTaskCounter}][after_image]" class="hidden image-input" accept="image/*" capture="environment">
                                    </label>
                                </div>
                            </div>

                            <div class="mt-6">
                                <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-2">Catatan Tugas Tambahan</label>
                                <input type="text" name="new_items[${newTaskCounter}][notes]" class="block w-full border-amber-200 bg-white text-slate-800 font-medium focus:border-amber-500 focus:ring-amber-500 rounded-xl shadow-sm py-3 px-4" placeholder="Ketik catatan tambahan jika ada...">
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
                });
            }

            if (modalBtnCancel) {
                modalBtnCancel.addEventListener('click', function() {
                    confirmModal.classList.add('hidden');
                });
            }

            if (modalBtnConfirm) {
                modalBtnConfirm.addEventListener('click', function() {
                    finalReportForm.submit();
                });
            }
        });
    </script>
    @endif
</x-app-layout>
