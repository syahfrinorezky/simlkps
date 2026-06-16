<?= $this->extend('layouts/app') ?>

<?= $this->section('title') ?>Dashboard Prodi<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php
    $userName = session()->get('userName') ?? 'Kaprodi';
?>

<!-- Welcome Header -->
<div class="mb-6">
    <div class="bg-gradient-to-r from-blue-600 to-indigo-700 rounded-2xl p-6 sm:p-8 text-white shadow-lg relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 400 200" xmlns="http://www.w3.org/2000/svg">
                <circle cx="350" cy="50" r="120" fill="white"/>
                <circle cx="50" cy="180" r="80" fill="white"/>
            </svg>
        </div>
        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <p class="text-blue-200 text-sm font-medium mb-1">Selamat Datang 👋</p>
                <h1 class="text-2xl sm:text-3xl font-bold"><?= esc($userName) ?></h1>
                <p class="text-blue-100 text-sm mt-1">Program Studi &mdash; Sistem Informasi LKPS</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="bg-white/20 backdrop-blur-sm rounded-xl px-4 py-3 text-center">
                    <p class="text-xs text-blue-100 mb-0.5">Periode</p>
                    <p class="text-lg font-bold"><?= date('Y') ?></p>
                </div>
                <div class="w-14 h-14 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center">
                    <i data-lucide="building-2" class="w-7 h-7 text-white"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center">
                <i data-lucide="graduation-cap" class="w-5 h-5 text-blue-600"></i>
            </div>
            <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">Aktif</span>
        </div>
        <p class="text-2xl font-bold text-slate-800">—</p>
        <p class="text-xs text-slate-500 mt-0.5">Total Mahasiswa</p>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-lg bg-indigo-50 flex items-center justify-center">
                <i data-lucide="contact" class="w-5 h-5 text-indigo-600"></i>
            </div>
            <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">Aktif</span>
        </div>
        <p class="text-2xl font-bold text-slate-800">—</p>
        <p class="text-xs text-slate-500 mt-0.5">Dosen Tetap</p>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-lg bg-violet-50 flex items-center justify-center">
                <i data-lucide="handshake" class="w-5 h-5 text-violet-600"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-slate-800">—</p>
        <p class="text-xs text-slate-500 mt-0.5">Kerjasama Aktif</p>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-4 shadow-sm hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center">
                <i data-lucide="award" class="w-5 h-5 text-amber-600"></i>
            </div>
        </div>
        <p class="text-2xl font-bold text-slate-800">—</p>
        <p class="text-xs text-slate-500 mt-0.5">Lulusan Tahun Ini</p>
    </div>
</div>

