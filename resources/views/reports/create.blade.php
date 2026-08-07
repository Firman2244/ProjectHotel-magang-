<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-2xl text-slate-800 dark:text-white leading-tight">
            {{ __('Buat Rencana Todo List') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-sky-50/60 dark:bg-slate-900 min-h-screen transition-colors duration-300">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Card Header User -->
            <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-md shadow-sm sm:rounded-2xl border border-sky-100 dark:border-slate-700 p-6 flex justify-between items-center transition-colors duration-300">
                <div>
                    <p class="text-xs font-bold text-sky-600 dark:text-sky-400 uppercase tracking-wider">Karyawan</p>
                    <p class="font-black text-xl text-slate-800 dark:text-white mt-0.5">{{ $user->name }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs font-bold text-sky-600 dark:text-sky-400 uppercase tracking-wider">Departemen</p>
                    <p class="font-black text-xl text-sky-700 dark:text-sky-300 mt-0.5">{{ $user->department }}</p>
                </div>
            </div>

            <!-- Card Utama Form -->
            <div class="bg-white/90 dark:bg-slate-800/90 backdrop-blur-md shadow-md sm:rounded-2xl p-8 border border-sky-100 dark:border-slate-700 transition-colors duration-300">
                <form id="report-form" action="{{ route('reports.store') }}" method="POST">
                    @csrf

                    <h3 class="text-lg font-black text-slate-800 dark:text-white mb-3 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-sky-500 inline-block"></span>
                        Pilih Tugas Standar (SOP)
                    </h3>

                    <!-- Wrapper Select -->
                    <div class="mb-8 bg-sky-50/50 dark:bg-slate-900/50 p-4 rounded-xl border border-sky-200/60 dark:border-slate-600 shadow-inner">
                        <select id="task-select" class="block w-full border-sky-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-800 dark:text-white font-semibold focus:border-sky-500 focus:ring-sky-500 rounded-xl shadow-sm py-3.5 px-4 cursor-pointer transition-colors duration-300">
                            <option value="" disabled selected>-- Ketuk di sini untuk memilih tugas --</option>
                            @foreach($tasks as $task)
                                <option value="{{ $task->id }}">{{ $task->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <h3 class="text-lg font-black text-slate-800 dark:text-white mb-3 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500 inline-block"></span>
                        Daftar Tugas yang Akan Dikerjakan
                    </h3>

                    <!-- Container Tugas -->
                    <div id="task-container" class="space-y-4">
                        <p id="empty-state" class="text-slate-400 dark:text-slate-500 font-medium text-center py-8 bg-sky-50/30 dark:bg-slate-900/30 border border-dashed border-sky-200 dark:border-slate-600 rounded-xl transition-colors duration-300">
                            Belum ada tugas yang ditambahkan. Silakan pilih dari menu di atas.
                        </p>
                        <div id="standard-task-zone" class="space-y-4"></div>
                    </div>

                    <!-- KUMPULAN TOMBOL (DITAMBAHKAN TOMBOL KEMBALI) -->
                    <div class="mt-10 flex flex-col sm:flex-row justify-end gap-4">
                        <a href="{{ route('dashboard') }}" class="w-full sm:w-auto bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-bold py-4 px-8 rounded-xl transition duration-150 ease-in-out text-center text-lg flex items-center justify-center">
                            Kembali
                        </a>
                        <button type="button" id="btn-send-todo" class="w-full sm:w-auto bg-sky-600 hover:bg-sky-700 text-white font-extrabold py-4 px-8 rounded-xl transition duration-150 ease-in-out shadow-lg shadow-sky-600/20 text-lg">
                            Kirim Todo List (Pagi)
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <!-- Modal Konfirmasi -->
    <div id="custom-confirm-modal" class="fixed inset-0 z-50 items-center justify-center bg-slate-900/60 backdrop-blur-sm hidden">
        <div class="bg-white dark:bg-slate-800 rounded-3xl p-8 max-w-md w-full mx-4 shadow-2xl border border-sky-100 dark:border-slate-700 text-center transform transition-all duration-300">
            <div class="w-16 h-16 bg-sky-100 dark:bg-sky-900/40 text-sky-600 dark:text-sky-400 rounded-2xl flex items-center justify-center mx-auto mb-4 text-3xl shadow-inner">
                📋
            </div>
            <h3 class="text-xl font-black text-slate-800 dark:text-white mb-2">Konfirmasi Kirim Laporan</h3>
            <p class="text-slate-500 dark:text-slate-400 mb-6 font-medium text-sm">Kirim daftar rencana tugas ini sekarang ke admin?</p>
            <div class="flex space-x-3">
                <button type="button" id="modal-btn-cancel" class="flex-1 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-bold py-3.5 px-4 rounded-xl transition-colors duration-200">
                    Batal
                </button>
                <button type="button" id="modal-btn-confirm" class="flex-1 bg-sky-600 hover:bg-sky-700 text-white font-bold py-3.5 px-4 rounded-xl shadow-md shadow-sky-600/20 transition-colors duration-200">
                    Ya, Kirim
                </button>
            </div>
        </div>
    </div>

    <!-- Script Refactor yang Lebih Ringan -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const taskSelect = document.getElementById('task-select');
            const standardTaskZone = document.getElementById('standard-task-zone');
            const emptyState = document.getElementById('empty-state');
            const reportForm = document.getElementById('report-form');
            const confirmModal = document.getElementById('custom-confirm-modal');
            let taskCounter = 0;

            // Optimasi Toggle State
            const toggleEmptyState = () => {
                emptyState.style.display = standardTaskZone.children.length === 0 ? 'block' : 'none';
            };

            taskSelect.addEventListener('change', function() {
                if (this.selectedIndex <= 0) return;

                const option = this.options[this.selectedIndex];
                const taskId = option.value;
                const taskName = option.text;

                emptyState.style.display = 'none';

                // Injeksi HTML statis (tercepat)
                standardTaskZone.insertAdjacentHTML('beforeend', `
                    <div class="border border-sky-200 dark:border-slate-600 p-4 rounded-xl bg-sky-50/80 dark:bg-slate-700/80 flex justify-between items-center task-item shadow-sm transition hover:shadow" data-task-id="${taskId}" data-task-name="${taskName}">
                        <div class="font-bold text-slate-800 dark:text-white flex-1 flex items-center gap-3">
                            <span class="w-2 h-2 rounded-full bg-sky-400 dark:bg-sky-500"></span>
                            ${taskName}
                            <input type="hidden" name="items[${taskCounter}][task_id]" value="${taskId}">
                            <input type="hidden" name="items[${taskCounter}][is_additional]" value="0">
                        </div>
                        <button type="button" class="bg-rose-50 dark:bg-rose-900/30 border border-rose-200 dark:border-rose-800/50 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/60 font-bold py-1.5 px-3 rounded-lg text-xs btn-remove-standard-task ml-4 transition">Hapus</button>
                    </div>
                `);

                option.remove();
                this.selectedIndex = 0;
                taskCounter++;
            });

            // Event Delegation & DocumentFragment (Mencegah Layout Thrashing)
            standardTaskZone.addEventListener('click', (e) => {
                if (!e.target.classList.contains('btn-remove-standard-task')) return;

                const taskBlock = e.target.closest('.task-item');
                const taskId = taskBlock.dataset.taskId;
                const taskName = taskBlock.dataset.taskName;

                taskSelect.add(new Option(taskName, taskId));

                // Optimasi pengurutan opsi menggunakan fragment
                const fragment = document.createDocumentFragment();
                const optionsArray = Array.from(taskSelect.options).slice(1).sort((a, b) => a.value - b.value);

                fragment.appendChild(taskSelect.options[0]); // Kembalikan opsi default
                optionsArray.forEach(opt => fragment.appendChild(opt)); // Masukkan opsi yang terurut

                taskSelect.innerHTML = '';
                taskSelect.appendChild(fragment);

                taskBlock.remove();
                toggleEmptyState();
            });

            // Optimasi Toggle Modal
            const toggleModal = (show) => {
                if (show) {
                    if (!standardTaskZone.children.length) return alert("Tambahkan minimal 1 tugas sebelum mengirim!");
                    confirmModal.classList.replace('hidden', 'flex');
                } else {
                    confirmModal.classList.replace('flex', 'hidden');
                }
            };

            document.getElementById('btn-send-todo').addEventListener('click', () => toggleModal(true));
            document.getElementById('modal-btn-cancel').addEventListener('click', () => toggleModal(false));
            document.getElementById('modal-btn-confirm').addEventListener('click', () => reportForm.submit());
        });
    </script>
</x-app-layout>
