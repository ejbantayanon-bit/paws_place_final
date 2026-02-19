<?php

namespace App\Controllers;

class Kitchen extends BaseController
{
    public function index()
    {
        return view('kitchen/display');
    }
}
