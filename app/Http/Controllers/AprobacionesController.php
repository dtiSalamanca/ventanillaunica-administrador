<?php

namespace App\Http\Controllers;

use App\Mail\DocumentoRechazado;
use App\Mail\PredioRevisado;
use App\Models\DocumentoPredio;
use App\Models\Predio;
use App\Models\tblDocumentoPersonal;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AprobacionesController extends Controller
{
    public function indexDocumentosPersonales(Request $request): View
    {
        $pendientesQuery = $request->query('pendientesQ');
        $sinPendientesQuery = $request->query('sinPendientesQ');

        $pendientes = $this->pendientesPaginator($pendientesQuery);
        $sinPendientes = $this->sinPendientesPaginator($sinPendientesQuery);

        return view('aprobaciones.aprobacionDocumentosPersonales', compact('pendientes', 'sinPendientes', 'pendientesQuery', 'sinPendientesQuery'));
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
            $query->where('estatus_documento', tblDocumentoPersonal::ESTATUS_EN_REVISION);
        })
            ->when($search, fn (Builder $query, string $search) => $this->filtrarPorNombreOCorreo($query, $search))
            ->with(['documentosPersonales' => function ($query) {
                $query->where('estatus_documento', tblDocumentoPersonal::ESTATUS_EN_REVISION)
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
                $query->where('estatus_documento', tblDocumentoPersonal::ESTATUS_EN_REVISION);
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

    public function aprobarDocumentoPersonal(tblDocumentoPersonal $documentoPersonal): JsonResponse
    {
        $documentoPersonal->update([
            'estatus_documento' => tblDocumentoPersonal::ESTATUS_APROBADO,
            // La vigencia del documento corre a partir de la aprobación.
            'fecha_aprobacion' => now(),
        ]);

        return response()->json(['message' => 'Documento aprobado correctamente.']);
    }

    public function rechazarDocumentoPersonal(Request $request, tblDocumentoPersonal $documentoPersonal): JsonResponse
    {
        $motivo = $request->validate([
            'motivo' => ['required', 'string', 'max:500'],
        ])['motivo'];

        $documentoPersonal->update([
            'estatus_documento' => tblDocumentoPersonal::ESTATUS_RECHAZADO,
            'motivo_rechazo' => $motivo,
        ]);

        $this->notificarRechazoDocumento(
            $documentoPersonal->usuario,
            $documentoPersonal->catalogoDocumento->nombre_documento,
            $motivo,
        );

        return response()->json(['message' => 'Documento rechazado y notificado correctamente.']);
    }

    public function indexPredios(): View
    {
        $sinPendientesQuery = request()->query('sinPendientesQ');
        $pendientesQuery = request()->query('pendientesQ');

        $pendientes = $this->pendientesPrediosPaginator($pendientesQuery);
        $sinPendientes = $this->sinPendientesPrediosPaginator($sinPendientesQuery);

        return view('aprobaciones.aprobacionPredios', compact('pendientes', 'sinPendientes', 'pendientesQuery', 'sinPendientesQuery'));
    }

    public function buscarPredios(Request $request): JsonResponse
    {
        $tab = $request->query('tab', 'pendientes');

        if ($tab === 'sin-pendientes') {
            $query = $request->query('sinPendientesQ');
            $usuarios = $this->sinPendientesPrediosPaginator($query);
            $pendiente = false;
            $prefijo = 'revisado';
        } else {
            $query = $request->query('pendientesQ');
            $usuarios = $this->pendientesPrediosPaginator($query);
            $pendiente = true;
            $prefijo = 'pendiente';
        }

        $html = view('aprobaciones.partials.gridUsuariosPredios', [
            'usuarios' => $usuarios,
            'pendiente' => $pendiente,
            'prefijo' => $prefijo,
            'query' => $query,
        ])->render();

        return response()->json(['html' => $html]);
    }

    /**
     * Condiciones para considerar un predio pendiente de revisión: está "en
     * revisión", "por revisar" (pendiente de validar) o tiene algún documento
     * en revisión.
     */
    private function scopePredioPendiente(Builder $query): void
    {
        $query->where(function (Builder $query) {
            $query->where('estatus_predio', Predio::ESTATUS_EN_REVISION)
                ->orWhere('estatus_predio', Predio::ESTATUS_POR_REVISAR)
                ->orWhereHas('documentos', function (Builder $query) {
                    $query->where('estatus_documento', DocumentoPredio::ESTATUS_EN_REVISION);
                });
        });
    }

    /**
     * Predios visibles en el admin: se ocultan (sin borrar) los que al ser
     * consultados resultaron inexistentes en el sistema de predial.
     */
    private function scopePredioVisible(Builder $query): void
    {
        $query->where('consultado', '!=', Predio::CONSULTADO_NO_EXISTE);
    }

    private function pendientesPrediosPaginator(?string $search)
    {
        return User::whereHas('predios', function (Builder $query) {
            $this->scopePredioVisible($query);
            $this->scopePredioPendiente($query);
        })
            ->when($search, fn (Builder $query, string $search) => $this->filtrarPorNombreOCorreo($query, $search))
            ->with(['predios' => function ($query) {
                $query->with('documentos.catalogoDocumento')
                    ->where('consultado', '!=', Predio::CONSULTADO_NO_EXISTE)
                    ->orderBy('clave_predio');
            }])
            ->orderBy('name')
            ->paginate(6, ['*'], 'pendientesPage')
            ->withQueryString();
    }

    private function sinPendientesPrediosPaginator(?string $search)
    {
        return User::whereHas('predios', fn (Builder $query) => $this->scopePredioVisible($query))
            ->whereDoesntHave('predios', fn (Builder $query) => $this->scopePredioPendiente($query))
            ->when($search, fn (Builder $query, string $search) => $this->filtrarPorNombreOCorreo($query, $search))
            ->with(['predios' => function ($query) {
                $query->with('documentos.catalogoDocumento')
                    ->where('consultado', '!=', Predio::CONSULTADO_NO_EXISTE)
                    ->orderBy('clave_predio');
            }])
            ->orderBy('name')
            ->paginate(6, ['*'], 'sinPendientesPage')
            ->withQueryString();
    }

    public function aprobarPredio(Predio $predio): JsonResponse
    {
        $predio->update(['estatus_predio' => Predio::ESTATUS_APROBADO]);

        Mail::to($predio->usuario->email)->send(new PredioRevisado($predio));

        return response()->json(['message' => 'Predio aprobado correctamente.']);
    }

    public function rechazarPredio(Request $request, Predio $predio): JsonResponse
    {
        $motivo = $request->validate([
            'motivo' => ['required', 'string', 'max:500'],
        ])['motivo'];

        $predio->update([
            'estatus_predio' => Predio::ESTATUS_RECHAZADO,
            'motivo_rechazo' => $motivo,
        ]);

        Mail::to($predio->usuario->email)->send(new PredioRevisado($predio, $motivo));

        return response()->json(['message' => 'Predio rechazado y notificado correctamente.']);
    }

    public function aprobarDocumentoPredio(DocumentoPredio $documentoPredio): JsonResponse
    {
        $documentoPredio->update([
            'estatus_documento' => DocumentoPredio::ESTATUS_APROBADO,
            // La vigencia del documento corre a partir de la aprobación.
            'fecha_aprobacion' => now(),
        ]);

        return response()->json(['message' => 'Documento de predio aprobado correctamente.']);
    }

    public function rechazarDocumentoPredio(Request $request, DocumentoPredio $documentoPredio): JsonResponse
    {
        $motivo = $request->validate([
            'motivo' => ['required', 'string', 'max:500'],
        ])['motivo'];

        $documentoPredio->update([
            'estatus_documento' => DocumentoPredio::ESTATUS_RECHAZADO,
            'motivo_rechazo' => $motivo,
        ]);

        $this->notificarRechazoDocumento(
            $documentoPredio->predio->usuario,
            $documentoPredio->catalogoDocumento->nombre_documento,
            $motivo,
        );

        return response()->json(['message' => 'Documento rechazado y notificado correctamente.']);
    }

    /**
     * Envía al ciudadano el correo de notificación de rechazo de un documento
     * (personal o de predio) con el motivo indicado.
     */
    private function notificarRechazoDocumento(User $usuario, string $nombreDocumento, string $motivo): void
    {
        Mail::to($usuario->email)->send(new DocumentoRechazado(
            nombreUsuario: $usuario->name,
            nombreDocumento: $nombreDocumento,
            motivoRechazo: $motivo,
            urlPortal: $this->urlPortalCiudadano(),
        ));
    }

    /**
     * URL del perfil del ciudadano en el portal de la Ventanilla Única.
     */
    private function urlPortalCiudadano(): string
    {
        return $this->urlCiudadano('/perfiles/mi-perfil');
    }

    /**
     * Construye una URL absoluta hacia el portal ciudadano garantizando que
     * incluya el esquema http(s), aunque la variable de entorno solo defina
     * el host y el puerto (p. ej. localhost:8001).
     */
    private function urlCiudadano(string $ruta): string
    {
        $base = rtrim((string) config('services.ventanilla_ciudadano.base_url'), '/');

        if (! preg_match('~^https?://~i', $base)) {
            $base = 'http://'.$base;
        }

        return $base.$ruta;
    }

    /**
     * Visualiza el archivo del documento personal consumiendo la API del
     * repositorio ventanillaunica-ciudadano, donde se almacenan los PDFs.
     * Devuelve el archivo inline para que el navegador lo muestre en su
     * visor nativo dentro de una pestaña nueva.
     */
    public function visualizarDocumentoPersonal(tblDocumentoPersonal $documentoPersonal): Response
    {
        return $this->visualizarDocumentoCiudadano(
            "/api/documentos-personales/{$documentoPersonal->id_documento}/archivo",
            $documentoPersonal->catalogoDocumento->nombre_documento,
        );
    }

    /**
     * Visualiza el archivo del documento de predio consumiendo la API del
     * repositorio ventanillaunica-ciudadano, donde se almacenan los PDFs.
     * Devuelve el archivo inline para que el navegador lo muestre en su
     * visor nativo dentro de una pestaña nueva.
     */
    public function visualizarDocumentoPredio(DocumentoPredio $documentoPredio): Response
    {
        return $this->visualizarDocumentoCiudadano(
            "/api/documentos-predios/{$documentoPredio->id_documento_predio}/archivo",
            $documentoPredio->catalogoDocumento->nombre_documento,
        );
    }

    private function visualizarDocumentoCiudadano(string $rutaApi, string $nombreDocumento): Response
    {
        $url = $this->urlCiudadano($rutaApi);

        try {
            $respuesta = Http::withHeaders([
                'X-Api-Token' => config('services.ventanilla_ciudadano.api_token'),
            ])
                ->timeout(15)
                ->connectTimeout(5)
                ->get($url);
        } catch (ConnectionException) {
            return $this->respuestaErrorVisualizacion(
                'No se pudo conectar con el servicio de documentos.',
            );
        }

        if (! $respuesta->successful()) {
            return $this->respuestaErrorVisualizacion(
                'El documento no está disponible en este momento.',
            );
        }

        $nombreArchivo = Str::slug($nombreDocumento).'.pdf';

        return response($respuesta->body(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$nombreArchivo.'"',
        ]);
    }

    /**
     * Página HTML mínima para reportar un error al visualizar el documento
     * dentro de la pestaña nueva abierta por el navegador.
     */
    private function respuestaErrorVisualizacion(string $mensaje): Response
    {
        $html = '<!DOCTYPE html><html lang="es"><head><meta charset="utf-8">'
            .'<meta name="viewport" content="width=device-width, initial-scale=1">'
            .'<title>Documento no disponible</title>'
            .'<style>body{font-family:system-ui,Arial,sans-serif;display:flex;'
            .'align-items:center;justify-content:center;height:100vh;margin:0;'
            .'background:#f8fafc;color:#334155;text-align:center}'
            .'.card{background:#fff;padding:2.5rem 3rem;border-radius:12px;'
            .'box-shadow:0 4px 16px rgba(0,0,0,.08);max-width:420px}'
            .'i{font-size:3rem;color:#ef4444;margin-bottom:1rem;display:block}'
            .'</style></head><body><div class="card">'
            .'<i>&#9888;</i><h2>Documento no disponible</h2>'
            .'<p>'.$mensaje.'</p>'
            .'<p style="color:#94a3b8;font-size:.9rem;margin-top:1.5rem">'
            .'Puedes cerrar esta pestaña e intentarlo nuevamente.</p>'
            .'</div></body></html>';

        return response($html, 502, [
            'Content-Type' => 'text/html; charset=UTF-8',
        ]);
    }
}
