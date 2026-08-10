<section>
    <header>
        <h2 class="text-xl font-black text-slate-900 dark:text-white">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm font-medium text-slate-600 dark:text-slate-400">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Current Password')" class="font-bold text-slate-700 dark:text-slate-300" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white focus:border-sky-500 focus:ring-sky-500 rounded-xl" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2 text-rose-600 dark:text-rose-400" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('New Password')" class="font-bold text-slate-700 dark:text-slate-300" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white focus:border-sky-500 focus:ring-sky-500 rounded-xl" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2 text-rose-600 dark:text-rose-400" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" class="font-bold text-slate-700 dark:text-slate-300" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white focus:border-sky-500 focus:ring-sky-500 rounded-xl" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2 text-rose-600 dark:text-rose-400" />
        </div>

        <div class="flex items-center gap-4 pt-4 border-t border-slate-100 dark:border-slate-700">
            <x-primary-button class="bg-sky-600 hover:bg-sky-700 text-white rounded-xl py-2.5 px-6 font-bold transition shadow-sm">{{ __('Update Password') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="text-sm font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ __('Saved Successfully.') }}
                </p>
            @endif
        </div>
    </form>
</section>
