<?php

namespace App\Http\Controllers;

use App\Models\DocumentoPredio;
use App\Models\DocumentoSolicitud;
use App\Models\DocumentoTramite;
use App\Models\ResolucionSolicitud;
use App\Models\Solicitud;
use App\Models\TurnadoSolicitud;
use App\Models\UsuarioAD;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EnlaceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Dashboard del enlace.
     */
    public function home(): Renderable
    {
        $usuarioAd = $this->getUsuarioAd();

        $totalTurnados = 0;
        $turnadosPendientes = 0;
        $turnadosCompletados = 0;
        $turnadosRecientes = collect();

        if ($usuarioAd) {
            $totalTurnados = TurnadoSolicitud::where('fk_usuario_ad', $usuarioAd->id_usuario)->count();

            $turnadosPendientes = TurnadoSolicitud::where('fk_usuario_ad', $usuarioAd->id_usuario)
                ->where('estatus_turnado', false)
                ->count();

            $turnadosCompletados = TurnadoSolicitud::where('fk_usuario_ad', $usuarioAd->id_usuario)
                ->where('estatus_turnado', true)
                ->count();

            $turnadosRecientes = TurnadoSolicitud::with('solicitud.tramite')
                ->where('fk_usuario_ad', $usuarioAd->id_usuario)
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        }

        return view('home_enlace', compact(
            'totalTurnados',
            'turnadosPendientes',
            'turnadosCompletados',
            'turnadosRecientes',
        ));
    }

    /**
     * Vista de trámites turnados al enlace.
     */
    public function tramitesTurnados(): Renderable
    {
        return view('enlace.tramites_turnados');
    }

    /**
     * Datos AJAX de trámites turnados.
     */
    public function getTramitesTurnados(Request $request): JsonResponse
    {
        $usuarioAd = $this->getUsuarioAd();

        if (! $usuarioAd) {
            return response()->json([
                'data' => [],
            ]);
        }

        $turnados = TurnadoSolicitud::with([
            'solicitud.tramite.dependencia',
            'solicitud.user',
        ])
            ->where('fk_usuario_ad', $usuarioAd->id_usuario)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function (TurnadoSolicitud $turnado) {
                $solicitud = $turnado->solicitud;

                return [
                    'id_turnado' => $turnado->id_turnado,
                    'id_solicitud' => $solicitud?->id_solicitud,
                    'tramite' => $solicitud?->tramite?->nombre_tramite ?? '—',
                    'dependencia' => $solicitud?->tramite?->dependencia?->nombre_dependencia ?? '—',
                    'ciudadano' => $solicitud?->user?->name ?? '—',
                    'fecha_turnado' => $turnado->created_at?->format('d/m/Y H:i') ?? '—',
                    'estatus_solicitud' => $solicitud?->estatus_solicitud,
                    'estatus_turnado' => (bool) $turnado->estatus_turnado,
                ];
            });

        return response()->json([
            'data' => $turnados,
        ]);
    }

    /**
     * Ver detalles de un trámite turnado.
     */
    public function verDetalles(int $id): Renderable
    {
        $usuarioAd = $this->getUsuarioAd();

        // Verificar que el turnado pertenece al enlace autenticado
        $turnado = TurnadoSolicitud::where('id_turnado', $id)
            ->where('fk_usuario_ad', $usuarioAd?->id_usuario)
            ->firstOrFail();

        $solicitud = Solicitud::with(['tramite', 'user'])
            ->findOrFail($turnado->fk_solicitud);

        // Documentos subidos directamente a la solicitud
        $documentosSolicitud = DocumentoSolicitud::where('fk_solicitud', $solicitud->id_solicitud)->get();

        // Requisitos del trámite con los documentos aportados
        $requisitosTramite = DocumentoTramite::where('fk_solicitud', $solicitud->id_solicitud)
            ->with(['requisito', 'documentoSolicitud', 'documentoPersonal'])
            ->get();

        // Documentos de predio del usuario (para requisitos tipo predio)
        $usuario = $solicitud->user;
        $predioDocs = collect();
        if ($usuario) {
            $predioDocs = DocumentoPredio::whereHas('predio', function ($q) use ($usuario) {
                $q->where('fk_usuario', $usuario->id);
            })->with('catalogoDocumento')->get();
        }

        // Resolución existente (si ya fue atendida o rechazada)
        $resolucion = ResolucionSolicitud::where('fk_turnado', $turnado->id_turnado)->first();

        return view('enlace.tramites_turnados_detalles', compact(
            'solicitud',
            'turnado',
            'documentosSolicitud',
            'requisitosTramite',
            'predioDocs',
            'resolucion',
        ));
    }

    /**
     * Aprobar (atender) un trámite turnado por parte del enlace.
     */
    public function aprobarSolicitud(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'resolucion_solicitud' => 'nullable|string|max:1000',
            'documento_resolucion' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
        ]);

        $usuarioAd = $this->getUsuarioAd();

        $turnado = TurnadoSolicitud::where('id_turnado', $id)
            ->where('fk_usuario_ad', $usuarioAd?->id_usuario)
            ->firstOrFail();

        $solicitud = Solicitud::findOrFail($turnado->fk_solicitud);
        $solicitud->estatus_solicitud = 3; // Atendido
        $solicitud->fecha_resolucion = now();
        $solicitud->save();

        $turnado->estatus_turnado = true;
        $turnado->save();

        // Guardar o actualizar resolución
        $resolucionData = [
            'fk_turnado' => $turnado->id_turnado,
            'resolucion_solicitud' => $request->input('resolucion_solicitud', 'Atendido'),
        ];

        $resolucion = ResolucionSolicitud::updateOrCreate(
            ['fk_turnado' => $turnado->id_turnado],
            $resolucionData,
        );

        if ($request->hasFile('documento_resolucion')) {
            $archivo = $request->file('documento_resolucion');
            $ext = $archivo->getClientOriginalExtension();
            $fecha = now()->format('Y-m-d');
            $nombre = "Res_{$resolucion->id_resolucion}_{$fecha}.{$ext}";
            $ruta = $archivo->storeAs('doc_resolutivos', $nombre, 'local');

            $resolucion->update(['documento_resolucion' => $ruta]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Solicitud atendida correctamente.',
        ]);
    }

    /**
     * Rechazar un trámite turnado por parte del enlace.
     */
    public function rechazarSolicitud(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'observacion_solicitud' => 'required|string|max:500',
        ]);

        $usuarioAd = $this->getUsuarioAd();

        $turnado = TurnadoSolicitud::where('id_turnado', $id)
            ->where('fk_usuario_ad', $usuarioAd?->id_usuario)
            ->firstOrFail();

        $solicitud = Solicitud::findOrFail($turnado->fk_solicitud);
        $solicitud->estatus_solicitud = 2; // Rechazada
        $solicitud->fecha_resolucion = now();
        $solicitud->observacion_solicitud = $request->input('observacion_solicitud');
        $solicitud->save();

        $turnado->estatus_turnado = true; // Marcamos como atendido (se rechazó)
        $turnado->save();

        // Guardar resolución de rechazo
        ResolucionSolicitud::updateOrCreate(
            ['fk_turnado' => $turnado->id_turnado],
            [
                'resolucion_solicitud' => 'Rechazado: '.$request->input('observacion_solicitud'),
            ],
        );

        return response()->json([
            'success' => true,
            'message' => 'Solicitud rechazada correctamente.',
        ]);
    }

    /**
     * Obtiene el registro local de UsuarioAD para el enlace autenticado.
     */
    private function getUsuarioAd(): ?UsuarioAD
    {
        $username = auth()->user()?->username;

        if (! $username) {
            return null;
        }

        return UsuarioAD::where('nombre_usuario', $username)->first();
    }
}
