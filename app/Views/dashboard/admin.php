<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-10 text-center space-y-6">
    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-primary/10 text-primary mb-2">
        <i data-lucide="shield-check" class="w-8 h-8"></i>
    </div>
    <h2 class="text-2xl font-bold text-slate-800">Selamat Datang di Dashboard Admin</h2>
    <p class="text-slate-500 max-w-2xl mx-auto">
        Anda memiliki hak akses penuh ke seluruh modul sistem informasi LKPS. Gunakan menu di sidebar untuk mengelola master data pengguna, sinkronisasi data borang prodi, dosen, mahasiswa, hingga fasilitas pendanaan.
    </p>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
        <div class="p-6 rounded-xl border border-slate-200 bg-slate-50 text-left">
            <h3 class="font-bold text-slate-700 mb-2">Manajemen User</h3>
            <p class="text-sm text-slate-500">Kelola role, dosen, dan prodi di seluruh institusi.</p>
        </div>
        <div class="p-6 rounded-xl border border-slate-200 bg-slate-50 text-left">
            <h3 class="font-bold text-slate-700 mb-2">Pengawasan Data</h3>
            <p class="text-sm text-slate-500">Pantau seluruh input borang yang masuk secara real-time.</p>
        </div>
        <div class="p-6 rounded-xl border border-slate-200 bg-slate-50 text-left">
            <h3 class="font-bold text-slate-700 mb-2">Laporan LKPS</h3>
            <p class="text-sm text-slate-500">Akses dokumen LKPS yang siap untuk diunduh dan dievaluasi.</p>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
