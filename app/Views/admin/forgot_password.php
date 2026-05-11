<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - Rental Mobil</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
</head>
<body class="auth-wrapper-gradient min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
        <div class="flex justify-center mb-6">
            <div class="w-16 h-16 bg-blue-50 rounded-full flex items-center justify-center text-blue-600 text-2xl">
                <i class="fa-solid fa-rotate-left"></i>
            </div>
        </div>

        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-800">Lupa Password</h2>
            <p class="text-gray-500 text-sm mt-1">Masukkan username dan password baru Anda</p>
        </div>

        <!-- Alert Messages -->
        <?php if (session()->getFlashdata('success')): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl flex items-start gap-3 mb-6">
                <i class="fa-solid fa-check-circle mt-0.5"></i>
                <p class="text-sm font-medium"><?= session()->getFlashdata('success') ?></p>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl flex items-start gap-3 mb-6">
                <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                <p class="text-sm font-medium"><?= session()->getFlashdata('error') ?></p>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl mb-6">
                <?php foreach (session()->getFlashdata('errors') as $error): ?>
                    <p class="text-sm"><i class="fa-solid fa-circle-exclamation mr-2"></i> <?= $error ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="bg-amber-50 border border-amber-200 text-amber-700 px-4 py-3 rounded-xl flex items-start gap-3 mb-6">
            <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
            <p class="text-xs font-medium leading-relaxed">Perhatian: Password akan langsung berubah tanpa verifikasi email</p>
        </div>

        <form action="<?= base_url('admin/reset-password') ?>" method="post" class="space-y-4">
            <?= csrf_field() ?>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Username</label>
                <div class="relative">
                    <i class="fa-solid fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="username" value="<?= old('username') ?>" 
                           class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                           placeholder="Masukkan username Anda" required>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Password Baru</label>
                <div class="relative">
                    <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="password" name="new_password" id="new_password"
                           class="w-full pl-11 pr-12 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                           placeholder="Buat password baru" required>
                    <button type="button" onclick="togglePassword('new_password', this)" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-eye-slash"></i>
                    </button>
                </div>
                <p class="text-xs text-gray-400 mt-1">Minimal 4 karakter</p>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Konfirmasi Password Baru</label>
                <div class="relative">
                    <i class="fa-solid fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="password" name="confirm_password" id="confirm_password"
                           class="w-full pl-11 pr-12 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition"
                           placeholder="Konfirmasi password baru" required>
                    <button type="button" onclick="togglePassword('confirm_password', this)" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-eye-slash"></i>
                    </button>
                </div>
                <div id="passwordMatchError" class="text-red-500 text-xs mt-1 hidden">
                    <i class="fa-solid fa-circle-exclamation mr-1"></i> Password tidak cocok
                </div>
            </div>

            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3.5 rounded-xl transition shadow-lg shadow-blue-200 mt-4">
                <i class="fa-solid fa-key mr-2"></i> RESET PASSWORD
            </button>

            <div class="text-center mt-4">
                <a href="<?= base_url('admin/login') ?>" class="text-sm font-semibold text-blue-600 hover:underline">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Kembali ke Login
                </a>
            </div>
        </form>

        <div class="mt-8 text-center border-t border-gray-100 pt-6">
            <p class="text-xs text-gray-400 uppercase tracking-widest"><i class="fa-solid fa-lock mr-1"></i> SISTEM RENTAL MOBIL</p>
        </div>
    </div>

    <script>
        window.baseUrl = '<?= base_url() ?>';
    </script>
    <script src="<?= base_url('assets/js/script.js') ?>"></script>
    

</body>
</html>