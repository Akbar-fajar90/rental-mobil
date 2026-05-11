<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light d-flex align-items-center" style="height: 100vh;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-sm border-0 text-center p-5">
                    <?php if ($status == 'success'): ?>
                        <div class="text-success mb-4">
                            <i class="bi bi-check-circle-fill" style="font-size: 5rem;"></i>
                        </div>
                        <h3 class="fw-bold">Pembayaran Berhasil!</h3>
                        <p class="text-muted">Terima kasih atas pembayaran Anda. Pesanan Anda akan segera diproses.</p>
                    <?php elseif ($status == 'pending'): ?>
                        <div class="text-warning mb-4">
                            <i class="bi bi-clock-history" style="font-size: 5rem;"></i>
                        </div>
                        <h3 class="fw-bold">Pembayaran Pending</h3>
                        <p class="text-muted">Pembayaran Anda sedang diproses. Silakan selesaikan pembayaran sesuai instruksi.</p>
                    <?php else: ?>
                        <div class="text-danger mb-4">
                            <i class="bi bi-x-circle-fill" style="font-size: 5rem;"></i>
                        </div>
                        <h3 class="fw-bold">Pembayaran Gagal</h3>
                        <p class="text-muted">Mohon maaf, transaksi Anda gagal diproses. Silakan coba lagi.</p>
                    <?php endif; ?>
                    
                    <a href="<?= base_url('riwayat') ?>" class="btn btn-primary mt-4 py-2 px-5 fw-bold">Kembali ke Riwayat</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
