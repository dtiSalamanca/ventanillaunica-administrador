<?php

namespace App\Http\Controllers;

use App\Models\Dependencia;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
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
        $dependencias = Dependencia::where('estatus_dependencia', true)
            ->select('id_dependencia', 'nombre_dependencia')
            ->orderBy('nombre_dependencia')
            ->get();

        return response()->json($dependencias);
    }

    public function registrarDependencia(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre_dependencia' => 'required|string|max:255|unique:cat_dependencias,nombre_dependencia',
        ], [
            'nombre_dependencia.required' => 'El nombre de la dependencia es obligatorio.',
            'nombre_dependencia.max' => 'El nombre no debe exceder los 255 caracteres.',
            'nombre_dependencia.unique' => 'Ya existe una dependencia con ese nombre.',
        ]);

        Dependencia::create([
            'nombre_dependencia' => $validated['nombre_dependencia'],
            'estatus_dependencia' => true,
        ]);

        return redirect()->route('indexDependencias')->with('success', 'Dependencia registrada correctamente.');
    }

    public function getDependenciasInactivas(): JsonResponse
    {
        $dependencias = Dependencia::where('estatus_dependencia', false)
            ->select('id_dependencia', 'nombre_dependencia')
            ->orderBy('nombre_dependencia')
            ->get();

        return response()->json($dependencias);
    }

    public function editarDependencia(Dependencia $dependencia): View
    {
        return view('dependencias.editarDependencia', compact('dependencia'));
    }

    public function actualizarDependencia(Request $request, Dependencia $dependencia): RedirectResponse
    {
        $validated = $request->validate([
            'nombre_dependencia' => 'required|string|max:255|unique:cat_dependencias,nombre_dependencia,'.$dependencia->id_dependencia.',id_dependencia',
        ], [
            'nombre_dependencia.required' => 'El nombre de la dependencia es obligatorio.',
            'nombre_dependencia.max' => 'El nombre no debe exceder los 255 caracteres.',
            'nombre_dependencia.unique' => 'Ya existe una dependencia con ese nombre.',
        ]);

        $dependencia->update([
            'nombre_dependencia' => $validated['nombre_dependencia'],
        ]);

        return redirect()->route('indexDependencias')->with('success', 'Dependencia actualizada correctamente.');
    }

    public function deshabilitarDependencia(Dependencia $dependencia): JsonResponse
    {
        $dependencia->update(['estatus_dependencia' => false]);

        return response()->json(['message' => 'Dependencia deshabilitada correctamente.']);
    }

    public function habilitarDependencia(Dependencia $dependencia): JsonResponse
    {
        $dependencia->update(['estatus_dependencia' => true]);

        return response()->json(['message' => 'Dependencia habilitada correctamente.']);
    }
}
