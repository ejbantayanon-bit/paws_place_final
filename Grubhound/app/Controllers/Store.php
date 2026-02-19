<?php

namespace App\Controllers;

class Store extends BaseController
{
    public function index()
    {
        return view('store/selection');
    }
}
