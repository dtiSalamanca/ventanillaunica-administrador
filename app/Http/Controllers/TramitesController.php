<?php

namespace App\Http\Controllers;

use App\Models\Requisito;
use App\Models\Tramite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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
        $tramites = Tramite::where('activo', true)
            ->select('id_tramite', 'nombre')
            ->orderBy('nombre')
            ->get();

        return response()->json($tramites);
    }

    public function registrarTramite(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:tbl_tramites,nombre',
            'fk_dependencia' => 'required|exists:cat_dependencias,id_dependencia',
        ], [
            'nombre.required' => 'El nombre del trámite es obligatorio.',
            'nombre.max' => 'El nombre no debe exceder los 255 caracteres.',
            'nombre.unique' => 'Ya existe un trámite con ese nombre.',
            'fk_dependencia.required' => 'La dependencia es obligatoria.',
            'fk_dependencia.exists' => 'La dependencia seleccionada no es válida.',
        ]);

        Tramite::create([
            'nombre' => $validated['nombre'],
            'activo' => true,
            'fk_dependencia' => $validated['fk_dependencia'],
        ]);

        return redirect()->route('indexTramites')->with('success', 'Trámite registrado correctamente.');
    }

    public function getTramitesInactivos(): JsonResponse
    {
        $tramites = Tramite::where('activo', false)
            ->select('id_tramite', 'nombre')
            ->orderBy('nombre')
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
            'nombre' => 'required|string|max:255|unique:tbl_tramites,nombre,'.$tramite->id_tramite.',id_tramite',
            'fk_dependencia' => 'required|exists:cat_dependencias,id_dependencia',
        ], [
            'nombre.required' => 'El nombre del trámite es obligatorio.',
            'nombre.max' => 'El nombre no debe exceder los 255 caracteres.',
            'nombre.unique' => 'Ya existe un trámite con ese nombre.',
            'fk_dependencia.required' => 'La dependencia es obligatoria.',
            'fk_dependencia.exists' => 'La dependencia seleccionada no es válida.',
        ]);

        $tramite->update([
            'nombre' => $validated['nombre'],
            'fk_dependencia' => $validated['fk_dependencia'],
        ]);

        return redirect()->route('indexTramites')->with('success', 'Trámite actualizado correctamente.');
    }

    public function deshabilitarTramite(Tramite $tramite): JsonResponse
    {
        $tramite->update(['activo' => false]);

        return response()->json(['message' => 'Trámite deshabilitado correctamente.']);
    }

    public function habilitarTramite(Tramite $tramite): JsonResponse
    {
        $tramite->update(['activo' => true]);

        return response()->json(['message' => 'Trámite habilitado correctamente.']);
    }

    public function revisarRequisitos(Tramite $tramite): View
    {
        return view('requisitos.revisarRequisitos', compact('tramite'));
    }

    public function getRequisitosActivos(Tramite $tramite): JsonResponse
    {
        $requisitos = $tramite->requisitos()
            ->where('activo', true)
            ->select('id_requisito', 'nombre')
            ->orderBy('nombre')
            ->get();

        return response()->json($requisitos);
    }

    public function getRequisitosInactivos(Tramite $tramite): JsonResponse
    {
        $requisitos = $tramite->requisitos()
            ->where('activo', false)
            ->select('id_requisito', 'nombre')
            ->orderBy('nombre')
            ->get();

        return response()->json($requisitos);
    }

    public function registrarRequisito(Request $request, Tramite $tramite): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tbl_requisitos', 'nombre')->where('fk_tramite', $tramite->id_tramite),
            ],
        ], [
            'nombre.required' => 'El nombre del requisito es obligatorio.',
            'nombre.max' => 'El nombre no debe exceder los 255 caracteres.',
            'nombre.unique' => 'Ya existe un requisito con ese nombre en este trámite.',
        ]);

        $requisito = Requisito::create([
            'nombre' => $validated['nombre'],
            'activo' => true,
            'fk_tramite' => $tramite->id_tramite,
        ]);

        return response()->json(['message' => 'Requisito registrado correctamente.', 'requisito' => $requisito], 201);
    }

    public function actualizarRequisito(Request $request, Tramite $tramite, Requisito $requisito): JsonResponse
    {
        $validated = $request->validate([
            'nombre' => [
                'required',
                'string',
                'max:255',
                Rule::unique('tbl_requisitos', 'nombre')
                    ->where('fk_tramite', $tramite->id_tramite)
                    ->ignore($requisito->id_requisito, 'id_requisito'),
            ],
        ], [
            'nombre.required' => 'El nombre del requisito es obligatorio.',
            'nombre.max' => 'El nombre no debe exceder los 255 caracteres.',
            'nombre.unique' => 'Ya existe un requisito con ese nombre en este trámite.',
        ]);

        $requisito->update(['nombre' => $validated['nombre']]);

        return response()->json(['message' => 'Requisito actualizado correctamente.']);
    }

    public function deshabilitarRequisito(Tramite $tramite, Requisito $requisito): JsonResponse
    {
        $requisito->update(['activo' => false]);

        return response()->json(['message' => 'Requisito deshabilitado correctamente.']);
    }

    public function habilitarRequisito(Tramite $tramite, Requisito $requisito): JsonResponse
    {
        $requisito->update(['activo' => true]);

        return response()->json(['message' => 'Requisito habilitado correctamente.']);
    }
}
