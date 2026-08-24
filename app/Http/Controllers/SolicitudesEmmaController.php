<?php

namespace App\Http\Controllers;

use App\Models\RequisitoTramite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SolicitudesEmmaController extends Controller
{
    //
    public function index()
    {
        return view('solicitudes.index');
    }

    public function asignarRequisitosE(Request $request, $tramite)
    {
        // Validar los datos recibidos
        $validator = Validator::make($request->all(), [
            'documentos_personales' => 'nullable|array',
            'documentos_predio' => 'nullable|array',

            'documentos_personales.*' => 'exists:cat_documentos_personales,id_documento',
            'documentos_predio.*' => 'exists:cat_documentos_predios,id_documento_predio',
        ]);

        $validator->after(function ($validator) use ($request) {

            $personales = $request->input('documentos_personales', []);
            $predio = $request->input('documentos_predio', []);

            if (empty($personales) && empty($predio)) {
                $validator->errors()->add(
                    'documentos_personales',
                    'Debe seleccionar al menos un documento personal o un documento del predio.'
                );
            }
        });

        $validated = $validator->validate();

        // Aquí puedes agregar la lógica para asignar los requisitos al trámite
        // Por ejemplo, podrías buscar el trámite y luego asociar los requisitos validados

        $documentosPersonales = $validated['documentos_personales'] ?? [];
        $documentosPredio = $validated['documentos_predio'] ?? [];

        foreach ($documentosPersonales as $documentoPersonal) {
            $nRequisitoTramite = new RequisitoTramite;
            $nRequisitoTramite->fk_tramite = $tramite;
            $nRequisitoTramite->fk_requisito = $documentoPersonal;
            $nRequisitoTramite->save();
        }

        foreach ($documentosPredio as $documentoPredio) {
            $nRequisitoTramite = new RequisitoTramite;
            $nRequisitoTramite->fk_tramite = $tramite;
            $nRequisitoTramite->fk_predio = $documentoPredio;
            $nRequisitoTramite->save();
        }

        // Retornar una respuesta exitosa
        return response()->json(['message' => 'Requisitos asignados correctamente.'], 201);
    }
}
