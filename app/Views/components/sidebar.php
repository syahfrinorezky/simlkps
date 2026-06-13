<?php
    $role = session()->get('userRole') ?? 'guest';
    $currentUri = uri_string();
    
    // Function to check if a menu is active
    $isActive = function($path) use ($currentUri) {
        return ($currentUri == $path || strpos($currentUri, $path . '/') === 0)
            ? 'bg-primary text-white shadow-md'
            : 'text-slate-500 hover:text-primary hover:bg-primary/10';
    };
    
    $iconColor = function($path) use ($currentUri) {
        return ($currentUri == $path || strpos($currentUri, $path . '/') === 0) ? 'text-white' : 'text-slate-400 group-hover:text-primary';
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
    class="flex flex-col absolute z-40 left-0 top-0 lg:static lg:left-auto lg:top-auto lg:translate-x-0 h-screen overflow-y-scroll lg:overflow-y-auto no-scrollbar shrink-0 bg-white border-r border-slate-200 transition-all duration-300 ease-in-out"
    :class="[
        sidebarOpen ? 'translate-x-0 w-72' : '-translate-x-72 w-72',
        'lg:translate-x-0',
        sidebarCollapsed ? 'lg:w-20' : 'lg:w-72'
    ]"
    @keydown.escape.window="sidebarOpen = false"
