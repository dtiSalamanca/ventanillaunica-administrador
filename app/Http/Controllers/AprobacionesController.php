<?php

namespace App\Http\Controllers;

use App\Models\catDocumentoPersonal;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AprobacionesController extends Controller
{
    public function indexDocumentosPersonales(Request $request): View
    {
        $pendientesQuery = $request->query('pendientesQ');
        $sinPendientesQuery = $request->query('sinPendientesQ');

        $pendientes = $this->pendientesPaginator($pendientesQuery);
        $sinPendientes = $this->sinPendientesPaginator($sinPendientesQuery);

        return view('aprobaciones.indexDocumentosPersonales', compact('pendientes', 'sinPendientes', 'pendientesQuery', 'sinPendientesQuery'));
    }

    public function buscarDocumentosPersonales(Request $request): JsonResponse
    {
        $tab = $request->query('tab', 'pendientes');

        if ($tab === 'sin-pendientes') {
            $query = $request->query('sinPendientesQ');
            $usuarios = $this->sinPendientesPaginator($query);
            $pendiente = false;
            $prefijo = 'revisado';
        } else {
            $query = $request->query('pendientesQ');
            $usuarios = $this->pendientesPaginator($query);
            $pendiente = true;
            $prefijo = 'pendiente';
        }

        $html = view('aprobaciones.partials.gridUsuarios', [
            'usuarios' => $usuarios,
            'pendiente' => $pendiente,
            'prefijo' => $prefijo,
            'query' => $query,
        ])->render();

        return response()->json(['html' => $html]);
    }

    private function pendientesPaginator(?string $search)
    {
        return User::whereHas('documentosPersonales', function ($query) {
            $query->where('estatus_documento', catDocumentoPersonal::ESTATUS_EN_REVISION);
        })
            ->when($search, fn (Builder $query, string $search) => $this->filtrarPorNombreOCorreo($query, $search))
            ->with(['documentosPersonales' => function ($query) {
                $query->where('estatus_documento', catDocumentoPersonal::ESTATUS_EN_REVISION)
                    ->with('catalogoDocumento')
                    ->orderBy('fecha_registro');
            }])
            ->orderBy('name')
            ->paginate(6, ['*'], 'pendientesPage')
            ->withQueryString();
    }

    private function sinPendientesPaginator(?string $search)
    {
        return User::whereHas('documentosPersonales')
            ->whereDoesntHave('documentosPersonales', function ($query) {
                $query->where('estatus_documento', catDocumentoPersonal::ESTATUS_EN_REVISION);
            })
            ->when($search, fn (Builder $query, string $search) => $this->filtrarPorNombreOCorreo($query, $search))
            ->with(['documentosPersonales' => function ($query) {
                $query->with('catalogoDocumento')->orderBy('fecha_registro');
            }])
            ->orderBy('name')
            ->paginate(6, ['*'], 'sinPendientesPage')
            ->withQueryString();
    }

    private function filtrarPorNombreOCorreo(Builder $query, string $search): Builder
    {
        return $query->where(function (Builder $query) use ($search) {
            $query->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        });
    }

    public function aprobarDocumentoPersonal(catDocumentoPersonal $documentoPersonal): JsonResponse
    {
        $documentoPersonal->update(['estatus_documento' => catDocumentoPersonal::ESTATUS_APROBADO]);

        return response()->json(['message' => 'Documento aprobado correctamente.']);
    }

    public function rechazarDocumentoPersonal(catDocumentoPersonal $documentoPersonal): JsonResponse
    {
        $documentoPersonal->update(['estatus_documento' => catDocumentoPersonal::ESTATUS_RECHAZADO]);

        return response()->json(['message' => 'Documento rechazado correctamente.']);
    }
}
