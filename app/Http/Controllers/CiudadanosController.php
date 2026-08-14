<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CiudadanosController extends Controller
{
    public function indexCiudadanos(): View
    {
        return view('ciudadanos.indexCiudadanos');
    }

    public function getCiudadanos(): JsonResponse
    {
        $ciudadanos = User::query()
            ->select(['id', 'name', 'email', 'email_verified_at', 'bloqueado', 'created_at'])
            ->get()
            ->map(function (User $ciudadano): array {
                return [
                    'id' => $ciudadano->id,
                    'nombre_completo' => $ciudadano->name,
                    'email' => $ciudadano->email,
                    'verificado' => $ciudadano->email_verified_at !== null,
                    'bloqueado' => $ciudadano->bloqueado,
                    'activo' => ! $ciudadano->bloqueado,
                    'fecha_registro' => $ciudadano->created_at?->format('d/m/Y'),
                ];
            })
            ->values();

        return response()->json($ciudadanos);
    }

    public function bloquearCiudadanos(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ciudadanos' => 'required|array',
            'ciudadanos.*' => 'required|integer|exists:users,id',
        ]);

        $bloqueados = User::whereIn('id', $validated['ciudadanos'])
            ->update(['bloqueado' => true]);

        return response()->json([
            'success' => true,
            'message' => "{$bloqueados} ciudadano(s) bloqueado(s) correctamente.",
        ]);
    }

    public function desbloquearCiudadanos(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'ciudadanos' => 'required|array',
            'ciudadanos.*' => 'required|integer|exists:users,id',
        ]);

        $desbloqueados = User::whereIn('id', $validated['ciudadanos'])
            ->update(['bloqueado' => false]);

        return response()->json([
            'success' => true,
            'message' => "{$desbloqueados} ciudadano(s) desbloqueado(s) correctamente.",
        ]);
    }
}
