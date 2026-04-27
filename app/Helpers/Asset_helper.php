<?php

if (!function_exists('getCarImage')) {
    function getCarImage($foto_mobil = null, $merk = null)
    {
        // Folder gambar
        $folder = 'assets/image/';
        
        // Default image
        $defaultImage = base_url($folder . 'default-car.png');
        
        // Mapping merk ke nama file (sesuaikan dengan nama file Anda)
        $map = [
            'avanza' => 'img-car-avanza.jpg',
            'brio' => 'img-car-brio.jpg',
            'ertiga' => 'img-car-ertiga.jpg',
            'xenia' => 'img-car-xenia.jpg',
            'innova' => 'img-car-innova.jpg',
            'pajero' => 'img-car-pajero.jpg',
            'civic' => 'img-car-civic.jpg',
        ];
        
        // Jika ada foto di database
        if ($foto_mobil && file_exists(FCPATH . $folder . $foto_mobil)) {
            return base_url($folder . $foto_mobil);
        }
        
        // Cari berdasarkan merk
        if ($merk) {
            $merkLower = strtolower($merk);
            foreach ($map as $key => $file) {
                if (strpos($merkLower, $key) !== false) {
                    return base_url($folder . $file);
                }
            }
        }
        
        return $defaultImage;
    }
}

if (!function_exists('getCustomerAvatar')) {
    function getCustomerAvatar($nama)
    {
        return "https://ui-avatars.com/api/?name=" . urlencode($nama) . "&background=0d6efd&color=fff&length=2&bold=true";
    }
}