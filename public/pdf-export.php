<?php
/**
 * public/pdf_export.php
 * Genera y descarga el PDF del inventario usando Dompdf
 * Accesible en: http://tudominio.com/pdf_export.php
 */

// ─── RUTAS ABSOLUTAS ─────────────────────────────────────────────
define('ROOT_PATH', dirname(__DIR__));           // /ruta/a/proyecto_uptp_336/
define('PUBLIC_PATH', __DIR__);                  // /ruta/a/proyecto_uptp_336/public/
define('SRC_PATH', ROOT_PATH . '/src');          // /ruta/a/proyecto_uptp_336/src/

// ─── AUTOLOAD ────────────────────────────────────────────────────
require_once ROOT_PATH . '/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// ─── CONFIGURACIÓN DOMPDF ──────────────────────────────────────
$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);
$options->set('defaultFont', 'Helvetica');

$dompdf = new Dompdf($options);

// ─── CONEXIÓN A LA BASE DE DATOS ───────────────────────────────
try {
    $dbPath = ROOT_PATH . '/microbiologia.db';
    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    http_response_code(500);
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

// ─── OBTENER DATOS DEL INVENTARIO ──────────────────────────────
try {
    $stmt = $db->query("
        SELECT 
            i.*,
            c.nombre as categoria_nombre
        FROM inventario i
        LEFT JOIN categorias c ON i.categoria_id = c.id
        ORDER BY i.id DESC
    ");
    $inventarios = $stmt->fetchAll();
} catch (Exception $e) {
    http_response_code(500);
    die("Error al obtener datos: " . $e->getMessage());
}

// ─── ESTADÍSTICAS ──────────────────────────────────────────────
$totalItems  = count($inventarios);
$consumibles = 0;
$equipos     = 0;
$bajoStock   = 0;

foreach ($inventarios as $item) {
    if ($item['tipo'] === 'Consumible') $consumibles++;
    elseif ($item['tipo'] === 'Equipo') $equipos++;
    
    $stockActual = (int)($item['stock_actual'] ?? 0);
    $stockMinimo = (int)($item['stock_minimo'] ?? 0);
    
    if ($stockMinimo > 0 && $stockActual <= $stockMinimo) {
        $bajoStock++;
    }
}

$fechaGeneracion = date('d/m/Y H:i:s');

// ─── FUNCIÓN AUXILIAR ────────────────────────────────────────────
function esc($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

// ─── GENERAR HTML ──────────────────────────────────────────────
ob_start();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reporte de Inventario - Microbiología</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: Helvetica, Arial, sans-serif;
            font-size: 10px;
            color: #1e293b;
            line-height: 1.4;
        }
        
        .header {
            background: #0f172a;
            color: white;
            padding: 20px 25px;
            margin-bottom: 15px;
        }
        
        .header h1 {
            font-size: 22px;
            font-weight: 600;
            margin-bottom: 4px;
        }
        
        .header .subtitle {
            font-size: 11px;
            opacity: 0.8;
        }
        
        .meta-info {
            padding: 0 25px;
            margin-bottom: 15px;
            font-size: 9px;
            color: #64748b;
        }
        
        .summary {
            display: flex;
            gap: 12px;
            padding: 0 25px;
            margin-bottom: 20px;
        }
        
        .summary-card {
            flex: 1;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px;
            text-align: center;
        }
        
        .summary-card .number {
            font-size: 20px;
            font-weight: 700;
            color: #0f172a;
        }
        
        .summary-card .label {
            font-size: 9px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        
        .summary-card.warning .number { color: #ef4444; }
        
        table {
            width: calc(100% - 50px);
            margin: 0 25px;
            border-collapse: collapse;
        }
        
        thead {
            background: #0f172a;
            color: white;
        }
        
        th {
            padding: 10px 8px;
            text-align: left;
            font-size: 9px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        td {
            padding: 8px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 9px;
            vertical-align: middle;
        }
        
        tr:nth-child(even) { background: #f8fafc; }
        tr.low-stock td { background: #fef2f2 !important; color: #991b1b; }
        
        .badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 999px;
            font-size: 8px;
            font-weight: 600;
        }
        
        .badge-consumible { background: #d1fae5; color: #065f46; }
        .badge-equipo     { background: #ede9fe; color: #5b21b6; }
        
        .badge-estado {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: 600;
        }
        
        .estado-disponible { background: #d1fae5; color: #065f46; }
        .estado-operativo  { background: #dbeafe; color: #1e40af; }
        .estado-mantenimiento { background: #fef3c7; color: #92400e; }
        .estado-fuera      { background: #fee2e2; color: #991b1b; }
        .estado-contaminado { background: #f3e8ff; color: #6b21a8; }
        
        .stock-alert {
            color: #ef4444;
            font-weight: 700;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 8px 25px;
            font-size: 8px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            background: white;
        }
        
        .text-muted { color: #94a3b8; }
        .text-small { font-size: 8px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Inventario</h1>
        <div class="subtitle">Laboratorio de Microbiología — Sistema de Gestión</div>
    </div>
    
    <div class="meta-info">
        Generado: <?= $fechaGeneracion ?> &nbsp;|&nbsp; Total: <?= $totalItems ?> artículos
    </div>
    
    <div class="summary">
        <div class="summary-card">
            <div class="number"><?= $totalItems ?></div>
            <div class="label">Total Artículos</div>
        </div>
        <div class="summary-card">
            <div class="number"><?= $equipos ?></div>
            <div class="label">Equipos</div>
        </div>
        <div class="summary-card">
            <div class="number"><?= $consumibles ?></div>
            <div class="label">Consumibles</div>
        </div>
        <div class="summary-card warning">
            <div class="number"><?= $bajoStock ?></div>
            <div class="label">Bajo Stock</div>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 6%;">ID</th>
                <th style="width: 18%;">Nombre</th>
                <th style="width: 10%;">Categoría</th>
                <th style="width: 10%;">Tipo</th>
                <th style="width: 12%;">Serial/Lote</th>
                <th style="width: 8%;">Stock</th>
                <th style="width: 8%;">Mín/Máx</th>
                <th style="width: 10%;">Estado</th>
                <th style="width: 12%;">Ubicación</th>
                <th style="width: 6%;">Venc.</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($inventarios)): ?>
                <tr>
                    <td colspan="10" style="text-align: center; padding: 30px; color: #94a3b8;">
                        No hay artículos registrados en el inventario.
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($inventarios as $item): 
                    $stockActual = (int)($item['stock_actual'] ?? 0);
                    $stockMinimo = (int)($item['stock_minimo'] ?? 0);
                    $stockMaximo = (int)($item['stock_maximo'] ?? 0);
                    $isLowStock  = ($stockMinimo > 0 && $stockActual <= $stockMinimo);
                    
                    $serialLote = $item['serial_codigo'] ?: $item['numero_lote'] ?: 'N/A';
                    
                    // Clase del estado
                    $estadoClass = 'estado-disponible';
                    $estado = $item['estado'] ?? 'Disponible';
                    switch ($estado) {
                        case 'Disponible': $estadoClass = 'estado-disponible'; break;
                        case 'Operativo con Observaciones': $estadoClass = 'estado-operativo'; break;
                        case 'En Mantenimiento': $estadoClass = 'estado-mantenimiento'; break;
                        case 'Fuera de Servicio': $estadoClass = 'estado-fuera'; break;
                        case 'Contaminado': $estadoClass = 'estado-contaminado'; break;
                    }
                    
                    $vencimiento = $item['fecha_vencimiento'] 
                        ? date('d/m/Y', strtotime($item['fecha_vencimiento'])) 
                        : 'N/A';
                ?>
                    <tr class="<?= $isLowStock ? 'low-stock' : '' ?>">
                        <td class="text-muted">#<?= esc($item['id']) ?></td>
                        <td><strong><?= esc($item['nombre']) ?></strong></td>
                        <td><?= esc($item['categoria_nombre'] ?: 'Sin categoría') ?></td>
                        <td>
                            <span class="badge <?= $item['tipo'] === 'Consumible' ? 'badge-consumible' : 'badge-equipo' ?>">
                                <?= esc($item['tipo']) ?>
                            </span>
                        </td>
                        <td class="text-small"><?= esc($serialLote) ?></td>
                        <td class="<?= $isLowStock ? 'stock-alert' : '' ?>">
                            <?= $stockActual ?>
                        </td>
                        <td class="text-small text-muted">
                            <?= $stockMinimo ?> / <?= $stockMaximo ?>
                        </td>
                        <td>
                            <span class="badge-estado <?= $estadoClass ?>">
                                <?= esc($estado) ?>
                            </span>
                        </td>
                        <td class="text-small"><?= esc($item['ubicacion'] ?: 'N/A') ?></td>
                        <td class="text-small text-muted"><?= $vencimiento ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
    
    <div class="footer">
        Sistema de Inventario Microbiología — Generado el <?= $fechaGeneracion ?>
    </div>
</body>
</html>
<?php
$html = ob_get_clean();

// ─── GENERAR Y DESCARGAR PDF ───────────────────────────────────
$dompdf->loadHtml($html);
$dompdf->setPaper('letter', 'landscape');
$dompdf->render();

// Nombre del archivo con fecha
$filename = 'inventario_microbiologia_' . date('Y-m-d_His') . '.pdf';

// Descargar (true) o mostrar en navegador (false)
$dompdf->stream($filename, ['Attachment' => false]);
exit;