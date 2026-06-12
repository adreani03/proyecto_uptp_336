# Sistema de Control de Inventario - Laboratorio de Microbiología

Bienvenido al repositorio del Sistema de Control de Inventario para el laboratorio de microbiología. Este proyecto es una aplicación web construida en **PHP Nativo** (sin frameworks pesados), utilizando **SQLite** como base de datos, y estructurada siguiendo los principios de la **Clean Architecture** (Arquitectura Limpia).

Este README está diseñado para ayudar a los nuevos desarrolladores a entender rápidamente cómo funciona el proyecto y cómo configurarlo en su entorno local.

---

## 🏗️ Arquitectura del Proyecto (Clean Architecture)

El proyecto está dividido en capas para asegurar que el código sea mantenible, escalable y fácil de probar. Toda la lógica principal se encuentra dentro de la carpeta `src/`.

*   **`Domain/` (Dominio):** El corazón de la aplicación. Aquí se encuentran las **Entidades** (como `Usuario.php`, `Inventario.php`), que representan los conceptos principales del negocio, y las **Interfaces de los Repositorios**, que dictan qué operaciones de base de datos se pueden hacer, pero sin saber *cómo* se hacen.
*   **`Application/` (Aplicación):** Contiene los **Casos de Uso** (Use Cases). Aquí reside la lógica específica de la aplicación (ej. `LoginUseCase.php`). Orquesta el flujo de datos entre la vista y el dominio.
*   **`Infrastructure/` (Infraestructura):** Es la capa técnica. Aquí se encuentra la conexión a la base de datos (`DatabaseConnection.php`) y la implementación real de los repositorios usando SQL/PDO (ej. `UsuarioRepositorySQLite.php`, `InventarioRepositorySQLite.php`).
*   **`Presentation/` (Presentación):** Es la capa visual y de interacción.
    *   **`Controllers/`:** Reciben las peticiones HTTP (GET, POST), llaman a la infraestructura o los casos de uso, y devuelven una vista (`InventarioController.php`, `AuthController.php`).
    *   **`Views/`:** Son los archivos HTML/PHP que dibujan la interfaz gráfica (Login, Dashboard, CRUD de Inventario).

## 🗂️ Estructura de Directorios Principal

```text
proyecto_uptp_336/
├── public/                 # Carpeta pública accesible desde el navegador
│   ├── index.php           # Front Controller y Enrutador principal de la app
│   ├── .htaccess           # Redirige todo el tráfico web hacia index.php (Apache)
│   └── assets/             # CSS, imágenes, y JS públicos
├── src/                    # Código fuente (Domain, Application, Infrastructure, Presentation)
├── init_db.php             # Script para crear la DB vacía (Producción)
├── init_test_db.php        # Script para crear y poblar la DB con datos de prueba
├── microbiologia.db        # Archivo físico de la base de datos SQLite (se auto-genera)
└── doc/                    # Documentación adicional (guías de arquitectura)
```

## 🚀 Requisitos e Instalación

Para ejecutar este proyecto en tu computadora, necesitas:
1. **XAMPP**, WAMP o similar (que incluya Apache y PHP 8.x).
2. Tener habilitada la extensión PDO de SQLite en PHP.

### Pasos para ejecutar:

1. **Clonar o copiar** esta carpeta dentro del directorio público de tu servidor local (en XAMPP, sería dentro de `C:\xampp\htdocs\`).
2. **Inicializar la Base de Datos:** Abre tu navegador y ejecuta el script de pruebas para crear las tablas y rellenarlas con información ficticia:
   ```text
   http://localhost/proyecto_uptp_336/init_test_db.php
   ```
   *(Asegúrate de cambiar `proyecto_uptp_336` por el nombre exacto de la carpeta si la renombraste).*
3. **Acceder al Sistema:** Una vez inicializada la base de datos, entra a la aplicación:
   ```text
   http://localhost/proyecto_uptp_336/public/
   ```

## 🔐 Usuarios de Prueba

Si ejecutaste `init_test_db.php`, puedes iniciar sesión en el sistema usando alguna de estas credenciales:

*   **Administrador:**
    *   Cédula: `V-87654321`
    *   Contraseña: `password123`
*   **Docente:**
    *   Cédula: `V-12345678`
    *   Contraseña: `password123`

## 🛣️ Enrutamiento y `BASE_URL`

El sistema utiliza un único punto de entrada: `public/index.php` (Front Controller). Gracias al archivo `.htaccess`, cualquier ruta (como `/login` o `/inventario`) es procesada por `index.php`.

Para evitar problemas de rutas en servidores locales (como XAMPP donde la app está en una subcarpeta), en `index.php` se define globalmente la constante **`BASE_URL`**.
*   **En Controladores:** Usamos `header('Location: ' . BASE_URL . '/ruta');` para redireccionar.
*   **En Vistas:** Usamos `<?= BASE_URL ?>/ruta` en los atributos `href` de enlaces o `action` de formularios.

## ✨ Características Principales
*   **Autenticación de Usuarios:** Login funcional con protección de contraseñas mediante hash.
*   **CRUD de Inventario:** Listar, crear, editar y eliminar equipos y consumibles.
*   **UI Moderna:** Interfaz implementada con CSS nativo, enfocada en una estética "Glassmorphism" (estilo cristal), garantizando una experiencia visual premium y fluida.

---

> Si tienes dudas adicionales, puedes revisar el código de `src/Presentation/Controllers/InventarioController.php` para ver cómo fluye una petición desde la ruta hasta la base de datos y la vista final.
