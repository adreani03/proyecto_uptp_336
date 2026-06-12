<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario - Microbiología</title>
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        h1 {
            margin: 0;
            font-weight: 600;
            font-size: 2.5rem;
            background: -webkit-linear-gradient(45deg, #38bdf8, #818cf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
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

        .btn-small {
            padding: 0.4rem 1rem;
            font-size: 0.9rem;
        }

        .btn-danger {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            border: 1px solid var(--danger);
        }

        .btn-danger:hover {
            background: var(--danger);
            color: white;
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
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th, td {
            padding: 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        th {
            color: var(--text-muted);
            font-weight: 400;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.05em;
        }

        tr {
            transition: background-color 0.2s ease;
        }

        tr:hover {
            background-color: rgba(255, 255, 255, 0.03);
        }

        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .badge-consumible { background: rgba(16, 185, 129, 0.1); color: #34d399; }
        .badge-equipo { background: rgba(139, 92, 246, 0.1); color: #a78bfa; }

        .actions {
            display: flex;
            gap: 0.5rem;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: var(--text-muted);
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div>
                <h1>Inventario</h1>
                <p style="color: var(--text-muted); margin-top: 0.5rem;">Gestión de equipos y reactivos</p>
            </div>
            <div style="display: flex; gap: 1rem;">
                <a href="<?= BASE_URL ?>/dashboard" class="btn btn-outline">Volver</a>
                <a href="<?= BASE_URL ?>/inventario/crear" class="btn btn-primary">+ Nuevo Artículo</a>
            </div>
        </header>

        <div class="glass-card">
            <?php if (empty($inventarios)): ?>
                <div class="empty-state">
                    <p>No hay artículos registrados en el inventario.</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Tipo</th>
                            <th>Lote/Serial</th>
                            <th>Stock</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($inventarios as $item): ?>
                            <tr>
                                <td style="color: var(--text-muted);">#<?= htmlspecialchars($item->getId()) ?></td>
                                <td><strong><?= htmlspecialchars($item->getNombre()) ?></strong></td>
                                <td>
                                    <span class="badge <?= $item->getTipo() === 'Consumible' ? 'badge-consumible' : 'badge-equipo' ?>">
                                        <?= htmlspecialchars($item->getTipo()) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($item->getNumeroLote() ?: $item->getSerialCodigo() ?: 'N/A') ?></td>
                                <td>
                                    <span style="<?= $item->getStockActual() <= $item->getStockMinimo() ? 'color: var(--danger); font-weight: bold;' : '' ?>">
                                        <?= htmlspecialchars($item->getStockActual()) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($item->getEstado()) ?></td>
                                <td class="actions">
                                    <a href="<?= BASE_URL ?>/inventario/editar?id=<?= $item->getId() ?>" class="btn btn-small btn-outline">Editar</a>
                                    <form action="<?= BASE_URL ?>/inventario/eliminar" method="POST" style="display:inline;" onsubmit="return confirm('¿Eliminar este artículo?');">
                                        <input type="hidden" name="id" value="<?= $item->getId() ?>">
                                        <button type="submit" class="btn btn-small btn-danger">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
