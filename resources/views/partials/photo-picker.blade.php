<div class="photo-picker" data-uid="{{ $uid }}">
    <div class="flex gap-2">
        <button type="button" class="btn-camera flex-1 text-xs font-bold bg-slate-800 hover:bg-slate-900 text-white py-2 px-2 rounded-lg flex items-center justify-center gap-1 transition shadow-sm">
            📷 <span class="hidden sm:inline">Kamera</span>
        </button>
        <button type="button" class="btn-gallery flex-1 text-xs font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 py-2 px-2 rounded-lg flex items-center justify-center gap-1 transition border border-slate-200">
            🖼️ <span class="hidden sm:inline">Galeri</span>
        </button>
    </div>

    <input type="file"
           accept="image/*"
           name="{{ $name }}"
           class="photo-input hidden">

    <p class="photo-filename hidden mt-1 text-[10px] font-semibold text-slate-500 truncate"></p>
    <img class="photo-preview hidden mt-2 w-full h-20 object-cover rounded-lg border border-slate-200" alt="preview">
</div>
