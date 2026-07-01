<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentosPersonalesController extends Controller
{
    public function indexDocumentosPersonales(): View
    {
        return view('documentosPersonales.indexDocumentosPersonales');
    }


    public function agregarDocumentoPersonal(): View
    {
        return view('documentosPersonales.agregarDocumentoPersonal');
    }
}
