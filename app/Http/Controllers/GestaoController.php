<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class GestaoController extends Controller
{
    public function __invoke(): View
    {
        return view('gestao.index');
    }
}

