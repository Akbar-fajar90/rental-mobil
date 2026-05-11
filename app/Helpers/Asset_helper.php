<?php

if (!function_exists('getCarImage')) 
{
    function getCarImage($foto_mobil = null, $merk = null)
    {
        // Default image
        $defaultImage = base_url('assets/image/default-img.png');
        
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
        
        // Jika ada foto di database (upload ke assets/img)
        if ($foto_mobil && file_exists(FCPATH . 'assets/img/' . $foto_mobil)) {
            return base_url('assets/img/' . $foto_mobil);
        }
        
        // Cari berdasarkan merk
        if ($merk) {
            $merkLower = strtolower($merk);
            foreach ($map as $key => $file) {
                if (strpos($merkLower, $key) !== false) {
                    if (file_exists(FCPATH . 'assets/image/' . $file)) {
                        return base_url('assets/image/' . $file);
                    }
                }
            }
        }
        
        return $defaultImage;
    }
}

if (!function_exists('getCustomerAvatar')) {
    function getCustomerAvatar(string $nama)
    {
        return "https://ui-avatars.com/api/?name=" . urlencode($nama) . "&background=0d6efd&color=fff&length=2&bold=true";
    }
}