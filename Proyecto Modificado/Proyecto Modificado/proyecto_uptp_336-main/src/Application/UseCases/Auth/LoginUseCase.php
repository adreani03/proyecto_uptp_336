<?php
namespace App\Application\UseCases\Auth;

use App\Domain\Repositories\UsuarioRepository;

class LoginUseCase {
    private UsuarioRepository $usuarioRepository;

    public function __construct(UsuarioRepository $usuarioRepository) {
        $this->usuarioRepository = $usuarioRepository;
    }

    public function execute(string $cedula, string $password): array {
        $usuario = $this->usuarioRepository->findByCedula($cedula);

        if (!$usuario) {
            return ['success' => false, 'message' => 'Usuario no encontrado'];
        }

        if (!$usuario->verifyPassword($password)) {
            return ['success' => false, 'message' => 'Contraseña incorrecta'];
        }

        // Si es válido, iniciar sesión
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['usuario_cedula'] = $usuario->getCedula();
        $_SESSION['usuario_nombre'] = $usuario->getNombre();
        $_SESSION['usuario_rol'] = $usuario->getRol();

        return ['success' => true, 'message' => 'Login exitoso', 'usuario' => $usuario];
    }
}
