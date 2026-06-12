-- 1. AGREGAR un nuevo profesor
INSERT INTO usuarios (cedula, nombre, pnf, rol) 
VALUES (?, ?, ?, 'docente');

-- 2. VER todos los profesores en una tabla
SELECT * FROM usuarios WHERE rol = 'docente';

-- 3. MODIFICAR los datos de un profesor (Buscando por su cédula)
UPDATE usuarios 
SET nombre = ?, pnf = ? 
WHERE cedula = ?;

-- 4. ELIMINAR a un profesor del sistema
DELETE FROM usuarios WHERE cedula = ?;

-- 1. AGREGAR un nuevo objeto (Equipo o Consumible)
INSERT INTO inventario (nombre, categoria_id, tipo, serial_codigo, numero_lote, fecha_vencimiento, stock_actual, stock_minimo, stock_maximo, estado, ubicacion, hoja_seguridad) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?);

-- 2. VER todo el inventario junto
SELECT * FROM inventario;

-- 3. VER solo lo que está dañado o en reparación (Módulo 3)
SELECT * FROM inventario 
WHERE estado IN ('Fuera de Servicio', 'Contaminado', 'Operativo con Observaciones');

-- 4. MODIFICAR un objeto o cambiar su estado/cantidad (Buscando por su ID)
UPDATE inventario 
SET nombre = ?, categoria_id = ?, tipo = ?, serial_codigo = ?, numero_lote = ?, fecha_vencimiento = ?, stock_actual = ?, stock_minimo = ?, stock_maximo = ?, estado = ?, ubicacion = ?, hoja_seguridad = ? 
WHERE id = ?;

-- 5. ELIMINAR un objeto del inventario
DELETE FROM inventario WHERE id = ?;


