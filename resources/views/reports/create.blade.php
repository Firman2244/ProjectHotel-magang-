<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-2xl text-slate-800 leading-tight">
            {{ __('Buat Rencana Todo List') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-sky-50/60 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white/90 backdrop-blur-md shadow-sm sm:rounded-2xl border border-sky-100 p-6 flex justify-between items-center">
                <div>
                    <p class="text-xs font-bold text-sky-600 uppercase tracking-wider">Karyawan</p>
                    <p class="font-black text-xl text-slate-800 mt-0.5">{{ $user->name }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs font-bold text-sky-600 uppercase tracking-wider">Departemen</p>
                    <p class="font-black text-xl text-sky-700 mt-0.5">{{ $user->department }}</p>
                </div>
            </div>

            <div class="bg-white/90 backdrop-blur-md shadow-md sm:rounded-2xl p-8 border border-sky-100">
                <form id="report-form" action="{{ route('reports.store') }}" method="POST">
                    @csrf

                    <h3 class="text-lg font-black text-slate-800 mb-3 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-sky-500 inline-block"></span>
                        Pilih Tugas Standar (SOP)
                    </h3>

                    <div class="mb-8 bg-sky-50/50 p-4 rounded-xl border border-sky-200/60 shadow-inner">
                        <select id="task-select" class="block w-full border-sky-200 bg-white text-slate-800 font-semibold focus:border-sky-500 focus:ring-sky-500 rounded-xl shadow-sm py-3.5 px-4 cursor-pointer">
                            <option value="" disabled selected>-- Ketuk di sini untuk memilih tugas --</option>
                            @foreach($tasks as $task)
                                <option value="{{ $task->id }}">{{ $task->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <h3 class="text-lg font-black text-slate-800 mb-3 flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500 inline-block"></span>
                        Daftar Tugas yang Akan Dikerjakan
                    </h3>

                    <div id="task-container" class="space-y-4">
                        <p id="empty-state" class="text-slate-400 font-medium text-center py-8 bg-sky-50/30 border border-dashed border-sky-200 rounded-xl">Belum ada tugas yang ditambahkan. Silakan pilih dari menu di atas.</p>
                        <div id="standard-task-zone" class="space-y-4"></div>
                    </div>

                    <div class="mt-10 flex justify-end">
                        <button type="button" id="btn-send-todo" class="w-full sm:w-auto bg-sky-600 hover:bg-sky-700 text-white font-extrabold py-4 px-8 rounded-xl transition duration-150 ease-in-out shadow-lg shadow-sky-600/20 text-lg">
                            Kirim Todo List (Pagi)
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    <div id="custom-confirm-modal" class="fixed inset-0 z-50 items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden">
        <div class="bg-white rounded-3xl p-8 max-w-md w-full mx-4 shadow-2xl border border-sky-100 text-center transform transition-all">
            <div class="w-16 h-16 bg-sky-100 text-sky-600 rounded-2xl flex items-center justify-center mx-auto mb-4 text-3xl shadow-inner">
                📋
            </div>
            <h3 class="text-xl font-black text-slate-800 mb-2">Konfirmasi Kirim Laporan</h3>
            <p class="text-slate-500 mb-6 font-medium text-sm">Kirim daftar rencana tugas ini sekarang ke admin?</p>
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
            const taskSelect = document.getElementById('task-select');
            const standardTaskZone = document.getElementById('standard-task-zone');
            const emptyState = document.getElementById('empty-state');
            const reportForm = document.getElementById('report-form');
            const btnSendTodo = document.getElementById('btn-send-todo');
            const confirmModal = document.getElementById('custom-confirm-modal');
            const modalBtnCancel = document.getElementById('modal-btn-cancel');
            const modalBtnConfirm = document.getElementById('modal-btn-confirm');
            let taskCounter = 0;

            function checkEmptyState() {
                if (standardTaskZone.children.length === 0) {
                    emptyState.style.display = 'block';
                } else {
                    emptyState.style.display = 'none';
                }
            }

            taskSelect.addEventListener('change', function() {
                if (this.selectedIndex === 0 || this.value === "") {
                    return;
                }

                const selectedOption = this.options[this.selectedIndex];
                const taskId = selectedOption.value;
                const taskName = selectedOption.text;

                emptyState.style.display = 'none';

                const taskHtml = `
                    <div class="border border-sky-200 p-4 rounded-xl bg-sky-50/80 flex justify-between items-center task-item shadow-sm transition hover:shadow" data-task-id="${taskId}" data-task-name="${taskName}">
                        <div class="font-bold text-slate-800 flex-1 flex items-center gap-3">
                            <span class="w-2 h-2 rounded-full bg-sky-400"></span>
                            ${taskName}
                            <input type="hidden" name="items[${taskCounter}][task_id]" value="${taskId}">
                            <input type="hidden" name="items[${taskCounter}][is_additional]" value="0">
                        </div>
                        <button type="button" class="bg-rose-50 border border-rose-200 text-rose-600 hover:bg-rose-100 font-bold py-1.5 px-3 rounded-lg text-xs btn-remove-standard-task ml-4 transition">Hapus</button>
                    </div>
                `;

                standardTaskZone.insertAdjacentHTML('beforeend', taskHtml);
                selectedOption.remove();
                this.selectedIndex = 0;
                taskCounter++;
            });

            standardTaskZone.addEventListener('click', function(e) {
                if (e.target.classList.contains('btn-remove-standard-task')) {
                    const taskBlock = e.target.closest('.task-item');
                    const taskId = taskBlock.getAttribute('data-task-id');
                    const taskName = taskBlock.getAttribute('data-task-name');

                    const newOption = document.createElement('option');
                    newOption.value = taskId;
                    newOption.text = taskName;
                    taskSelect.appendChild(newOption);

                    const optionsArray = Array.from(taskSelect.options).slice(1);
                    optionsArray.sort((a, b) => parseInt(a.value) - parseInt(b.value));
                    optionsArray.forEach(opt => taskSelect.appendChild(opt));

                    taskBlock.remove();
                    checkEmptyState();
                }
            });

            btnSendTodo.addEventListener('click', function() {
                if (standardTaskZone.children.length === 0) {
                    alert("Tambahkan minimal 1 tugas sebelum mengirim!");
                    return;
                }
                confirmModal.classList.remove('hidden');
                confirmModal.classList.add('flex');
            });

            modalBtnCancel.addEventListener('click', function() {
                confirmModal.classList.add('hidden');
                confirmModal.classList.remove('flex');
            });

            modalBtnConfirm.addEventListener('click', function() {
                reportForm.submit();
            });
        });
    </script>
</x-app-layout>
