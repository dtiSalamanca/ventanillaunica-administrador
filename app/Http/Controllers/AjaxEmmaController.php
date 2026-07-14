<?php

namespace App\Http\Controllers;

use App\Models\Solicitud;
use Illuminate\Http\Request;

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
}
