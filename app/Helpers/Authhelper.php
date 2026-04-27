<?php

if (!function_exists('is_logged_in')) {
    function is_logged_in()
    {
        return session()->get('isLoggedIn') === true;
    }
}

if (!function_exists('is_admin')) {
    function is_admin()
    {
        return session()->get('isLoggedIn') && session()->get('role') === 'admin';
    }
}

if (!function_exists('is_pelanggan')) {
    function is_pelanggan()
    {
        return session()->get('isLoggedIn') && session()->get('role') === 'pelanggan';
    }
}

if (!function_exists('get_user_data')) {
    function get_user_data($key = null)
    {
        if ($key) {
            return session()->get($key);
        }
        return session()->get();
    }
}