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
            ->select('id_tramite', 'nombre_tramite', 'descripcion_tramite', 'precio_tramite')
            ->orderBy('nombre_tramite')
            ->get();

        return response()->json($tramites);
    }

    public function registrarTramite(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:cat_tramites,nombre_tramite',
            'descripcion' => 'required|string',
            'fk_dependencia' => 'required|exists:cat_dependencias,id_dependencia',
            'precio' => 'required|numeric|min:0|max:99999999.99',
        ], [
            'nombre.required' => 'El nombre del trámite es obligatorio.',
            'nombre.max' => 'El nombre no debe exceder los 255 caracteres.',
            'nombre.unique' => 'Ya existe un trámite con ese nombre.',
            'descripcion.required' => 'La descripción del trámite es obligatoria.',
            'fk_dependencia.required' => 'La dependencia es obligatoria.',
            'fk_dependencia.exists' => 'La dependencia seleccionada no es válida.',
            'precio.required' => 'El precio del trámite es obligatorio.',
            'precio.numeric' => 'El precio debe ser un número válido.',
            'precio.min' => 'El precio no puede ser negativo.',
            'precio.max' => 'El precio excede el monto máximo permitido.',
        ]);

        Tramite::create([
            'nombre_tramite' => $validated['nombre'],
            'descripcion_tramite' => $validated['descripcion'],
            'estatus_tramite' => true,
            'fk_dependencia' => $validated['fk_dependencia'],
            'precio_tramite' => $validated['precio'],
        ]);

        return redirect()->route('indexTramites')->with('success', 'Trámite registrado correctamente.');
    }

    public function getTramitesInactivos(): JsonResponse
    {
        $tramites = Tramite::where('estatus_tramite', false)
            ->select('id_tramite', 'nombre_tramite', 'descripcion_tramite', 'precio_tramite')
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
            'descripcion' => 'required|string',
            'fk_dependencia' => 'required|exists:cat_dependencias,id_dependencia',
            'precio' => 'required|numeric|min:0|max:99999999.99',
        ], [
            'nombre.required' => 'El nombre del trámite es obligatorio.',
            'nombre.max' => 'El nombre no debe exceder los 255 caracteres.',
            'nombre.unique' => 'Ya existe un trámite con ese nombre.',
            'descripcion.required' => 'La descripción del trámite es obligatoria.',
            'fk_dependencia.required' => 'La dependencia es obligatoria.',
            'fk_dependencia.exists' => 'La dependencia seleccionada no es válida.',
            'precio.required' => 'El precio del trámite es obligatorio.',
            'precio.numeric' => 'El precio debe ser un número válido.',
            'precio.min' => 'El precio no puede ser negativo.',
            'precio.max' => 'El precio excede el monto máximo permitido.',
        ]);

        $tramite->update([
            'nombre_tramite' => $validated['nombre'],
            'descripcion_tramite' => $validated['descripcion'],
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
        $prerequisitos = $tramite->tramitesRequeridos()
            ->select('cat_tramites.id_tramite', 'cat_tramites.nombre_tramite', 'cat_tramites.estatus_tramite')
            ->orderBy('cat_tramites.nombre_tramite')
            ->get();

        return view('requisitos.revisarRequisitos', compact('tramite', 'prerequisitos'));
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

    // ─── Prerequisitos (trámites requeridos) ─────────────────────────────────

    public function revisarPrerequisitos(Tramite $tramite): View
    {
        return view('tramites.revisarPrerequisitos', compact('tramite'));
    }

    public function getPrerequisitosAsignados(Tramite $tramite): JsonResponse
    {
        $prerequisitos = $tramite->tramitesRequeridos()
            ->select('cat_tramites.id_tramite', 'cat_tramites.nombre_tramite', 'cat_tramites.estatus_tramite')
            ->orderBy('cat_tramites.nombre_tramite')
            ->get()
            ->map(function ($item) {
                return [
                    'id_tramite' => $item->id_tramite,
                    'nombre_tramite' => $item->nombre_tramite,
                    'estatus_tramite' => $item->estatus_tramite,
                ];
            });

        return response()->json($prerequisitos);
    }

    public function getPrerequisitosDisponibles(Tramite $tramite): JsonResponse
    {
        $asignados = $tramite->tramitesRequeridos()->pluck('cat_tramites.id_tramite');

        // También excluir el propio trámite (no puede requerirse a sí mismo)
        $excluir = $asignados->push($tramite->id_tramite);

        $disponibles = Tramite::where('estatus_tramite', true)
            ->whereNotIn('id_tramite', $excluir)
            ->orderBy('nombre_tramite')
            ->get(['id_tramite', 'nombre_tramite']);

        return response()->json($disponibles);
    }

    public function asignarPrerequisitos(Request $request, Tramite $tramite): JsonResponse
    {
        $validated = $request->validate([
            'prerequisitos' => 'required|array|min:1',
            'prerequisitos.*' => 'exists:cat_tramites,id_tramite',
        ], [
            'prerequisitos.required' => 'Debe seleccionar al menos un trámite prerequisito.',
            'prerequisitos.min' => 'Debe seleccionar al menos un trámite prerequisito.',
            'prerequisitos.*.exists' => 'Uno o más trámites seleccionados no son válidos.',
        ]);

        // Validar que no se asigne a sí mismo
        if (in_array($tramite->id_tramite, $validated['prerequisitos'])) {
            return response()->json(['message' => 'Un trámite no puede requerirse a sí mismo.'], 422);
        }

        // Validar circularidad simple (A requiere B y B requiere A)
        $yaAsignados = $tramite->tramitesRequeridos()->pluck('cat_tramites.id_tramite')->toArray();
        $nuevos = array_diff($validated['prerequisitos'], $yaAsignados);

        if (empty($nuevos)) {
            return response()->json(['message' => 'Los trámites seleccionados ya están asignados como prerequisitos.'], 422);
        }

        // Validar que ningún prerequisito nuevo tenga ya este trámite como su prerequisito (circular)
        $conflictos = Tramite::whereIn('id_tramite', $nuevos)
            ->whereHas('tramitesRequeridos', function ($query) use ($tramite) {
                $query->where('cat_tramites.id_tramite', $tramite->id_tramite);
            })
            ->pluck('nombre_tramite');

        if ($conflictos->isNotEmpty()) {
            $nombres = $conflictos->implode(', ');
            $message = "No se puede asignar porque los siguientes trámites ya requieren a este: {$nombres}. Esto crearía una dependencia circular.";

            return response()->json(['message' => $message], 422);
        }

        $tramite->tramitesRequeridos()->attach($nuevos);

        $count = count($nuevos);
        $message = $count === 1
            ? 'Trámite prerequisito asignado correctamente.'
            : "{$count} trámites prerequisito asignados correctamente.";

        return response()->json(['message' => $message], 201);
    }

    public function quitarPrerequisito(Tramite $tramite, Tramite $requerido): JsonResponse
    {
        $tramite->tramitesRequeridos()->detach($requerido->id_tramite);

        return response()->json(['message' => 'Trámite prerequisito quitado correctamente.']);
    }
}
