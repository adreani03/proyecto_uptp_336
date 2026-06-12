<?php
namespace App\Domain\Repositories;

use App\Domain\Entities\Inventario;

interface InventarioRepository {
    public function findAll(): array;
    public function findById(int $id): ?Inventario;
    public function save(Inventario $inventario): void;
    public function delete(int $id): void;
}
