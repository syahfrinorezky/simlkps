<!-- Toast Notification -->
<div
    x-data="{
        toasts: [],
        add(toast) {
            const id = Date.now();
            this.toasts.push({ ...toast, id, visible: true });
            setTimeout(() => this.remove(id), 4000);
        },
        remove(id) {
            const t = this.toasts.find(t => t.id === id);
            if (t) t.visible = false;
            setTimeout(() => this.toasts = this.toasts.filter(t => t.id !== id), 400);
        }
    }"
    @show-toast.window="add($event.detail)"
    class="fixed bottom-6 right-6 z-[9999] flex flex-col gap-3 pointer-events-none"
    aria-live="polite"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-show="toast.visible"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 scale-95"
            :class="{
                'bg-white border-emerald-200 text-emerald-800': toast.type === 'success',
                'bg-white border-red-200 text-red-700': toast.type === 'error',
                'bg-white border-amber-200 text-amber-700': toast.type === 'warning'
            }"
            class="pointer-events-auto flex items-center gap-3 min-w-[260px] max-w-xs px-4 py-3.5 rounded-2xl border shadow-lg shadow-slate-200/60 backdrop-blur-sm"
        >
            <!-- ikon per tipe toast -->
            <span :class="{
                'text-emerald-500': toast.type === 'success',
                'text-red-500': toast.type === 'error',
                'text-amber-500': toast.type === 'warning'
            }">
                <template x-if="toast.type === 'success'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </template>
                <template x-if="toast.type === 'error'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </template>
                <template x-if="toast.type === 'warning'">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                </template>
            </span>
            <p class="text-sm font-medium leading-snug flex-1" x-text="toast.message"></p>
            <!-- tombol tutup -->
            <button @click="remove(toast.id)" class="text-slate-400 hover:text-slate-600 transition-colors shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </template>
</div>
