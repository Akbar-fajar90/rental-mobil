<!DOCTYPE html>
<html>
<head>
    <title>Test Image</title>
</head>
<body>
    <h1>Test Gambar</h1>
    
    <?php helper('asset'); ?>
    
    <p>Helper getCarImage: <?= getCarImage(null, 'avanza') ?></p>
    
    <img src="<?= getCarImage(null, 'avanza') ?>" style="width: 200px; border: 1px solid red;">
    
    <hr>
    
    <p>Manual path: <?= base_url('assets/image/img-car-avanza.png') ?></p>
    
    <img src="<?= base_url('assets/image/img-car-avanza.png') ?>" style="width: 200px; border: 1px solid blue;">
    
    <hr>
    
    <?php
    $path = FCPATH . 'assets/image/img-car-avanza.png';
    echo "File exists: " . (file_exists($path) ? 'YES' : 'NO');
    echo "<br>Full path: " . $path;
    ?>
</body>
</html>