<x-app-layout>
    <div x-data="{ imageModalOpen: false, imageModalSrc: '', deleteModalOpen: false, deleteActionUrl: '' }">
        <header class="bg-white/80 dark:bg-slate-900/50 backdrop-blur-md border-b border-slate-200 dark:border-slate-700 h-16 hidden md:flex items-center justify-between px-6 shadow-sm sticky top-0 z-20">
            <div class="flex items-center">
                <span class="font-extrabold text-xl text-slate-800 dark:text-white tracking-tight">Catatan & Laporan Masuk</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400">Login as: <span class="text-sky-700 dark:text-sky-400 font-black">{{ Auth::user()->name }}</span></span>
            </div>
        </header>

        <div class="p-4 md:p-8 space-y-6">
            <div class="md:hidden flex items-center justify-between bg-white dark:bg-slate-800 p-4 rounded-2xl shadow-sm border border-slate-200 dark:border-slate-700">
                <span class="font-extrabold text-sm text-slate-800 dark:text-white tracking-tight">Catatan & Laporan</span>
            </div>

            <div class="flex justify-between items-center mb-2">
                <div>
                    <h2 class="text-xl font-black text-slate-800 dark:text-white">Daftar Keluhan & Perbaikan</h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">Pantau, tindak lanjuti, dan verifikasi setiap laporan kerusakan atau catatan dari staf.</p>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-400 p-4 rounded-xl font-bold text-sm shadow-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 xl:grid-cols-2 gap-6 mt-6">
                @forelse($notes as $note)
                    @php
                        $borderColor = 'border-slate-200 dark:border-slate-700';
                        $bgColor = 'bg-white dark:bg-slate-800';
                        $opacityClass = 'opacity-100';

                        if ($note->status === 'open') {
                            $borderColor = 'border-rose-300 dark:border-rose-700';
                            $bgColor = 'bg-rose-50/30 dark:bg-rose-900/10';
                        } elseif ($note->status === 'resolved') {
                            $borderColor = 'border-amber-400 dark:border-amber-600 shadow-md ring-4 ring-amber-50 dark:ring-amber-900/20';
                            $bgColor = 'bg-amber-50/40 dark:bg-amber-900/10';
                        } elseif ($note->status === 'verified') {
                            $borderColor = 'border-emerald-200 dark:border-emerald-800';
                            $bgColor = 'bg-emerald-50/20 dark:bg-emerald-900/5';
                            $opacityClass = 'opacity-75 hover:opacity-100';
                        }
                    @endphp

                    <div class="p-6 rounded-3xl border-2 flex flex-col justify-between {{ $bgColor }} {{ $borderColor }} {{ $opacityClass }}">
                        <div>
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-2xl flex items-center justify-center font-black shadow-inner border border-slate-200 dark:border-slate-600">
                                        {{ strtoupper(substr($note->user->name ?? 'S', 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="font-black text-slate-800 dark:text-white block text-sm">{{ $note->user->name ?? 'Staf Anonim' }}</span>
                                        <span class="text-[10px] text-slate-500 dark:text-slate-400 font-bold block uppercase tracking-wider">{{ $note->user->department ?? 'Departemen' }}</span>
                                    </div>
                                </div>
                                <div class="flex flex-col items-end gap-2">
                                    @if($note->status === 'open')
                                        <span class="bg-rose-100 dark:bg-rose-900/40 text-rose-700 dark:text-rose-400 px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider border border-rose-200 dark:border-rose-800 animate-pulse">Menunggu Tindakan</span>
                                    @elseif($note->status === 'resolved')
                                        <span class="bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400 px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider border border-amber-200 dark:border-amber-800">Menunggu ACC Admin</span>
                                    @elseif($note->status === 'verified')
                                        <span class="bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400 px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider border border-emerald-200 dark:border-emerald-800">Tuntas & Selesai</span>
                                    @endif
                                    <span class="text-[10px] text-slate-400 font-bold">{{ $note->created_at->format('d M Y, H:i') }}</span>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h4 class="font-black text-lg text-slate-800 dark:text-white mb-2">{{ $note->title }}</h4>
                                <p class="text-sm text-slate-600 dark:text-slate-300 bg-white/60 dark:bg-slate-900/40 p-4 rounded-xl border border-slate-100 dark:border-slate-700/50 font-medium leading-relaxed">{{ $note->message }}</p>
                            </div>

                            @if($note->image && \Illuminate\Support\Facades\Storage::disk('public')->exists($note->image))
                                <div class="mb-5">
                                    <p class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">📸 Lampiran Keluhan:</p>
                                    <button type="button" @click.prevent="imageModalSrc = '{{ asset('storage/' . $note->image) }}'; imageModalOpen = true" class="w-32 h-32 focus:outline-none block rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 hover:opacity-80 cursor-zoom-in relative group">
                                        <img src="{{ asset('storage/' . $note->image) }}" class="w-full h-full object-cover">
                                        <div class="absolute inset-0 bg-slate-900/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                            <span class="text-white text-2xl drop-shadow-md">🔍</span>
                                        </div>
                                    </button>
                                </div>
                            @endif
                        </div>

                        @if($note->status === 'resolved' || $note->status === 'verified')
                            @if($note->category === 'Kerusakan')
                            <div class="mt-4 pt-5 border-t-2 border-dashed {{ $note->status === 'verified' ? 'border-emerald-200 dark:border-emerald-800/50' : 'border-amber-200 dark:border-amber-800/50' }}">
                                <h5 class="text-xs font-black uppercase tracking-wider mb-3 flex items-center gap-2 {{ $note->status === 'verified' ? 'text-emerald-700 dark:text-emerald-400' : 'text-amber-700 dark:text-amber-400' }}">
                                    <span class="text-lg">🔧</span> Laporan Perbaikan Teknisi
                                </h5>

                                <div class="bg-white/60 dark:bg-slate-900/40 p-4 rounded-xl border border-slate-100 dark:border-slate-700/50">
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <span class="block text-[10px] font-bold text-slate-400 uppercase">Diperbaiki Oleh:</span>
                                            <div class="flex items-center gap-1.5 flex-wrap mt-0.5">
                                                <span class="block font-bold text-sm text-slate-800 dark:text-white">{{ $note->resolver->name ?? 'Teknisi' }}</span>

                                                @php
                                                    $helperIds = is_string($note->helpers) ? json_decode($note->helpers, true) : $note->helpers;
                                                    $helperNames = [];
                                                    if (!empty($helperIds) && is_array($helperIds)) {
                                                        $helperNames = \App\Models\User::whereIn('id', $helperIds)->pluck('name')->toArray();
                                                    }
                                                @endphp

                                                @if(!empty($helperNames))
                                                    <span class="text-[10px] font-bold text-slate-600 dark:text-slate-300 bg-slate-200/70 dark:bg-slate-700/70 px-2 py-0.5 rounded-md border border-slate-300 dark:border-slate-600 whitespace-nowrap">
                                                        + {{ implode(', ', $helperNames) }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="text-right flex-shrink-0">
                                            <span class="block text-[10px] font-bold text-slate-400 uppercase">Waktu Selesai:</span>
                                            <span class="block font-bold text-xs text-slate-600 dark:text-slate-300 mt-0.5">{{ $note->resolved_at ? $note->resolved_at->format('d M Y, H:i') : '-' }}</span>
                                        </div>
                                    </div>

                                    @if($note->resolved_note)
                                        <p class="text-xs text-slate-600 dark:text-slate-400 font-medium italic border-l-4 border-amber-400 pl-3 py-1 mb-3">"{{ $note->resolved_note }}"</p>
                                    @endif

                                    @php
                                        $resolvedImages = is_string($note->resolved_image) ? json_decode($note->resolved_image, true) : $note->resolved_image;
                                        if(!is_array($resolvedImages)) $resolvedImages = [$note->resolved_image];

                                        $validResolvedImages = array_filter($resolvedImages, function($img) {
                                            return !empty($img) && \Illuminate\Support\Facades\Storage::disk('public')->exists($img);
                                        });
                                    @endphp

                                    @if(count($validResolvedImages) > 0)
                                        <div class="mt-3">
                                            <p class="text-[10px] font-bold text-slate-400 uppercase mb-2">Foto Hasil Perbaikan:</p>
                                            <div class="flex gap-2 overflow-x-auto pb-2">
                                                @foreach($validResolvedImages as $resImg)
                                                    <button type="button" @click.prevent="imageModalSrc = '{{ asset('storage/' . $resImg) }}'; imageModalOpen = true" class="w-32 h-32 flex-shrink-0 focus:outline-none rounded-xl overflow-hidden border border-slate-200 dark:border-slate-700 bg-slate-950 hover:opacity-90 cursor-zoom-in relative group flex items-center justify-center p-2">
                                                        <img src="{{ asset('storage/' . $resImg) }}" class="max-h-full w-auto object-contain rounded-lg">
                                                        <div class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col items-center justify-center text-white p-2">
                                                            <span class="text-2xl mb-1 drop-shadow-md">🔍</span>
                                                            <span class="text-[10px] font-bold text-center">Lihat Bukti</span>
                                                        </div>
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @endif

                            @if($note->status === 'resolved' && $note->category === 'Kerusakan')
                                <form action="{{ route('admin.notes.verify', $note->id) }}" method="POST" class="mt-4">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-black py-3.5 px-4 rounded-xl shadow-lg shadow-emerald-600/30 flex items-center justify-center gap-2">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Verifikasi & Tutup Laporan
                                    </button>
                                </form>
                            @elseif($note->status === 'verified')
                                <div class="mt-4 p-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800/50 rounded-xl flex items-center justify-between">
                                    <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-500 uppercase tracking-wider">Di-ACC Oleh:</span>
                                    <span class="text-xs font-black text-emerald-700 dark:text-emerald-400">{{ $note->verifier->name ?? 'Admin' }} ({{ $note->verified_at ? $note->verified_at->format('d/m/y H:i') : '' }})</span>
                                </div>
                            @endif
                        @endif

                        <div class="mt-5 pt-5 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-3">
                            @if($note->status === 'open' && $note->category !== 'Kerusakan')
                                <form action="{{ route('admin.notes.read', $note->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="bg-emerald-50 dark:bg-emerald-900/30 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 text-emerald-700 dark:text-emerald-400 font-bold py-2 px-4 text-xs rounded-xl border border-emerald-200 dark:border-emerald-800 shadow-sm">
                                        Tandai Selesai & Dibaca
                                    </button>
                                </form>
                            @endif

                            <button type="button" @click="deleteActionUrl = '{{ route('admin.notes.destroy', $note->id) }}'; deleteModalOpen = true" class="bg-rose-50 dark:bg-rose-900/20 hover:bg-rose-100 dark:hover:bg-rose-900/40 text-rose-600 dark:text-rose-400 font-bold py-2 px-4 text-xs rounded-xl border border-rose-200 dark:border-rose-800/50 shadow-sm">
                                Hapus Arsip
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="xl:col-span-2 bg-white dark:bg-slate-800 p-12 rounded-3xl border border-slate-200 dark:border-slate-700 text-center shadow-sm">
                        <span class="text-6xl block mb-4 opacity-50">✨</span>
                        <h3 class="font-black text-xl text-slate-800 dark:text-white mb-2">Tidak ada laporan masuk</h3>
                        <p class="text-slate-500 dark:text-slate-400 font-medium text-sm">Semua keluhan dan laporan perbaikan telah diselesaikan.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div x-cloak x-show="imageModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div x-show="imageModalOpen" class="absolute inset-0 bg-slate-900/90 backdrop-blur-sm" @click="imageModalOpen = false"></div>
            <div x-show="imageModalOpen" class="relative z-[110] max-w-5xl w-full flex flex-col items-center">
                <img @click="imageModalOpen = false" :src="imageModalSrc" class="max-h-[85vh] w-auto max-w-full rounded-2xl shadow-2xl border border-white/10 object-contain bg-black/50 cursor-zoom-out" title="Klik untuk menutup">
            </div>
        </div>

        <div x-cloak x-show="deleteModalOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
            <div x-show="deleteModalOpen" class="absolute inset-0 bg-slate-900/70 backdrop-blur-sm" @click="deleteModalOpen = false"></div>

            <div x-show="deleteModalOpen" class="bg-white dark:bg-slate-800 rounded-3xl shadow-2xl w-full max-w-sm p-6 relative z-[110] text-center border border-slate-100 dark:border-slate-700">
                <div class="w-16 h-16 rounded-2xl bg-rose-100 dark:bg-rose-900/40 text-rose-600 dark:text-rose-400 flex items-center justify-center text-3xl mx-auto mb-4 shadow-inner border border-rose-200 dark:border-rose-800">
                    🗑️
                </div>
                <h3 class="text-xl font-black text-slate-800 dark:text-white mb-2">Hapus Laporan?</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 font-medium mb-6">Laporan dan foto lampirannya akan dihapus permanen dari sistem.</p>

                <div class="flex gap-3">
                    <button type="button" @click="deleteModalOpen = false" class="flex-1 px-4 py-3 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-slate-200 font-bold rounded-2xl text-sm border border-slate-200 dark:border-slate-600">
                        Batal
                    </button>
                    <form :action="deleteActionUrl" method="POST" class="flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-3 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-2xl shadow-lg shadow-rose-600/30 text-sm">
                            Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
