<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Artículo - Inventario</title>
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
            <h1>Nuevo Artículo</h1>
            <p style="color: var(--text-muted); margin-top: 0.5rem;">Registra un nuevo reactivo o equipo en el inventario</p>
        </header>

        <div class="glass-card">
            <form action="<?= BASE_URL ?>/inventario/crear" method="POST">
                <div class="form-grid">
                    <div class="form-group full-width">
                        <label for="nombre">Nombre del Artículo *</label>
                        <input type="text" id="nombre" name="nombre" required placeholder="Ej: Microscopio Óptico / Ácido Clorhídrico">
                    </div>

                    <div class="form-group">
                        <label for="tipo">Tipo *</label>
                        <select id="tipo" name="tipo" required>
                            <option value="Consumible">Consumible / Reactivo</option>
                            <option value="Equipo">Equipo</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="categoria_id">ID Categoría (Opcional)</label>
                        <input type="number" id="categoria_id" name="categoria_id" placeholder="ID numérico">
                    </div>

                    <div class="form-group">
                        <label for="serial_codigo">Serial / Código</label>
                        <input type="text" id="serial_codigo" name="serial_codigo" placeholder="Aplica más para equipos">
                    </div>

                    <div class="form-group">
                        <label for="numero_lote">Número de Lote</label>
                        <input type="text" id="numero_lote" name="numero_lote" placeholder="Aplica para reactivos">
                    </div>

                    <div class="form-group">
                        <label for="stock_actual">Stock Actual *</label>
                        <input type="number" id="stock_actual" name="stock_actual" value="0" required>
                    </div>

                    <div class="form-group">
                        <label for="stock_minimo">Stock Mínimo</label>
                        <input type="number" id="stock_minimo" name="stock_minimo" value="0">
                    </div>

                    <div class="form-group">
                        <label for="fecha_vencimiento">Fecha de Vencimiento</label>
                        <input type="date" id="fecha_vencimiento" name="fecha_vencimiento">
                    </div>

                    <div class="form-group">
                        <label for="estado">Estado</label>
                        <select id="estado" name="estado">
                            <option value="Disponible">Disponible</option>
                            <option value="Operativo con Observaciones">Operativo con Observaciones</option>
                            <option value="En Mantenimiento">En Mantenimiento</option>
                            <option value="Fuera de Servicio">Fuera de Servicio</option>
                            <option value="Contaminado">Contaminado</option>
                        </select>
                    </div>

                    <div class="form-group full-width">
                        <label for="ubicacion">Ubicación</label>
                        <input type="text" id="ubicacion" name="ubicacion" placeholder="Ej: Estante A, Fila 3">
                    </div>
                </div>

                <div class="actions">
                    <a href="<?= BASE_URL ?>/inventario" class="btn btn-outline">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Guardar Artículo</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
