<section class="space-y-6">
    <header>
        <h2 class="text-xl font-black text-rose-600 dark:text-rose-500 flex items-center gap-2">
            ⚠️ {{ __('Delete Account') }}
        </h2>

        <p class="mt-1 text-sm font-medium text-slate-600 dark:text-slate-400">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <x-danger-button x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')" class="bg-rose-600 hover:bg-rose-700 text-white rounded-xl py-2.5 px-6 font-bold transition shadow-sm">
        {{ __('Delete Account') }}
    </x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-8 bg-white dark:bg-slate-800 rounded-3xl border border-rose-100 dark:border-rose-900/50">
            @csrf
            @method('delete')

            <div class="w-16 h-16 rounded-2xl bg-rose-100 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 flex items-center justify-center text-3xl mx-auto mb-4 shadow-inner">🗑️</div>

            <h2 class="text-xl font-black text-center text-slate-900 dark:text-white">
                {{ __('Are you sure you want to delete your account?') }}
            </h2>

            <p class="mt-2 text-sm text-center font-medium text-slate-600 dark:text-slate-400 mb-6">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />
                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full bg-slate-50 dark:bg-slate-900 border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white focus:border-rose-500 focus:ring-rose-500 rounded-xl py-3" placeholder="{{ __('Enter your Password to confirm') }}" />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2 text-rose-600 dark:text-rose-400 font-bold" />
            </div>

            <div class="mt-8 flex gap-3">
                <button type="button" x-on:click="$dispatch('close')" class="flex-1 px-4 py-3 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-bold rounded-xl transition">
                    {{ __('Cancel') }}
                </button>

                <button type="submit" class="flex-1 px-4 py-3 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl transition shadow-lg shadow-rose-600/30">
                    {{ __('Delete Account') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>
