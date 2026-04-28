<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Rental Mobil' ?></title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    
    <style>
        :root {
            --primary-color: #1d63ed;
            --secondary-color: #2a3eff;
            --accent-color: #ff9f43;
            --dark-blue: #0a0e1a;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            overflow-x: hidden;
        }
    </style>
</head>
<body>

    <?= $this->include('layout/landing_header'); ?>

    <main style="padding-top: 90px;">
        <?= $this->renderSection('content'); ?>
    </main>

    <?= $this->include('layout/landing_footer'); ?>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?= base_url('assets/js/script.js') ?>"></script>
    
    <?= $this->renderSection('scripts'); ?>
</body>
</html>
