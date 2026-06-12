<?php
namespace App\Presentation\Controllers;

use App\Infrastructure\Database\DatabaseConnection;
use App\Infrastructure\Persistence\InventarioRepositorySQLite;
use App\Domain\Entities\Inventario;

class InventarioController {
    private InventarioRepositorySQLite $repository;

    public function __construct() {
        $db = DatabaseConnection::getInstance();
        $this->repository = new InventarioRepositorySQLite($db);
        
        // Ensure session is started for auth checks and flash messages
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    private function checkAuth() {
        if (!isset($_SESSION['usuario_cedula'])) {
            header('Location: ' . BASE_URL . '/login');
            exit;
        }
    }

    public function index(): void {
        $this->checkAuth();
        $inventarios = $this->repository->findAll();
        require __DIR__ . '/../Views/inventario/index.php';
    }

    public function create(): void {
        $this->checkAuth();
        require __DIR__ . '/../Views/inventario/crear.php';
    }

    public function store(): void {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inventario = new Inventario(
                null,
                $_POST['nombre'] ?? '',
                !empty($_POST['categoria_id']) ? (int)$_POST['categoria_id'] : null,
                $_POST['tipo'] ?? 'Consumible',
                $_POST['serial_codigo'] ?: null,
                $_POST['numero_lote'] ?: null,
                $_POST['fecha_vencimiento'] ?: null,
                isset($_POST['stock_actual']) ? (int)$_POST['stock_actual'] : 0,
                isset($_POST['stock_minimo']) ? (int)$_POST['stock_minimo'] : 0,
                isset($_POST['stock_maximo']) ? (int)$_POST['stock_maximo'] : 0,
                $_POST['estado'] ?? 'Disponible',
                $_POST['ubicacion'] ?: null,
                $_POST['hoja_seguridad'] ?: null
            );

            $this->repository->save($inventario);
            header('Location: ' . BASE_URL . '/inventario');
            exit;
        }
    }

    public function edit(): void {
        $this->checkAuth();
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $inventario = $this->repository->findById($id);

        if (!$inventario) {
            header('Location: ' . BASE_URL . '/inventario');
            exit;
        }

        require __DIR__ . '/../Views/inventario/editar.php';
    }

    public function update(): void {
        $this->checkAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
            $inventario = new Inventario(
                $id,
                $_POST['nombre'] ?? '',
                !empty($_POST['categoria_id']) ? (int)$_POST['categoria_id'] : null,
                $_POST['tipo'] ?? 'Consumible',
                $_POST['serial_codigo'] ?: null,
                $_POST['numero_lote'] ?: null,
                $_POST['fecha_vencimiento'] ?: null,
                isset($_POST['stock_actual']) ? (int)$_POST['stock_actual'] : 0,
                isset($_POST['stock_minimo']) ? (int)$_POST['stock_minimo'] : 0,
                isset($_POST['stock_maximo']) ? (int)$_POST['stock_maximo'] : 0,
                $_POST['estado'] ?? 'Disponible',
                $_POST['ubicacion'] ?: null,
                $_POST['hoja_seguridad'] ?: null
            );

            $this->repository->save($inventario);
            header('Location: ' . BASE_URL . '/inventario');
            exit;
        }
    }

    public function delete(): void {
        $this->checkAuth();
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id > 0) {
            $this->repository->delete($id);
        }
        header('Location: ' . BASE_URL . '/inventario');
        exit;
    }
}
