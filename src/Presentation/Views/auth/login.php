<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Laboratorio Microbiología</title>
    <meta name="description" content="Inicia sesión en el sistema de inventario del laboratorio de microbiología.">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-box glassmorphism">
            <div class="login-header">
                <div class="logo-icon">🔬</div>
                <h1>Bienvenido</h1>
                <p>Sistema de Inventario - Microbiología</p>
            </div>
            
            <?php if (isset($error) && $error): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/login" method="POST" class="login-form">
                <div class="input-group">
                    <label for="cedula">Cédula</label>
                    <input type="text" id="cedula" name="cedula" required placeholder="V-12345678" autofocus>
                </div>
                <div class="input-group">
                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" required placeholder="••••••••">
                </div>
                <button type="submit" class="btn-primary">Ingresar</button>
            </form>
            <div class="login-footer">
                <p>¿Problemas para acceder? Contacta al administrador.</p>
            </div>
        </div>
        <div class="background-blobs">
            <div class="blob blob-1"></div>
            <div class="blob blob-2"></div>
        </div>
    </div>
</body>
</html>
