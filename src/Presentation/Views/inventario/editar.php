<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Artículo - Inventario</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0f172a;
            --glass-bg: rgba(30, 41, 59, 0.7);
            --glass-border: rgba(255, 255, 255, 0.1);
            --primary: #38bdf8;
            --primary-hover: #0ea5e9;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --input-bg: rgba(15, 23, 42, 0.6);
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
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
        }

        header {
            margin-bottom: 2rem;
        }

        h1 {
            margin: 0;
            font-weight: 600;
            font-size: 2rem;
            background: -webkit-linear-gradient(45deg, #38bdf8, #818cf8);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        label {
            font-size: 0.9rem;
            color: var(--text-muted);
            font-weight: 600;
        }

        input, select {
            background: var(--input-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-main);
            padding: 0.75rem 1rem;
            border-radius: 10px;
            font-family: inherit;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        input:focus, select:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(56, 189, 248, 0.2);
        }

        select option {
            background: var(--bg-color);
            color: var(--text-main);
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
            text-align: center;
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

        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--glass-border);
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Editar Artículo</h1>
            <p style="color: var(--text-muted); margin-top: 0.5rem;">Modificando: <?= htmlspecialchars($inventario->getNombre()) ?></p>
        </header>

        <?php if (isset($_SESSION['error'])): ?>
            <div style="background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.4); padding: 1rem; border-radius: 10px; margin-bottom: 1.5rem; color: #fca5a5;">
                <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="glass-card">
            <form action="<?= BASE_URL ?>/inventario/editar" method="POST">
                <input type="hidden" name="id" value="<?= htmlspecialchars($inventario->getId()) ?>">
                
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label for="nombre">Nombre del Artículo *</label>
                        <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($inventario->getNombre()) ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="tipo">Tipo *</label>
                        <select id="tipo" name="tipo" required>
                            <option value="Consumible" <?= $inventario->getTipo() === 'Consumible' ? 'selected' : '' ?>>Consumible / Reactivo</option>
                            <option value="Equipo" <?= $inventario->getTipo() === 'Equipo' ? 'selected' : '' ?>>Equipo</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="categoria_id">Categoría (Opcional)</label>
                        <select id="categoria_id" name="categoria_id">
                            <option value="">-- Seleccionar Categoría --</option>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= $inventario->getCategoriaId() == $cat['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat['nombre']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="serial_codigo">Serial / Código</label>
                        <input type="text" id="serial_codigo" name="serial_codigo" value="<?= htmlspecialchars($inventario->getSerialCodigo() ?? '') ?>" placeholder="Aplica para equipos">
                    </div>

                    <div class="form-group">
                        <label for="numero_lote">Número de Lote</label>
                        <input type="text" id="numero_lote" name="numero_lote" value="<?= htmlspecialchars($inventario->getNumeroLote() ?? '') ?>" placeholder="Aplica para reactivos">
                    </div>

                    <div class="form-group">
                        <label for="fecha_vencimiento">Fecha de Vencimiento</label>
                        <input type="date" id="fecha_vencimiento" name="fecha_vencimiento" value="<?= htmlspecialchars($inventario->getFechaVencimiento() ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="estado">Estado</label>
                        <select id="estado" name="estado">
                            <?php 
                            $estados = ['Disponible', 'Operativo con Observaciones', 'En Mantenimiento', 'Fuera de Servicio', 'Contaminado'];
                            foreach($estados as $e): 
                            ?>
                                <option value="<?= $e ?>" <?= $inventario->getEstado() === $e ? 'selected' : '' ?>><?= $e ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="stock_actual">Stock Actual *</label>
                        <input type="number" id="stock_actual" name="stock_actual" value="<?= htmlspecialchars($inventario->getStockActual()) ?>" min="0" required>
                    </div>

                    <div class="form-group">
                        <label for="stock_minimo">Stock Mínimo</label>
                        <input type="number" id="stock_minimo" name="stock_minimo" value="<?= htmlspecialchars($inventario->getStockMinimo()) ?>" min="0">
                    </div>

                    <div class="form-group">
                        <label for="stock_maximo">Stock Máximo</label>
                        <input type="number" id="stock_maximo" name="stock_maximo" value="<?= htmlspecialchars($inventario->getStockMaximo()) ?>" min="0">
                    </div>

                    <div class="form-group">
                        <label for="ubicacion">Ubicación</label>
                        <input type="text" id="ubicacion" name="ubicacion" value="<?= htmlspecialchars($inventario->getUbicacion() ?? '') ?>" placeholder="Ej: Estante A, Fila 3">
                    </div>

                    <div class="form-group">
                        <label for="hoja_seguridad">Ficha de Seguridad (Hoja MSDS)</label>
                        <input type="text" id="hoja_seguridad" name="hoja_seguridad" value="<?= htmlspecialchars($inventario->getHojaSeguridad() ?? '') ?>" placeholder="Ej: Enlace web o ubicación física">
                    </div>
                </div>

                <div class="actions">
                    <a href="<?= BASE_URL ?>/inventario" class="btn btn-outline">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Actualizar Artículo</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tipoSelect = document.getElementById('tipo');
            const serialGroup = document.getElementById('serial_codigo').closest('.form-group');
            const loteGroup = document.getElementById('numero_lote').closest('.form-group');
            const vencimientoGroup = document.getElementById('fecha_vencimiento').closest('.form-group');
            
            const serialInput = document.getElementById('serial_codigo');
            const loteInput = document.getElementById('numero_lote');
            const vencimientoInput = document.getElementById('fecha_vencimiento');

            function toggleFields() {
                const tipo = tipoSelect.value;
                if (tipo === 'Equipo') {
                    // Mostrar Serial, Ocultar Lote y Vencimiento
                    serialGroup.style.display = 'flex';
                    serialInput.disabled = false;
                    
                    loteGroup.style.display = 'none';
                    loteInput.disabled = true;
                    vencimientoGroup.style.display = 'none';
                    vencimientoInput.disabled = true;
                } else {
                    // Mostrar Lote y Vencimiento, Ocultar Serial
                    serialGroup.style.display = 'none';
                    serialInput.disabled = true;
                    
                    loteGroup.style.display = 'flex';
                    loteInput.disabled = false;
                    vencimientoGroup.style.display = 'flex';
                    vencimientoInput.disabled = false;
                }
            }

            tipoSelect.addEventListener('change', toggleFields);
            toggleFields(); // Ejecutar al inicio
            
            // Validación del cliente
            const form = document.querySelector('form');
            form.addEventListener('submit', function(e) {
                const stockMin = parseInt(document.getElementById('stock_minimo').value) || 0;
                const stockMax = parseInt(document.getElementById('stock_maximo').value) || 0;
                const stockAct = parseInt(document.getElementById('stock_actual').value) || 0;

                if (stockMin < 0 || stockMax < 0 || stockAct < 0) {
                    alert('Los valores de stock no pueden ser negativos.');
                    e.preventDefault();
                    return;
                }

                if (stockMax > 0 && stockMin > stockMax) {
                    alert('El Stock Mínimo no puede ser mayor que el Stock Máximo.');
                    e.preventDefault();
                    return;
                }
            });
        });
    </script>
</body>
</html>
