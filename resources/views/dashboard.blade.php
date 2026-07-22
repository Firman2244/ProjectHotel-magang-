<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Selamat Datang, {{ auth()->user()->name }}!</h3>

                    <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                        <ul class="list-disc list-inside space-y-1">
                            <li><strong>Hotel:</strong> {{ auth()->user()->hotel->name ?? 'Belum ada hotel' }}</li>
                            <li><strong>Role:</strong> <span class="capitalize">{{ auth()->user()->role }}</span></li>
                            <li><strong>Departemen:</strong> {{ auth()->user()->department ?? '-' }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
