<?php

namespace App\Http\Controllers;

use App\Models\Dependencia;
use App\Models\DocumentoPredio;
use App\Models\DocumentoSolicitud;
use App\Models\DocumentoTramite;
use App\Models\OrdenPago;
use App\Models\ResolucionSolicitud;
use App\Models\Solicitud;
use App\Models\TurnadoSolicitud;
use App\Models\UsuarioAD;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class SolicitudesController extends Controller
{
    /**
     * Devuelve todas las solicitudes con indicador de si tienen turnado.
     */
    public function getSolicitudesCompletas()
    {
        $solicitudes = DB::table('tbl_solicitudes')
            ->join('cat_tramites', 'tbl_solicitudes.fk_tramite', '=', 'cat_tramites.id_tramite')
            ->join('users', 'tbl_solicitudes.fk_usuario', '=', 'users.id')
            ->select(
                'tbl_solicitudes.*',
                'cat_tramites.nombre_tramite',
                'cat_tramites.sin_costo',
                'users.name as nombre_usuario',
                DB::raw('(SELECT CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END FROM tbl_turnados_solicitudes WHERE fk_solicitud = tbl_solicitudes.id_solicitud) as has_turnado'),
                DB::raw('(SELECT CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END FROM tbl_turnados_solicitudes ts INNER JOIN tbl_resoluciones_solicitudes rs ON rs.fk_turnado = ts.id_turnado WHERE ts.fk_solicitud = tbl_solicitudes.id_solicitud) as has_resolucion')
            )
            ->get();

        // Trámite sin costo ya resuelto (con resolutivo): no genera orden de pago ni
        // folio, por lo que se muestra como Completado (4) igual que el portal ciudadano.
        $solicitudes = $solicitudes->map(function ($solicitud) {
            $solicitud->estatus_mostrado = (int) $solicitud->sin_costo === 1 && (int) $solicitud->has_resolucion === 1
                ? 4
                : (int) $solicitud->estatus_solicitud;

            return $solicitud;
        });

        return response()->json($solicitudes);
    }

    public function verDetalles($id)
    {
        $solicitud = Solicitud::with(['tramite', 'user'])
            ->findOrFail($id);

        // Documentos subidos directamente a la solicitud
        $documentosSolicitud = DocumentoSolicitud::where('fk_solicitud', $id)->get();

        // Requisitos del trámite con los documentos aportados
        $requisitosTramite = DocumentoTramite::where('fk_solicitud', $id)
            ->with(['requisito', 'documentoSolicitud', 'documentoPersonal'])
            ->get();

        // Dependencias activas para el turnado
        $dependencias = Dependencia::where('estatus_dependencia', 1)->get();

        // Documentos de predio del usuario (para requisitos tipo predio)
        $usuario = $solicitud->user;
        $predioDocs = collect();
        if ($usuario) {
            $predioDocs = DocumentoPredio::whereHas('predio', function ($q) use ($usuario) {
                $q->where('fk_usuario', $usuario->id);
            })->with('catalogoDocumento')->get();
        }

        // Resolución del enlace (si fue atendida/rechazada por enlace)
        $turnado = TurnadoSolicitud::where('fk_solicitud', $solicitud->id_solicitud)->first();
        $resolucion = null;
        if ($turnado) {
            $resolucion = ResolucionSolicitud::where('fk_turnado', $turnado->id_turnado)->first();
        }

        // Orden de pago generada por el enlace (contiene el precio designado)
        $ordenPago = OrdenPago::where('fk_solicitud', $solicitud->id_solicitud)->latest('id_orden_pago')->first();

        // Nombre del registro CRI (mismo catálogo con el que se asigna el CRI al crear el trámite)
        $nombreCri = null;
        if ($ordenPago && $ordenPago->numero_cri) {
            $nombreCri = $this->resolverNombreCri((int) $ordenPago->numero_cri);
        }

        return view('solicitudes.ver_detalles', compact(
            'solicitud',
            'documentosSolicitud',
            'requisitosTramite',
            'dependencias',
            'predioDocs',
            'resolucion',
            'ordenPago',
            'nombreCri',
        ));
    }

    /**
     * Resuelve el nombre del registro CRI usando el mismo catálogo con el que se
     * asigna el CRI a los trámites al crearlos (consultaCuentasCri). El catálogo
     * se cachea una hora para no consultar la API interna en cada petición; si la
     * API no responde, devuelve null y se muestra solo el número.
     */
    private function resolverNombreCri(int $numeroCri): ?string
    {
        $catalogo = Cache::remember('catalogo_cri', now()->addHour(), function () {
            try {
                $response = Http::timeout(5)
                    ->connectTimeout(3)
                    ->get('http://172.17.5.214/sisalamanca/public/api/consultaCuentasCri');

                if (! $response->successful()) {
                    return [];
                }

                $payload = $response->json();

                if (is_array($payload) && isset($payload['data']) && is_array($payload['data'])) {
                    return $payload['data'];
                }

                return is_array($payload) ? $payload : [];
            } catch (\Throwable $exception) {
                return [];
            }
        });

        foreach ($catalogo as $item) {
            if (! is_array($item)) {
                continue;
            }

            $valor = $item['id'] ?? $item['id_cri'] ?? $item['value'] ?? $item['cri'] ?? null;

            if ((int) $valor !== $numeroCri) {
                continue;
            }

            // Mismo formato que el select "Elegir cuenta contable" al crear trámites:
            // "código de cuenta - nombre" (p. ej. "4111010001 - Juegos y Apuestas Permitidas")
            $codigo = $item['account_code'] ?? $item['codigo'] ?? $item['codigo_cuenta'] ?? null;
            $nombre = $item['account_name'] ?? $item['nombre'] ?? $item['descripcion'] ?? null;

            if (is_string($codigo) && $codigo !== '' && is_string($nombre) && $nombre !== '') {
                return "{$codigo} - {$nombre}";
            }

            return is_string($nombre) && $nombre !== '' ? $nombre : null;
        }

        return null;
    }

    public function getUsuariosPorDependencia(Request $request)
    {
        $request->validate([
            'dependencia_id' => 'required|integer|exists:cat_dependencias,id_dependencia',
        ]);

        $usuarios = UsuarioAD::where('fk_dependencia', $request->dependencia_id)->get();

        return response()->json($usuarios);
    }

    public function verDocumento(Request $request)
    {
        $request->validate([
            'ruta' => 'required|string',
        ]);

        $ruta = $request->input('ruta');

        // Limpiar la ruta por seguridad (evitar path traversal)
        $ruta = ltrim($ruta, '/\\');

        // Buscar primero en el disco local (este proyecto)
        if (Storage::disk('local')->exists($ruta)) {
            return Storage::disk('local')->response($ruta);
        }

        // Si no está en local, buscar en el disco del proyecto ciudadano
        if (Storage::disk('documentos_ciudadano')->exists($ruta)) {
            return Storage::disk('documentos_ciudadano')->response($ruta);
        }

        abort(404, 'El archivo no existe.');
    }

    public function aprobarSolicitud(Request $request, $id)
    {
        $request->validate([
            'fk_usuario_ad' => 'required|integer|exists:tbl_usuarios_ad,id_usuario',
        ]);

        $solicitud = Solicitud::findOrFail($id);
        $solicitud->estatus_solicitud = 1;
        $solicitud->fecha_resolucion = now();
        $solicitud->save();

        // Crear turnado
        TurnadoSolicitud::create([
            'fk_usuario_ad' => $request->fk_usuario_ad,
            'fk_solicitud' => $solicitud->id_solicitud,
            'estatus_turnado' => 1,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Solicitud aprobada y turnada correctamente.',
        ]);
    }

    public function rechazarSolicitud(Request $request, $id)
    {
        $request->validate([
            'observacion_solicitud' => 'required|string|max:500',
        ]);

        $solicitud = Solicitud::findOrFail($id);
        $solicitud->estatus_solicitud = 2;
        $solicitud->fecha_resolucion = now();
        $solicitud->observacion_solicitud = $request->input('observacion_solicitud');
        $solicitud->save();

        return response()->json([
            'success' => true,
            'message' => 'Solicitud rechazada.',
        ]);
    }
}
