<?php /* Component: No Periods Banner — tampilkan ketika tabel reporting_periods kosong */ ?>
<div class="flex flex-col items-center justify-center py-20 text-center space-y-5">
    <div class="w-20 h-20 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center text-amber-500 shadow-sm">
        <i data-lucide="calendar-x" class="w-10 h-10"></i>
    </div>
    <div class="space-y-2">
        <h3 class="text-lg font-bold text-slate-800">Belum Ada Periode Pelaporan</h3>
        <p class="text-sm text-slate-500 max-w-sm mx-auto leading-relaxed">
            Sistem tidak dapat menampilkan data karena belum ada periode akademik yang terdaftar.
            Tambahkan periode terlebih dahulu melalui menu <strong>Manajemen → Periode</strong>.
        </p>
    </div>
    <a href="<?= base_url('periods') ?>" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary/90 text-white font-semibold rounded-xl text-sm transition-all shadow-sm">
        <i data-lucide="plus" class="w-4 h-4"></i>
        Tambah Periode Sekarang
    </a>
</div>
