<!-- Modal Hapus -->
<div 
    x-data="{ isOpen: false, url: '', title: 'Hapus Data', message: '' }"
    x-show="isOpen"
    @open-delete-modal.window="isOpen = true; url = $event.detail.url; title = $event.detail.title || 'Hapus Data'; message = $event.detail.message || ''"
    @keydown.escape.window="isOpen = false"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 min-h-screen"
    role="dialog"
    aria-modal="true"
    x-cloak
>
    <!-- Backdrop -->
    <div 
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="isOpen = false" 
        class="fixed inset-0 bg-slate-900/50 backdrop-blur-md"
    ></div>

    <!-- Konten -->
    <div 
        x-show="isOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-4"
        class="relative w-full max-w-md bg-white rounded-3xl overflow-hidden shadow-2xl border border-slate-100/80 z-10"
    >
        <!-- Header & Ikon -->
        <div class="p-8 text-center">
            <div class="mx-auto w-16 h-16 rounded-2xl bg-red-50 border border-red-100/50 text-red-500 flex items-center justify-center mb-5 shadow-sm">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            
            <h3 class="text-xl font-bold text-slate-900 tracking-tight" x-text="title">Hapus Data</h3>
            <p class="text-sm text-slate-500 mt-2.5 leading-relaxed" x-text="message">Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.</p>
        </div>
        
        <!-- Tombol Aksi -->
        <div class="px-8 pb-8 flex items-center justify-center gap-3">
            <button 
                type="button" 
                @click="isOpen = false" 
                class="flex-1 py-3 px-5 bg-slate-50 border border-slate-200/60 hover:bg-slate-100/80 hover:border-slate-300/80 text-slate-700 text-sm font-semibold rounded-2xl transition-all cursor-pointer text-center"
            >
                Batal
            </button>
            <button 
                type="button" 
                @click="window.location.href = url" 
                class="flex-1 py-3 px-5 bg-red-600 hover:bg-red-700 active:scale-[0.98] text-white text-sm font-semibold rounded-2xl shadow-lg shadow-red-500/20 hover:shadow-red-500/30 transition-all cursor-pointer text-center"
            >
                Ya, Hapus
            </button>
        </div>
    </div>
</div>
