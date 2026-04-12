<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Informe de Análisis de Ventas — iieEdu</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: DejaVu Sans, Arial, sans-serif;
            background: #f5f4ee;
            color: #2d2d2d;
            font-size: 11px;
            line-height: 1.4;
        }

        .page {
            background: #f5f4ee;
            padding: 36px 44px 80px;
        }

        /* ══ TÍTULO PRINCIPAL ══════════════════════════════════════ */
        .main-title {
            text-align: center;
            margin-bottom: 32px;
        }
        .main-title h1 {
            font-size: 26px;
            font-weight: bold;
            color: #8a8a5a;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            line-height: 1.3;
        }
        .main-title .subtitle {
            font-size: 12px;
            color: #9a9a6a;
            margin-top: 4px;
            font-style: italic;
            letter-spacing: 0.1em;
        }

        /* ══ PANEL BLANCO CON SOMBRA ════════════════════════════════ */
        .panel {
            background: #fff;
            border-radius: 8px;
            padding: 20px 24px;
            margin-bottom: 20px;
            box-shadow: 0 1px 6px rgba(0,0,0,0.06);
        }
        .panel-title {
            font-size: 12px;
            font-weight: bold;
            color: #57572A;
            margin-bottom: 14px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e7e6ab;
        }
        .panel-subtitle {
            font-size: 9px;
            color: #9ca3af;
            font-style: italic;
            float: right;
            font-weight: normal;
        }

        /* ══ TABLA MAESTRA ══════════════════════════════════════════ */
        .master-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        .master-table thead tr th {
            background: #57572A;
            color: #e7e6ab;
            padding: 7px 8px;
            text-align: center;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            border: 1px solid #6a6a30;
        }
        .master-table thead tr th:first-child {
            text-align: left;
            padding-left: 10px;
        }
        .master-table tbody tr td {
            padding: 6px 8px;
            text-align: center;
            border: 1px solid #e5e7eb;
            font-size: 10px;
        }
        .master-table tbody tr td:first-child {
            text-align: left;
            font-weight: bold;
            padding-left: 10px;
            background: #f9f9f5;
            color: #374151;
        }
        .master-table tbody tr:nth-child(odd) td { background: #fafafa; }
        .master-table tbody tr:nth-child(odd) td:first-child { background: #f9f9f5; }
        .master-table tbody tr.total-row td {
            background: #e7e6ab;
            font-weight: bold;
            color: #1f2937;
            border-top: 2px solid #57572A;
        }
        .master-table tbody tr.total-row td:first-child {
            background: #e7e6ab;
            color: #57572A;
        }
        .amount { color: #57572A; font-weight: bold; }
        .amount-alt { color: #b45309; font-weight: bold; }

        /* ══ GRÁFICOS DE BARRAS HTML ════════════════════════════════ */
        .charts-row {
            width: 100%;
        }
        .chart-col-left {
            width: 60%;
            display: inline-block;
            vertical-align: top;
            padding-right: 12px;
        }
        .chart-col-right {
            width: 38%;
            display: inline-block;
            vertical-align: top;
        }
        .bar-chart-title {
            font-size: 10px;
            font-weight: bold;
            color: #374151;
            margin-bottom: 10px;
        }
        .bar-group {
            margin-bottom: 8px;
        }
        .bar-label {
            font-size: 9px;
            color: #6b7280;
            margin-bottom: 3px;
            font-weight: bold;
        }
        .bar-row {
            display: flex;
            align-items: center;
            gap: 4px;
            margin-bottom: 2px;
        }
        .bar-legend-dot {
            width: 8px;
            height: 8px;
            border-radius: 2px;
            display: inline-block;
            flex-shrink: 0;
        }
        .bar-track {
            flex: 1;
            background: #f3f4f6;
            border-radius: 3px;
            height: 12px;
            position: relative;
        }
        .bar-fill {
            height: 100%;
            border-radius: 3px;
        }
        .bar-value {
            font-size: 9px;
            font-weight: bold;
            color: #374151;
            width: 70px;
            text-align: right;
        }
        .color-total    { background: #57572A; }
        .color-subs     { background: #a1a166; }
        .color-courses  { background: #d97706; }
        .color-certs    { background: #7c3aed; }
        .color-users    { background: #0891b2; }

        /* ══ KPI GRID PEQUEÑO ═══════════════════════════════════════ */
        .kpi-small-grid {
            width: 100%;
        }
        .kpi-small {
            width: 23%;
            display: inline-block;
            vertical-align: top;
            margin-right: 2%;
            background: #f9f9f5;
            border: 1px solid #e7e6ab;
            border-top: 3px solid #57572A;
            border-radius: 6px;
            padding: 10px 12px;
            text-align: center;
        }
        .kpi-small:last-child { margin-right: 0; }
        .kpi-small.dark {
            background: #1a1a10;
            border-color: #1a1a10;
            border-top-color: #a1a166;
        }
        .kpi-small-label {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #9ca3af;
            margin-bottom: 5px;
        }
        .kpi-small.dark .kpi-small-label { color: #a1a166; }
        .kpi-small-value {
            font-size: 20px;
            font-weight: bold;
            color: #1f2937;
            line-height: 1;
        }
        .kpi-small.dark .kpi-small-value { color: #e7e6ab; }
        .kpi-small-sub {
            font-size: 8px;
            color: #9ca3af;
            margin-top: 3px;
        }
        .kpi-small.dark .kpi-small-sub { color: #57572A; }
        .badge-green {
            display: inline-block;
            background: #d1fae5;
            color: #059669;
            font-size: 8px;
            font-weight: bold;
            padding: 1px 6px;
            border-radius: 8px;
            margin-top: 4px;
        }
        .badge-amber {
            display: inline-block;
            background: #fef3c7;
            color: #d97706;
            font-size: 8px;
            font-weight: bold;
            padding: 1px 6px;
            border-radius: 8px;
            margin-top: 4px;
        }
        .badge-purple {
            display: inline-block;
            background: #ede9fe;
            color: #7c3aed;
            font-size: 8px;
            font-weight: bold;
            padding: 1px 6px;
            border-radius: 8px;
            margin-top: 4px;
        }

        /* ══ TABLA DE CURSOS ════════════════════════════════════════ */
        .course-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        .course-table thead th {
            background: #f3f4f6;
            padding: 8px 10px;
            font-size: 8px;
            font-weight: bold;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            border-bottom: 2px solid #e5e7eb;
            text-align: left;
        }
        .course-table thead th.right { text-align: right; }
        .course-table thead th.center { text-align: center; }
        .course-table tbody td {
            padding: 8px 10px;
            border-bottom: 1px solid #f3f4f6;
        }
        .course-table tbody tr:last-child td { border-bottom: none; }
        .rank-circle {
            display: inline-block;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            text-align: center;
            line-height: 20px;
            font-size: 9px;
            font-weight: bold;
            color: #fff;
            background: #57572A;
        }
        .rank-circle.r2 { background: #9ca3af; }
        .rank-circle.r3 { background: #d97706; }
        .pill-grabado {
            background: #e7e6ab;
            color: #57572A;
            font-size: 8px;
            font-weight: bold;
            padding: 2px 8px;
            border-radius: 8px;
        }
        .pill-vivo {
            background: #fed7aa;
            color: #c2410c;
            font-size: 8px;
            font-weight: bold;
            padding: 2px 8px;
            border-radius: 8px;
        }
        .progress-mini {
            width: 80px;
            height: 6px;
            background: #f3f4f6;
            border-radius: 3px;
            display: inline-block;
            vertical-align: middle;
        }
        .progress-mini-fill {
            height: 100%;
            background: #57572A;
            border-radius: 3px;
        }

        /* ══ RESUMEN EJECUTIVO ══════════════════════════════════════ */
        .exec-box {
            background: #fbf9f2;
            border-left: 4px solid #57572A;
            padding: 12px 16px;
            border-radius: 6px;
            font-size: 11px;
            color: #374151;
            line-height: 1.7;
        }

        /* ══ DONUT VISUAL HTML-ONLY ═════════════════════════════════ */
        .donut-row {
            width: 100%;
            margin-top: 10px;
        }
        .donut-item {
            width: 48%;
            display: inline-block;
            vertical-align: top;
            text-align: center;
        }
        .donut-circle {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            margin: 0 auto 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        .donut-pct {
            font-size: 18px;
            font-weight: bold;
        }
        .donut-label {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #6b7280;
        }

        /* ══ LEYENDA ════════════════════════════════════════════════ */
        .legend-row {
            margin-top: 10px;
        }
        .legend-item {
            display: inline-block;
            margin-right: 16px;
            font-size: 9px;
            color: #6b7280;
        }
        .legend-dot {
            width: 8px;
            height: 8px;
            border-radius: 2px;
            display: inline-block;
            vertical-align: middle;
            margin-right: 4px;
        }

        /* ══ FOOTER ═════════════════════════════════════════════════ */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: #57572A;
            color: #e7e6ab;
            padding: 8px 44px;
            font-size: 9px;
            display: flex;
            justify-content: space-between;
        }
        .divider { height: 1px; background: #e7e6ab; margin: 16px 0; opacity: 0.3; }
    </style>
</head>
<body>
<div class="page">

    <!-- ══ TÍTULO ══════════════════════════════════════════════════ -->
    <div class="main-title">
        <h1>Informe de Análisis de Gestión Académica</h1>
        <div class="subtitle">
            Instituto de Investigación y Educación · iieEdu &nbsp;·&nbsp;
            Generado el {{ now()->setTimezone('America/Lima')->format('d/m/Y \a \l\a\s H:i') }} hrs
        </div>
    </div>

    <!-- ══ RESUMEN EJECUTIVO ════════════════════════════════════════ -->
    <div class="panel">
        <div class="panel-title">
            Resumen Ejecutivo
            <span class="panel-subtitle">Período: {{ $monthName }}</span>
        </div>
        <div class="exec-box">
            Durante <strong>{{ $monthName }}</strong>, el ecosistema académico de <strong>iieEdu</strong> registra una
            facturación total acumulada de <strong>S/ {{ number_format($stats['totalIncome'], 2) }}</strong>,
            con <strong>{{ $stats['activeUsers'] }} alumnos activos</strong> y
            <strong>{{ $stats['premiumUsers'] }} membresías Premium</strong> vigentes.
            Los ingresos del mes en curso ascienden a <strong>S/ {{ number_format($stats['currentMonthIncome'], 2) }}</strong>
            (@if($stats['incomeGrowth'] >= 0)
                <span style="color:#059669; font-weight:bold;">▲ {{ number_format($stats['incomeGrowth'], 1) }}%</span>
            @else
                <span style="color:#dc2626; font-weight:bold;">▼ {{ number_format(abs($stats['incomeGrowth']), 1) }}%</span>
            @endif
            vs. mes anterior).
            El modelo de membresías aporta el
            <strong>{{ $stats['totalIncome'] > 0 ? number_format($stats['subIncome'] / $stats['totalIncome'] * 100, 1) : 0 }}%</strong>
            de la facturación total, mientras que la venta directa de cursos representa el
            <strong>{{ $stats['totalIncome'] > 0 ? number_format($stats['courseIncome'] / $stats['totalIncome'] * 100, 1) : 0 }}%</strong>.
            Se han emitido <strong>{{ $stats['certificatesIssued'] }}</strong> certificados académicos en total.
        </div>
    </div>

    <!-- ══ KPI RÁPIDOS ══════════════════════════════════════════════ -->
    <div class="panel">
        <div class="panel-title">Indicadores Clave de Rendimiento (KPI)</div>
        <div class="kpi-small-grid">
            <div class="kpi-small dark">
                <div class="kpi-small-label">Facturación Total</div>
                <div class="kpi-small-value">S/ {{ number_format($stats['totalIncome'], 0) }}</div>
                <div class="kpi-small-sub">Histórico acumulado</div>
            </div>
            <div class="kpi-small">
                <div class="kpi-small-label">Mes en Curso</div>
                <div class="kpi-small-value">S/ {{ number_format($stats['currentMonthIncome'], 0) }}</div>
                @if($stats['incomeGrowth'] >= 0)
                    <div class="badge-green">▲ {{ number_format($stats['incomeGrowth'], 1) }}%</div>
                @else
                    <span style="color:#dc2626; font-size:8px; font-weight:bold;">▼ {{ number_format(abs($stats['incomeGrowth']), 1) }}%</span>
                @endif
            </div>
            <div class="kpi-small">
                <div class="kpi-small-label">Membresías Premium</div>
                <div class="kpi-small-value">{{ $stats['premiumUsers'] }}</div>
                <div class="badge-amber">Usuarios activos</div>
            </div>
            <div class="kpi-small">
                <div class="kpi-small-label">Certificados Emitidos</div>
                <div class="kpi-small-value">{{ $stats['certificatesIssued'] }}</div>
                <div class="badge-purple">Total histórico</div>
            </div>
        </div>
    </div>

    <!-- ══ TABLA MAESTRA: DISTRIBUCIÓN DE INGRESOS ══════════════════ -->
    <div class="panel">
        <div class="panel-title">
            Análisis Comparativo de Flujos de Ingresos
            <span class="panel-subtitle">Unidad: Soles peruanos (S/)</span>
        </div>

        @php
            $totalGlobal = max($stats['totalIncome'], 1);
            $subPct      = round($stats['subIncome'] / $totalGlobal * 100, 1);
            $crsPct      = round($stats['courseIncome'] / $totalGlobal * 100, 1);
            $subW        = round($stats['subIncome'] / $totalGlobal * 100);
            $crsW        = round($stats['courseIncome'] / $totalGlobal * 100);
        @endphp

        <table class="master-table">
            <thead>
                <tr>
                    <th style="width:22%">Fuente de Ingreso</th>
                    <th>Tipo</th>
                    <th>Modelo</th>
                    <th>Unidades</th>
                    <th>Monto (S/)</th>
                    <th>% del Total</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Membresías Premium</td>
                    <td style="text-align:center">Recurrente</td>
                    <td style="text-align:center">Suscripción</td>
                    <td style="text-align:center">{{ $stats['activeSubs'] }} activas</td>
                    <td class="amount" style="text-align:right">{{ number_format($stats['subIncome'], 2) }}</td>
                    <td style="text-align:center; font-weight:bold; color:#57572A">{{ $subPct }}%</td>
                    <td style="text-align:center"><span style="background:#d1fae5; color:#059669; padding:2px 8px; border-radius:8px; font-size:9px; font-weight:bold;">Activo</span></td>
                </tr>
                <tr>
                    <td>Cursos Individuales</td>
                    <td style="text-align:center">Directo</td>
                    <td style="text-align:center">Venta única</td>
                    <td style="text-align:center">{{ $stats['publishedCourses'] }} publicados</td>
                    <td class="amount-alt" style="text-align:right">{{ number_format($stats['courseIncome'], 2) }}</td>
                    <td style="text-align:center; font-weight:bold; color:#d97706">{{ $crsPct }}%</td>
                    <td style="text-align:center"><span style="background:#fef3c7; color:#d97706; padding:2px 8px; border-radius:8px; font-size:9px; font-weight:bold;">Activo</span></td>
                </tr>
                <tr>
                    <td>Libros / Publicaciones</td>
                    <td style="text-align:center">Directo</td>
                    <td style="text-align:center">Descarga</td>
                    <td style="text-align:center">{{ $stats['totalBooks'] }} títulos</td>
                    <td style="text-align:right; color:#6b7280;">—</td>
                    <td style="text-align:center; color:#6b7280;">—</td>
                    <td style="text-align:center"><span style="background:#e0e7ff; color:#4338ca; padding:2px 8px; border-radius:8px; font-size:9px; font-weight:bold;">Catálogo</span></td>
                </tr>
                <tr class="total-row">
                    <td>TOTAL ACUMULADO</td>
                    <td style="text-align:center">—</td>
                    <td style="text-align:center">—</td>
                    <td style="text-align:center">—</td>
                    <td style="text-align:right">{{ number_format($stats['totalIncome'], 2) }}</td>
                    <td style="text-align:center">100%</td>
                    <td style="text-align:center">—</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- ══ GRÁFICOS DE BARRAS ══════════════════════════════════════ -->
    <div class="panel">
        <div class="panel-title">
            Visualización de Distribución de Ingresos
        </div>
        <div class="charts-row">

            <!-- Barras de Ingresos -->
            <div class="chart-col-left">
                <div class="bar-chart-title">Flujos de Ingreso por Categoría (S/)</div>

                @php
                    $maxBar = max($stats['totalIncome'], $stats['subIncome'], $stats['courseIncome'], 1);
                @endphp

                <div class="bar-group">
                    <div class="bar-label">Facturación Total</div>
                    <div class="bar-row">
                        <div class="bar-legend-dot color-total"></div>
                        <div class="bar-track">
                            <div class="bar-fill color-total" style="width:100%;"></div>
                        </div>
                        <div class="bar-value">S/ {{ number_format($stats['totalIncome'], 0) }}</div>
                    </div>
                </div>

                <div class="bar-group">
                    <div class="bar-label">Membresías Premium</div>
                    <div class="bar-row">
                        <div class="bar-legend-dot color-subs"></div>
                        <div class="bar-track">
                            <div class="bar-fill color-subs" style="width:{{ round($stats['subIncome'] / $maxBar * 100) }}%;"></div>
                        </div>
                        <div class="bar-value">S/ {{ number_format($stats['subIncome'], 0) }}</div>
                    </div>
                </div>

                <div class="bar-group">
                    <div class="bar-label">Cursos Individuales</div>
                    <div class="bar-row">
                        <div class="bar-legend-dot color-courses"></div>
                        <div class="bar-track">
                            <div class="bar-fill color-courses" style="width:{{ round($stats['courseIncome'] / $maxBar * 100) }}%;"></div>
                        </div>
                        <div class="bar-value">S/ {{ number_format($stats['courseIncome'], 0) }}</div>
                    </div>
                </div>

                <div class="divider"></div>

                <div class="bar-chart-title" style="margin-top:10px;">Métricas de Usuarios</div>

                @php $maxUsers = max($stats['totalUsers'], 1); @endphp

                <div class="bar-group">
                    <div class="bar-label">Total de Alumnos</div>
                    <div class="bar-row">
                        <div class="bar-legend-dot color-users"></div>
                        <div class="bar-track">
                            <div class="bar-fill color-users" style="width:100%;"></div>
                        </div>
                        <div class="bar-value">{{ $stats['totalUsers'] }} alumnos</div>
                    </div>
                </div>

                <div class="bar-group">
                    <div class="bar-label">Alumnos Activos</div>
                    <div class="bar-row">
                        <div class="bar-legend-dot" style="background:#10b981;"></div>
                        <div class="bar-track">
                            <div class="bar-fill" style="width:{{ round($stats['activeUsers'] / $maxUsers * 100) }}%; background:#10b981;"></div>
                        </div>
                        <div class="bar-value">{{ $stats['activeUsers'] }} activos</div>
                    </div>
                </div>

                <div class="bar-group">
                    <div class="bar-label">Usuarios Premium</div>
                    <div class="bar-row">
                        <div class="bar-legend-dot color-total"></div>
                        <div class="bar-track">
                            <div class="bar-fill color-total" style="width:{{ round($stats['premiumUsers'] / $maxUsers * 100) }}%;"></div>
                        </div>
                        <div class="bar-value">{{ $stats['premiumUsers'] }} premium</div>
                    </div>
                </div>

                <div class="bar-group">
                    <div class="bar-label">Nuevos Este Mes</div>
                    <div class="bar-row">
                        <div class="bar-legend-dot" style="background:#0891b2;"></div>
                        <div class="bar-track">
                            <div class="bar-fill" style="width:{{ round($stats['newUsersMonth'] / $maxUsers * 100) }}%; background:#0891b2;"></div>
                        </div>
                        <div class="bar-value">{{ $stats['newUsersMonth'] }} nuevos</div>
                    </div>
                </div>
            </div>

            <!-- Tabla de indicadores secundarios -->
            <div class="chart-col-right">
                <div class="bar-chart-title">Indicadores Académicos</div>

                @php
                    $subPctRound = round($stats['subIncome'] / max($stats['totalIncome'],1) * 100);
                    $crsPctRound = round($stats['courseIncome'] / max($stats['totalIncome'],1) * 100);
                @endphp

                <table style="width:100%; border-collapse:collapse; font-size:10px; margin-bottom:16px;">
                    <thead>
                        <tr>
                            <th style="background:#f9f9f5; padding:6px 8px; text-align:left; font-size:8px; color:#6b7280; border-bottom:1px solid #e5e7eb; text-transform:uppercase;">Indicador</th>
                            <th style="background:#f9f9f5; padding:6px 8px; text-align:center; font-size:8px; color:#6b7280; border-bottom:1px solid #e5e7eb; text-transform:uppercase;">Valor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding:6px 8px; border-bottom:1px solid #f3f4f6; color:#374151;">Tasa de Aprobación</td>
                            <td style="padding:6px 8px; border-bottom:1px solid #f3f4f6; text-align:center; font-weight:bold; color:#57572A;">{{ number_format($stats['approvalRate'], 1) }}%</td>
                        </tr>
                        <tr>
                            <td style="padding:6px 8px; border-bottom:1px solid #f3f4f6; color:#374151;">Cursos Publicados</td>
                            <td style="padding:6px 8px; border-bottom:1px solid #f3f4f6; text-align:center; font-weight:bold;">{{ $stats['publishedCourses'] }}</td>
                        </tr>
                        <tr>
                            <td style="padding:6px 8px; border-bottom:1px solid #f3f4f6; color:#374151;">Suscripciones Activas</td>
                            <td style="padding:6px 8px; border-bottom:1px solid #f3f4f6; text-align:center; font-weight:bold; color:#059669;">{{ $stats['activeSubs'] ?? 0 }}</td>
                        </tr>
                        <tr>
                            <td style="padding:6px 8px; border-bottom:1px solid #f3f4f6; color:#374151;">Suscripciones Vencidas</td>
                            <td style="padding:6px 8px; border-bottom:1px solid #f3f4f6; text-align:center; font-weight:bold; color:#dc2626;">{{ $stats['expiredSubs'] ?? 0 }}</td>
                        </tr>
                        <tr>
                            <td style="padding:6px 8px; border-bottom:1px solid #f3f4f6; color:#374151;">Pagos Pendientes</td>
                            <td style="padding:6px 8px; border-bottom:1px solid #f3f4f6; text-align:center; font-weight:bold; color:#d97706;">{{ $stats['pendingPayments'] ?? 0 }}</td>
                        </tr>
                        <tr>
                            <td style="padding:6px 8px; border-bottom:1px solid #f3f4f6; color:#374151;">Certificados Emitidos</td>
                            <td style="padding:6px 8px; border-bottom:1px solid #f3f4f6; text-align:center; font-weight:bold; color:#7c3aed;">{{ $stats['certificatesIssued'] }}</td>
                        </tr>
                        <tr>
                            <td style="padding:6px 8px; border-bottom:1px solid #f3f4f6; color:#374151;">Libros en Catálogo</td>
                            <td style="padding:6px 8px; border-bottom:1px solid #f3f4f6; text-align:center; font-weight:bold;">{{ $stats['totalBooks'] }}</td>
                        </tr>
                        <tr>
                            <td style="padding:6px 8px; color:#374151;">Leads WhatsApp (mes)</td>
                            <td style="padding:6px 8px; text-align:center; font-weight:bold; color:#16a34a;">{{ $stats['whatsappLeadsMonth'] ?? 0 }}</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Composición porcentual -->
                <div style="font-size:9px; font-weight:bold; color:#374151; margin-bottom:8px; text-transform:uppercase; letter-spacing:0.08em;">Composición de Ingresos</div>
                <div style="background:#f3f4f6; border-radius:4px; overflow:hidden; height:14px; margin-bottom:6px; width:100%;">
                    <div style="width:{{ $subPctRound }}%; background:#57572A; height:100%; display:inline-block; vertical-align:top;"></div>
                    <div style="width:{{ $crsPctRound }}%; background:#d97706; height:100%; display:inline-block; vertical-align:top;"></div>
                </div>
                <div>
                    <span class="legend-item"><span class="legend-dot color-total"></span>Membresías {{ $subPctRound }}%</span>
                    <span class="legend-item"><span class="legend-dot color-courses"></span>Cursos {{ $crsPctRound }}%</span>
                </div>
            </div>
        </div>
    </div>

    <!-- ══ RANKING DE PROGRAMAS ════════════════════════════════════ -->
    <div class="panel">
        <div class="panel-title">Ranking de Programas Académicos · Mayor Tracción de Ventas</div>

        @php $maxVentas = $courseSales->first()->approved_payments_count ?? 1; @endphp

        <table class="course-table">
            <thead>
                <tr>
                    <th class="center" style="width:5%">#</th>
                    <th>Programa Académico</th>
                    <th class="center" style="width:12%">Modalidad</th>
                    <th class="center" style="width:15%">Progreso Relativo</th>
                    <th class="center" style="width:10%">Matrículas</th>
                    <th class="right" style="width:14%">Facturado (S/)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($courseSales as $i => $sale)
                <tr>
                    <td style="text-align:center;">
                        <span class="rank-circle {{ $i === 1 ? 'r2' : ($i === 2 ? 'r3' : '') }}">{{ $i + 1 }}</span>
                    </td>
                    <td style="font-weight:600; color:#1f2937;">{{ $sale->title }}</td>
                    <td style="text-align:center;">
                        <span class="{{ $sale->type === 'grabado' ? 'pill-grabado' : 'pill-vivo' }}">
                            {{ $sale->type === 'grabado' ? 'Grabado' : 'En Vivo' }}
                        </span>
                    </td>
                    <td style="text-align:center;">
                        <div class="progress-mini">
                            <div class="progress-mini-fill" style="width:{{ round($sale->approved_payments_count / $maxVentas * 100) }}%;"></div>
                        </div>
                    </td>
                    <td style="text-align:center; font-weight:bold;">{{ $sale->approved_payments_count }}</td>
                    <td style="text-align:right; font-weight:bold; color:#57572A;">{{ number_format($sale->total_earned ?? 0, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- ══ NOTA LEGAL ══════════════════════════════════════════════ -->
    <div style="font-size:9px; color:#9ca3af; font-style:italic; margin-top:12px; padding:10px 16px; background:#fff; border-radius:6px; border:1px dashed #e5e7eb;">
        * Todos los montos corresponden a pagos con estado "aprobado" registrados en el sistema. El crecimiento mensual se calcula comparando
        el mes en curso vs. el mes inmediatamente anterior. Los datos son generados en tiempo real desde la base de datos de iieEdu. <br>
        ** Hora del servidor: {{ now()->setTimezone('America/Lima')->format('d/m/Y H:i:s') }} (Hora de Lima, Perú).
    </div>

</div>

<!-- ══ FOOTER ══════════════════════════════════════════════════════ -->
<div class="footer">
    <span><strong>iieEdu</strong> · Instituto de Investigación y Educación</span>
    <span>Reporte mensual: <strong>{{ $monthName }}</strong></span>
    <span>Sistema de Gestión Académica · {{ now()->setTimezone('America/Lima')->format('d/m/Y') }}</span>
</div>
</body>
</html>
