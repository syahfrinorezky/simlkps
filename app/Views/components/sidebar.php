<?php
    $role = session()->get('userRole') ?? 'guest';
    $currentUri = uri_string();
    $currentTab = request()->getVar('tab') ?? 'admission';

    // Function to check if a menu is active
    $isActive = function($path, $tab = null) use ($currentUri, $currentTab) {
        if ($path === 'students' && $tab !== null) {
            return ($currentUri == 'students' && $currentTab === $tab)
                ? 'bg-primary text-white shadow-md'
                : 'text-slate-500 hover:text-primary hover:bg-primary/10';
        }
        return ($currentUri == $path || strpos($currentUri, $path . '/') === 0)
            ? 'bg-primary text-white shadow-md'
            : 'text-slate-500 hover:text-primary hover:bg-primary/10';
    };

    $iconColor = function($path) use ($currentUri) {
        return ($currentUri == $path || strpos($currentUri, $path . '/') === 0) ? 'text-white' : 'text-slate-400 group-hover:text-primary';
    };

    $isGroupActive = function($paths) use ($currentUri) {
        foreach ($paths as $path) {
            if ($currentUri == $path || strpos($currentUri, $path . '/') === 0) {
                return true;
            }
        }
        return false;
    };
?>

<!-- Sidebar backdrop (mobile only) -->
<div
    class="fixed inset-0 bg-slate-900 bg-opacity-30 z-40 lg:hidden lg:z-auto transition-opacity duration-200"
    :class="sidebarOpen ? 'opacity-100' : 'opacity-0 pointer-events-none'"
    aria-hidden="true"
    x-cloak
></div>

<!-- Sidebar -->
<div
    id="sidebar"
    class="flex flex-col absolute z-40 left-0 top-0 lg:static lg:left-auto lg:top-auto lg:translate-x-0 h-screen no-scrollbar shrink-0 bg-white border-r border-slate-200 transition-all duration-300 ease-in-out"
    :class="[
        sidebarOpen ? 'translate-x-0 w-72' : '-translate-x-72 w-72',
        'lg:translate-x-0',
        sidebarCollapsed ? 'lg:w-20' : 'lg:w-72'
    ]"
    @keydown.escape.window="sidebarOpen = false"
