<?php

namespace App\Http\Controllers;

use App\Models\Dependencia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DependenciasController extends Controller
{
    public function indexDependencias(): View
    {
        return view('dependencias.indexDependencias');
    }

    public function agregarDependencia(): View
    {
        return view('dependencias.agregarDependencia');
    }

    public function getDependenciasActivas(): JsonResponse
    {
        $dependencias = Dependencia::where('activo', true)
            ->select('id_dependencia', 'nombre')
            ->orderBy('nombre')
            ->get();

        return response()->json($dependencias);
    }

    public function getDependenciasInactivas(): JsonResponse
    {
        $dependencias = Dependencia::where('activo', false)
            ->select('id_dependencia', 'nombre')
            ->orderBy('nombre')
            ->get();

        return response()->json($dependencias);
    }
}
