<?php
namespace App\Domain\Entities;

class Inventario {
    private ?int $id;
    private string $nombre;
    private ?int $categoriaId;
    private string $tipo;
    private ?string $serialCodigo;
    private ?string $numeroLote;
    private ?string $fechaVencimiento;
    private int $stockActual;
    private int $stockMinimo;
    private int $stockMaximo;
    private string $estado;
    private ?string $ubicacion;
    private ?string $hojaSeguridad;

    public function __construct(
        ?int $id,
        string $nombre,
        ?int $categoriaId,
        string $tipo,
        ?string $serialCodigo,
        ?string $numeroLote,
        ?string $fechaVencimiento,
        int $stockActual,
        int $stockMinimo,
        int $stockMaximo,
        string $estado,
        ?string $ubicacion,
        ?string $hojaSeguridad
    ) {
        if ($stockActual < 0) {
            throw new \InvalidArgumentException("El stock actual no puede ser negativo.");
        }
        if ($stockMinimo < 0) {
            throw new \InvalidArgumentException("El stock mínimo no puede ser negativo.");
        }
        if ($stockMaximo < 0) {
            throw new \InvalidArgumentException("El stock máximo no puede ser negativo.");
        }
        $this->id = $id;
        $this->nombre = $nombre;
        $this->categoriaId = $categoriaId;
        $this->tipo = $tipo;
        $this->serialCodigo = $serialCodigo;
        $this->numeroLote = $numeroLote;
        $this->fechaVencimiento = $fechaVencimiento;
        $this->stockActual = $stockActual;
        $this->stockMinimo = $stockMinimo;
        $this->stockMaximo = $stockMaximo;
        $this->estado = $estado;
        $this->ubicacion = $ubicacion;
        $this->hojaSeguridad = $hojaSeguridad;
    }

    public function getId(): ?int { return $this->id; }
    public function getNombre(): string { return $this->nombre; }
    public function getCategoriaId(): ?int { return $this->categoriaId; }
    public function getTipo(): string { return $this->tipo; }
    public function getSerialCodigo(): ?string { return $this->serialCodigo; }
    public function getNumeroLote(): ?string { return $this->numeroLote; }
    public function getFechaVencimiento(): ?string { return $this->fechaVencimiento; }
    public function getStockActual(): int { return $this->stockActual; }
    public function getStockMinimo(): int { return $this->stockMinimo; }
    public function getStockMaximo(): int { return $this->stockMaximo; }
    public function getEstado(): string { return $this->estado; }
    public function getUbicacion(): ?string { return $this->ubicacion; }
    public function getHojaSeguridad(): ?string { return $this->hojaSeguridad; }
}
