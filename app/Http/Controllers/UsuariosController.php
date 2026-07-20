<?php

namespace App\Http\Controllers;

use App\Models\UsuarioAD;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class UsuariosController extends Controller
{
    public function indexUsuarios(): View
    {
        return view('usuarios.indexUsuarios');
    }

    public function getUsuariosAd(): JsonResponse
    {
        $usuario = auth('ad')->user();

        if (! $usuario || ! method_exists($usuario, 'getEncryptedPassword') || ! $usuario->getEncryptedPassword()) {
            return response()->json([
                'message' => 'No se encontró la contraseña de AD en la sesión. Vuelva a iniciar sesión.',
            ], 422);
        }

        try {
            $password = Crypt::decryptString($usuario->getEncryptedPassword());
        } catch (DecryptException $exception) {
            Log::warning('Unable to decrypt AD password for users list.', [
                'user_id' => $usuario->getAuthIdentifier(),
            ]);

            return response()->json([
                'message' => 'No fue posible recuperar las credenciales de AD. Vuelva a iniciar sesión.',
            ], 422);
        }

        try {
            $response = Http::timeout(10)
                ->connectTimeout(3)
                ->retry([100, 300], throw: false)
                ->post(config('services.active_directory.users_url'), [
                    'usuario' => $usuario->username,
                    'password' => $password,
                    'aplicativo' => config('services.active_directory.aplicativo'),
                ]);
        } catch (ConnectionException $exception) {
            Log::error('AD users connection error: '.$exception->getMessage());

            return response()->json([
                'message' => 'No fue posible conectar con el servicio de usuarios AD.',
            ], 502);
        }

        if (! $response->successful()) {
            Log::warning('AD users service returned an unsuccessful response.', [
                'status' => $response->status(),
            ]);

            return response()->json([
                'message' => 'El servicio de usuarios AD no respondió correctamente.',
            ], 502);
        }

        $payload = $response->json();

        if (! is_array($payload)) {
            return response()->json([
                'message' => 'El servicio de usuarios AD devolvió un formato inválido.',
            ], 502);
        }

        $usuarios = collect($payload)
            ->filter(fn (mixed $item): bool => is_array($item)
                && ($item['success'] ?? false) === true
                && isset($item['permisos'])
                && is_array($item['permisos']))
            ->map(function (array $item): array {
                $permisos = $item['permisos'];
                $nombreCompleto = collect([
                    $permisos['nombre'] ?? null,
                    $permisos['apaterno'] ?? null,
                    $permisos['amaterno'] ?? null,
                ])->filter()->implode(' ');

                return [
                    'id_usuario' => $permisos['id_usuario'] ?? null,
                    'username' => $permisos['username'] ?? '',
                    'nombre' => $permisos['nombre'] ?? '',
                    'apaterno' => $permisos['apaterno'] ?? '',
                    'amaterno' => $permisos['amaterno'] ?? '',
                    'nombre_completo' => $nombreCompleto,
                    'app' => $permisos['app'] ?? '',
                    'rol_id' => $permisos['rol_id'] ?? null,
                    'id_area' => $permisos['id_area'] ?? null,
                    'rol' => $permisos['rol'] ?? '',
                    'activo' => (bool) ($permisos['activo'] ?? false),
                ];
            })
            ->values();

        return response()->json($usuarios);
    }

    public function asignarDependencia(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'usuarios' => 'required|array',
            'usuarios.*' => 'required|string',
            'fk_dependencia' => 'required|integer|exists:cat_dependencias,id_dependencia',
        ]);

        $asignados = 0;
        foreach ($validated['usuarios'] as $username) {
            UsuarioAD::updateOrCreate(
                ['nombre_usuario' => $username],
                ['fk_dependencia' => $validated['fk_dependencia']]
            );
            $asignados++;
        }

        return response()->json([
            'success' => true,
            'message' => "Dependencia asignada a {$asignados} usuario(s) correctamente.",
        ]);
    }
}
