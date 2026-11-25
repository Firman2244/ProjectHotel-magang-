<x-auth-card title="Lupa Password">
    <div class="mb-6 text-center">
        <h2 class="font-black text-lg text-slate-800 dark:text-white">Lupa Password?</h2>
        <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mt-1">Masukkan email akun kamu untuk melanjutkan.</p>
    </div>

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
        @csrf
        <div>
            <label for="email" class="block text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="contoh: budi@wahyu.com" class="w-full border-sky-200 dark:border-slate-600 bg-sky-50/50 dark:bg-slate-900/50 text-slate-800 dark:text-white font-bold rounded-xl shadow-sm py-3 px-4 focus:border-sky-500 focus:ring-sky-500 transition-colors duration-300">
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-rose-600 dark:text-rose-400" />
        </div>

        <button type="submit" class="w-full bg-sky-600 hover:bg-sky-700 text-white font-black py-3.5 px-4 rounded-xl shadow-lg shadow-sky-600/30 transition duration-200 transform hover:-translate-y-0.5">
            CARI AKUN
        </button>

        <div class="text-center">
            <a href="{{ route('login') }}" class="text-sm font-bold text-sky-600 hover:text-sky-700 dark:text-sky-400 dark:hover:text-sky-300 transition-colors duration-300">&larr; Kembali ke Login</a>
        </div>
    </form>
</x-auth-card>
