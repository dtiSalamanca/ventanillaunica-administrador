<?php

namespace App\Http\Controllers;

use App\Models\catDocumentoPersonal;
use App\Models\catDocumentoPredio;
use App\Models\Tramite;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            ->select('id_tramite', 'nombre_tramite', 'descripcion_tramite', 'precio_tramite', 'cobra_por_m2', 'sin_costo', 'vigencia_dias')
            ->orderBy('nombre_tramite')
            ->get();

        return response()->json($tramites);
    }

    public function registrarTramite(Request $request): RedirectResponse
    {
        $cobraPorM2 = $request->boolean('cobra_por_m2');
        $cuentaPredial = $request->boolean('cuenta_predial');
        $sinCosto = $request->boolean('sin_costo');

        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:cat_tramites,nombre_tramite',
            'descripcion' => 'required|string',
            'fk_dependencia' => 'required|exists:cat_dependencias,id_dependencia',
            'fk_cri' => 'required|numeric|min:1',
            'vigencia_dias' => 'nullable|integer|max_digits:3',
        ], [
            'nombre.required' => 'El nombre del trámite es obligatorio.',
            'nombre.max' => 'El nombre no debe exceder los 255 caracteres.',
            'nombre.unique' => 'Ya existe un trámite con ese nombre.',
            'descripcion.required' => 'La descripción del trámite es obligatoria.',
            'fk_dependencia.required' => 'La dependencia es obligatoria.',
            'fk_dependencia.exists' => 'La dependencia seleccionada no es válida.',
            'fk_cri.required' => 'El campo CRI es obligatorio.',
            'fk_cri.numeric' => 'El campo CRI debe ser un número válido.',
            'fk_cri.min' => 'El campo CRI debe ser mayor o igual a 1.',
            'vigencia_dias.integer' => 'La vigencia debe contener solo números.',
            'vigencia_dias.max_digits' => 'La vigencia debe tener como máximo 3 dígitos.',
        ]);

        $tramite = Tramite::create([
            'nombre_tramite' => $validated['nombre'],
            'descripcion_tramite' => $validated['descripcion'],
            'estatus_tramite' => true,
            'fk_dependencia' => $validated['fk_dependencia'],
            'precio_tramite' => 0,
            'tramite_cri' => $validated['fk_cri'],
            'cobra_por_m2' => $cobraPorM2,
            'cuenta_predial' => $cuentaPredial,
            'sin_costo' => $sinCosto,
            'vigencia_dias' => $validated['vigencia_dias'] ?? 0,
        ]);

        return redirect()->route('indexTramites')->with('success', "Trámite '{$tramite->nombre_tramite}' registrado correctamente.");
    }

    public function getTramitesInactivos(): JsonResponse
    {
        $tramites = Tramite::where('estatus_tramite', false)
            ->select('id_tramite', 'nombre_tramite', 'descripcion_tramite', 'precio_tramite', 'cobra_por_m2', 'sin_costo', 'vigencia_dias')
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
        $cobraPorM2 = $request->boolean('cobra_por_m2');
        $cuentaPredial = $request->boolean('cuenta_predial');
        $sinCosto = $request->boolean('sin_costo');

        $validated = $request->validate([
            'nombre' => 'required|string|max:255|unique:cat_tramites,nombre_tramite,'.$tramite->id_tramite.',id_tramite',
            'descripcion' => 'required|string',
            'fk_dependencia' => 'required|exists:cat_dependencias,id_dependencia',
            'fk_cri' => 'required|numeric|min:1',
            'vigencia_dias' => 'nullable|integer|max_digits:3',
        ], [
            'nombre.required' => 'El nombre del trámite es obligatorio.',
            'nombre.max' => 'El nombre no debe exceder los 255 caracteres.',
            'nombre.unique' => 'Ya existe un trámite con ese nombre.',
            'descripcion.required' => 'La descripción del trámite es obligatoria.',
            'fk_dependencia.required' => 'La dependencia es obligatoria.',
            'fk_dependencia.exists' => 'La dependencia seleccionada no es válida.',
            'fk_cri.required' => 'El campo CRI es obligatorio.',
            'fk_cri.numeric' => 'El campo CRI debe ser un número válido.',
            'fk_cri.min' => 'El campo CRI debe ser mayor o igual a 1.',
            'vigencia_dias.integer' => 'La vigencia debe contener solo números.',
            'vigencia_dias.max_digits' => 'La vigencia debe tener como máximo 3 dígitos.',
        ]);

        $tramite->update([
            'nombre_tramite' => $validated['nombre'],
            'descripcion_tramite' => $validated['descripcion'],
            'fk_dependencia' => $validated['fk_dependencia'],
            'precio_tramite' => 0,
            'tramite_cri' => $validated['fk_cri'],
            'cobra_por_m2' => $cobraPorM2,
            'cuenta_predial' => $cuentaPredial,
            'sin_costo' => $sinCosto,
            'vigencia_dias' => $validated['vigencia_dias'] ?? 0,
        ]);

        return redirect()->route('indexTramites')->with('success', "Trámite '{$tramite->nombre_tramite}' actualizado correctamente.");
    }

    public function deshabilitarTramite(Tramite $tramite): JsonResponse
    {
        $tramite->update(['estatus_tramite' => false]);

        return response()->json(['message' => "Trámite '{$tramite->nombre_tramite}' deshabilitado correctamente."]);
    }

    public function habilitarTramite(Tramite $tramite): JsonResponse
    {
        $tramite->update(['estatus_tramite' => true]);

        return response()->json(['message' => "Trámite '{$tramite->nombre_tramite}' habilitado correctamente."]);
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

        $requisitos = DB::table('tbl_requisitos_tramites as rt')
            ->leftJoin('cat_documentos_personales as cp', 'cp.id_documento', '=', 'rt.fk_requisito')
            ->leftJoin('cat_documentos_predios as cpr', 'cpr.id_documento_predio', '=', 'rt.fk_predio')
            ->where('rt.fk_tramite', $tramite->id_tramite)
            ->selectRaw("
                rt.id_requisito,
                    COALESCE(cp.id_documento, cpr.id_documento_predio) as id_documento,
                    COALESCE(cp.nombre_documento, cpr.nombre_documento) as nombre_documento,
                    CASE
                        WHEN cp.id_documento IS NOT NULL THEN 'd'
                        ELSE 'p'
                    END as tipo_documento
                ")
            ->get();

        return response()->json($requisitos);
    }

    public function getCatalogoDisponible(Tramite $tramite): JsonResponse
    {
        // IDs de documentos personales ya asignados
        $personalesAsignados = DB::table('tbl_requisitos_tramites')
            ->where('fk_tramite', $tramite->id_tramite) // Ajusta el nombre de la PK si es diferente
            ->whereNotNull('fk_requisito')
            ->pluck('fk_requisito');

        // IDs de documentos del predio ya asignados
        $prediosAsignados = DB::table('tbl_requisitos_tramites')
            ->where('fk_tramite', $tramite->id_tramite)
            ->whereNotNull('fk_predio')
            ->pluck('fk_predio');

        $docsPersonales = catDocumentoPersonal::where('estatus_documento', true)
            ->whereNotIn('id_documento', $personalesAsignados)
            ->orderBy('nombre_documento')
            ->get(['id_documento', 'nombre_documento']);

        $docsPredio = catDocumentoPredio::where('estatus_documento', true)
            ->whereNotIn('id_documento_predio', $prediosAsignados)
            ->orderBy('nombre_documento')
            ->get(['id_documento_predio', 'nombre_documento']);

        return response()->json([
            'docsPredio' => $docsPredio,
            'docsPersonales' => $docsPersonales,
        ]);
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

    public function quitarRequisito(Tramite $tramite, int $requisito): JsonResponse
    {
        $eliminado = DB::table('tbl_requisitos_tramites')
            ->where('id_requisito', $requisito)
            ->where('fk_tramite', $tramite->id_tramite)
            ->delete();

        if ($eliminado === 0) {
            return response()->json([
                'message' => 'El requisito ya no está asignado a este trámite.',
            ], 404);
        }

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
