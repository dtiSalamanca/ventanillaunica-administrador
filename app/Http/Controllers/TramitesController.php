<?php

namespace App\Http\Controllers;

use App\Models\Requisito;
use App\Models\Tramite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TramitesController extends Controller
{
    public function indexTramites(): View
    {
        return view('tramites.indexTramites');
    }

    public function agregarTramite(): View
    {
        return view('tramites.agregarTramite');
    }

    public function getTramitesActivos(): JsonResponse
    {
        $tramites = Tramite::where('estatus_tramite', true)
            ->select('id_tramite', 'nombre_tramite', 'precio_tramite')
            ->orderBy('nombre_tramite')
            ->get();

        return response()->json($tramites);
    }

    public function registrarTramite(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:cat_tramites,nombre_tramite',
            'fk_dependencia' => 'required|exists:cat_dependencias,id_dependencia',
            'precio' => 'required|numeric|min:0|max:99999999.99',
        ], [
            'nombre.required' => 'El nombre del trámite es obligatorio.',
            'nombre.max' => 'El nombre no debe exceder los 255 caracteres.',
            'nombre.unique' => 'Ya existe un trámite con ese nombre.',
            'fk_dependencia.required' => 'La dependencia es obligatoria.',
            'fk_dependencia.exists' => 'La dependencia seleccionada no es válida.',
            'precio.required' => 'El precio del trámite es obligatorio.',
            'precio.numeric' => 'El precio debe ser un número válido.',
            'precio.min' => 'El precio no puede ser negativo.',
            'precio.max' => 'El precio excede el monto máximo permitido.',
        ]);

        Tramite::create([
            'nombre_tramite' => $validated['nombre'],
            'estatus_tramite' => true,
            'fk_dependencia' => $validated['fk_dependencia'],
            'precio_tramite' => $validated['precio'],
        ]);

        return redirect()->route('indexTramites')->with('success', 'Trámite registrado correctamente.');
    }

    public function getTramitesInactivos(): JsonResponse
    {
        $tramites = Tramite::where('estatus_tramite', false)
            ->select('id_tramite', 'nombre_tramite', 'precio_tramite')
            ->orderBy('nombre_tramite')
            ->get();

        return response()->json($tramites);
    }

    public function editarTramite(Tramite $tramite): View
    {
        return view('tramites.editarTramite', compact('tramite'));
    }

    public function actualizarTramite(Request $request, Tramite $tramite): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:cat_tramites,nombre_tramite,'.$tramite->id_tramite.',id_tramite',
            'fk_dependencia' => 'required|exists:cat_dependencias,id_dependencia',
            'precio' => 'required|numeric|min:0|max:99999999.99',
        ], [
            'nombre.required' => 'El nombre del trámite es obligatorio.',
            'nombre.max' => 'El nombre no debe exceder los 255 caracteres.',
            'nombre.unique' => 'Ya existe un trámite con ese nombre.',
            'fk_dependencia.required' => 'La dependencia es obligatoria.',
            'fk_dependencia.exists' => 'La dependencia seleccionada no es válida.',
            'precio.required' => 'El precio del trámite es obligatorio.',
            'precio.numeric' => 'El precio debe ser un número válido.',
            'precio.min' => 'El precio no puede ser negativo.',
            'precio.max' => 'El precio excede el monto máximo permitido.',
        ]);

        $tramite->update([
            'nombre_tramite' => $validated['nombre'],
            'fk_dependencia' => $validated['fk_dependencia'],
            'precio_tramite' => $validated['precio'],
        ]);

        return redirect()->route('indexTramites')->with('success', 'Trámite actualizado correctamente.');
    }

    public function deshabilitarTramite(Tramite $tramite): JsonResponse
    {
        $tramite->update(['estatus_tramite' => false]);

        return response()->json(['message' => 'Trámite deshabilitado correctamente.']);
    }

    public function habilitarTramite(Tramite $tramite): JsonResponse
    {
        $tramite->update(['estatus_tramite' => true]);

        return response()->json(['message' => 'Trámite habilitado correctamente.']);
    }

    public function revisarRequisitos(Tramite $tramite): View
    {
        return view('requisitos.revisarRequisitos', compact('tramite'));
    }

    public function getRequisitosAsignados(Tramite $tramite): JsonResponse
    {
        $requisitos = $tramite->requisitos()
            ->select('cat_requisitos.id_requisito', 'cat_requisitos.nombre_requisito', 'cat_requisitos.estatus_requisito')
            ->orderBy('cat_requisitos.nombre_requisito')
            ->get();

        return response()->json($requisitos);
    }

    public function getCatalogoDisponible(Tramite $tramite): JsonResponse
    {
        $asignados = $tramite->requisitos()->pluck('cat_requisitos.id_requisito');

        $disponibles = Requisito::where('estatus_requisito', true)
            ->whereNotIn('id_requisito', $asignados)
            ->orderBy('nombre_requisito')
            ->get(['id_requisito', 'nombre_requisito']);

        return response()->json($disponibles);
    }

    public function asignarRequisitos(Request $request, Tramite $tramite): JsonResponse
    {
        $validated = $request->validate([
            'requisitos' => 'required|array|min:1',
            'requisitos.*' => 'exists:cat_requisitos,id_requisito',
        ], [
            'requisitos.required' => 'Debe seleccionar al menos un requisito.',
            'requisitos.min' => 'Debe seleccionar al menos un requisito.',
            'requisitos.*.exists' => 'Uno o más requisitos seleccionados no son válidos.',
        ]);

        $yaAsignados = $tramite->requisitos()->pluck('cat_requisitos.id_requisito')->toArray();
        $nuevos = array_diff($validated['requisitos'], $yaAsignados);

        if (empty($nuevos)) {
            return response()->json(['message' => 'Los requisitos seleccionados ya están asignados a este trámite.'], 422);
        }

        $tramite->requisitos()->attach($nuevos);

        $count = count($nuevos);
        $message = $count === 1 ? 'Requisito asignado correctamente.' : "{$count} requisitos asignados correctamente.";

        return response()->json(['message' => $message], 201);
    }

    public function quitarRequisito(Tramite $tramite, Requisito $requisito): JsonResponse
    {
        $tramite->requisitos()->detach($requisito->id_requisito);

        return response()->json(['message' => 'Requisito quitado del trámite correctamente.']);
    }
}
