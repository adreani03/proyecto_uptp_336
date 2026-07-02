<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Ruta robusta a la base de datos (compatible Windows/Linux)
$dbPath = dirname(__DIR__, 4) . DIRECTORY_SEPARATOR . 'microbiologia.db';

$db = null;
$errorMsg = '';

if (!file_exists($dbPath)) {
    // Intento fallback: buscar en la raíz del documento
    $docRoot = $_SERVER['DOCUMENT_ROOT'] ?? '';
    $altPath = $docRoot . DIRECTORY_SEPARATOR . 'proyecto_uptp_336-main' . DIRECTORY_SEPARATOR . 'microbiologia.db';
    if (file_exists($altPath)) {
        $dbPath = $altPath;
    }
}

if (file_exists($dbPath)) {
    try {
        $db = new PDO('sqlite:' . $dbPath);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        $errorMsg = 'Error PDO: ' . $e->getMessage();
    }
} else {
    $errorMsg = 'No se encontró microbiologia.db';
}

$tables = ['categorias', 'inventario', 'materias', 'movimiento_inventario', 'prestamos', 'usuarios'];
$tableData = [];
$tableColumns = [];

if ($db) {
    foreach ($tables as $table) {
        try {
            $cols = $db->query("PRAGMA table_info($table)")->fetchAll(PDO::FETCH_ASSOC);
            $rows = $db->query("SELECT * FROM $table")->fetchAll(PDO::FETCH_ASSOC);
            $tableColumns[$table] = $cols;
            $tableData[$table] = $rows;
        } catch (PDOException $e) {
            $tableColumns[$table] = [];
            $tableData[$table] = [];
        }
    }
} else {
    foreach ($tables as $table) {
        $tableColumns[$table] = [];
        $tableData[$table] = [];
    }
}

function badgeClass($value, $field = '') {
    $val = strtolower($value ?? '');
    if ($field === 'tipo' || $field === 'rol') {
        if (strpos($val, 'admin') !== false) return 'badge-admin';
        if (strpos($val, 'docente') !== false) return 'badge-docente';
        if (strpos($val, 'equipo') !== false) return 'badge-equipo';
        if (strpos($val, 'consumible') !== false) return 'badge-consumible';
        return 'badge-equipo';
    }
    if ($field === 'estado' || $field === 'estado_entrega' || $field === 'estado_devolucion') {
        if (strpos($val, 'disponible') !== false) return 'badge-disponible';
        if (strpos($val, 'entregado') !== false || strpos($val, 'activo') !== false) return 'badge-activo';
        if (strpos($val, 'devuelto') !== false) return 'badge-devuelto';
        if (strpos($val, 'pendiente') !== false) return 'badge-pendiente';
        if (strpos($val, 'danado') !== false || strpos($val, 'dan') !== false) return 'badge-danado';
        return 'badge-disponible';
    }
    if ($field === 'tipo_movimiento') {
        if (strpos($val, 'entrada') !== false) return 'badge-consumible';
        if (strpos($val, 'salida') !== false) return 'badge-danado';
        return 'badge-equipo';
    }
    return 'badge-equipo';
}

function truncate($text, $len = 40) {
    if ($text === null || $text === '') return '—';
    $str = (string) $text;
    return strlen($str) > $len ? htmlspecialchars(substr($str, 0, $len)) . '…' : htmlspecialchars($str);
}

