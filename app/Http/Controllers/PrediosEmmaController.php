<?php

namespace App\Http\Controllers;

use App\Mail\PredioRevisado;
use App\Models\Predio;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class PrediosEmmaController extends Controller
{
    /**
     * Valida la existencia de un predio contra el sistema de predial
     * (https://recibopredial.salamanca.gob.mx/api/consulta/predio/{clave}).
     *
     * Si la cuenta predial existe, el predio se APRUEBA automáticamente y se
     * notifica al ciudadano. Si no existe, se rechaza automáticamente con el
     * motivo correspondiente.
     */
    public function validar(string $clave): JsonResponse
    {
        $predio = Predio::query()->where('clave_predio', $clave)->first();

        if (! $predio) {
            return response()->json([
                'existe' => false,
                'message' => 'No se encontró el predio registrado.',
            ], 404);
        }

        try {
            $respuesta = Http::timeout(15)
                ->connectTimeout(5)
                ->get(config('services.recibo_predial.base_url').'/'.$predio->clave_predio);
        } catch (ConnectionException) {
            return response()->json([
                'existe' => null,
                'message' => 'No se pudo conectar con el servicio de predial.',
            ], 502);
        }

        $existe = $this->predioExisteEnPredial($respuesta);

        if ($existe === null) {
            return response()->json([
                'existe' => null,
                'message' => 'El servicio de predial respondió de forma inesperada.',
            ], 502);
        }

        if ($existe) {
            $predio->update([
                'estatus_predio' => Predio::ESTATUS_APROBADO,
                'consultado' => Predio::CONSULTADO_EXISTE,
            ]);

            Mail::to($predio->usuario->email)->send(new PredioRevisado($predio));

            return response()->json([
                'existe' => true,
                'aprobado' => true,
                'message' => 'El predio existe en el sistema de predial y fue aprobado automáticamente.',
            ]);
        }

        $predio->update([
            'estatus_predio' => Predio::ESTATUS_RECHAZADO,
            'consultado' => Predio::CONSULTADO_NO_EXISTE,
            'motivo_rechazo' => 'El predio registrado no existe en el sistema de predial.',
        ]);

        return response()->json([
            'existe' => false,
            'message' => 'La cuenta predial no existe en el sistema de predial.',
        ]);
    }

    /**
     * Extrae el booleano de la respuesta del API de predial
     * ({"respuesta": true|false}). Devuelve null si no se pudo interpretar.
     */
    private function predioExisteEnPredial(Response $respuesta): ?bool
    {
        $datos = $respuesta->json();

        $valor = is_array($datos) ? ($datos['respuesta'] ?? null) : $datos;

        return is_bool($valor) ? $valor : null;
    }
}
