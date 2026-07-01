<?php

namespace App\Http\Controllers;

use App\Models\tblDocumentoPersonal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DocumentosPersonalesController extends Controller
{
    public function indexDocumentosPersonales(): View
    {
        return view('documentosPersonales.indexDocumentosPersonales');
    }

    public function agregarDocumentoPersonal(): View
    {
        return view('documentosPersonales.agregarDocumentoPersonal');
    }

    public function getDocumentosPersonalesActivos(): JsonResponse
    {
        $documentos = tblDocumentoPersonal::where('estatus_documento', true)
            ->select('id_documento', 'nombre_documento', 'vigencia_meses')
            ->orderBy('nombre_documento')
            ->get();

        return response()->json($documentos);
    }

    public function getDocumentosPersonalesInactivos(): JsonResponse
    {
        $documentos = tblDocumentoPersonal::where('estatus_documento', false)
            ->select('id_documento', 'nombre_documento', 'vigencia_meses')
            ->orderBy('nombre_documento')
            ->get();

        return response()->json($documentos);
    }

    public function registrarDocumentoPersonal(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre_documento' => 'required|string|max:255|unique:cat_documentos_personales,nombre_documento',
            'vigencia_meses' => 'required|integer|min:1',
        ], [
            'nombre_documento.required' => 'El nombre del documento es obligatorio.',
            'nombre_documento.max' => 'El nombre no debe exceder los 255 caracteres.',
            'nombre_documento.unique' => 'Ya existe un documento personal con ese nombre.',
            'vigencia_meses.required' => 'La vigencia en meses es obligatoria.',
            'vigencia_meses.integer' => 'La vigencia debe ser un número entero.',
            'vigencia_meses.min' => 'La vigencia debe ser al menos 1 mes.',
        ]);

        tblDocumentoPersonal::create([
            'nombre_documento' => $validated['nombre_documento'],
            'vigencia_meses' => $validated['vigencia_meses'],
            'estatus_documento' => true,
        ]);

        return redirect()->route('indexDocumentosPersonales')->with('success', 'Documento personal registrado correctamente.');
    }

    public function editarDocumentoPersonal(tblDocumentoPersonal $documentoPersonal): View
    {
        return view('documentosPersonales.editarDocumentoPersonal', compact('documentoPersonal'));
    }

    public function actualizarDocumentoPersonal(Request $request, tblDocumentoPersonal $documentoPersonal): RedirectResponse
    {
        $validated = $request->validate([
            'nombre_documento' => 'required|string|max:255|unique:cat_documentos_personales,nombre_documento,'.$documentoPersonal->id_documento.',id_documento',
            'vigencia_meses' => 'required|integer|min:1',
        ], [
            'nombre_documento.required' => 'El nombre del documento es obligatorio.',
            'nombre_documento.max' => 'El nombre no debe exceder los 255 caracteres.',
            'nombre_documento.unique' => 'Ya existe un documento personal con ese nombre.',
            'vigencia_meses.required' => 'La vigencia en meses es obligatoria.',
            'vigencia_meses.integer' => 'La vigencia debe ser un número entero.',
            'vigencia_meses.min' => 'La vigencia debe ser al menos 1 mes.',
        ]);

        $documentoPersonal->update([
            'nombre_documento' => $validated['nombre_documento'],
            'vigencia_meses' => $validated['vigencia_meses'],
        ]);

        return redirect()->route('indexDocumentosPersonales')->with('success', 'Documento personal actualizado correctamente.');
    }

    public function deshabilitarDocumentoPersonal(tblDocumentoPersonal $documentoPersonal): JsonResponse
    {
        $documentoPersonal->update(['estatus_documento' => false]);

        return response()->json(['message' => 'Documento personal deshabilitado correctamente.']);
    }

    public function habilitarDocumentoPersonal(tblDocumentoPersonal $documentoPersonal): JsonResponse
    {
        $documentoPersonal->update(['estatus_documento' => true]);

        return response()->json(['message' => 'Documento personal habilitado correctamente.']);
    }
}
