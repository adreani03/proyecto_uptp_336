<?php
namespace App\Domain\Entities;

class Usuario {
    private string $cedula;
    private string $nombre;
    private ?string $pnf;
    private string $rol;
    private string $password;

    public function __construct(string $cedula, string $nombre, ?string $pnf, string $rol, string $password) {
        $this->cedula = $cedula;
        $this->nombre = $nombre;
        $this->pnf = $pnf;
        $this->rol = $rol;
        $this->password = $password;
    }

    public function getCedula(): string { return $this->cedula; }
    public function getNombre(): string { return $this->nombre; }
    public function getPnf(): ?string { return $this->pnf; }
    public function getRol(): string { return $this->rol; }
    public function getPassword(): string { return $this->password; }

    public function verifyPassword(string $inputPassword): bool {
        return password_verify($inputPassword, $this->password);
    }
}
