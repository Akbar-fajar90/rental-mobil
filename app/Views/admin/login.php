<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc((string)($title ?? $tittle ?? 'Login Admin')); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body class="auth-wrapper">

<div class="login-card">
    <div class="brand-logo">
        <i class="bi bi-car-front-fill"></i> Rental Mobil
    </div>

    <div class="mb-4">
        <h5 class="fw-bold mb-1">Selamat Datang Kembali</h5>
        <p class="text-muted small">Login untuk menuju ke Dashboard</p>
    </div>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger small py-2">
            <?= session()->getFlashdata('error'); ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')) : ?>
        <div class="alert alert-success small py-2">
            <?= session()->getFlashdata('success'); ?>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('admin/doLogin'); ?>" method="POST">
        <?= csrf_field(); ?>
        
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <div class="input-group">
                <input type="text" name="username" class="form-control" id="username" placeholder="e.g. admin_pro" required>
                <span class="input-group-text"><i class="bi bi-person"></i></span>
            </div>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
                <input type="password" name="password" class="form-control" id="password" placeholder="••••••••" required>
                <span class="input-group-text"><i class="bi bi-lock"></i></span>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-login w-100">
            LOGIN KE RENTAL MOBIL <i class="bi bi-arrow-right ms-2"></i>
        </button>

        <div class="text-center mt-4">
    <a href="<?= base_url('admin/forgot-password') ?>" class="text-sm text-blue-600 hover:underline">
        Lupa Password?
    </a>
</div>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>