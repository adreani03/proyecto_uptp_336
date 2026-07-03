<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Laboratorio de Microbiología</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0f172a;
            --glass-bg: rgba(30, 41, 59, 0.6);
            --glass-border: rgba(255, 255, 255, 0.08);
            --primary: #38bdf8;
            --primary-hover: #0ea5e9;
            --accent: #6366f1;
            --accent-hover: #4f46e5;
            --danger: #ef4444;
            --warning: #f59e0b;
            --success: #10b981;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, var(--bg-color), #12102e, #1e1b4b);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2.5rem 1.5rem;
            width: 100%;
            flex-grow: 1;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
            flex-wrap: wrap;
            gap: 1.5rem;
        }

        .welcome-title h1 {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(to right, #38bdf8, #818cf8, #c084fc);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }

        .welcome-title p {
            color: var(--text-muted);
            font-size: 1.05rem;
        }

        /* Buttons & Actions */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.8rem 1.6rem;
            border-radius: 9999px;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            border: none;
            font-family: inherit;
        }

        .btn-logout {
            background: rgba(239, 68, 68, 0.1);
            color: #f87171;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .btn-logout:hover {
            background: var(--danger);
            color: white;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4);
            transform: translateY(-2px);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 1.75rem;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: transparent;
            transition: background 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            border-color: rgba(255, 255, 255, 0.15);
            box-shadow: 0 30px 50px -15px rgba(0, 0, 0, 0.4);
        }

        .stat-card.primary:hover::before {
            background: var(--primary);
        }

        .stat-card.accent:hover::before {
            background: var(--accent);
        }

        .stat-card.warning:hover::before {
            background: var(--warning);
        }

        .stat-card.success:hover::before {
            background: var(--success);
        }

        .stat-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.03);
        }

        .stat-title {
            color: var(--text-muted);
            font-size: 0.9rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 0.5rem;
        }

        .stat-value {
            font-size: 2.25rem;
            font-weight: 700;
            color: var(--text-main);
            line-height: 1.2;
        }

        .stat-sub {
            margin-top: 0.75rem;
            font-size: 0.85rem;
            color: var(--text-muted);
            display: flex;
            gap: 1rem;
        }

        .stat-sub span {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .badge-sub {
            background: rgba(255, 255, 255, 0.05);
            padding: 0.15rem 0.5rem;
            border-radius: 6px;
            color: var(--text-main);
            font-weight: 600;
        }

        /* Highlight classes */
        .warning-glow {
            border-color: rgba(245, 158, 11, 0.2);
        }

        .warning-glow .stat-value {
            color: #fbbf24;
        }

        /* Action Cards Section */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        @media (max-width: 900px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

        .section-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 1.75rem;
            box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.3);
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(to right, #ffffff, var(--text-muted));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        /* Quick Actions Menu */
        .quick-actions-menu {
            display: flex;
            flex-direction: column;
            gap: 0.85rem;
        }

        .action-link {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.03);
            text-decoration: none;
            color: var(--text-main);
            transition: all 0.25s ease;
        }

        .action-link:hover {
            background: rgba(56, 189, 248, 0.08);
            border-color: rgba(56, 189, 248, 0.3);
            transform: translateX(5px);
        }

        .action-icon {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: rgba(56, 189, 248, 0.1);
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            transition: all 0.3s ease;
        }

        .action-link:hover .action-icon {
            background: var(--primary);
            color: #0f172a;
        }

        .action-info {
            display: flex;
            flex-direction: column;
        }

        .action-name {
            font-weight: 600;
            font-size: 0.95rem;
        }

        .action-desc {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-top: 0.15rem;
        }

        /* Recent Activity Feed */
        .activity-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
        }

        .activity-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            padding: 1rem;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.01);
            border: 1px solid rgba(255, 255, 255, 0.02);
            font-size: 0.9rem;
        }

        .activity-icon-indicator {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            flex-shrink: 0;
        }

        .act-entrada {
            background: rgba(16, 185, 129, 0.1);
            color: #34d399;
        }

        .act-consumo {
            background: rgba(56, 189, 248, 0.1);
            color: #38bdf8;
        }

        .act-extravio {
            background: rgba(245, 158, 11, 0.1);
            color: #fbbf24;
        }

        .act-dañado {
            background: rgba(239, 68, 68, 0.1);
            color: #f87171;
        }

        .activity-content {
            flex-grow: 1;
        }

        .activity-header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 0.25rem;
            flex-wrap: wrap;
            gap: 0.25rem;
        }

        .activity-time {
            font-size: 0.75rem;
            color: var(--text-muted);
        }

        .activity-user {
            font-weight: 600;
            color: var(--text-main);
        }

        .activity-text {
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        .activity-item strong {
            color: var(--text-main);
        }

        .empty-activity {
            text-align: center;
            color: var(--text-muted);
            padding: 2.5rem;
            font-style: italic;
        }

        footer {
            text-align: center;
            padding: 2rem;
            color: var(--text-muted);
            font-size: 0.85rem;
            border-top: 1px solid rgba(255, 255, 255, 0.03);
            margin-top: auto;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

    <div class="container">
        <header>
            <div class="welcome-title">
                <h1>Bienvenido, <?= htmlspecialchars($_SESSION['usuario_nombre']) ?></h1>
                <p>Panel de Control general del Laboratorio de Microbiología</p>
            </div>
            <a href="<?= BASE_URL ?>/logout" class="btn btn-logout">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path fill-rule="evenodd"
                        d="M6 12.5a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-8a.5.5 0 0 0-.5.5v2a.5.5 0 0 1-1 0v-2A1.5 1.5 0 0 1 6.5 2h8A1.5 1.5 0 0 1 16 3.5v9a1.5 1.5 0 0 1-1.5 1.5h-8A1.5 1.5 0 0 1 5 12.5v-2a.5.5 0 0 1 1 0v2z" />
                    <path fill-rule="evenodd"
                        d="M.146 8.354a.5.5 0 0 1 0-.708l3-3a.5.5 0 1 1 .708.708L1.707 7.5H10.5a.5.5 0 0 1 0 1H1.707l2.147 2.146a.5.5 0 0 1-.708.708l-3-3z" />
                </svg>
                Cerrar Sesión
            </a>
        </header>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-icon">📦</div>
                <div class="stat-title">Artículos en Inventario</div>
                <div class="stat-value"><?= $totalItems ?></div>
                <div class="stat-sub">
                    <span>Equipos: <strong class="badge-sub"><?= $equipos ?></strong></span>
                    <span>Reactivos: <strong class="badge-sub"><?= $consumibles ?></strong></span>
                </div>
            </div>

            <div class="stat-card warning <?= $bajoStock > 0 ? 'warning-glow' : '' ?>">
                <div class="stat-icon">⚠️</div>
                <div class="stat-title">Alerta de Bajo Stock</div>
                <div class="stat-value"><?= $bajoStock ?></div>
                <div class="stat-sub">
                    <span>Requieren reposición inmediata</span>
                </div>
            </div>

            <div class="stat-card success">
                <div class="stat-icon">📋</div>
                <div class="stat-title">Préstamos Activos</div>
                <div class="stat-value"><?= $prestamosActivos ?></div>
                <div class="stat-sub">
                    <span>Equipos en uso por docentes</span>
                </div>
            </div>

            <div class="stat-card accent">
                <div class="stat-icon">👥</div>
                <div class="stat-title">Categorías y Usuarios</div>
                <div class="stat-value"><?= $totalCategorias ?></div>
                <div class="stat-sub">
                    <span>Usuarios registrados: <strong class="badge-sub"><?= $totalUsuarios ?></strong></span>
                </div>
            </div>
        </div>

        <!-- Dashboard Grid (Activity + Actions) -->
        <div class="dashboard-grid">

            <!-- Recent Activity replaced with Inventory Bar Chart -->
            <div class="section-card">
                <h2 class="section-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                        viewBox="0 0 16 16" style="color: var(--primary);">
                        <path d="M4 11H2v3h2v-3zm5-4H7v7h2V7zm5-5v12h-2V2h2zm-2-1a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h2a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1h-2zM6 7a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1H7a1 1 0 0 1-1-1V7zm-5 4a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1v-3z"/>
                    </svg>
                    Nivel de Stock - Artículos Principales
                </h2>

                <?php
                // Ordenar por stock_actual desc y tomar los 8 primeros
                $chartItems = $inventarios;
                usort($chartItems, function($a, $b) {
                    return $b->getStockActual() <=> $a->getStockActual();
                });
                $topItems = array_slice($chartItems, 0, 8);

                $labels = [];
                $actual = [];
                $minimo = [];
                foreach ($topItems as $item) {
                    $labels[] = $item->getNombre();
                    $actual[] = $item->getStockActual();
                    $minimo[] = $item->getStockMinimo();
                }
                ?>

                <div class="chart-container" style="position: relative; height: 350px; width: 100%;">
                    <canvas id="inventoryBarChart"></canvas>
                </div>

                <script>
                    document.addEventListener("DOMContentLoaded", function() {
                        const ctx = document.getElementById('inventoryBarChart').getContext('2d');
                        
                        // Gradients for modern corporate style
                        const gradientActual = ctx.createLinearGradient(0, 0, 0, 350);
                        gradientActual.addColorStop(0, '#38bdf8'); // primary
                        gradientActual.addColorStop(1, '#0284c7');

                        const gradientMinimo = ctx.createLinearGradient(0, 0, 0, 350);
                        gradientMinimo.addColorStop(0, '#f87171'); // light red
                        gradientMinimo.addColorStop(1, '#ef4444'); // danger red

                        new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels: <?= json_encode($labels) ?>,
                                datasets: [
                                    {
                                        label: 'Stock Actual',
                                        data: <?= json_encode($actual) ?>,
                                        backgroundColor: gradientActual,
                                        borderRadius: 8,
                                        borderSkipped: false,
                                        barPercentage: 0.6,
                                        categoryPercentage: 0.7
                                    },
                                    {
                                        label: 'Stock Mínimo',
                                        data: <?= json_encode($minimo) ?>,
                                        backgroundColor: gradientMinimo,
                                        borderRadius: 8,
                                        borderSkipped: false,
                                        barPercentage: 0.6,
                                        categoryPercentage: 0.7
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        position: 'top',
                                        labels: {
                                            color: '#94a3b8', // text-muted
                                            font: {
                                                family: 'Outfit',
                                                size: 13
                                            },
                                            usePointStyle: true,
                                            pointStyle: 'circle'
                                        }
                                    },
                                    tooltip: {
                                        backgroundColor: 'rgba(15, 23, 42, 0.9)',
                                        titleFont: { family: 'Outfit', size: 14, weight: 'bold' },
                                        bodyFont: { family: 'Outfit', size: 13 },
                                        borderColor: 'rgba(255, 255, 255, 0.1)',
                                        borderWidth: 1,
                                        padding: 12,
                                        displayColors: true,
                                        callbacks: {
                                            label: function(context) {
                                                return ` ${context.dataset.label}: ${context.raw} unidades`;
                                            }
                                        }
                                    }
                                },
                                scales: {
                                    x: {
                                        grid: {
                                            display: false
                                        },
                                        ticks: {
                                            color: '#94a3b8',
                                            font: {
                                                family: 'Outfit',
                                                size: 11
                                            }
                                        }
                                    },
                                    y: {
                                        grid: {
                                            color: 'rgba(255, 255, 255, 0.05)',
                                            drawBorder: false
                                        },
                                        ticks: {
                                            color: '#94a3b8',
                                            font: {
                                                family: 'Outfit',
                                                size: 11
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    });
                </script>
            </div>

            <!-- Quick Actions -->
            <div class="section-card">
                <h2 class="section-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                        viewBox="0 0 16 16" style="color: var(--accent);">
                        <path
                            d="M9.5 1.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0zm-3 1.5a.5.5 0 0 0-.5.5v1.5a.5.5 0 0 0 .5.5h3a.5.5 0 0 0 .5-.5V3.5a.5.5 0 0 0-.5-.5h-3zm-3 1a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3zm.5 1.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v10a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5V5zm10-3a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3zm.5 1.5a.5.5 0 0 0-.5-.5h-1a.5.5 0 0 0-.5.5v10a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5V5zM2 13h12v1H2v-1z" />
                    </svg>
                    Acciones Rápidas
                </h2>

                <div class="quick-actions-menu">
                    <a href="<?= BASE_URL ?>/inventario" class="action-link">
                        <div class="action-icon">📦</div>
                        <div class="action-info">
                            <span class="action-name">Gestionar Inventario</span>
                            <span class="action-desc">Ver, editar y eliminar artículos</span>
                        </div>
                    </a>

                    <a href="<?= BASE_URL ?>/inventario/crear" class="action-link">
                        <div class="action-icon">➕</div>
                        <div class="action-info">
                            <span class="action-name">Registrar Artículo</span>
                            <span class="action-desc">Añadir reactivos o equipos</span>
                        </div>
                    </a>

                    <a href="<?= BASE_URL ?>/pdf-export.php" class="action-link" target="_blank">
                        <div class="action-icon">📄</div>
                        <div class="action-info">
                            <span class="action-name">Reporte PDF</span>
                            <span class="action-desc">Exportar inventario actual</span>
                        </div>
                    </a>

                    <a href="<?= BASE_URL ?>/inventario/listar" class="action-link">
                        <div class="action-icon">⚙️</div>
                        <div class="action-info">
                            <span class="action-name">Tablas de Depuración</span>
                            <span class="action-desc">Ver todas las tablas relacionales</span>
                        </div>
                    </a>
                </div>
            </div>

        </div>
    </div>

    <footer>
        Laboratorio de Microbiología &copy; <?= date('Y') ?> - UPTP. Todos los derechos reservados.
    </footer>

</body>

</html>