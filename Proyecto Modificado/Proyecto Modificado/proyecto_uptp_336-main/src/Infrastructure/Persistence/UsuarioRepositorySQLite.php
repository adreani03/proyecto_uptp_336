<?php
namespace App\Infrastructure\Persistence;

use App\Domain\Entities\Usuario;
use App\Domain\Repositories\UsuarioRepository;
use PDO;

class UsuarioRepositorySQLite implements UsuarioRepository {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function findByCedula(string $cedula): ?Usuario {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE cedula = :cedula LIMIT 1");
        $stmt->execute(['cedula' => $cedula]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return new Usuario(
            $row['cedula'],
            $row['nombre'],
            $row['pnf'],
            $row['rol'],
            $row['password']
        );
    }

    public function save(Usuario $usuario): void {
        $stmt = $this->db->prepare("
            INSERT INTO usuarios (cedula, nombre, pnf, rol, password) 
            VALUES (:cedula, :nombre, :pnf, :rol, :password)
            ON CONFLICT(cedula) DO UPDATE SET 
                nombre = :nombre,
                pnf = :pnf,
                rol = :rol,
                password = :password
        ");

        $stmt->execute([
            'cedula' => $usuario->getCedula(),
            'nombre' => $usuario->getNombre(),
            'pnf' => $usuario->getPnf(),
            'rol' => $usuario->getRol(),
            'password' => $usuario->getPassword()
        ]);
    }
}
