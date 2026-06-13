<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <?php $title = trim(implode('', (array) $this->renderSection('title'))); ?>

    <title>
        <?= $title ? $title . ' | SIM-LKPS' : 'SIM-LKPS' ?>
    </title>

    <link rel="stylesheet" href="<?= base_url('css/app.css') ?>">

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="min-h-screen bg-surface font-poppins">

    <?= $this->renderSection('content') ?>

    <script src="<?= base_url('js/bundle.js') ?>"></script>

    <?= $this->renderSection('scripts') ?>

</body>

</html>