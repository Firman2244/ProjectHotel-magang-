<x-app-layout>
    <x-slot name="header">
        <h2 class="font-black text-2xl text-slate-800 dark:text-white leading-tight">
            {{ __('Profile Settings') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-sky-50/40 dark:bg-slate-900 min-h-screen transition-colors duration-300">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-6 sm:p-8 bg-white dark:bg-slate-800 shadow-sm border border-sky-100 dark:border-slate-700 rounded-2xl transition-colors duration-300">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-6 sm:p-8 bg-white dark:bg-slate-800 shadow-sm border border-sky-100 dark:border-slate-700 rounded-2xl transition-colors duration-300">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-6 sm:p-8 bg-rose-50/50 dark:bg-rose-900/10 shadow-sm border border-rose-100 dark:border-rose-900/50 rounded-2xl transition-colors duration-300">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
