<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OrdenPago;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiOrdenesPagoController extends Controller
{
    /**
     * Lista de órdenes de pago con ciudadano, dependencia y monto.
     *
     * La consume el sistema externo de pagos para mostrar las órdenes.
     */
    public function index(): JsonResponse
    {
        $ordenes = OrdenPago::query()
            ->with(['solicitud.user', 'tramite.dependencia'])
            ->where('orden_estatus', 1) // Pendiente (no pagada)
            ->whereNull('folio_pago') // Aún sin folio asignado
            ->orderByDesc('id_orden_pago')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Órdenes de pago obtenidas correctamente.',
            'data' => $ordenes->map(fn (OrdenPago $orden) => [
                'id_orden_pago' => $orden->id_orden_pago,
                'nombre_ciudadano' => $orden->solicitud?->user?->name,
                'dependencia' => $orden->tramite?->dependencia?->nombre_dependencia,
                'monto' => (float) $orden->precio_tramite,
                'orden_estatus' => $orden->orden_estatus,
                'folio_pago' => $orden->folio_pago,
            ]),
        ]);
    }

    /**
     * Detalle de una orden de pago: descripción (acrónimo + folio resolutivo) y CRI.
     */
    public function show(int $idOrdenPago): JsonResponse
    {
        $orden = OrdenPago::query()
            ->with(['solicitud.user', 'tramite.dependencia'])
            ->find($idOrdenPago);

        if ($orden === null) {
            return response()->json([
                'success' => false,
                'message' => 'La orden de pago no existe.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Orden de pago obtenida correctamente.',
            'data' => [
                'id_orden_pago' => $orden->id_orden_pago,
                'nombre_ciudadano' => $orden->solicitud?->user?->name,
                'dependencia' => $orden->tramite?->dependencia?->nombre_dependencia,
                'monto' => (float) $orden->precio_tramite,
                'orden_estatus' => $orden->orden_estatus,
                'folio_pago' => $orden->folio_pago,
                'descripcion' => $orden->descripcionResolutivo(),
                'cri' => $orden->tramite?->tramite_cri ?? $orden->numero_cri,
            ],
        ]);
    }

    /**
     * Guarda el pago: asigna el folio a una orden.
     *
     * Lo invoca el sistema externo de pagos para asociar el folio generado
     * a la orden de pago correspondiente. Solo actualiza el folio, no el estatus.
     */
    public function aplicarFolio(Request $request, int $idOrdenPago): JsonResponse
    {
        $request->validate([
            'folio' => ['required', 'string', 'max:255'],
        ]);

        $orden = OrdenPago::find($idOrdenPago);

        if ($orden === null) {
            return response()->json([
                'success' => false,
                'message' => 'La orden de pago no existe.',
            ], 404);
        }

        $orden->update([
            'folio_pago' => $request->input('folio'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Folio asignado correctamente.',
            'data' => [
                'id_orden_pago' => $orden->id_orden_pago,
                'folio_pago' => $orden->folio_pago,
                'orden_estatus' => $orden->orden_estatus,
            ],
        ]);
    }
}
