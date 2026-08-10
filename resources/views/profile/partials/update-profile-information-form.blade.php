<section>
    <header>
        <h2 class="text-xl font-black text-slate-900 dark:text-white">
            {{ __('Profile Information') }}
        </h2>

        <p class="mt-1 text-sm font-medium text-slate-600 dark:text-slate-400">
            {{ __("Update your account's profile information, email address, and profile picture.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form id="delete-avatar-form" method="post" action="{{ route('profile.avatar.destroy') }}" class="hidden">
        @csrf
        @method('delete')
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="flex items-center gap-6 p-4 bg-slate-50 dark:bg-slate-900/50 rounded-xl border border-slate-200 dark:border-slate-700">
            <div class="shrink-0">
                <div id="avatar-preview-container" class="w-20 h-20 rounded-2xl overflow-hidden bg-sky-600 flex items-center justify-center text-white text-3xl font-black shadow-md border-2 border-white dark:border-slate-600">
                    @if(!empty($user->avatar))
                        <img id="avatar-img" src="{{ asset('storage/' . $user->avatar) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        <span id="avatar-initial" class="hidden">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                    @else
                        <span id="avatar-initial">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                        <img id="avatar-img" src="" alt="Preview" class="w-full h-full object-cover hidden">
                    @endif
                </div>
            </div>
            <div class="flex-1">
                <label class="block">
                    <span class="sr-only">Choose profile photo</span>
                    <input type="file" name="avatar" id="avatar-input" accept="image/*" class="block w-full text-sm text-slate-500 dark:text-slate-400
                        file:mr-4 file:py-2.5 file:px-5
                        file:rounded-xl file:border-0
                        file:text-xs file:font-bold
                        file:bg-sky-100 file:text-sky-700
                        dark:file:bg-sky-900/30 dark:file:text-sky-400
                        hover:file:bg-sky-200 dark:hover:file:bg-sky-900/50 cursor-pointer transition
                    "/>
                </label>
                @if(!empty($user->avatar))
                    <div class="mt-3">
                        <button type="submit" form="delete-avatar-form" onclick="return confirm('Yakin ingin menghapus foto profil?')" class="text-xs font-bold text-rose-600 hover:text-rose-700 dark:text-rose-500 dark:hover:text-rose-400 transition bg-rose-50 dark:bg-rose-900/30 px-3 py-1.5 rounded-xl border border-rose-200 dark:border-rose-800 inline-flex items-center gap-1">
                            🗑️ Hapus Foto Saat Ini
                        </button>
                    </div>
                @endif
            </div>
        </div>
        @error('avatar')
            <p class="text-sm font-bold text-rose-600 dark:text-rose-400 mt-2">{{ $message }}</p>
        @enderror

        <div>
            <x-input-label for="name" :value="__('Name')" class="font-bold text-slate-700 dark:text-slate-300" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white focus:border-sky-500 focus:ring-sky-500 rounded-xl" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2 text-rose-600 dark:text-rose-400" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Email')" class="font-bold text-slate-700 dark:text-slate-300" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full bg-white dark:bg-slate-900 border-slate-300 dark:border-slate-600 text-slate-900 dark:text-white focus:border-sky-500 focus:ring-sky-500 rounded-xl" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2 text-rose-600 dark:text-rose-400" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-3 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl">
                    <p class="text-sm font-medium text-amber-800 dark:text-amber-400">
                        {{ __('Your email address is unverified.') }}
                        <button form="send-verification" class="underline font-bold text-amber-600 dark:text-amber-300 hover:text-amber-900 dark:hover:text-amber-100 rounded-md focus:outline-none">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>
                    @if (session('status') === 'verification-link-email-sent')
                        <p class="mt-2 font-bold text-xs text-emerald-600 dark:text-emerald-400">
                            {{ __('A new verification link has been generated and sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-4 border-t border-slate-100 dark:border-slate-700">
            <x-primary-button class="bg-sky-600 hover:bg-sky-700 text-white rounded-xl py-2.5 px-6 font-bold transition shadow-sm">{{ __('Save Changes') }}</x-primary-button>

            @if (session('status') === 'profile-updated' || session('status') === 'avatar-deleted')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" class="text-sm font-bold text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    {{ __('Saved Successfully.') }}
                </p>
            @endif
        </div>
    </form>
</section>

<script>
    document.getElementById('avatar-input').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('avatar-img');
                const initial = document.getElementById('avatar-initial');
                if (img) {
                    img.src = e.target.result;
                    img.classList.remove('hidden');
                }
                if (initial) {
                    initial.classList.add('hidden');
                }
            }
            reader.readAsDataURL(file);
        }
    });
</script>
