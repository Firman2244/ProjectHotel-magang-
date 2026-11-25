<x-auth-card title="Akun Ditemukan">
    <div class="mb-6 text-center">
        <h2 class="font-black text-lg text-slate-800 dark:text-white">Akun Ditemukan</h2>
        <p class="text-sm font-medium text-slate-500 dark:text-slate-400 mt-1">Pilih untuk lanjut login atau ganti password.</p>
    </div>

    {{-- Info akun --}}
    <div class="space-y-4 mb-6">
        <div>
            <span class="block text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Email</span>
            <div class="w-full border border-sky-200 dark:border-slate-600 bg-sky-50/50 dark:bg-slate-900/50 text-slate-800 dark:text-white font-bold rounded-xl py-3 px-4 break-all">{{ $user->email }}</div>
        </div>
        <div>
            <span class="block text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Password</span>
            <div class="w-full border border-sky-200 dark:border-slate-600 bg-sky-50/50 dark:bg-slate-900/50 text-slate-800 dark:text-white font-bold rounded-xl py-3 px-4 tracking-widest">••••••••••</div>
            <p class="text-[11px] font-medium text-slate-400 dark:text-slate-500 mt-1.5">Password tersimpan terenkripsi, jadi tidak bisa ditampilkan aslinya.</p>
        </div>
    </div>

    {{-- Pilihan aksi --}}
    <div id="action-buttons" class="grid grid-cols-2 gap-3 {{ $errors->any() ? 'hidden' : '' }}">
        <a href="{{ route('login', ['email' => $user->email]) }}" class="text-center bg-sky-600 hover:bg-sky-700 text-white font-black py-3.5 px-4 rounded-xl shadow-lg shadow-sky-600/30 transition duration-200 transform hover:-translate-y-0.5">
            LANJUT LOGIN
        </a>
        <button type="button" id="show-change-form" class="bg-white dark:bg-slate-900/50 border border-sky-300 dark:border-slate-600 text-sky-700 dark:text-sky-400 hover:bg-sky-50 dark:hover:bg-slate-900 font-black py-3.5 px-4 rounded-xl transition duration-200 transform hover:-translate-y-0.5">
            GANTI PASSWORD
        </button>
    </div>

    {{-- Form ganti password (verifikasi 2 langkah: password baru + ulangi password) --}}
    <form id="change-form" method="POST" action="{{ route('password.store') }}" class="space-y-5 {{ $errors->any() ? '' : 'hidden' }}">
        @csrf
        @method('PUT')

        <div>
            <label for="password" class="block text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Langkah 1 &mdash; Password Baru</label>
            <input id="password" type="password" name="password" required minlength="8" autocomplete="new-password" class="w-full border-sky-200 dark:border-slate-600 bg-sky-50/50 dark:bg-slate-900/50 text-slate-800 dark:text-white font-bold rounded-xl shadow-sm py-3 px-4 focus:border-sky-500 focus:ring-sky-500 transition-colors duration-300">
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-rose-600 dark:text-rose-400" />
        </div>

        <div>
            <label for="password_confirmation" class="block text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Langkah 2 &mdash; Ulangi Password Baru</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="w-full border-sky-200 dark:border-slate-600 bg-sky-50/50 dark:bg-slate-900/50 text-slate-800 dark:text-white font-bold rounded-xl shadow-sm py-3 px-4 focus:border-sky-500 focus:ring-sky-500 transition-colors duration-300">
            <p id="match-msg" class="mt-2 text-xs font-bold hidden"></p>
        </div>

        <button type="submit" id="submit-change" disabled class="w-full bg-sky-600 hover:bg-sky-700 text-white font-black py-3.5 px-4 rounded-xl shadow-lg shadow-sky-600/30 transition duration-200 transform hover:-translate-y-0.5 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none disabled:hover:bg-sky-600">
            GANTI PASSWORD
        </button>

        <button type="button" id="cancel-change" class="w-full text-sm font-bold text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors duration-300">
            Batal
        </button>
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const actions = document.getElementById('action-buttons');
            const form = document.getElementById('change-form');
            const pass = document.getElementById('password');
            const confirm = document.getElementById('password_confirmation');
            const btn = document.getElementById('submit-change');
            const msg = document.getElementById('match-msg');

            document.getElementById('show-change-form').addEventListener('click', () => {
                actions.classList.add('hidden');
                form.classList.remove('hidden');
                pass.focus();
            });

            document.getElementById('cancel-change').addEventListener('click', () => {
                form.classList.add('hidden');
                actions.classList.remove('hidden');
                pass.value = '';
                confirm.value = '';
                check();
            });

            function check() {
                const filled = pass.value.length > 0 && confirm.value.length > 0;
                const match = filled && pass.value === confirm.value;

                btn.disabled = !match;

                msg.classList.toggle('hidden', !filled);
                if (filled) {
                    msg.textContent = match ? '✓ Password cocok.' : '✕ Password tidak cocok.';
                    msg.className = 'mt-2 text-xs font-bold ' + (match ? 'text-sky-600 dark:text-sky-400' : 'text-rose-600 dark:text-rose-400');
                }
            }

            pass.addEventListener('input', check);
            confirm.addEventListener('input', check);
            check();
        });
    </script>
</x-auth-card>