<!-- Menu Grid by Module -->
<div class="mb-4">
    <h2 class="text-lg font-bold text-slate-800 mb-4">Modul Input Data LKPS</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

        <!-- Modul 1: Kerjasama Tridarma -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden hover:shadow-md transition-all group">
            <div class="h-2 bg-gradient-to-r from-violet-500 to-purple-600"></div>
            <div class="p-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-lg bg-violet-50 flex items-center justify-center shrink-0">
                        <i data-lucide="handshake" class="w-5 h-5 text-violet-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-medium">Modul 1</p>
                        <h3 class="font-bold text-slate-800 text-sm leading-tight">Kerjasama Tridarma</h3>
                    </div>
                </div>
                <ul class="space-y-2">
                    <li>
                        <a href="<?= base_url('cooperations/education') ?>" class="flex items-center gap-2 text-sm text-slate-600 hover:text-violet-600 transition-colors py-1 px-2 rounded-lg hover:bg-violet-50">
                            <span class="w-5 h-5 flex items-center justify-center rounded bg-violet-100 text-violet-600 text-xs font-bold shrink-0">1</span>
                            Kerjasama Pendidikan
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('cooperations/research') ?>" class="flex items-center gap-2 text-sm text-slate-600 hover:text-violet-600 transition-colors py-1 px-2 rounded-lg hover:bg-violet-50">
                            <span class="w-5 h-5 flex items-center justify-center rounded bg-violet-100 text-violet-600 text-xs font-bold shrink-0">2</span>
                            Kerjasama Penelitian
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('cooperations/community') ?>" class="flex items-center gap-2 text-sm text-slate-600 hover:text-violet-600 transition-colors py-1 px-2 rounded-lg hover:bg-violet-50">
                            <span class="w-5 h-5 flex items-center justify-center rounded bg-violet-100 text-violet-600 text-xs font-bold shrink-0">3</span>
                            Kerjasama Pengabdian Masyarakat
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Modul 2: Mahasiswa -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden hover:shadow-md transition-all group">
            <div class="h-2 bg-gradient-to-r from-blue-500 to-cyan-500"></div>
            <div class="p-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-lg bg-blue-50 flex items-center justify-center shrink-0">
                        <i data-lucide="graduation-cap" class="w-5 h-5 text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-medium">Modul 2</p>
                        <h3 class="font-bold text-slate-800 text-sm leading-tight">Mahasiswa</h3>
                    </div>
                </div>
                <ul class="space-y-2">
                    <li>
                        <a href="<?= base_url('students/selection') ?>" class="flex items-center gap-2 text-sm text-slate-600 hover:text-blue-600 transition-colors py-1 px-2 rounded-lg hover:bg-blue-50">
                            <span class="w-5 h-5 flex items-center justify-center rounded bg-blue-100 text-blue-600 text-xs font-bold shrink-0">a</span>
                            Seleksi Mahasiswa
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('students/foreign') ?>" class="flex items-center gap-2 text-sm text-slate-600 hover:text-blue-600 transition-colors py-1 px-2 rounded-lg hover:bg-blue-50">
                            <span class="w-5 h-5 flex items-center justify-center rounded bg-blue-100 text-blue-600 text-xs font-bold shrink-0">b</span>
                            Mahasiswa Asing
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Modul 5: Pembelajaran -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden hover:shadow-md transition-all group">
            <div class="h-2 bg-gradient-to-r from-emerald-500 to-teal-500"></div>
            <div class="p-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 flex items-center justify-center shrink-0">
                        <i data-lucide="book-open" class="w-5 h-5 text-emerald-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-medium">Modul 5</p>
                        <h3 class="font-bold text-slate-800 text-sm leading-tight">Pembelajaran</h3>
                    </div>
                </div>
                <ul class="space-y-2">
                    <li>
                        <a href="<?= base_url('courses/curriculum') ?>" class="flex items-center gap-2 text-sm text-slate-600 hover:text-emerald-600 transition-colors py-1 px-2 rounded-lg hover:bg-emerald-50">
                            <span class="w-5 h-5 flex items-center justify-center rounded bg-emerald-100 text-emerald-600 text-xs font-bold shrink-0">a</span>
                            Kurikulum Pembelajaran
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('courses/research-integration') ?>" class="flex items-center gap-2 text-sm text-slate-600 hover:text-emerald-600 transition-colors py-1 px-2 rounded-lg hover:bg-emerald-50">
                            <span class="w-5 h-5 flex items-center justify-center rounded bg-emerald-100 text-emerald-600 text-xs font-bold shrink-0">b</span>
                            Integrasi Penelitian
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('courses/excellence') ?>" class="flex items-center gap-2 text-sm text-slate-600 hover:text-emerald-600 transition-colors py-1 px-2 rounded-lg hover:bg-emerald-50">
                            <span class="w-5 h-5 flex items-center justify-center rounded bg-emerald-100 text-emerald-600 text-xs font-bold shrink-0">c</span>
                            Kepuasan Mahasiswa
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Modul 8: Kelulusan -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden hover:shadow-md transition-all group">
            <div class="h-2 bg-gradient-to-r from-amber-500 to-orange-500"></div>
            <div class="p-5">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-lg bg-amber-50 flex items-center justify-center shrink-0">
                        <i data-lucide="award" class="w-5 h-5 text-amber-600"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 font-medium">Modul 8</p>
                        <h3 class="font-bold text-slate-800 text-sm leading-tight">Kelulusan</h3>
                    </div>
                </div>
                <ul class="space-y-2">
                    <li>
                        <a href="<?= base_url('graduates/academic-index') ?>" class="flex items-center gap-2 text-sm text-slate-600 hover:text-amber-600 transition-colors py-1 px-2 rounded-lg hover:bg-amber-50">
                            <span class="w-5 h-5 flex items-center justify-center rounded bg-amber-100 text-amber-600 text-xs font-bold shrink-0">a</span>
                            IPK Kumulatif
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('graduates/waiting-time') ?>" class="flex items-center gap-2 text-sm text-slate-600 hover:text-amber-600 transition-colors py-1 px-2 rounded-lg hover:bg-amber-50">
                            <span class="w-5 h-5 flex items-center justify-center rounded bg-amber-100 text-amber-600 text-xs font-bold shrink-0">d</span>
                            Tracer Study
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('graduates/user-satisfaction') ?>" class="flex items-center gap-2 text-sm text-slate-600 hover:text-amber-600 transition-colors py-1 px-2 rounded-lg hover:bg-amber-50">
                            <span class="w-5 h-5 flex items-center justify-center rounded bg-amber-100 text-amber-600 text-xs font-bold shrink-0">e</span>
                            Kepuasan Pengguna
                        </a>
                    </li>
                    <li>
                        <a href="<?= base_url('graduates/publications/scientific') ?>" class="flex items-center gap-2 text-sm text-slate-600 hover:text-amber-600 transition-colors py-1 px-2 rounded-lg hover:bg-amber-50">
                            <span class="w-5 h-5 flex items-center justify-center rounded bg-amber-100 text-amber-600 text-xs font-bold shrink-0">f</span>
                            Publikasi Mahasiswa
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Info Banner -->
<div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-start gap-3">
    <div class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center shrink-0 mt-0.5">
        <i data-lucide="info" class="w-4 h-4 text-blue-600"></i>
    </div>
    <div>
        <p class="text-sm font-semibold text-blue-800 mb-0.5">Panduan Pengisian Data LKPS</p>
        <p class="text-xs text-blue-600">Lengkapi semua modul yang tersedia di sidebar untuk mempersiapkan dokumen LKPS secara lengkap. Data yang telah diinput dapat diverifikasi dan diajukan ke asesor.</p>
    </div>
</div>

<?= $this->endSection() ?>