>
    <!-- Sidebar header -->
    <div class="flex justify-between items-center pr-3 sm:px-2 py-4 border-b border-slate-200 px-4">
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

    <!-- Search input -->
    <div class="px-4 py-4 overflow-hidden" :class="sidebarCollapsed ? 'lg:hidden' : ''">
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

                <!-- Manajemen User (Admin only) -->
                <?php if ($role === 'admin'): ?>
                <li>
                    <a href="<?= base_url('users') ?>" class="group flex py-2.5 px-3 rounded-lg font-medium transition-colors <?= $isActive('users') ?>">
                        <i data-lucide="users" class="w-5 h-5 shrink-0 <?= $iconColor('users') ?>"></i>
                        <span class="ml-3 whitespace-nowrap overflow-hidden transition-all duration-300" :class="sidebarCollapsed ? 'lg:opacity-0 lg:w-0 lg:ml-0' : 'opacity-100'">Manajemen User</span>
                    </a>
                </li>
                <?php endif; ?>

                <!-- Kerja Sama (Admin) -->
                <?php if (in_array($role, ['admin'])): ?>
                <li>
                    <a href="<?= base_url('cooperations') ?>" class="group flex py-2.5 px-3 rounded-lg font-medium transition-colors <?= $isActive('cooperations') ?>">
                        <i data-lucide="handshake" class="w-5 h-5 shrink-0 <?= $iconColor('cooperations') ?>"></i>
                        <span class="ml-3 whitespace-nowrap overflow-hidden transition-all duration-300" :class="sidebarCollapsed ? 'lg:opacity-0 lg:w-0 lg:ml-0' : 'opacity-100'">Kerja Sama</span>
                    </a>
                </li>
                <?php endif; ?>

                <!-- Mahasiswa (Admin, Prodi) -->
                <?php if (in_array($role, ['admin', 'prodi'])): ?>
                <li>
                    <a href="<?= base_url('students') ?>" class="group flex py-2.5 px-3 rounded-lg font-medium transition-colors <?= $isActive('students') ?>">
                        <i data-lucide="graduation-cap" class="w-5 h-5 shrink-0 <?= $iconColor('students') ?>"></i>
                        <span class="ml-3 whitespace-nowrap overflow-hidden transition-all duration-300" :class="sidebarCollapsed ? 'lg:opacity-0 lg:w-0 lg:ml-0' : 'opacity-100'">Mahasiswa</span>
                    </a>
                </li>
                <?php endif; ?>

                <!-- Dosen (Admin, Prodi) -->
                <?php if (in_array($role, ['admin', 'prodi'])): ?>
                <li>
                    <a href="<?= base_url('lecturers') ?>" class="group flex py-2.5 px-3 rounded-lg font-medium transition-colors <?= $isActive('lecturers') ?>">
                        <i data-lucide="contact" class="w-5 h-5 shrink-0 <?= $iconColor('lecturers') ?>"></i>
                        <span class="ml-3 whitespace-nowrap overflow-hidden transition-all duration-300" :class="sidebarCollapsed ? 'lg:opacity-0 lg:w-0 lg:ml-0' : 'opacity-100'">Dosen</span>
                    </a>
                </li>
                <?php endif; ?>

                <!-- Penggunaan Dana (Admin) -->
                <?php if (in_array($role, ['admin'])): ?>
                <li>
                    <a href="<?= base_url('funds') ?>" class="group flex py-2.5 px-3 rounded-lg font-medium transition-colors <?= $isActive('funds') ?>">
                        <i data-lucide="wallet" class="w-5 h-5 shrink-0 <?= $iconColor('funds') ?>"></i>
                        <span class="ml-3 whitespace-nowrap overflow-hidden transition-all duration-300" :class="sidebarCollapsed ? 'lg:opacity-0 lg:w-0 lg:ml-0' : 'opacity-100'">Penggunaan Dana</span>
                    </a>
                </li>
                <?php endif; ?>

                <!-- Pembelajaran (Admin, Prodi, Dosen) -->
                <?php if (in_array($role, ['admin', 'prodi', 'dosen'])): ?>
                <li>
                    <a href="<?= base_url('courses') ?>" class="group flex py-2.5 px-3 rounded-lg font-medium transition-colors <?= $isActive('courses') ?>">
                        <i data-lucide="book-open" class="w-5 h-5 shrink-0 <?= $iconColor('courses') ?>"></i>
                        <span class="ml-3 whitespace-nowrap overflow-hidden transition-all duration-300" :class="sidebarCollapsed ? 'lg:opacity-0 lg:w-0 lg:ml-0' : 'opacity-100'">Pembelajaran</span>
                    </a>
                </li>
                <?php endif; ?>

                <!-- Penelitian (Admin, Dosen) -->
                <?php if (in_array($role, ['admin', 'dosen'])): ?>
                <li>
                    <a href="<?= base_url('researches') ?>" class="group flex py-2.5 px-3 rounded-lg font-medium transition-colors <?= $isActive('researches') ?>">
                        <i data-lucide="flask-conical" class="w-5 h-5 shrink-0 <?= $iconColor('researches') ?>"></i>
                        <span class="ml-3 whitespace-nowrap overflow-hidden transition-all duration-300" :class="sidebarCollapsed ? 'lg:opacity-0 lg:w-0 lg:ml-0' : 'opacity-100'">Penelitian</span>
                    </a>
                </li>
                <?php endif; ?>

                <!-- Pengabdian (Admin, Dosen) -->
                <?php if (in_array($role, ['admin', 'dosen'])): ?>
                <li>
                    <a href="<?= base_url('community-services') ?>" class="group flex py-2.5 px-3 rounded-lg font-medium transition-colors <?= $isActive('community-services') ?>">
                        <i data-lucide="users-round" class="w-5 h-5 shrink-0 <?= $iconColor('community-services') ?>"></i>
                        <span class="ml-3 whitespace-nowrap overflow-hidden transition-all duration-300" :class="sidebarCollapsed ? 'lg:opacity-0 lg:w-0 lg:ml-0' : 'opacity-100'">Pengabdian</span>
                    </a>
                </li>
                <?php endif; ?>

                <!-- Kelulusan (Admin, Prodi) -->
                <?php if (in_array($role, ['admin', 'prodi'])): ?>
                <li>
                    <a href="<?= base_url('graduates') ?>" class="group flex py-2.5 px-3 rounded-lg font-medium transition-colors <?= $isActive('graduates') ?>">
                        <i data-lucide="award" class="w-5 h-5 shrink-0 <?= $iconColor('graduates') ?>"></i>
                        <span class="ml-3 whitespace-nowrap overflow-hidden transition-all duration-300" :class="sidebarCollapsed ? 'lg:opacity-0 lg:w-0 lg:ml-0' : 'opacity-100'">Kelulusan</span>
                    </a>
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