function isPasswordField($colName) {
    return stripos($colName, 'password') !== false || stripos($colName, 'pass') !== false || stripos($colName, 'clave') !== false;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado de Inventario - Microbiología</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0f172a;
            --glass-bg: rgba(30, 41, 59, 0.7);
            --glass-border: rgba(255, 255, 255, 0.1);
            --primary: #38bdf8;
            --primary-hover: #0ea5e9;
            --danger: #ef4444;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, var(--bg-color), #1e1b4b);
            color: var(--text-main);
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        h1 {
            margin: 0;
            font-weight: 600;
            font-size: 2.5rem;
            background: -webkit-linear-gradient(45deg, #38bdf8, #818cf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        h2 {
            margin: 0 0 1rem 0;
            font-weight: 600;
            font-size: 1.5rem;
            color: var(--text-main);
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        h2 .count {
            font-size: 0.85rem;
            font-weight: 400;
            color: var(--text-muted);
            background: rgba(255,255,255,0.05);
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border-radius: 9999px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            font-family: inherit;
        }

        .btn-primary {
            background: var(--primary);
            color: #0f172a;
            box-shadow: 0 4px 15px rgba(56, 189, 248, 0.3);
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(56, 189, 248, 0.4);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid var(--glass-border);
            color: var(--text-main);
        }

        .btn-outline:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            overflow-x: auto;
            margin-bottom: 2.5rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.95rem;
        }

        th, td {
            padding: 0.85rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            white-space: nowrap;
        }

        th {
            color: var(--text-muted);
            font-weight: 400;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }

        tr {
            transition: background-color 0.2s ease;
        }

        tr:hover {
            background-color: rgba(255, 255, 255, 0.03);
        }

        td {
            color: var(--text-main);
        }

        td.muted {
            color: var(--text-muted);
        }

        td.null {
            color: #64748b;
            font-style: italic;
        }

        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 600;
            display: inline-block;
        }

        .badge-consumible { background: rgba(16, 185, 129, 0.1); color: #34d399; }
        .badge-equipo      { background: rgba(139, 92, 246, 0.1); color: #a78bfa; }
        .badge-admin       { background: rgba(239, 68, 68, 0.1); color: #f87171; }
        .badge-docente     { background: rgba(56, 189, 248, 0.1); color: #38bdf8; }
        .badge-disponible  { background: rgba(16, 185, 129, 0.1); color: #34d399; }
        .badge-activo      { background: rgba(56, 189, 248, 0.1); color: #38bdf8; }
        .badge-devuelto    { background: rgba(16, 185, 129, 0.1); color: #34d399; }
        .badge-pendiente   { background: rgba(234, 179, 8, 0.1); color: #facc15; }
        .badge-danado      { background: rgba(239, 68, 68, 0.1); color: #f87171; }

        .empty-state {
            text-align: center;
            padding: 2rem;
            color: var(--text-muted);
            font-style: italic;
        }

        .nav-tabs {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
            margin-bottom: 1.5rem;
        }

        .nav-tab {
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            border: 1px solid var(--glass-border);
            background: transparent;
            color: var(--text-muted);
            transition: all 0.2s ease;
            font-family: inherit;
        }

        .nav-tab:hover {
            background: rgba(255, 255, 255, 0.05);
            color: var(--text-main);
        }

        .nav-tab.active {
            background: var(--primary);
            color: #0f172a;
            border-color: var(--primary);
        }

        .table-section {
            display: none;
        }

        .table-section.active {
            display: block;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div>
                <h1>Listado de Inventario</h1>
                <p style="color: var(--text-muted); margin-top: 0.5rem;">Vista completa de todas las tablas de microbiología</p>
            </div>
            <div style="display: flex; gap: 1rem;">
                <a href="<?= defined('BASE_URL') ? BASE_URL : '' ?>/dashboard" class="btn btn-outline">Volver</a>
                <a href="<?= defined('BASE_URL') ? BASE_URL : '' ?>/inventario" class="btn btn-primary">Ir a Inventario</a>
            </div>
        </header>

        <?php if (!$db): ?>
            <div class="glass-card" style="border-color: var(--danger);">
                <h2 style="color: var(--danger);">⚠️ No hay conexión a la base de datos</h2>
                <p>Verifica que el archivo <code>microbiologia.db</code> exista en la raíz del proyecto.</p>
                <p><strong>Ruta buscada:</strong> <code><?= htmlspecialchars($dbPath) ?></code></p>
            </div>
        <?php else: ?>

            <div class="nav-tabs">
                <?php foreach ($tables as $table): ?>
                    <button class="nav-tab <?= $table === 'categorias' ? 'active' : '' ?>" onclick="showTable('<?= $table ?>')">
                        <?= ucfirst(str_replace('_', ' ', $table)) ?>
                        <span style="opacity:0.7; font-size:0.8em;">(<?= count($tableData[$table]) ?>)</span>
                    </button>
                <?php endforeach; ?>
            </div>

            <?php foreach ($tables as $table): ?>
                <div id="section-<?= $table ?>" class="table-section <?= $table === 'categorias' ? 'active' : '' ?>">
                    <div class="glass-card">
                        <h2>
                            <?= ucfirst(str_replace('_', ' ', $table)) ?>
                            <span class="count"><?= count($tableData[$table]) ?> registro<?= count($tableData[$table]) !== 1 ? 's' : '' ?></span>
                        </h2>
                        <?php if (empty($tableData[$table])): ?>
                            <div class="empty-state">
                                <p>Esta tabla no contiene registros.</p>
                            </div>
                        <?php else: ?>
                            <table>
                                <thead>
                                    <tr>
                                        <?php foreach ($tableColumns[$table] as $col): ?>
                                            <th><?= htmlspecialchars($col['name']) ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($tableData[$table] as $row): ?>
                                        <tr>
                                            <?php foreach ($tableColumns[$table] as $col): ?>
                                                <?php $val = $row[$col['name']] ?? null; ?>
                                                <td class="<?= $val === null ? 'null' : '' ?> <?= $col['name'] === 'id' ? 'muted' : '' ?>">
                                                    <?php if ($val === null): ?>
                                                        NULL
                                                    <?php elseif (isPasswordField($col['name'])): ?>
                                                        ••••••••
                                                    <?php elseif ($col['name'] === 'id'): ?>
                                                        #<?= htmlspecialchars($val) ?>
                                                    <?php elseif ($col['name'] === 'tipo' || $col['name'] === 'rol' || $col['name'] === 'tipo_movimiento'): ?>
                                                        <span class="badge <?= badgeClass($val, $col['name']) ?>">
                                                            <?= htmlspecialchars($val) ?>
                                                        </span>
                                                    <?php elseif ($col['name'] === 'estado' || $col['name'] === 'estado_entrega' || $col['name'] === 'estado_devolucion'): ?>
                                                        <span class="badge <?= badgeClass($val, $col['name']) ?>">
                                                            <?= htmlspecialchars($val) ?>
                                                        </span>
                                                    <?php elseif ($col['name'] === 'protocolo_limpieza'): ?>
                                                        <?= $val ? '✅ Sí' : '❌ No' ?>
                                                    <?php else: ?>
                                                        <?= truncate($val, 50) ?>
                                                    <?php endif; ?>
                                                </td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>

        <?php endif; ?>
    </div>

    <script>
        function showTable(tableId) {
            document.querySelectorAll('.table-section').forEach(s => s.classList.remove('active'));
            document.querySelectorAll('.nav-tab').forEach(t => t.classList.remove('active'));
            document.getElementById('section-' + tableId).classList.add('active');
            event.target.closest('.nav-tab').classList.add('active');
        }
    </script>
</body>
</html>
