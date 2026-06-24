<?php

namespace App\Http\Controllers;

use App\Models\Requisito;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

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

    public function getRequisitosActivos(): JsonResponse
    {
        $requisitos = Requisito::where('activo', true)
            ->select('id_requisito', 'nombre')
            ->orderBy('nombre')
            ->get();

        return response()->json($requisitos);
    }

    public function getRequisitosInactivos(): JsonResponse
    {
        $requisitos = Requisito::where('activo', false)
            ->select('id_requisito', 'nombre')
            ->orderBy('nombre')
            ->get();

        return response()->json($requisitos);
    }

    public function deshabilitarRequisito(Requisito $requisito): JsonResponse
    {
        $requisito->update(['activo' => false]);

        return response()->json(['message' => 'Requisito deshabilitado correctamente.']);
    }

    public function habilitarRequisito(Requisito $requisito): JsonResponse
    {
        $requisito->update(['activo' => true]);

        return response()->json(['message' => 'Requisito habilitado correctamente.']);
    }
}
