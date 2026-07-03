<?php
// Iniciar sesión globalmente
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Autocargador simple tipo PSR-4
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = __DIR__ . '/../src/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

use App\Presentation\Controllers\AuthController;
use App\Presentation\Controllers\InventarioController;

// Enrutador muy simple
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Eliminar el script name base si la app está en un subdirectorio
$base_path = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
define('BASE_URL', $base_path);

if (strlen($base_path) > 0 && strpos($request_uri, $base_path) === 0) {
    $request_uri = substr($request_uri, strlen($base_path));
}
if ($request_uri === '' || $request_uri === '/') {
    if (isset($_SESSION['usuario_cedula'])) {
        $request_uri = '/dashboard';
    } else {
        $request_uri = '/login';
    }
}

if ($request_uri === '/login') {
    $controller = new AuthController();
    $controller->login();
} elseif ($request_uri === '/logout') {
    $controller = new AuthController();
    $controller->logout();
} elseif ($request_uri === '/dashboard') {
    $controller = new InventarioController();
    $controller->dashboard();
} elseif ($request_uri === '/inventario') {
    $controller = new InventarioController();
    $controller->index();
} elseif ($request_uri === '/inventario/crear') {
    $controller = new InventarioController();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->store();
    } else {
        $controller->create();
    }
} elseif ($request_uri === '/inventario/editar') {
    $controller = new InventarioController();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->update();
    } else {
        $controller->edit();
    }
} elseif ($request_uri === '/inventario/eliminar') {
    $controller = new InventarioController();
    $controller->delete();
} elseif ($request_uri === '/inventario/listar') {
    $controller = new InventarioController();
    $controller->listar();
} else {
    // Para servir estáticos en el servidor de desarrollo PHP
    if (php_sapi_name() === 'cli-server') {
        $file = __DIR__ . $request_uri;
        if (is_file($file)) {
            return false;
        }
    }
    http_response_code(404);
    echo "Página no encontrada";
}
