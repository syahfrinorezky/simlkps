<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-10 text-center space-y-6">
    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-emerald-50 text-emerald-600 mb-2">
        <i data-lucide="book-copy" class="w-8 h-8"></i>
    </div>
    <h2 class="text-2xl font-bold text-slate-800">Selamat Datang di Dashboard Dosen</h2>
    <p class="text-slate-500 max-w-2xl mx-auto">
        Di sini Anda dapat mengelola rekam jejak pelaksanaan Tridharma Perguruan Tinggi, mulai dari beban pengajaran, keanggotaan penelitian, aktivitas pengabdian, hingga hasil publikasi dan HKI.
    </p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
        <div class="p-6 rounded-xl border border-slate-200 bg-slate-50 text-left">
            <h3 class="font-bold text-slate-700 mb-2">Penelitian</h3>
            <p class="text-sm text-slate-500">Input data proyek penelitian dan jumlah dana yang dikelola.</p>
        </div>
        <div class="p-6 rounded-xl border border-slate-200 bg-slate-50 text-left">
            <h3 class="font-bold text-slate-700 mb-2">Pengabdian</h3>
            <p class="text-sm text-slate-500">Catat kegiatan pengabdian masyarakat (PkM) dan output pendamping.</p>
        </div>
        <div class="p-6 rounded-xl border border-slate-200 bg-slate-50 text-left">
            <h3 class="font-bold text-slate-700 mb-2">Publikasi & HKI</h3>
            <p class="text-sm text-slate-500">Laporkan artikel jurnal, paten, dan luaran produk komersial lainnya.</p>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
