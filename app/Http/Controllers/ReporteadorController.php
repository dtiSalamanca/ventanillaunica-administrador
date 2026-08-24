<?php

namespace App\Http\Controllers;

use App\Models\Dependencia;
use App\Models\Tramite;
use App\Models\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ReporteadorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Panel del reporteador con indicadores generales.
     */
    public function indexReporteador(): Renderable
    {
        $conteos = $this->conteosPorEstatus();

        $totalSolicitudes = (int) $conteos->sum();
        $solicitudesPendientes = (int) $conteos->get(0, 0);
        $solicitudesTurnadas = (int) $conteos->get(1, 0);
        $solicitudesRechazadas = (int) $conteos->get(2, 0);
        $solicitudesPorPagar = (int) $conteos->get(3, 0);
        $solicitudesCompletadas = (int) $conteos->get(4, 0) + (int) $conteos->get(5, 0);

        $totalTramites = Tramite::count();
        $totalCiudadanos = User::count();
        $totalDependencias = DB::table('cat_dependencias')->count();

        $dependencias = Dependencia::where('estatus_dependencia', true)
            ->orderBy('nombre_dependencia')
            ->get(['id_dependencia', 'nombre_dependencia']);

        return view('reporteador.indexReporteador', compact(
            'totalSolicitudes',
            'solicitudesPendientes',
            'solicitudesTurnadas',
            'solicitudesRechazadas',
            'solicitudesPorPagar',
            'solicitudesCompletadas',
            'totalTramites',
            'totalCiudadanos',
            'totalDependencias',
            'dependencias',
        ));
    }

    /**
     * Datos AJAX de solicitudes para el reporte, respetando los filtros.
     */
    public function getReporteadorSolicitudes(Request $request): JsonResponse
    {
        $solicitudes = $this->solicitudesParaReporte($request)
            ->map(fn ($s) => [
                'id_solicitud' => $s->id_solicitud,
                'nombre_tramite' => $s->nombre_tramite,
                'nombre_dependencia' => $s->nombre_dependencia,
                'nombre_usuario' => $s->nombre_usuario,
                'fecha_solicitud' => $s->fecha_solicitud,
                'estatus_mostrado' => $s->estatus_mostrado,
            ])
            ->values();

        return response()->json($solicitudes);
    }

    /**
     * Genera el reporte de solicitudes en Excel (XLSX) aplicando los filtros.
     */
    public function generarReporteExcel(Request $request)
    {
        $solicitudes = $this->solicitudesParaReporte($request);

        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Reporte de solicitudes');

        $colorGuinda = '601028';
        $colorEncabezado = '164A3F';
        $colorBorde = 'D5DBDB';

        // ── Título ──
        $sheet->setCellValue('A1', 'Reporte de solicitudes — Ventanilla Única de Salamanca');
        $sheet->mergeCells('A1:M1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->getColor()->setARGB($colorGuinda);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension(1)->setRowHeight(28);

        // ── Criterios y fecha de generación ──
        $sheet->setCellValue('A2', 'Criterios: '.$this->textoFiltros($request).'   |   Generado: '.now()->format('d/m/Y h:i A'));
        $sheet->mergeCells('A2:M2');
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10)->getColor()->setARGB('6B7280');
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // ── Encabezados ──
        $encabezados = [
            '# Solicitud',
            'Trámite',
            'Dependencia',
            'Ciudadano',
            'Correo electrónico',
            'Fecha de solicitud',
            'Estado',
            'Precio (MXN)',
            '¿Abonado?',
            'Folio de pago',
            'Motivo del rechazo',
            'Autorizado por',
            'Fecha de resolución',
        ];

        $filaEncabezado = 4;
        foreach ($encabezados as $indice => $encabezado) {
            $sheet->setCellValue($this->letraColumna($indice + 1).$filaEncabezado, $encabezado);
        }

        $sheet->getStyle("A{$filaEncabezado}:M{$filaEncabezado}")
            ->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle("A{$filaEncabezado}:M{$filaEncabezado}")
            ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB($colorEncabezado);
        $sheet->getStyle("A{$filaEncabezado}:M{$filaEncabezado}")
            ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getRowDimension($filaEncabezado)->setRowHeight(24);

        // ── Datos ──
        $fila = $filaEncabezado + 1;
        foreach ($solicitudes as $solicitud) {
            $sheet->setCellValue("A{$fila}", $solicitud->id_solicitud);
            $sheet->setCellValue("B{$fila}", $solicitud->nombre_tramite);
            $sheet->setCellValue("C{$fila}", $solicitud->nombre_dependencia);
            $sheet->setCellValue("D{$fila}", $solicitud->nombre_usuario);
            $sheet->setCellValue("E{$fila}", $solicitud->email_usuario);
            $sheet->setCellValue("F{$fila}", $solicitud->fecha_solicitud ? $this->formatearFecha($solicitud->fecha_solicitud) : '—');
            $sheet->setCellValue("G{$fila}", $this->nombreEstatus($solicitud->estatus_mostrado));
            $sheet->setCellValue("H{$fila}", $solicitud->precio_tramite !== null ? (float) $solicitud->precio_tramite : '—');
            $sheet->setCellValue("I{$fila}", $this->textoAbonado($solicitud));
            $sheet->setCellValue("J{$fila}", $solicitud->folio_pago ?? '—');
            $sheet->setCellValue("K{$fila}", $solicitud->observacion_solicitud ?? '—');
            $sheet->setCellValue("L{$fila}", $solicitud->autorizado_por ?? '—');
            $sheet->setCellValue("M{$fila}", $solicitud->fecha_resolucion ? $this->formatearFecha($solicitud->fecha_resolucion) : '—');

            // Filas con color alternado
            if ($fila % 2 === 0) {
                $sheet->getStyle("A{$fila}:M{$fila}")
                    ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('F3F4F6');
            }

            $fila++;
        }

        $ultimaFila = $fila - 1;

        // ── Bordes ──
        $sheet->getStyle("A{$filaEncabezado}:M{$ultimaFila}")
            ->getBorders()->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN)
            ->getColor()->setARGB($colorBorde);

        // ── Ancho de columnas ──
        $anchos = [12, 32, 30, 26, 30, 18, 14, 13, 18, 20, 34, 18, 18];
        foreach ($anchos as $indice => $ancho) {
            $sheet->getColumnDimension($this->letraColumna($indice + 1))->setWidth($ancho);
        }

        // ── Congelar encabezado y autofiltro ──
        $sheet->freezePane('A'.($filaEncabezado + 1));
        if ($ultimaFila > $filaEncabezado) {
            $sheet->setAutoFilter("A{$filaEncabezado}:M{$ultimaFila}");
        }

        $nombre = 'reporte_solicitudes_'.now()->format('Ymd_His').'.xlsx';

        return response()->streamDownload(
            fn () => (new Xlsx($spreadsheet))->save('php://output'),
            $nombre,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'],
        );
    }

    /**
     * Solicitudes listas para el reporte, con el estatus efectivo y los filtros
     * aplicados (dependencia, estado y rango de fechas).
     */
    private function solicitudesParaReporte(Request $request): Collection
    {
        $solicitudes = DB::table('tbl_solicitudes')
            ->join('cat_tramites', 'tbl_solicitudes.fk_tramite', '=', 'cat_tramites.id_tramite')
            ->join('cat_dependencias', 'cat_tramites.fk_dependencia', '=', 'cat_dependencias.id_dependencia')
            ->join('users', 'tbl_solicitudes.fk_usuario', '=', 'users.id')
            ->select(
                'tbl_solicitudes.*',
                'cat_tramites.nombre_tramite',
                'cat_tramites.sin_costo',
                'cat_dependencias.nombre_dependencia',
                'users.name as nombre_usuario',
                'users.email as email_usuario',
                DB::raw('(SELECT o.precio_tramite FROM ordenes_pagos o WHERE o.fk_solicitud = tbl_solicitudes.id_solicitud ORDER BY o.id_orden_pago DESC LIMIT 1) as precio_tramite'),
                DB::raw('(SELECT o.folio_pago FROM ordenes_pagos o WHERE o.fk_solicitud = tbl_solicitudes.id_solicitud ORDER BY o.id_orden_pago DESC LIMIT 1) as folio_pago'),
                DB::raw('(SELECT ua.nombre_usuario FROM tbl_turnados_solicitudes ts INNER JOIN tbl_resoluciones_solicitudes rs ON rs.fk_turnado = ts.id_turnado INNER JOIN tbl_usuarios_ad ua ON ua.id_usuario = ts.fk_usuario_ad WHERE ts.fk_solicitud = tbl_solicitudes.id_solicitud ORDER BY rs.id_resolucion DESC LIMIT 1) as autorizado_por'),
                DB::raw('(SELECT CASE WHEN COUNT(*) > 0 THEN 1 ELSE 0 END FROM tbl_turnados_solicitudes ts INNER JOIN tbl_resoluciones_solicitudes rs ON rs.fk_turnado = ts.id_turnado WHERE ts.fk_solicitud = tbl_solicitudes.id_solicitud) as has_resolucion')
            )
            ->when($request->filled('fk_dependencia'), fn ($q) => $q->where('cat_tramites.fk_dependencia', $request->integer('fk_dependencia')))
            ->when($request->filled('fecha_desde'), fn ($q) => $q->whereDate('tbl_solicitudes.fecha_solicitud', '>=', $request->input('fecha_desde')))
            ->when($request->filled('fecha_hasta'), fn ($q) => $q->whereDate('tbl_solicitudes.fecha_solicitud', '<=', $request->input('fecha_hasta')))
            ->orderByDesc('tbl_solicitudes.created_at')
            ->get()
            ->map(function ($solicitud) {
                // Trámite sin costo ya resuelto (con resolutivo): no genera orden de
                // pago ni folio, por lo que se muestra como Completado (4).
                $solicitud->estatus_mostrado = (int) $solicitud->sin_costo === 1 && (int) $solicitud->has_resolucion === 1
                    ? 4
                    : (int) $solicitud->estatus_solicitud;

                return $solicitud;
            });

        if ($request->filled('estatus')) {
            $estatus = (int) $request->input('estatus');
            $solicitudes = $solicitudes->filter(fn ($solicitud) => $solicitud->estatus_mostrado === $estatus);
        }

        return $solicitudes->values();
    }

    /**
     * Conteos de solicitudes por estatus efectivo. Los trámites sin costo ya
     * resueltos se cuentan como Completado (4), no como "Por pagar".
     */
    private function conteosPorEstatus(): Collection
    {
        return DB::table('tbl_solicitudes')
            ->join('cat_tramites', 'tbl_solicitudes.fk_tramite', '=', 'cat_tramites.id_tramite')
            ->selectRaw('
                CASE
                    WHEN cat_tramites.sin_costo = 1 AND EXISTS (
                        SELECT 1
                        FROM tbl_turnados_solicitudes ts
                        INNER JOIN tbl_resoluciones_solicitudes rs ON rs.fk_turnado = ts.id_turnado
                        WHERE ts.fk_solicitud = tbl_solicitudes.id_solicitud
                    ) THEN 4
                    ELSE tbl_solicitudes.estatus_solicitud
                END as estatus_mostrado,
                COUNT(*) as total
            ')
            ->groupBy('estatus_mostrado')
            ->pluck('total', 'estatus_mostrado');
    }

    private function textoAbonado(object $solicitud): string
    {
        if ((int) $solicitud->sin_costo === 1) {
            return 'No aplica (sin costo)';
        }

        return filled($solicitud->folio_pago) ? 'Sí' : 'No';
    }

    private function nombreEstatus(int $estatus): string
    {
        return match ($estatus) {
            0 => 'Pendiente',
            1 => 'Turnada',
            2 => 'Rechazada',
            3 => 'Por pagar',
            4, 5 => 'Completado',
            default => 'Desconocido',
        };
    }

    private function formatearFecha(string $fecha): string
    {
        return Carbon::parse($fecha)->format('d/m/Y h:i A');
    }

    private function textoFiltros(Request $request): string
    {
        $partes = [];

        if ($request->filled('fk_dependencia')) {
            $dependencia = Dependencia::find($request->integer('fk_dependencia'));
            $partes[] = 'Dependencia: '.($dependencia?->nombre_dependencia ?? $request->input('fk_dependencia'));
        }

        if ($request->filled('estatus')) {
            $partes[] = 'Estado: '.$this->nombreEstatus((int) $request->input('estatus'));
        }

        if ($request->filled('fecha_desde')) {
            $partes[] = 'Desde: '.Carbon::parse($request->input('fecha_desde'))->format('d/m/Y');
        }

        if ($request->filled('fecha_hasta')) {
            $partes[] = 'Hasta: '.Carbon::parse($request->input('fecha_hasta'))->format('d/m/Y');
        }

        return $partes === []
            ? 'Sin filtros (todas las solicitudes)'
            : implode('   |   ', $partes);
    }

    private function letraColumna(int $numero): string
    {
        $letra = '';
        while ($numero > 0) {
            $modulo = ($numero - 1) % 26;
            $letra = chr(65 + $modulo).$letra;
            $numero = intdiv($numero - 1, 26);
        }

        return $letra;
    }
}
