<?php
namespace App\Presentation\Controllers;

use App\Application\UseCases\Auth\LoginUseCase;
use App\Infrastructure\Database\DatabaseConnection;
use App\Infrastructure\Persistence\UsuarioRepositorySQLite;

class AuthController {
    private LoginUseCase $loginUseCase;

    public function __construct() {
        $db = DatabaseConnection::getInstance();
        $repository = new UsuarioRepositorySQLite($db);
        $this->loginUseCase = new LoginUseCase($repository);
    }

    public function login(): void {
        if (isset($_SESSION['usuario_cedula'])) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit;
        }

        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cedula = $_POST['cedula'] ?? '';
            $password = $_POST['password'] ?? '';

            $result = $this->loginUseCase->execute($cedula, $password);

            if ($result['success']) {
                header('Location: ' . BASE_URL . '/dashboard');
                exit;
            } else {
                $error = $result['message'];
            }
        }

        require __DIR__ . '/../Views/auth/login.php';
    }

    public function logout(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        header('Location: ' . BASE_URL . '/login');
        exit;
    }
}
