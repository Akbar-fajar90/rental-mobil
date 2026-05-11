<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;

class Filters extends BaseConfig
{
    public $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'auth'          => \App\Filters\Auth::class,
        'auth_pelanggan' => \App\Filters\AuthPelanggan::class,
    ];
    
    public $globals = [
        'before' => [
            // 'honeypot',
            'csrf' => ['except' => ['payment/notification', 'payment/notification*']],
            'invalidchars',
        ],
        'after' => [
            'toolbar',
            // 'honeypot',
            'secureheaders',
        ],
    ];
    
    public $methods = [];
    
    public $filters = [];
}