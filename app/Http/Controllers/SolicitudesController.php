<?php

namespace App\Http\Controllers;

use App\Models\Dependencia;
use App\Models\DocumentoPredio;
use App\Models\DocumentoSolicitud;
use App\Models\DocumentoTramite;
use App\Models\ResolucionSolicitud;
use App\Models\Solicitud;
use App\Models\TurnadoSolicitud;
use App\Models\UsuarioAD;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
                'users.name as nombre_usuario',
                DB::raw('(SELECT CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END FROM tbl_turnados_solicitudes WHERE fk_solicitud = tbl_solicitudes.id_solicitud) as has_turnado')
            )
            ->get();

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

        return view('solicitudes.ver_detalles', compact(
            'solicitud',
            'documentosSolicitud',
            'requisitosTramite',
            'dependencias',
            'predioDocs',
            'resolucion',
        ));
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