>
    <!-- Sidebar header -->
    <div class="shrink-0 flex justify-between items-center pr-3 sm:px-2 py-4 border-b border-slate-200 px-4">
        <!-- Logo -->
        <a class="flex items-center overflow-hidden min-w-0" href="<?= base_url('/') ?>">
            <span class="text-2xl font-bold text-primary tracking-tight whitespace-nowrap transition-all duration-300 origin-left" :class="sidebarCollapsed ? 'lg:scale-x-0 lg:opacity-0 lg:w-0 lg:overflow-hidden' : 'scale-x-100 opacity-100'">LKPS</span>
        </a>

        <!-- Close button (mobile) -->
        <button class="lg:hidden text-slate-500 hover:text-slate-400" @click.stop="sidebarOpen = !sidebarOpen" aria-controls="sidebar" :aria-expanded="sidebarOpen">
            <span class="sr-only">Close sidebar</span>
            <i data-lucide="arrow-left" class="w-6 h-6"></i>
        </button>

        <!-- Collapse button (desktop) -->
        <button class="hidden lg:flex items-center justify-center w-8 h-8 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors" @click="sidebarCollapsed = !sidebarCollapsed">
            <i data-lucide="panel-left-close" class="w-5 h-5 transition-transform duration-300" :class="sidebarCollapsed ? 'rotate-180' : ''"></i>
        </button>
    </div>

    <!-- Scrollable Body -->
    <div class="flex-1 overflow-y-auto no-scrollbar">
        <!-- Search input -->
        <div class="px-4 py-4 overflow-hidden shrink-0" :class="sidebarCollapsed ? 'lg:hidden' : ''">
            <div class="relative">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400"></i>
                <input type="text" class="w-full bg-slate-50 border border-slate-200 rounded-lg pl-9 pr-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary focus:border-primary transition-all" placeholder="Search">
            </div>
        </div>

        <!-- Links -->
        <ul class="pt-3 pb-6 space-y-1" :class="sidebarCollapsed ? 'lg:px-2' : 'px-4'">

            <!-- Dashboard (All Roles) -->
        <li>
            <a href="<?= base_url('/') ?>" class="group flex py-2.5 px-3 rounded-lg font-medium transition-colors <?= $isActive('') ?> <?= $isActive('dashboard') ?>">
                <i data-lucide="layout-dashboard" class="w-5 h-5 shrink-0 <?= $iconColor('') ?>"></i>
                <span class="ml-3 whitespace-nowrap overflow-hidden transition-all duration-300" :class="sidebarCollapsed ? 'lg:opacity-0 lg:w-0 lg:ml-0' : 'opacity-100'">Dashboard</span>
            </a>
        </li>

        <!-- ==================== MANAJEMEN ==================== -->
        <?php
            $manajemenPaths = ['users', 'periods'];
            $manajemenActive = $isGroupActive($manajemenPaths);
        ?>
        <?php if ($role === 'admin'): ?>
        <li x-data="{ open: <?= $manajemenActive ? 'true' : 'false' ?> }">
            <button @click="open = !open"
                class="group w-full flex items-center justify-between py-2.5 px-3 rounded-lg font-medium transition-colors <?= $manajemenActive ? 'text-primary' : 'text-slate-500 hover:text-primary hover:bg-primary/10' ?>">
                <div class="flex items-center">
                    <i data-lucide="settings" class="w-5 h-5 shrink-0 <?= $manajemenActive ? 'text-primary' : 'text-slate-400 group-hover:text-primary' ?>"></i>
                    <span class="ml-3 whitespace-nowrap overflow-hidden transition-all duration-300" :class="sidebarCollapsed ? 'lg:opacity-0 lg:w-0 lg:ml-0' : 'opacity-100'">Manajemen</span>
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 shrink-0 transition-transform duration-200 overflow-hidden" :class="[open ? 'rotate-180' : '', sidebarCollapsed ? 'lg:hidden' : '']"></i>
            </button>
            <div x-show="open && !sidebarCollapsed" x-cloak
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-1"
                class="ml-4 mt-1 space-y-0.5 border-l-2 border-slate-200 pl-3">
                <a href="<?= base_url('users') ?>"
                    class="group flex items-center py-2 px-2 rounded-lg text-sm font-medium transition-colors <?= $isActive('users') ?>">
                    User
                </a>
                <a href="<?= base_url('periods') ?>"
                    class="group flex items-center py-2 px-2 rounded-lg text-sm font-medium transition-colors <?= $isActive('periods') ?>">
                    Periode
                </a>
            </div>
        </li>
        <?php endif; ?>

        <!-- ==================== KERJASAMA TRIDARMA ==================== -->
        <?php
            $kerjasamaPaths = ['cooperations/education', 'cooperations/research', 'cooperations/community'];
            $kerjasamaActive = $isGroupActive($kerjasamaPaths);
        ?>
        <?php if (in_array($role, ['admin', 'prodi'])): ?>
        <li x-data="{ open: <?= $kerjasamaActive ? 'true' : 'false' ?> }">
            <button @click="open = !open"
                class="group w-full flex items-center justify-between py-2.5 px-3 rounded-lg font-medium transition-colors <?= $kerjasamaActive ? 'text-primary' : 'text-slate-500 hover:text-primary hover:bg-primary/10' ?>">
                <div class="flex items-center">
                    <i data-lucide="handshake" class="w-5 h-5 shrink-0 <?= $kerjasamaActive ? 'text-primary' : 'text-slate-400 group-hover:text-primary' ?>"></i>
                    <span class="ml-3 whitespace-nowrap overflow-hidden transition-all duration-300" :class="sidebarCollapsed ? 'lg:opacity-0 lg:w-0 lg:ml-0' : 'opacity-100'">Kerjasama</span>
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 shrink-0 transition-transform duration-200 overflow-hidden" :class="[open ? 'rotate-180' : '', sidebarCollapsed ? 'lg:hidden' : '']"></i>
            </button>
            <div x-show="open && !sidebarCollapsed" x-cloak
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-1"
                class="ml-4 mt-1 space-y-0.5 border-l-2 border-slate-200 pl-3">
                <a href="<?= base_url('cooperations/education') ?>"
                    class="group flex items-center py-2 px-2 rounded-lg text-sm font-medium transition-colors <?= $isActive('cooperations/education') ?>">
                    Kerjasama Pendidikan
                </a>
                <a href="<?= base_url('cooperations/research') ?>"
                    class="group flex items-center py-2 px-2 rounded-lg text-sm font-medium transition-colors <?= $isActive('cooperations/research') ?>">
                    Kerjasama Penelitian
                </a>
                <a href="<?= base_url('cooperations/community') ?>"
                    class="group flex items-center py-2 px-2 rounded-lg text-sm font-medium transition-colors <?= $isActive('cooperations/community') ?>">
                    Kerjasama Pengabdian Masyarakat
                </a>
            </div>
        </li>
        <?php endif; ?>

        <!-- ==================== MAHASISWA ==================== -->
        <?php
            $mahasiswaPaths = ['students'];
            $mahasiswaActive = $isGroupActive($mahasiswaPaths);
        ?>
        <?php if (in_array($role, ['admin', 'prodi'])): ?>
        <li x-data="{ open: <?= $mahasiswaActive ? 'true' : 'false' ?> }">
            <button @click="open = !open"
                class="group w-full flex items-center justify-between py-2.5 px-3 rounded-lg font-medium transition-colors <?= $mahasiswaActive ? 'text-primary' : 'text-slate-500 hover:text-primary hover:bg-primary/10' ?>">
                <div class="flex items-center">
                    <i data-lucide="graduation-cap" class="w-5 h-5 shrink-0 <?= $mahasiswaActive ? 'text-primary' : 'text-slate-400 group-hover:text-primary' ?>"></i>
                    <span class="ml-3 whitespace-nowrap overflow-hidden transition-all duration-300" :class="sidebarCollapsed ? 'lg:opacity-0 lg:w-0 lg:ml-0' : 'opacity-100'">Mahasiswa</span>
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 shrink-0 transition-transform duration-200 overflow-hidden" :class="[open ? 'rotate-180' : '', sidebarCollapsed ? 'lg:hidden' : '']"></i>
            </button>
            <div x-show="open && !sidebarCollapsed" x-cloak
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="ml-4 mt-1 space-y-0.5 border-l-2 border-slate-200 pl-3">
                <a href="<?= base_url('students?tab=admission') ?>"
                    class="group flex items-center py-2 px-2 rounded-lg text-sm font-medium transition-colors <?= $isActive('students', 'admission') ?>">
                    Seleksi Mahasiswa
                </a>
                <a href="<?= base_url('students?tab=foreign') ?>"
                    class="group flex items-center py-2 px-2 rounded-lg text-sm font-medium transition-colors <?= $isActive('students', 'foreign') ?>">
                    Mahasiswa Asing
                </a>
            </div>
        </li>
        <?php endif; ?>

        <!-- ==================== DOSEN ==================== -->
        <?php
            $dosenPaths = ['lecturers/permanent', 'lecturers/supervisor', 'lecturers/non-permanent', 'lecturers/industry',
                           'lecturers/workload', 'lecturers/recognition', 'lecturers/research-performance',
                           'lecturers/community-service', 'lecturers/publications/scientific', 'lecturers/publications/creative-works',
                           'lecturers/hki/industry-products', 'lecturers/outputs'];
            $dosenActive = $isGroupActive($dosenPaths);
        ?>
        <?php if (in_array($role, ['admin', 'prodi', 'dosen'])): ?>
        <li x-data="{ open: <?= $dosenActive ? 'true' : 'false' ?> }">
            <button @click="open = !open"
                class="group w-full flex items-center justify-between py-2.5 px-3 rounded-lg font-medium transition-colors <?= $dosenActive ? 'text-primary' : 'text-slate-500 hover:text-primary hover:bg-primary/10' ?>">
                <div class="flex items-center">
                    <i data-lucide="contact" class="w-5 h-5 shrink-0 <?= $dosenActive ? 'text-primary' : 'text-slate-400 group-hover:text-primary' ?>"></i>
                    <span class="ml-3 whitespace-nowrap overflow-hidden transition-all duration-300" :class="sidebarCollapsed ? 'lg:opacity-0 lg:w-0 lg:ml-0' : 'opacity-100'">Dosen</span>
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 shrink-0 transition-transform duration-200 overflow-hidden" :class="[open ? 'rotate-180' : '', sidebarCollapsed ? 'lg:hidden' : '']"></i>
            </button>
            <div x-show="open && !sidebarCollapsed" x-cloak
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="ml-4 mt-1 space-y-0.5 border-l-2 border-slate-200 pl-3">

                <!-- Sub-heading Profil -->
                <div class="px-2 pt-2 pb-0.5">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Profil</span>
                </div>
                <a href="<?= base_url('lecturers/permanent') ?>"
                    class="group flex items-center py-2 px-2 rounded-lg text-sm font-medium transition-colors <?= $isActive('lecturers/permanent') ?>">
                    Dosen Tetap
                </a>
                <a href="<?= base_url('lecturers/supervisor') ?>"
                    class="group flex items-center py-2 px-2 rounded-lg text-sm font-medium transition-colors <?= $isActive('lecturers/supervisor') ?>">
                    Pembimbing Tugas Akhir
                </a>
                <a href="<?= base_url('lecturers/non-permanent') ?>"
                    class="group flex items-center py-2 px-2 rounded-lg text-sm font-medium transition-colors <?= $isActive('lecturers/non-permanent') ?>">
                    Dosen Tidak Tetap
                </a>
                <a href="<?= base_url('lecturers/industry') ?>"
                    class="group flex items-center py-2 px-2 rounded-lg text-sm font-medium transition-colors <?= $isActive('lecturers/industry') ?>">
                    Dosen Industri
                </a>

                <!-- Sub-heading Beban Kerja -->
                <div class="px-2 pt-2 pb-0.5">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Beban Kerja</span>
                </div>
                <a href="<?= base_url('lecturers/workload') ?>"
                    class="group flex items-center py-2 px-2 rounded-lg text-sm font-medium transition-colors <?= $isActive('lecturers/workload') ?>">
                    Ekivalen Waktu Mengajar Penuh
                </a>

                <!-- Sub-heading Rekognisi -->
                <div class="px-2 pt-2 pb-0.5">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Rekognisi</span>
                </div>
                <a href="<?= base_url('lecturers/recognition') ?>"
                    class="group flex items-center py-2 px-2 rounded-lg text-sm font-medium transition-colors <?= $isActive('lecturers/recognition') ?>">
                    Pengakuan Dosen
                </a>

                <!-- Sub-heading Kinerja TriDharma -->
                <div class="px-2 pt-2 pb-0.5">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Kinerja TriDharma</span>
                </div>
                <a href="<?= base_url('lecturers/research-performance') ?>"
                    class="group flex items-center py-2 px-2 rounded-lg text-sm font-medium transition-colors <?= $isActive('lecturers/research-performance') ?>">
                    Penelitian Dosen
                </a>
                <a href="<?= base_url('lecturers/community-service') ?>"
                    class="group flex items-center py-2 px-2 rounded-lg text-sm font-medium transition-colors <?= $isActive('lecturers/community-service') ?>">
                    Pengabdian Masyarakat Dosen
                </a>

                <!-- Sub-heading Publikasi -->
                <div class="px-2 pt-2 pb-0.5">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Publikasi</span>
                </div>
                <a href="<?= base_url('lecturers/publications/scientific') ?>"
                    class="group flex items-center py-2 px-2 rounded-lg text-sm font-medium transition-colors <?= $isActive('lecturers/publications/scientific') ?>">
                    Publikasi Ilmiah
                </a>
                <a href="<?= base_url('lecturers/publications/creative-works') ?>"
                    class="group flex items-center py-2 px-2 rounded-lg text-sm font-medium transition-colors <?= $isActive('lecturers/publications/creative-works') ?>">
                    Sitasi
                </a>

                <!-- Sub-heading Luaran -->
                <div class="px-2 pt-2 pb-0.5">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Luaran</span>
                </div>
                <a href="<?= base_url('lecturers/hki/industry-products') ?>"
                    class="group flex items-center py-2 px-2 rounded-lg text-sm font-medium transition-colors <?= $isActive('lecturers/hki/industry-products') ?>">
                    Produk/Jasa
                </a>
                <a href="<?= base_url('lecturers/outputs') ?>"
                    class="group flex items-center py-2 px-2 rounded-lg text-sm font-medium transition-colors <?= $isActive('lecturers/outputs') ?>">
                    Luaran Penelitian/PkM
                </a>
            </div>
        </li>
        <?php endif; ?>

        <!-- ==================== PENGGUNAAN DANA ==================== -->
        <?php if (in_array($role, ['admin', 'prodi', 'asesor'])): ?>
        <li>
            <a href="<?= base_url('funds') ?>" class="group flex py-2.5 px-3 rounded-lg font-medium transition-colors <?= $isActive('funds') ?>">
                <i data-lucide="wallet" class="w-5 h-5 shrink-0 <?= $iconColor('funds') ?>"></i>
                <span class="ml-3 whitespace-nowrap overflow-hidden transition-all duration-300" :class="sidebarCollapsed ? 'lg:opacity-0 lg:w-0 lg:ml-0' : 'opacity-100'">Penggunaan Dana</span>
            </a>
        </li>
        <?php endif; ?>

        <!-- ==================== PEMBELAJARAN ==================== -->
        <?php
            $pembelajaranPaths = ['courses/curriculum', 'courses/research-integration', 'courses/excellence'];
            $pembelajaranActive = $isGroupActive($pembelajaranPaths);
        ?>
        <?php if (in_array($role, ['admin', 'prodi', 'dosen', 'asesor'])): ?>
        <li x-data="{ open: <?= $pembelajaranActive ? 'true' : 'false' ?> }">
            <button @click="open = !open"
                class="group w-full flex items-center justify-between py-2.5 px-3 rounded-lg font-medium transition-colors <?= $pembelajaranActive ? 'text-primary' : 'text-slate-500 hover:text-primary hover:bg-primary/10' ?>">
                <div class="flex items-center">
                    <i data-lucide="book-open" class="w-5 h-5 shrink-0 <?= $pembelajaranActive ? 'text-primary' : 'text-slate-400 group-hover:text-primary' ?>"></i>
                    <span class="ml-3 whitespace-nowrap overflow-hidden transition-all duration-300" :class="sidebarCollapsed ? 'lg:opacity-0 lg:w-0 lg:ml-0' : 'opacity-100'">Pembelajaran</span>
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 shrink-0 transition-transform duration-200 overflow-hidden" :class="[open ? 'rotate-180' : '', sidebarCollapsed ? 'lg:hidden' : '']"></i>
            </button>
            <div x-show="open && !sidebarCollapsed" x-cloak
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="ml-4 mt-1 space-y-0.5 border-l-2 border-slate-200 pl-3">
                <a href="<?= base_url('courses/curriculum') ?>"
                    class="group flex items-center py-2 px-2 rounded-lg text-sm font-medium transition-colors <?= $isActive('courses/curriculum') ?>">
                    Kurikulum Pembelajaran
                </a>
                <a href="<?= base_url('courses/research-integration') ?>"
                    class="group flex items-center py-2 px-2 rounded-lg text-sm font-medium transition-colors <?= $isActive('courses/research-integration') ?>">
                    Integrasi Penelitian Pembelajaran
                </a>
                <a href="<?= base_url('courses/excellence') ?>"
                    class="group flex items-center py-2 px-2 rounded-lg text-sm font-medium transition-colors <?= $isActive('courses/excellence') ?>">
                    Kepuasan Mahasiswa
                </a>
            </div>
        </li>
        <?php endif; ?>

        <!-- ==================== PENELITIAN ==================== -->
        <?php
            $penelitianPaths = ['researches/collaboration', 'researches/references'];
            $penelitianActive = $isGroupActive($penelitianPaths);
        ?>
        <?php if (in_array($role, ['admin', 'prodi', 'dosen', 'asesor'])): ?>
        <li x-data="{ open: <?= $penelitianActive ? 'true' : 'false' ?> }">
            <button @click="open = !open"
                class="group w-full flex items-center justify-between py-2.5 px-3 rounded-lg font-medium transition-colors <?= $penelitianActive ? 'text-primary' : 'text-slate-500 hover:text-primary hover:bg-primary/10' ?>">
                <div class="flex items-center">
                    <i data-lucide="flask-conical" class="w-5 h-5 shrink-0 <?= $penelitianActive ? 'text-primary' : 'text-slate-400 group-hover:text-primary' ?>"></i>
                    <span class="ml-3 whitespace-nowrap overflow-hidden transition-all duration-300" :class="sidebarCollapsed ? 'lg:opacity-0 lg:w-0 lg:ml-0' : 'opacity-100'">Penelitian</span>
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 shrink-0 transition-transform duration-200 overflow-hidden" :class="[open ? 'rotate-180' : '', sidebarCollapsed ? 'lg:hidden' : '']"></i>
            </button>
            <div x-show="open && !sidebarCollapsed" x-cloak
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="ml-4 mt-1 space-y-0.5 border-l-2 border-slate-200 pl-3">
                <a href="<?= base_url('researches/collaboration') ?>"
                    class="group flex items-center py-2 px-2 rounded-lg text-sm font-medium transition-colors <?= $isActive('researches/collaboration') ?>">
                    Penelitian Kolaborasi Mahasiswa
                </a>
                <a href="<?= base_url('researches/references') ?>"
                    class="group flex items-center py-2 px-2 rounded-lg text-sm font-medium transition-colors <?= $isActive('researches/references') ?>">
                    Rujukan Penelitian
                </a>
            </div>
        </li>
        <?php endif; ?>

        <!-- ==================== PENGABDIAN MASYARAKAT ==================== -->
        <?php
            $pengabdianPaths = ['community-services/collaboration'];
            $pengabdianActive = $isGroupActive($pengabdianPaths);
        ?>
        <?php if (in_array($role, ['admin', 'prodi', 'dosen', 'asesor'])): ?>
        <li x-data="{ open: <?= $pengabdianActive ? 'true' : 'false' ?> }">
            <button @click="open = !open"
                class="group w-full flex items-center justify-between py-2.5 px-3 rounded-lg font-medium transition-colors <?= $pengabdianActive ? 'text-primary' : 'text-slate-500 hover:text-primary hover:bg-primary/10' ?>">
                <div class="flex items-center">
                    <i data-lucide="users-round" class="w-5 h-5 shrink-0 <?= $pengabdianActive ? 'text-primary' : 'text-slate-400 group-hover:text-primary' ?>"></i>
                    <span class="ml-3 whitespace-nowrap overflow-hidden transition-all duration-300" :class="sidebarCollapsed ? 'lg:opacity-0 lg:w-0 lg:ml-0' : 'opacity-100'">Pengabdian Masyarakat</span>
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 shrink-0 transition-transform duration-200 overflow-hidden" :class="[open ? 'rotate-180' : '', sidebarCollapsed ? 'lg:hidden' : '']"></i>
            </button>
            <div x-show="open && !sidebarCollapsed" x-cloak
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="ml-4 mt-1 space-y-0.5 border-l-2 border-slate-200 pl-3">
                <a href="<?= base_url('community-services/collaboration') ?>"
                    class="group flex items-center py-2 px-2 rounded-lg text-sm font-medium transition-colors <?= $isActive('community-services/collaboration') ?>">
                    Pengabdian Kolaborasi Mahasiswa
                </a>
            </div>
        </li>
        <?php endif; ?>

        <!-- ==================== KELULUSAN ==================== -->
        <?php
            $kelulusanPaths = [
                'graduates/academic-index', 'graduates/academic-achievements', 'graduates/non-academic',
                'graduates/study-period', 'graduates/waiting-time', 'graduates/job-match', 'graduates/workplace',
                'graduates/user-satisfaction', 'graduates/publications/scientific', 'graduates/publications/presentations',
                'graduates/publications/creative-works', 'graduates/hki/industry-products', 'graduates/hki/patents',
                'graduates/hki/copyright', 'graduates/hki/technology', 'graduates/hki/books'
            ];
            $kelulusanActive = $isGroupActive($kelulusanPaths);
        ?>
        <?php if (in_array($role, ['admin', 'prodi'])): ?>
        <li x-data="{ open: <?= $kelulusanActive ? 'true' : 'false' ?> }">
            <button @click="open = !open"
                class="group w-full flex items-center justify-between py-2.5 px-3 rounded-lg font-medium transition-colors <?= $kelulusanActive ? 'text-primary' : 'text-slate-500 hover:text-primary hover:bg-primary/10' ?>">
                <div class="flex items-center">
                    <i data-lucide="award" class="w-5 h-5 shrink-0 <?= $kelulusanActive ? 'text-primary' : 'text-slate-400 group-hover:text-primary' ?>"></i>
                    <span class="ml-3 whitespace-nowrap overflow-hidden transition-all duration-300" :class="sidebarCollapsed ? 'lg:opacity-0 lg:w-0 lg:ml-0' : 'opacity-100'">Kelulusan</span>
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 shrink-0 transition-transform duration-200 overflow-hidden" :class="[open ? 'rotate-180' : '', sidebarCollapsed ? 'lg:hidden' : '']"></i>
            </button>
            <div x-show="open && !sidebarCollapsed" x-cloak
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 -translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                class="ml-4 mt-1 space-y-0.5 border-l-2 border-slate-200 pl-3">

                <!-- Prestasi -->
                <div class="px-2 pt-2 pb-0.5">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Prestasi</span>
                </div>
                <a href="<?= base_url('graduates/academic-index') ?>"
                    class="group flex items-center py-2 px-2 rounded-lg text-sm font-medium transition-colors <?= $isActive('graduates/academic-index') ?>">
                    Indeks Prestasi Kumulatif
                </a>
                <a href="<?= base_url('graduates/academic-achievements') ?>"
                    class="group flex items-center py-2 px-2 rounded-lg text-sm font-medium transition-colors <?= $isActive('graduates/academic-achievements') ?>">
                    Prestasi Akademik
                </a>
                <a href="<?= base_url('graduates/non-academic') ?>"
                    class="group flex items-center py-2 px-2 rounded-lg text-sm font-medium transition-colors <?= $isActive('graduates/non-academic') ?>">
                    Prestasi Non-Akademik
                </a>
                <a href="<?= base_url('graduates/study-period') ?>"
                    class="group flex items-center py-2 px-2 rounded-lg text-sm font-medium transition-colors <?= $isActive('graduates/study-period') ?>">
                    Masa Studi
                </a>

                <!-- Tracer Study -->
                <div class="px-2 pt-2 pb-0.5">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Tracer Study</span>
                </div>
                <a href="<?= base_url('graduates/waiting-time') ?>"
                    class="group flex items-center py-2 px-2 rounded-lg text-sm font-medium transition-colors <?= $isActive('graduates/waiting-time') ?>">
                    Waktu Tunggu
                </a>
                <a href="<?= base_url('graduates/job-match') ?>"
                    class="group flex items-center py-2 px-2 rounded-lg text-sm font-medium transition-colors <?= $isActive('graduates/job-match') ?>">
                    Kesesuaian Kerja
                </a>
                <a href="<?= base_url('graduates/workplace') ?>"
                    class="group flex items-center py-2 px-2 rounded-lg text-sm font-medium transition-colors <?= $isActive('graduates/workplace') ?>">
                    Tempat Kerja
                </a>

                <!-- Kepuasan Pengguna -->
                <div class="px-2 pt-2 pb-0.5">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Kepuasan Pengguna</span>
                </div>
                <a href="<?= base_url('graduates/user-satisfaction/reference') ?>"
                    class="group flex items-center py-2 px-2 rounded-lg text-sm font-medium transition-colors <?= $isActive('graduates/user-satisfaction/reference') ?>">
                    Referensi Indikator Kepuasan
                </a>
                <a href="<?= base_url('graduates/user-satisfaction') ?>"
                    class="group flex items-center py-2 px-2 rounded-lg text-sm font-medium transition-colors <?= $isActive('graduates/user-satisfaction') ?>">
                    Kepuasan Pengguna Lulusan
                </a>

                <!-- Publikasi Mahasiswa -->
                <div class="px-2 pt-2 pb-0.5">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Publikasi Mahasiswa</span>
                </div>
                <a href="<?= base_url('graduates/publications/scientific') ?>"
                    class="group flex items-center py-2 px-2 rounded-lg text-sm font-medium transition-colors <?= $isActive('graduates/publications/scientific') ?>">
                    Publikasi Ilmiah Mahasiswa
                </a>
                <a href="<?= base_url('graduates/publications/presentations') ?>"
                    class="group flex items-center py-2 px-2 rounded-lg text-sm font-medium transition-colors <?= $isActive('graduates/publications/presentations') ?>">
                    Presentasi Ilmiah Mahasiswa
                </a>
                <a href="<?= base_url('graduates/publications/creative-works') ?>"
                    class="group flex items-center py-2 px-2 rounded-lg text-sm font-medium transition-colors <?= $isActive('graduates/publications/creative-works') ?>">
                    Sitasi Karya Ilmiah Mahasiswa
                </a>

                <!-- Luaran HKI Mahasiswa -->
                <div class="px-2 pt-2 pb-0.5">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Luaran HKI Mahasiswa</span>
                </div>
                <a href="<?= base_url('graduates/hki/industry-products') ?>"
                    class="group flex items-center py-2 px-2 rounded-lg text-sm font-medium transition-colors <?= $isActive('graduates/hki/industry-products') ?>">
                    Produk Mahasiswa Adopsi Industri
                </a>
                <a href="<?= base_url('graduates/hki/patents') ?>"
                    class="group flex items-center py-2 px-2 rounded-lg text-sm font-medium transition-colors <?= $isActive('graduates/hki/patents') ?>">
                    HKI Paten Mahasiswa
                </a>
                <a href="<?= base_url('graduates/hki/copyright') ?>"
                    class="group flex items-center py-2 px-2 rounded-lg text-sm font-medium transition-colors <?= $isActive('graduates/hki/copyright') ?>">
                    HKI Hak Cipta Mahasiswa
                </a>
                <a href="<?= base_url('graduates/hki/technology') ?>"
                    class="group flex items-center py-2 px-2 rounded-lg text-sm font-medium transition-colors <?= $isActive('graduates/hki/technology') ?>">
                    Teknologi Tepat Guna Mahasiswa
                </a>
                <a href="<?= base_url('graduates/hki/books') ?>"
                    class="group flex items-center py-2 px-2 rounded-lg text-sm font-medium transition-colors <?= $isActive('graduates/hki/books') ?>">
                    Buku Mahasiswa Ber-ISBN
                </a>
            </div>
        </li>
        <?php endif; ?>

        <!-- Laporan LKPS (Asesor, Admin) -->
        <?php if (in_array($role, ['admin', 'asesor'])): ?>
        <li>
            <a href="<?= base_url('reports') ?>" class="group flex py-2.5 px-3 rounded-lg font-medium transition-colors <?= $isActive('reports') ?>">
                <i data-lucide="file-text" class="w-5 h-5 shrink-0 <?= $iconColor('reports') ?>"></i>
                <span class="ml-3 whitespace-nowrap overflow-hidden transition-all duration-300" :class="sidebarCollapsed ? 'lg:opacity-0 lg:w-0 lg:ml-0' : 'opacity-100'">Laporan LKPS</span>
            </a>
        </li>
        <?php endif; ?>

    </ul>
    </div>
</div>
