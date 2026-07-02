<?php
namespace App\Domain\Repositories;

use App\Domain\Entities\Usuario;

interface UsuarioRepository {
    public function findByCedula(string $cedula): ?Usuario;
    public function save(Usuario $usuario): void;
}
