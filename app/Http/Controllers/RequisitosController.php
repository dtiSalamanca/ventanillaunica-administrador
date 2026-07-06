<?php

namespace App\Http\Controllers;

use App\Models\Requisito;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function registrarRequisito(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:cat_requisitos,nombre_requisito',
            'descripcion' => 'required|string',
        ], [
            'nombre.required' => 'El nombre del requisito es obligatorio.',
            'nombre.max' => 'El nombre no debe exceder los 255 caracteres.',
            'nombre.unique' => 'Ya existe un requisito con ese nombre.',
            'descripcion.required' => 'La descripción del requisito es obligatoria.',
        ]);

        Requisito::create([
            'nombre_requisito' => $validated['nombre'],
            'descripcion_requisito' => $validated['descripcion'],
            'estatus_requisito' => true,
        ]);

        return redirect()->route('indexRequisitos')->with('success', 'Requisito registrado correctamente.');
    }

    public function getRequisitosActivos(): JsonResponse
    {
        $requisitos = Requisito::where('estatus_requisito', true)
            ->select('id_requisito', 'nombre_requisito', 'descripcion_requisito')
            ->orderBy('nombre_requisito')
            ->get();

        return response()->json($requisitos);
    }

    public function getRequisitosInactivos(): JsonResponse
    {
        $requisitos = Requisito::where('estatus_requisito', false)
            ->select('id_requisito', 'nombre_requisito', 'descripcion_requisito')
            ->orderBy('nombre_requisito')
            ->get();

        return response()->json($requisitos);
    }

    public function deshabilitarRequisito(Requisito $requisito): JsonResponse
    {
        $requisito->update(['estatus_requisito' => false]);

        return response()->json(['message' => 'Requisito deshabilitado correctamente.']);
    }

    public function habilitarRequisito(Requisito $requisito): JsonResponse
    {
        $requisito->update(['estatus_requisito' => true]);

        return response()->json(['message' => 'Requisito habilitado correctamente.']);
    }

    public function editarDependencia(Dependencia $dependencia): View
    {
        return view('dependencias.editarDependencia', compact('dependencia'));
    }
}
