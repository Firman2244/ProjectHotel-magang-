<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-900 leading-tight">
            {{ __('Detail Laporan & Upload Foto') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white shadow-md sm:rounded-lg mb-6 border border-gray-300">
                <div class="p-6 text-gray-900 flex flex-col md:flex-row justify-between items-start md:items-center bg-gray-100 border-b border-gray-300 gap-4">
                    <div>
                        <p class="text-sm font-bold text-gray-700 uppercase">Karyawan</p>
                        <p class="font-extrabold text-xl text-gray-900">{{ $report->user->name }}</p>
                    </div>
                    <div class="text-left md:text-right">
                        <p class="text-sm font-bold text-gray-700 uppercase">Departemen</p>
                        <p class="font-extrabold text-xl text-blue-800">{{ $report->user->department }}</p>
                    </div>
                </div>
            </div>

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-6 font-bold" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white shadow-md sm:rounded-lg p-6 border border-gray-300">

                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 border-b-2 border-gray-400 pb-4 gap-4">
                    <div class="flex items-center gap-4">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg transition border border-gray-400 text-sm">
                            &#8592; Kembali
                        </a>
                        <h3 class="text-xl font-extrabold text-gray-900">Daftar Pekerjaan</h3>
                    </div>

                    <div class="flex items-center gap-4">
                        <span class="px-3 py-1 rounded-full text-sm font-bold {{ $report->status == 'completed' ? 'bg-green-100 text-green-700 border border-green-400' : 'bg-yellow-100 text-yellow-700 border border-yellow-400' }}">
                            Status: {{ strtoupper($report->status) }}
                        </span>

                        @if($report->status == 'planned')
                            <form action="{{ route('reports.destroy', $report->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan ini? Seluruh data yang belum disubmit akan hilang secara permanen.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold py-1 px-4 rounded shadow transition text-sm">
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
                        <div class="border-2 border-gray-300 p-5 rounded-xl bg-gray-50 relative shadow-sm hover:shadow-md transition">
                            <div class="mb-4">
                                <span class="font-extrabold text-lg text-gray-900">
                                    {{ $index + 1 }}. {{ $item->is_additional ? ($item->task_name ?? 'Tugas Tambahan') : ($item->task->name ?? 'Tugas') }}
                                </span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <p class="block text-base font-bold text-gray-900 mb-2">Foto Before</p>
                                    @if($report->status == 'planned')
                                        <label class="relative flex flex-col items-center justify-center w-full h-64 border-2 border-dashed border-gray-400 rounded-xl cursor-pointer bg-white hover:bg-gray-100 overflow-hidden group">
                                            <div class="upload-placeholder flex flex-col items-center justify-center pt-5 pb-6 {{ $item->before_image ? 'hidden' : '' }}">
                                                <span class="text-4xl text-gray-400 font-bold group-hover:text-blue-600">+</span>
                                                <p class="text-sm text-gray-500 font-bold mt-2 group-hover:text-blue-600">Tap Upload Foto</p>
                                            </div>
                                            <img class="preview-img absolute inset-0 w-full h-full object-contain bg-gray-800 {{ $item->before_image ? '' : 'hidden' }}" src="{{ $item->before_image ? asset('storage/' . $item->before_image) : '' }}" alt="Preview">
                                            <div class="preview-overlay absolute top-2 left-2 bg-black bg-opacity-70 text-white text-xs font-extrabold px-3 py-1 rounded shadow {{ $item->before_image ? '' : 'hidden' }}">BEFORE</div>
                                            <input type="file" name="items[{{ $item->id }}][before_image]" class="hidden image-input" accept="image/*" capture="environment">
                                        </label>
                                    @else
                                        @if($item->before_image)
                                            <a href="{{ asset('storage/' . $item->before_image) }}" target="_blank" class="block w-full h-64 border-2 border-gray-300 rounded-xl overflow-hidden bg-gray-800 relative group cursor-pointer" title="Klik untuk lihat ukuran penuh">
                                                <img src="{{ asset('storage/' . $item->before_image) }}" class="w-full h-full object-contain" alt="Before">
                                            </a>
                                        @else
                                            <div class="w-full h-64 border-2 border-dashed border-gray-300 rounded-xl flex items-center justify-center bg-gray-100 text-gray-400 font-medium">Belum ada foto</div>
                                        @endif
                                    @endif
                                </div>

                                <div>
                                    <p class="block text-base font-bold text-gray-900 mb-2">Foto After</p>
                                    @if($report->status == 'planned')
                                        <label class="relative flex flex-col items-center justify-center w-full h-64 border-2 border-dashed border-gray-400 rounded-xl cursor-pointer bg-white hover:bg-gray-100 overflow-hidden group">
                                            <div class="upload-placeholder flex flex-col items-center justify-center pt-5 pb-6 {{ $item->after_image ? 'hidden' : '' }}">
                                                <span class="text-4xl text-gray-400 font-bold group-hover:text-blue-600">+</span>
                                                <p class="text-sm text-gray-500 font-bold mt-2 group-hover:text-blue-600">Tap Upload Foto</p>
                                            </div>
                                            <img class="preview-img absolute inset-0 w-full h-full object-contain bg-gray-800 {{ $item->after_image ? '' : 'hidden' }}" src="{{ $item->after_image ? asset('storage/' . $item->after_image) : '' }}" alt="Preview">
                                            <div class="preview-overlay absolute top-2 right-2 bg-green-600 text-white text-xs font-extrabold px-3 py-1 rounded shadow {{ $item->after_image ? '' : 'hidden' }}">AFTER</div>
                                            <input type="file" name="items[{{ $item->id }}][after_image]" class="hidden image-input" accept="image/*" capture="environment">
                                        </label>
                                    @else
                                        @if($item->after_image)
                                            <a href="{{ asset('storage/' . $item->after_image) }}" target="_blank" class="block w-full h-64 border-2 border-gray-300 rounded-xl overflow-hidden bg-gray-800 relative group cursor-pointer" title="Klik untuk lihat ukuran penuh">
                                                <img src="{{ asset('storage/' . $item->after_image) }}" class="w-full h-full object-contain" alt="After">
                                            </a>
                                        @else
                                            <div class="w-full h-64 border-2 border-dashed border-gray-300 rounded-xl flex items-center justify-center bg-gray-100 text-gray-400 font-medium">Belum ada foto</div>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            <div class="mt-6">
                                <label class="block text-base font-bold text-gray-900 mb-2">Catatan Tugas</label>
                                @if($report->status == 'planned')
                                    <input type="text" name="items[{{ $item->id }}][notes]" value="{{ old('items.'.$item->id.'.notes', $item->notes) }}" class="block w-full border-gray-400 focus:border-blue-700 focus:ring-blue-700 rounded-md shadow-sm py-3 text-gray-900" placeholder="Ketik catatan tambahan jika ada...">
                                @else
                                    <div class="w-full border border-gray-300 rounded-md bg-white py-3 px-4 text-gray-800 min-h-[50px]">
                                        {{ $item->notes ?: '-' }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($report->status == 'planned')
                    <div class="mt-6 border-t-2 border-gray-300 pt-6">
                        <button type="button" id="btn-add-additional-task" class="w-full sm:w-auto bg-gray-100 hover:bg-gray-200 text-gray-900 font-bold py-3 px-6 rounded-lg border-2 border-gray-500 shadow-sm transition duration-150 ease-in-out">
                            + Input Tugas Tambahan (Manual)
                        </button>
                    </div>

                    <div class="mt-8 flex justify-between items-center bg-blue-50 p-4 rounded-xl border border-blue-200">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg transition duration-150 shadow">
                            Kembali ke Dashboard
                        </a>
                        <button type="button" id="btn-submit-final" class="bg-blue-700 hover:bg-blue-800 text-white font-extrabold py-4 px-8 rounded-lg transition duration-150 ease-in-out border-2 border-blue-900 shadow-lg text-lg">
                            Kirim Laporan Akhir Shift
                        </button>
                    </div>
                    </form>
                @else
                    <div class="mt-8 flex justify-start">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-6 rounded-lg transition duration-150 shadow">
                            Kembali ke Dashboard
                        </a>
                    </div>
                @endif
            </div>

        </div>
    </div>

    <!-- MODAL KONFIRMASI KUSTOM AKHIR SHIFT -->
    @if($report->status == 'planned')
    <div id="custom-confirm-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 backdrop-blur-sm hidden">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4 shadow-2xl border border-gray-200 text-center transform transition-all">
            <div class="w-16 h-16 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">
                🚀
            </div>
            <h3 class="text-xl font-extrabold text-gray-900 mb-2">Kirim Laporan Akhir Shift?</h3>
            <p class="text-gray-600 mb-6 font-medium">Pastikan semua foto After sudah diunggah. Data tidak dapat diubah setelah disubmit.</p>
            <div class="flex space-x-4">
                <button type="button" id="modal-btn-cancel" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-3 px-4 rounded-xl transition">
                    Batal
                </button>
                <button type="button" id="modal-btn-confirm" class="flex-1 bg-blue-700 hover:bg-blue-800 text-white font-bold py-3 px-4 rounded-xl shadow-md transition">
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

            btnAddAdditional.addEventListener('click', function() {
                const html = `
                    <div class="border-2 border-yellow-400 p-5 rounded-xl bg-yellow-50 relative shadow-sm hover:shadow-md transition mt-6 additional-item-block">
                        <button type="button" class="absolute top-2 right-2 bg-red-100 border border-red-400 text-red-700 hover:bg-red-200 font-bold py-1 px-3 rounded z-10 btn-remove-additional">X Batal</button>

                        <div class="mb-4 pr-24">
                            <label class="block text-base font-extrabold text-gray-900 mb-2">Nama Tugas Tambahan</label>
                            <input type="text" name="new_items[${newTaskCounter}][task_name]" class="block w-full border-gray-400 focus:border-blue-700 focus:ring-blue-700 rounded-md shadow-sm py-3 text-gray-900" placeholder="Ketik nama tugas di sini..." required>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <p class="block text-base font-bold text-gray-900 mb-2">Foto Before</p>
                                <label class="relative flex flex-col items-center justify-center w-full h-64 border-2 border-dashed border-gray-400 rounded-xl cursor-pointer bg-white hover:bg-gray-100 overflow-hidden group">
                                    <div class="upload-placeholder flex flex-col items-center justify-center pt-5 pb-6">
                                        <span class="text-4xl text-gray-400 font-bold group-hover:text-blue-600">+</span>
                                        <p class="text-sm text-gray-500 font-bold mt-2 group-hover:text-blue-600">Tap Upload Foto</p>
                                    </div>
                                    <img class="preview-img absolute inset-0 w-full h-full object-contain bg-gray-800 hidden" src="" alt="Preview">
                                    <div class="preview-overlay absolute top-2 left-2 bg-black bg-opacity-70 text-white text-xs font-extrabold px-3 py-1 rounded shadow hidden">BEFORE</div>
                                    <input type="file" name="new_items[${newTaskCounter}][before_image]" class="hidden image-input" accept="image/*" capture="environment">
                                </label>
                            </div>
                            <div>
                                <p class="block text-base font-bold text-gray-900 mb-2">Foto After</p>
                                <label class="relative flex flex-col items-center justify-center w-full h-64 border-2 border-dashed border-gray-400 rounded-xl cursor-pointer bg-white hover:bg-gray-100 overflow-hidden group">
                                    <div class="upload-placeholder flex flex-col items-center justify-center pt-5 pb-6">
                                        <span class="text-4xl text-gray-400 font-bold group-hover:text-blue-600">+</span>
                                        <p class="text-sm text-gray-500 font-bold mt-2 group-hover:text-blue-600">Tap Upload Foto</p>
                                    </div>
                                    <img class="preview-img absolute inset-0 w-full h-full object-contain bg-gray-800 hidden" src="" alt="Preview">
                                    <div class="preview-overlay absolute top-2 right-2 bg-green-600 text-white text-xs font-extrabold px-3 py-1 rounded shadow hidden">AFTER</div>
                                    <input type="file" name="new_items[${newTaskCounter}][after_image]" class="hidden image-input" accept="image/*" capture="environment">
                                </label>
                            </div>
                        </div>

                        <div class="mt-6">
                            <label class="block text-base font-bold text-gray-900 mb-2">Catatan Tugas Tambahan</label>
                            <input type="text" name="new_items[${newTaskCounter}][notes]" class="block w-full border-gray-400 focus:border-blue-700 focus:ring-blue-700 rounded-md shadow-sm py-3 text-gray-900" placeholder="Ketik catatan tambahan jika ada...">
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', html);
                newTaskCounter++;
            });

            container.addEventListener('click', function(e) {
                if (e.target.classList.contains('btn-remove-additional')) {
                    e.target.closest('.additional-item-block').remove();
                }
            });

            btnSubmitFinal.addEventListener('click', function() {
                confirmModal.classList.remove('hidden');
            });

            modalBtnCancel.addEventListener('click', function() {
                confirmModal.classList.add('hidden');
            });

            modalBtnConfirm.addEventListener('click', function() {
                finalReportForm.submit();
            });
        });
    </script>
    @endif
</x-app-layout>
