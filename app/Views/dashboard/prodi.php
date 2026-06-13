<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-10 text-center space-y-6">
    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-blue-50 text-blue-600 mb-2">
        <i data-lucide="building-2" class="w-8 h-8"></i>
    </div>
    <h2 class="text-2xl font-bold text-slate-800">Selamat Datang di Dashboard Prodi</h2>
    <p class="text-slate-500 max-w-2xl mx-auto">
        Sebagai perwakilan Program Studi, tugas Anda adalah memverifikasi data yang diinput oleh dosen, serta mengelola data kependidikan seperti jumlah mahasiswa dan kelulusan untuk pengajuan LKPS.
    </p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
        <div class="p-6 rounded-xl border border-slate-200 bg-slate-50 text-left">
            <h3 class="font-bold text-slate-700 mb-2">Verifikasi Dosen</h3>
            <p class="text-sm text-slate-500">Tinjau dan setujui (approve/reject) input data Tridharma Dosen.</p>
        </div>
        <div class="p-6 rounded-xl border border-slate-200 bg-slate-50 text-left">
            <h3 class="font-bold text-slate-700 mb-2">Input Data Kemahasiswaan</h3>
            <p class="text-sm text-slate-500">Kelola angka pendaftar, kelulusan, dan IPK lulusan Program Studi.</p>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
