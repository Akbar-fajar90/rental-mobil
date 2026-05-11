<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script type="text/javascript"
            src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="<?= env('midtrans.client_key') ?>"></script>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm border-0 text-center p-5">
                    <h4 class="fw-bold mb-4">Selesaikan Pembayaran</h4>
                    <p class="text-muted">Klik tombol di bawah untuk membayar penyewaan mobil <strong><?= esc($sewa->car_name) ?></strong> sebesar</p>
                    <h2 class="text-primary fw-bold mb-5">Rp <?= number_format($amount, 0, ',', '.') ?></h2>
                    
                    <button id="pay-button" class="btn btn-primary btn-lg w-100 fw-bold py-3 shadow-sm">BAYAR SEKARANG</button>
                    
                    <a href="<?= base_url('riwayat') ?>" class="btn btn-link mt-3 text-muted text-decoration-none">Kembali ke Riwayat</a>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        var payButton = document.getElementById('pay-button');
        payButton.addEventListener('click', function () {
            window.snap.pay('<?= $snapToken ?>', {
                onSuccess: function (result) {
                    window.location.href = "<?= base_url('payment/finish') ?>?order_id=" + result.order_id;
                },
                onPending: function (result) {
                    window.location.href = "<?= base_url('payment/unfinish') ?>?order_id=" + result.order_id;
                },
                onError: function (result) {
                    window.location.href = "<?= base_url('payment/error') ?>";
                },
                onClose: function () {
                    alert('Anda menutup jendela pembayaran sebelum selesai.');
                }
            });
        });
    </script>
</body>
</html>
