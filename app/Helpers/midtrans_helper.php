<?php

use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

if (!function_exists('initMidtrans')) {
    function initMidtrans()
    {
        Config::$serverKey = env('midtrans.server_key');
        Config::$isProduction = env('midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }
}

if (!function_exists('createMidtransTransaction')) {
    function createMidtransTransaction($params)
    {
        initMidtrans();
        try {
            return Snap::getSnapToken($params);
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}

if (!function_exists('getMidtransStatus')) {
    function getMidtransStatus($order_id)
    {
        initMidtrans();
        try {
            return Transaction::status($order_id);
        } catch (\Exception $e) {
            return null;
        }
    }
}

if (!function_exists('generateOrderId')) {
    function generateOrderId($prefix = 'RENT')
    {
        return $prefix . '-' . time() . '-' . rand(100, 999);
    }
}
