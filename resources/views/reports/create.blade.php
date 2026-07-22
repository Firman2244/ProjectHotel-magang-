<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Buat Laporan Harian') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Kotak Informasi Karyawan -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 flex justify-between items-center bg-gray-50 border-b">
                    <div>
                        <p class="text-sm text-gray-500">Karyawan</p>
                        <p class="font-bold text-lg">{{ $user->name }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-500">Departemen</p>
                        <p class="font-bold text-lg text-blue-600">{{ $user->department }}</p>
                    </div>
                </div>
            </div>

            <!-- Form Laporan -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form action="#" method="POST" enctype="multipart/form-data">
                    @csrf

                    <h3 class="text-lg font-bold mb-4 border-b pb-2">Daftar Tugas Standar</h3>

                    <div class="space-y-6">
                        @foreach($tasks as $index => $task)
                            <div class="border border-gray-200 p-4 rounded-lg bg-gray-50">
                                <div class="mb-3">
                                    <span class="font-semibold text-gray-800">{{ $index + 1 }}. {{ $task->name }}</span>
                                    <!-- Input tersembunyi untuk membawa task_id ke database nantinya -->
                                    <input type="hidden" name="items[{{ $index }}][task_id]" value="{{ $task->id }}">
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Foto Before -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Foto Before</label>
                                        <input type="file" name="items[{{ $index }}][before_image]" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" accept="image/*">
                                    </div>
                                    <!-- Foto After -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Foto After</label>
                                        <input type="file" name="items[{{ $index }}][after_image]" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" accept="image/*">
                                    </div>
                                </div>

                                <div class="mt-3">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (No. Kamar / Alasan Batal)</label>
                                    <input type="text" name="items[{{ $index }}][notes]" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" placeholder="Opsional...">
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-8 flex justify-end">
                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition duration-150 ease-in-out">
                            Kirim Laporan
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
