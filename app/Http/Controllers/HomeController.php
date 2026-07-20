<?php

namespace App\Http\Controllers;

use App\Models\DocumentoPredio;
use App\Models\Solicitud;
use App\Models\tblDocumentoPersonal;
use App\Models\Tramite;
use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public function index()
    {
        $totalSolicitudes = Solicitud::count();
        $solicitudesPendientes = Solicitud::where('estatus_solicitud', 0)->count();
        $solicitudesEnProceso = Solicitud::where('estatus_solicitud', 1)->count();

        $documentosPendientes = tblDocumentoPersonal::where('estatus_documento', tblDocumentoPersonal::ESTATUS_EN_REVISION)->count();
        $documentosPrediosPendientes = DocumentoPredio::where('estatus_documento', DocumentoPredio::ESTATUS_EN_REVISION)->count();

        $totalTramites = Tramite::count();
        $totalUsuarios = User::count();

        $solicitudesRecientes = Solicitud::with('tramite')
            ->where('estatus_solicitud', 0)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $totalDependencias = DB::table('cat_dependencias')->count();
        $totalRequisitos = DB::table('cat_requisitos')->count();

        return view('home', compact(
            'totalSolicitudes',
            'solicitudesPendientes',
            'solicitudesEnProceso',
            'documentosPendientes',
            'documentosPrediosPendientes',
            'totalTramites',
            'totalUsuarios',
            'solicitudesRecientes',
            'totalDependencias',
            'totalRequisitos',
        ));
    }
}
