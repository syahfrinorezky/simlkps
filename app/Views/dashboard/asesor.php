<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-10 text-center space-y-6">
    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-orange-50 text-orange-600 mb-2">
        <i data-lucide="eye" class="w-8 h-8"></i>
    </div>
    <h2 class="text-2xl font-bold text-slate-800">Selamat Datang, Tim Asesor</h2>
    <p class="text-slate-500 max-w-2xl mx-auto">
        Sebagai asesor, Anda diberikan hak akses <i>View-Only</i> (Hanya Lihat). Anda dapat mengevaluasi dan menilai seluruh laporan kinerja program studi (LKPS) serta melihat bukti dokumen (SK) pendukung yang dilampirkan.
    </p>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8 max-w-4xl mx-auto">
        <div class="p-6 rounded-xl border border-slate-200 bg-slate-50 text-left flex items-start space-x-4">
            <div class="bg-orange-100 p-3 rounded-lg text-orange-600">
                <i data-lucide="file-search" class="w-6 h-6"></i>
            </div>
            <div>
                <h3 class="font-bold text-slate-700 mb-1">Evaluasi Laporan LKPS</h3>
                <p class="text-sm text-slate-500">Lihat rekapitulasi capaian prodi per periode pelaporan (TS).</p>
            </div>
        </div>
        <div class="p-6 rounded-xl border border-slate-200 bg-slate-50 text-left flex items-start space-x-4">
            <div class="bg-orange-100 p-3 rounded-lg text-orange-600">
                <i data-lucide="book-marked" class="w-6 h-6"></i>
            </div>
            <div>
                <h3 class="font-bold text-slate-700 mb-1">Tinjau Bukti Fisik</h3>
                <p class="text-sm text-slate-500">Unduh dokumen SK, sertifikat akreditasi, dan bukti pendukung Tridharma.</p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
