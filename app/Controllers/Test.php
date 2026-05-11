<?php

namespace App\Controllers;

class Test extends BaseController
{
    public function index()
    {
        helper('asset');
        return view('index');
    }
}