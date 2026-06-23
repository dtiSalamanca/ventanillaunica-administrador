<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DependenciasController extends Controller
{
    public function indexPanteones(): View
    {
        return view('dependencias.indexDependencias');
    }
}
