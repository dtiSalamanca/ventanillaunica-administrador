<?php

namespace App\Http\Controllers;

use App\Models\catDocumentoPredio;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PrediosController extends Controller
{
    public function indexPredios(): View
    {
        return view('predios.indexPredios');
    }

    public function agregarDocumentoPredio(): View
    {
        return view('predios.agregarDocumentoPredio');
    }

    public function getDocumentosPrediosActivos(): JsonResponse
    {
        $documentos = catDocumentoPredio::where('estatus_documento', true)
            ->select('id_documento_predio', 'nombre_documento', 'vigencia_meses')
            ->orderBy('nombre_documento')
            ->get();

        return response()->json($documentos);
    }

    public function getDocumentosPrediosInactivos(): JsonResponse
    {
        $documentos = catDocumentoPredio::where('estatus_documento', false)
            ->select('id_documento_predio', 'nombre_documento', 'vigencia_meses')
            ->orderBy('nombre_documento')
            ->get();

        return response()->json($documentos);
    }

    public function registrarDocumentoPredio(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre_documento' => 'required|string|max:255|unique:cat_documentos_predios,nombre_documento',
            'vigencia_meses' => 'required|integer|min:1',
        ], [
            'nombre_documento.required' => 'El nombre del documento es obligatorio.',
            'nombre_documento.max' => 'El nombre no debe exceder los 255 caracteres.',
            'nombre_documento.unique' => 'Ya existe un documento de predio con ese nombre.',
            'vigencia_meses.required' => 'La vigencia en meses es obligatoria.',
            'vigencia_meses.integer' => 'La vigencia debe ser un número entero.',
            'vigencia_meses.min' => 'La vigencia debe ser al menos 1 mes.',
        ]);

        catDocumentoPredio::create([
            'nombre_documento' => $validated['nombre_documento'],
            'vigencia_meses' => $validated['vigencia_meses'],
            'estatus_documento' => true,
        ]);

        return redirect()->route('indexPredios')->with('success', 'Documento de predio registrado correctamente.');
    }

    public function editarDocumentoPredio(catDocumentoPredio $documentoPredio): View
    {
        return view('predios.editarDocumentoPredio', compact('documentoPredio'));
    }

    public function actualizarDocumentoPredio(Request $request, catDocumentoPredio $documentoPredio): RedirectResponse
    {
        $validated = $request->validate([
            'nombre_documento' => 'required|string|max:255|unique:cat_documentos_predios,nombre_documento,'.$documentoPredio->id_documento_predio.',id_documento_predio',
            'vigencia_meses' => 'required|integer|min:1',
        ], [
            'nombre_documento.required' => 'El nombre del documento es obligatorio.',
            'nombre_documento.max' => 'El nombre no debe exceder los 255 caracteres.',
            'nombre_documento.unique' => 'Ya existe un documento de predio con ese nombre.',
            'vigencia_meses.required' => 'La vigencia en meses es obligatoria.',
            'vigencia_meses.integer' => 'La vigencia debe ser un número entero.',
            'vigencia_meses.min' => 'La vigencia debe ser al menos 1 mes.',
        ]);

        $documentoPredio->update([
            'nombre_documento' => $validated['nombre_documento'],
            'vigencia_meses' => $validated['vigencia_meses'],
        ]);

        return redirect()->route('indexPredios')->with('success', 'Documento de predio actualizado correctamente.');
    }

    public function deshabilitarDocumentoPredio(catDocumentoPredio $documentoPredio): JsonResponse
    {
        $documentoPredio->update(['estatus_documento' => false]);

        return response()->json(['message' => 'Documento de predio deshabilitado correctamente.']);
    }

    public function habilitarDocumentoPredio(catDocumentoPredio $documentoPredio): JsonResponse
    {
        $documentoPredio->update(['estatus_documento' => true]);

        return response()->json(['message' => 'Documento de predio habilitado correctamente.']);
    }
}
