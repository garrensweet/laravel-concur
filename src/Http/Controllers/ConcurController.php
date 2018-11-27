<?php

namespace VdPoel\Concur\Http\Controllers;

use Illuminate\Routing\Controller;

class ConcurController extends Controller
{
    public function index ()
    {
        return view('concur::index');
    }
}