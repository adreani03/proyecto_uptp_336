
<?php
// init_test_db.php - Script con datos simulados para pruebas de programaci�n

try {
    // 1. Crear y conectar la base de datos en un solo archivo SQLite
    $db = new PDO('sqlite:microbiologia.db');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h2>--- INICIANDO INSTALACI�N DE LA BASE DE DATOS (MODO TEST) ---</h2>";

    // 2. Definir las 6 tablas estructuradas limpiamente
    $sql_tablas = "
        -- TABLA 1: Categor�as
        CREATE TABLE IF NOT EXISTS categorias (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nombre TEXT NOT NULL UNIQUE
        );

        -- TABLA 2: Materias
        CREATE TABLE IF NOT EXISTS materias (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nombre TEXT NOT NULL UNIQUE
        );

        -- TABLA 3: Usuarios
        CREATE TABLE IF NOT EXISTS usuarios (
            cedula TEXT PRIMARY KEY,
            nombre TEXT NOT NULL,
            pnf TEXT,
            rol TEXT NOT NULL CHECK(rol IN ('administrador', 'docente', 'estudiante', 'investigador'))
        );

        -- TABLA 4: Inventario (Equipos y Consumibles juntos)
        CREATE TABLE IF NOT EXISTS inventario (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            nombre TEXT NOT NULL,
            categoria_id INTEGER,
            tipo TEXT NOT NULL CHECK(tipo IN ('Equipo', 'Consumible')),
            serial_codigo TEXT UNIQUE,
            numero_lote TEXT,
            fecha_vencimiento TEXT,
            stock_actual INTEGER DEFAULT 0,
            stock_minimo INTEGER DEFAULT 0,
            stock_maximo INTEGER DEFAULT 0,
            estado TEXT DEFAULT 'Disponible' CHECK(estado IN ('Disponible', 'Operativo con Observaciones', 'En Mantenimiento', 'Fuera de Servicio', 'Contaminado')),
            ubicacion TEXT,
            hoja_seguridad TEXT,
            FOREIGN KEY (categoria_id) REFERENCES categorias(id)
        );

        -- TABLA 5: Pr�stamos
        CREATE TABLE IF NOT EXISTS prestamos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            docente_cedula TEXT NOT NULL,
            inventario_id INTEGER NOT NULL,
            materia_id INTEGER NOT NULL,
            seccion TEXT NOT NULL,
            fecha_prestamo TEXT DEFAULT CURRENT_TIMESTAMP,
            fecha_devolucion TEXT,
            estado_entrega TEXT NOT NULL,
            estado_devolucion TEXT,
            observaciones TEXT,
            protocolo_limpieza INTEGER DEFAULT 0 CHECK(protocolo_limpieza IN (0, 1)),
            FOREIGN KEY (docente_cedula) REFERENCES usuarios(cedula),
            FOREIGN KEY (inventario_id) REFERENCES inventario(id),
            FOREIGN KEY (materia_id) REFERENCES materias(id)
        );

        -- TABLA 6: Movimiento de Inventario (Auditor�a de Stock)
        CREATE TABLE IF NOT EXISTS movimiento_inventario (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            inventario_id INTEGER NOT NULL,
            usuario_cedula TEXT NOT NULL,
            tipo_movimiento TEXT NOT NULL CHECK(tipo_movimiento IN ('Entrada', 'Consumo Directo', 'Extrav�o', 'Mal Estado')),
            cantidad INTEGER NOT NULL,
            fecha TEXT DEFAULT CURRENT_TIMESTAMP,
            motivo TEXT,
            FOREIGN KEY (inventario_id) REFERENCES inventario(id),
            FOREIGN KEY (usuario_cedula) REFERENCES usuarios(cedula)
        );
    ";

    // Ejecutar la creaci�n de las tablas
    $db->exec($sql_tablas);
    echo "? Las 6 tablas l�gicas han sido creadas de forma exitosa.<br>";

    // 3. Insertar datos simulados para mantener las apariencias con el inventario
    $conteo_productos = $db->query("SELECT COUNT(*) FROM inventario")->fetchColumn();
    
    if ($conteo_productos == 0) {
        
        // Insertar Categor�as b�sicas
        $db->exec("INSERT INTO categorias (nombre) VALUES ('Equipos'), ('Reactivos'), ('Vidrier�a')");
        
        // Insertar Materias de Veterinaria
        $db->exec("INSERT INTO materias (nombre) VALUES ('Microbiolog�a II'), ('Parasitolog�a')");
        
        // Insertar Usuarios de prueba
        $db->exec("INSERT INTO usuarios (cedula, nombre, pnf, rol) VALUES 
            ('V-12345678', 'Profesor Jes�s Aguiar', 'Veterinaria', 'docente'),
            ('V-87654321', 'Darwin Encargado', 'Inform�tica', 'administrador')");

        // Insertar un Equipo (Microscopio) y un Consumible (Agar)
        $db->exec("INSERT INTO inventario (nombre, categoria_id, tipo, serial_codigo, stock_actual, stock_minimo, stock_maximo, estado, ubicacion) VALUES 
            ('Microscopio �ptico Olympus 01', 1, 'Equipo', 'MIC-OLY-01', 1, 1, 1, 'Disponible', 'Estante A-Estraquina derecha'),
            ('Agar Nutritivo (Medio de Cultivo)', 2, 'Consumible', NULL, 500, 100, 1000, 'Disponible', 'Nevera de Reactivos N-1')");

        // Simular un pr�stamo activo
        $db->exec("INSERT INTO prestamos (docente_cedula, inventario_id, materia_id, seccion, estado_entrega) VALUES 
            ('V-12345678', 1, 1, 'Secci�n A', 'Disponible')");

        echo "? Datos de prueba e inventario simulado cargados correctamente.<br>";
    }

    echo "<h3>?? �Minijuego configurado en modo TEST! Base de datos lista.</h3>";

} catch (PDOException $e) {
    echo "? Ocurri� un error en la base de datos: " . $e->getMessage();
}
?>
