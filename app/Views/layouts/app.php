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

    <link rel="icon" type="image/png" href="<?= base_url('pnb-logo.png') ?>">

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

    <script src="<?= base_url('js/bundle.js') ?>"></script>

    <!-- delete modal -->
    <?= $this->include('components/delete_modal') ?>

    <!-- toast notification -->
    <?= $this->include('components/toast') ?>

    <?= $this->renderSection('scripts') ?>

</body>

</html>