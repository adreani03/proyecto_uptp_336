<?php
namespace App\Infrastructure\Database;

use PDO;
use PDOException;

class DatabaseConnection {
    private static ?PDO $instance = null;

    public static function getInstance(): PDO {
        if (self::$instance === null) {
            try {
                // Asumiendo que la BD está en la raíz del proyecto
                $dbPath = __DIR__ . '/../../../microbiologia.db';
                self::$instance = new PDO("sqlite:$dbPath");
                self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("Error de conexión a la base de datos: " . $e->getMessage());
            }
        }
        return self::$instance;
    }
}
