<?php

if (!function_exists('get_car_info')) {
    /**
     * Fetch car information from Wikipedia API
     * 
     * @param string $merk
     * @return array
     */
    function get_car_info($merk)
    {
        $client = \Config\Services::curlrequest();
        $merk_encoded = urlencode($merk);
        
        try {
            // Wikipedia API endpoint for extract
            $url = "https://id.wikipedia.org/api/rest_v1/page/summary/{$merk_encoded}";
            $response = $client->get($url, [
                'headers' => [
                    'Accept' => 'application/json',
                    'User-Agent' => 'RentalMobilApp/1.0 (contact: info@rentalmobil.com)'
                ],
                'timeout' => 5
            ]);
            
            if ($response->getStatusCode() === 200) {
                $data = json_decode($response->getBody(), true);
                return [
                    'extract' => $data['extract'] ?? 'Informasi tidak tersedia.',
                    'thumbnail' => $data['thumbnail']['source'] ?? null,
                    'features' => [
                        'Kapasitas' => '5-7 Orang',
                        'Bahan Bakar' => 'Bensin/Diesel',
                        'Transmisi' => 'Manual/Otomatis',
                        'AC' => 'Double Blower'
                    ]
                ];
            }
        } catch (\Exception $e) {
            // Fallback if API fails
        }
        
        return [
            'extract' => "{$merk} adalah salah satu pilihan armada terbaik kami untuk perjalanan Anda.",
            'thumbnail' => null,
            'features' => [
                'Kapasitas' => '5-7 Orang',
                'Bahan Bakar' => 'Bensin/Diesel',
                'Transmisi' => 'Manual/Otomatis',
                'AC' => 'Double Blower'
            ]
        ];
    }
}
