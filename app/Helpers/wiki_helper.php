<?php

if (!function_exists('get_car_info')) {
    /**
     * Fetch car information from Wikipedia and technical specs from CarAPI
     * @param string $merk
     * @param string $model_mobil
     * @return array
     */
    function get_car_info(string $merk, string $model_mobil = '')
    {
        $client = \Config\Services::curlrequest();
        $full_name = trim($merk . ' ' . $model_mobil);
        
        // --- 1. AMBIL NARASI DARI WIKIPEDIA ---
        $wiki_extract = "{$full_name} adalah salah satu pilihan armada terbaik kami.";
        $wiki_thumb = null;
        
        try {
            $url_wiki = "https://id.wikipedia.org/api/rest_v1/page/summary/" . urlencode($full_name);
            $res_wiki = $client->get($url_wiki, [
                'headers' => ['Accept' => 'application/json', 'User-Agent' => 'RentalApp/1.0'],
                'timeout' => 3,
                'http_errors' => false
            ]);
            
            if ($res_wiki->getStatusCode() === 200) {
                $data_wiki = json_decode($res_wiki->getBody(), true);
                $wiki_extract = $data_wiki['extract'] ?? $wiki_extract;
                $wiki_thumb = $data_wiki['thumbnail']['source'] ?? null;
            }
        } catch (\Exception $e) {}

        // --- 2. AMBIL SPESIFIKASI DARI CARAPI ---
        // Default Specs (Fallback jika API gagal)
        $features = [
            'Kapasitas'   => '- Orang',
            'Bahan Bakar' => '-',
            'Transmisi'   => '-',
            'Tipe Bodi'   => 'Tidak Spesifik'
        ];

        try {
            // Endpoint pencarian CarAPI (menggunakan API Key Anda)
            $url_carapi = "https://carapi.app/api/models?make=" . urlencode($merk) . "&verbose=yes";
            $res_carapi = $client->get($url_carapi, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer 44def6e7-8c19-4323-be3e-4ff45e34f828'
                ],
                'timeout' => 5,
                'http_errors' => false
            ]);

            if ($res_carapi->getStatusCode() === 200) {
                $data_carapi = json_decode($res_carapi->getBody(), true);
                
                // Cari model yang paling mendekati jika ada hasil
                if (!empty($data_carapi['data'])) {
                    $car_spec = null;
                    
                    // Jika model_mobil diisi, cari yang cocok. Jika tidak, ambil yang pertama.
                    if ($model_mobil) {
                        foreach ($data_carapi['data'] as $m) {
                            if (stripos($m['name'], $model_mobil) !== false) {
                                $car_spec = $m;
                                break;
                            }
                        }
                    }
                    
                    $car_spec = $car_spec ?: $data_carapi['data'][0];

                    // Mapping data CarAPI ke format kita
                    $features = [
                        'Kapasitas'   => isset($car_spec['body_style']) && stripos($car_spec['body_style'], 'SUV') !== false ? '7 Orang' : '5 Orang',
                        'Bahan Bakar' => 'Bensin', // CarAPI model level seringkali tidak spesifik fuel, default ke Bensin
                        'Transmisi'   => 'Manual/Otomatis',
                        'Tipe Bodi'   => $car_spec['body_style'] ?? 'City Car'
                    ];
                }
            }
        } catch (\Exception $e) {}

        return [
            'extract'   => $wiki_extract,
            'thumbnail' => $wiki_thumb,
            'features'  => $features
        ];
    }
}