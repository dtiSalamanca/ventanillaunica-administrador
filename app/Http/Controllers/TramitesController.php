<?php

namespace App\Http\Controllers;

use App\Models\catDocumentoPersonal;
use App\Models\catDocumentoPredio;
use App\Models\Requisito;
use App\Models\Tramite;
use App\Models\RequisitoTramite;
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
            'fk_cri' => 'required|numeric|min:1',
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
            'fk_cri.required' => 'El campo CRI es obligatorio.',
            'fk_cri.numeric' => 'El campo CRI debe ser un número válido.',
            'fk_cri.min' => 'El campo CRI debe ser mayor o igual a 1.',
        ]);

        Tramite::create([
            'nombre_tramite' => $validated['nombre'],
            'descripcion_tramite' => $validated['descripcion'],
            'estatus_tramite' => true,
            'fk_dependencia' => $validated['fk_dependencia'],
            'precio_tramite' => $validated['precio'],
            'tramite_cri' => $validated['fk_cri'],
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
            'fk_cri' => 'required|numeric|min:1',
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
            'fk_cri.required' => 'El campo CRI es obligatorio.',
            'fk_cri.numeric' => 'El campo CRI debe ser un número válido.',
            'fk_cri.min' => 'El campo CRI debe ser mayor o igual a 1.',
        ]);

        $tramite->update([
            'nombre_tramite' => $validated['nombre'],
            'descripcion_tramite' => $validated['descripcion'],
            'fk_dependencia' => $validated['fk_dependencia'],
            'precio_tramite' => $validated['precio'],
            'tramite_cri' => $validated['fk_cri'],
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

    public function quitarRequisito(Tramite $tramite, Requisito $requisito): JsonResponse
    {
        $tramite->requisitos()->detach($requisito->id_requisito);

        return response()->json(['message' => 'Requisito quitado del trámite correctamente.']);
    }
}
