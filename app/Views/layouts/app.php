<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php $title = trim(implode('', (array) $this->renderSection('title'))); ?>

    <title>
        <?= $title ? $title . ' | SIM-LKPS' : 'SIM-LKPS' ?>
    </title>

    <meta name="description" content="Sistem Informasi Laporan Kinerja Program Studi">
    <meta name="author" content="Program Studi">

    <link rel="icon" type="image/png" href="<?= base_url('favicon.png') ?>">

    <link rel="stylesheet" href="<?= base_url('css/app.css') ?>">

    <?= $this->renderSection('styles') ?>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="min-h-screen bg-slate-50 font-sans antialiased text-slate-800" x-data="{ sidebarOpen: false, sidebarCollapsed: false }">

    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar Component -->
        <?= $this->include('components/sidebar') ?>

        <!-- Main Content Area -->
        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden transition-all duration-300">
            
            <!-- Header Component -->
            <?= $this->include('components/header') ?>

            <!-- Main Content -->
            <main class="w-full grow p-6">
                <?= $this->renderSection('content') ?>
            </main>
            
        </div>
    </div>

    <!-- Script for Alpine (if not bundled) and lucide icons -->
    <script src="<?= base_url('js/bundle.js') ?>"></script>
    <!-- Fallback CDN for Lucide Icons & Alpine in case bundle.js doesn't have them -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        lucide.createIcons();
    </script>

    <?= $this->renderSection('scripts') ?>

</body>

</html>