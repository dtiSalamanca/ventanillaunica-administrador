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
        $dependencias = Dependencia::where('activo', true)
            ->select('id_dependencia', 'nombre')
            ->orderBy('nombre')
            ->get();

        return response()->json($dependencias);
    }

    public function registrarDependencia(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:cat_dependencias,nombre',
        ], [
            'nombre.required' => 'El nombre de la dependencia es obligatorio.',
            'nombre.max' => 'El nombre no debe exceder los 255 caracteres.',
            'nombre.unique' => 'Ya existe una dependencia con ese nombre.',
        ]);

        Dependencia::create([
            'nombre' => $validated['nombre'],
            'activo' => true,
        ]);

        return redirect()->route('indexDependencias')->with('success', 'Dependencia registrada correctamente.');
    }

    public function getDependenciasInactivas(): JsonResponse
    {
        $dependencias = Dependencia::where('activo', false)
            ->select('id_dependencia', 'nombre')
            ->orderBy('nombre')
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
            'nombre' => 'required|string|max:255|unique:cat_dependencias,nombre,'.$dependencia->id_dependencia.',id_dependencia',
        ], [
            'nombre.required' => 'El nombre de la dependencia es obligatorio.',
            'nombre.max' => 'El nombre no debe exceder los 255 caracteres.',
            'nombre.unique' => 'Ya existe una dependencia con ese nombre.',
        ]);

        $dependencia->update([
            'nombre' => $validated['nombre'],
        ]);

        return redirect()->route('indexDependencias')->with('success', 'Dependencia actualizada correctamente.');
    }

    public function deshabilitarDependencia(Dependencia $dependencia): JsonResponse
    {
        $dependencia->update(['activo' => false]);

        return response()->json(['message' => 'Dependencia deshabilitada correctamente.']);
    }

    public function habilitarDependencia(Dependencia $dependencia): JsonResponse
    {
        $dependencia->update(['activo' => true]);

        return response()->json(['message' => 'Dependencia habilitada correctamente.']);
    }
}
