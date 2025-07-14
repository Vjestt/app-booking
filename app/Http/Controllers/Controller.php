<?php

namespace App\Http\Controllers;

abstract class Controller
{
    // Common controller logic can go here

    public function index()
    {
        return view('welcome');
    }
}
