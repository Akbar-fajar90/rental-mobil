<?php

use Midtrans\Config;
use Midtrans\Snap;

/**
 * Initialize Midtrans Configuration
 */
function initMidtrans()
{
    Config::$serverKey = env('midtrans.server_key');
    Config::$clientKey = env('midtrans.client_key');
    Config::$isProduction = (bool)env('midtrans.is_production');
    Config::$isSanitized = true;
    Config::$is3ds = true;
}

/**
 * Create a Midtrans Snap Transaction
 * 
 * @param string $order_id
 * @param int $total
 * @param array $customer_details
 * @param array $item_details
 * @return string Snap Token
 * @throws Exception
 */
function createMidtransTransaction($order_id, $total, $customer_details, $item_details)
{
    initMidtrans();

    $params = [
        'transaction_details' => [
            'order_id' => $order_id,
            'gross_amount' => $total,
        ],
        'customer_details' => $customer_details,
        'item_details' => $item_details,
        'callbacks' => [
            'finish' => env('midtrans.redirect_uri')
        ]
    ];

    return Snap::getSnapToken($params);
}
