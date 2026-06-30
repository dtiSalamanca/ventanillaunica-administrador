<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UsuariosController extends Controller
{
    public function indexUsuarios(): View
    {
        return view('usuarios.indexUsuarios');
    }
}
