<?php

namespace App\Http\Controllers;

use App\Models\Predio;
use App\Models\Solicitud;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AjaxEmmaController extends Controller
{
    //
    public function solicitudes(Request $request)
    {
        // Aquí puedes implementar la lógica para manejar la solicitud AJAX
        // Por ejemplo, podrías devolver una lista de solicitudes en formato JSON
        $solicitudes = Solicitud::join('cat_tramites', 'tbl_solicitudes.fk_tramite', '=', 'cat_tramites.id_tramite')
            ->join('users', 'tbl_solicitudes.fk_usuario', '=', 'users.id')
            ->select('tbl_solicitudes.*', 'cat_tramites.nombre_tramite', 'users.name as nombre_usuario')
            ->get();

        return response()->json($solicitudes);
    }

    public function consultaSolicitud($id)
    {
        $solicitud = Solicitud::findOrFail($id);
        $prediosIds = Predio::where('fk_usuario', $solicitud->fk_usuario)->pluck('id_predio');

        $documentos = DB::table('tbl_requisitos_tramites as rt')
            ->join('cat_requisitos as r', 'r.id_requisito', '=', 'rt.fk_requisito')
            ->leftJoin('tbl_documentos_personales as dp', function ($join) use ($solicitud) {
                $join->on('dp.fk_requisito', '=', 'r.id_requisito')
                    ->where('dp.fk_usuario', $solicitud->fk_usuario)
                    ->where('dp.estatus_documento', '!=', 0);
            })
            ->leftJoin('tbl_documentos_predios as dpr', function ($join) use ($prediosIds) {
                $join->on('dpr.fk_requisito', '=', 'r.id_requisito')
                    ->whereIn('dpr.fk_predio', $prediosIds)
                    ->where('dpr.estatus_documento', '!=', 0);
            })
            ->where('rt.fk_tramite', $solicitud->fk_tramite)
            ->select(
                'r.id_requisito',
                'r.nombre_requisito',
                DB::raw('MAX(CASE WHEN dp.id_documento IS NOT NULL OR dpr.id_documento_predio IS NOT NULL THEN 1 ELSE 0 END) as entregado'),
                DB::raw('COALESCE(MAX(dp.ruta_archivo), MAX(dpr.ruta_documento)) as ruta_documento')
            )
            ->groupBy('r.id_requisito', 'r.nombre_requisito')
            ->get();

        return response()->json($documentos);
    }

    public function consultaUsuarios()
    {
        $usuarios = DB::table('tbl_usuarios_ad')->select('id_usuario', 'nombre_usuario')->get();
        return response()->json($usuarios);
    }
}
