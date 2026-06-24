<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RequisitosController extends Controller
{
    public function indexRequisitos(): View
    {
        return view('requisitos.indexRequisitos');
    }

    public function agregarRequisito(): View
    {
        return view('requisitos.agregarRequisito');
    }

}
