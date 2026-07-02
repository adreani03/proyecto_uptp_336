<?php
namespace App\Infrastructure\Persistence;

use App\Domain\Entities\Inventario;
use App\Domain\Repositories\InventarioRepository;
use PDO;

class InventarioRepositorySQLite implements InventarioRepository {
    private PDO $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    private function mapRowToEntity(array $row): Inventario {
        return new Inventario(
            $row['id'] ? (int)$row['id'] : null,
            $row['nombre'],
            $row['categoria_id'] ? (int)$row['categoria_id'] : null,
            $row['tipo'],
            $row['serial_codigo'] ?? null,
            $row['numero_lote'] ?? null,
            $row['fecha_vencimiento'] ?? null,
            (int)($row['stock_actual'] ?? 0),
            (int)($row['stock_minimo'] ?? 0),
            (int)($row['stock_maximo'] ?? 0),
            $row['estado'] ?? 'Disponible',
            $row['ubicacion'] ?? null,
            $row['hoja_seguridad'] ?? null
        );
    }

    public function findAll(): array {
        $stmt = $this->db->query("SELECT * FROM inventario ORDER BY id DESC");
        $items = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $items[] = $this->mapRowToEntity($row);
        }
        return $items;
    }

    public function findById(int $id): ?Inventario {
        $stmt = $this->db->prepare("SELECT * FROM inventario WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            return null;
        }

        return $this->mapRowToEntity($row);
    }

    public function save(Inventario $inventario): void {
        if ($inventario->getId() !== null) {
            // Update
            $stmt = $this->db->prepare("
                UPDATE inventario SET 
                    nombre = :nombre,
                    categoria_id = :categoria_id,
                    tipo = :tipo,
                    serial_codigo = :serial_codigo,
                    numero_lote = :numero_lote,
                    fecha_vencimiento = :fecha_vencimiento,
                    stock_actual = :stock_actual,
                    stock_minimo = :stock_minimo,
                    stock_maximo = :stock_maximo,
                    estado = :estado,
                    ubicacion = :ubicacion,
                    hoja_seguridad = :hoja_seguridad
                WHERE id = :id
            ");
            $stmt->execute([
                'id' => $inventario->getId(),
                'nombre' => $inventario->getNombre(),
                'categoria_id' => $inventario->getCategoriaId(),
                'tipo' => $inventario->getTipo(),
                'serial_codigo' => $inventario->getSerialCodigo(),
                'numero_lote' => $inventario->getNumeroLote(),
                'fecha_vencimiento' => $inventario->getFechaVencimiento(),
                'stock_actual' => $inventario->getStockActual(),
                'stock_minimo' => $inventario->getStockMinimo(),
                'stock_maximo' => $inventario->getStockMaximo(),
                'estado' => $inventario->getEstado(),
                'ubicacion' => $inventario->getUbicacion(),
                'hoja_seguridad' => $inventario->getHojaSeguridad()
            ]);
        } else {
            // Insert
            $stmt = $this->db->prepare("
                INSERT INTO inventario (
                    nombre, categoria_id, tipo, serial_codigo, numero_lote, 
                    fecha_vencimiento, stock_actual, stock_minimo, stock_maximo, 
                    estado, ubicacion, hoja_seguridad
                ) VALUES (
                    :nombre, :categoria_id, :tipo, :serial_codigo, :numero_lote, 
                    :fecha_vencimiento, :stock_actual, :stock_minimo, :stock_maximo, 
                    :estado, :ubicacion, :hoja_seguridad
                )
            ");
            $stmt->execute([
                'nombre' => $inventario->getNombre(),
                'categoria_id' => $inventario->getCategoriaId(),
                'tipo' => $inventario->getTipo(),
                'serial_codigo' => $inventario->getSerialCodigo(),
                'numero_lote' => $inventario->getNumeroLote(),
                'fecha_vencimiento' => $inventario->getFechaVencimiento(),
                'stock_actual' => $inventario->getStockActual(),
                'stock_minimo' => $inventario->getStockMinimo(),
                'stock_maximo' => $inventario->getStockMaximo(),
                'estado' => $inventario->getEstado(),
                'ubicacion' => $inventario->getUbicacion(),
                'hoja_seguridad' => $inventario->getHojaSeguridad()
            ]);
        }
    }

    public function delete(int $id): void {
        $stmt = $this->db->prepare("DELETE FROM inventario WHERE id = :id");
        $stmt->execute(['id' => $id]);
    }
}
