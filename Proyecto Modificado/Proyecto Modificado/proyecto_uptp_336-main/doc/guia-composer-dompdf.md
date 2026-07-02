# Guía: Instalar Composer y DomPDF en el Proyecto

## Requisitos previos
- Tener **XAMPP** instalado (con PHP 8.x)
- Tener **Visual Studio Code** instalado
- El proyecto debe estar en `C:\xampp\htdocs\proyecto_uptp_336-main`

---

## Paso 1: Instalar Composer (gestor de paquetes PHP)

### 1.1 Descargar Composer
1. Abre tu navegador y ve a: **https://getcomposer.org/download/**
2. Descarga el archivo **Composer-Setup.exe** (para Windows)
3. Ejecuta el instalador y sigue los pasos del asistente

### 1.2 Configurar Composer durante la instalación
- Cuando te pregunte por la **ruta de PHP**, selecciona:
  ```
  C:\xampp\php\php.exe
  ```
- Marca la opción de **agregar Composer al PATH** (esencial)
- Termina la instalación

### 1.3 Verificar la instalación
1. Abre **Visual Studio Code**
2. Abre una terminal integrada con: `Ctrl + ñ` (o `Terminal > Nueva terminal`)
3. Escribe y ejecuta:
   ```bash
   composer --version
   ```
   Si ves algo como `Composer version 2.x.x`, la instalación fue exitosa.

---

## Paso 2: Abrir el proyecto en VS Code

1. En VS Code, ve a `Archivo > Abrir carpeta...`
2. Navega hasta: `C:\xampp\htdocs\proyecto_uptp_336-main`
3. Selecciona la carpeta y haz clic en **Abrir carpeta**
4. VS Code cargará todo el proyecto en el explorador lateral

---

## Paso 3: Instalar DomPDF con Composer

### 3.1 Abrir terminal en VS Code
1. Presiona `Ctrl + ñ` para abrir la terminal integrada
2. Asegúrate de que la terminal esté ubicada en la raíz del proyecto (donde está `public/`, `src/`, etc.)
3. Si no estás en la raíz, escribe:
   ```bash
   cd C:\xampp\htdocs\proyecto_uptp_336-main
   ```

### 3.2 Inicializar Composer en el proyecto (si no existe composer.json)
Si tu proyecto **no tiene** un archivo `composer.json` en la raíz, ejecuta:
```bash
composer init
```
- Presiona **Enter** varias veces para aceptar las opciones por defecto
- Cuando te pregunte si deseas definir dependencias, escribe `no` y presiona Enter
- Al final, confirma con `yes`

> **Nota:** Si ya existe `composer.json`, salta este paso.

### 3.3 Instalar DomPDF
En la misma terminal, ejecuta:
```bash
composer require dompdf/dompdf
```

Composer descargará automáticamente:
- DomPDF
- Todas sus dependencias (como `php-font-lib`, `php-svg-lib`, etc.)
- Creará/actualizará la carpeta `vendor/`

### 3.4 Verificar la instalación
Después de la instalación, en la raíz del proyecto debes ver:
- `vendor/` (carpeta nueva con las librerías)
- `composer.json` (actualizado con la dependencia de dompdf)
- `composer.lock` (archivo de bloqueo de versiones)

---

## Paso 4: Configurar el autoload de Composer en el proyecto

### 4.1 Modificar `public/index.php`
Abre el archivo `public/index.php` y **agrega al inicio**, justo después de `<?php`:

```php
<?php
require __DIR__ . '/../vendor/autoload.php';

// Autocargador simple tipo PSR-4 (el que ya existe...)
```

Esto carga automáticamente todas las librerías instaladas por Composer, incluyendo DomPDF.

---

## Paso 5: Usar DomPDF en el código (ejemplo básico)

### 5.1 Crear un archivo de exportación PDF
Puedes crear un archivo como `public/pdf-export.php` con este contenido:

```php
<?php
require __DIR__ . '/../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('isHtml5ParserEnabled', true);
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);

$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; }
        h1 { color: #333; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Reporte de Inventario</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Tipo</th>
            <th>Stock</th>
        </tr>
        <tr>
            <td>1</td>
            <td>Lámpara térmica</td>
            <td>Equipo</td>
            <td>1</td>
        </tr>
    </table>
</body>
</html>
';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('reporte_inventario.pdf', ['Attachment' => false]);
```

### 5.2 Acceder al PDF
Abre tu navegador y ve a:
```
http://localhost/proyecto_uptp_336-main/public/pdf-export.php
```

Debería mostrarte el PDF generado con DomPDF.

---

## Paso 6: Solución de problemas comunes

### Error: "composer no se reconoce como comando"
- **Solución:** Cierra VS Code completamente y vuelve a abrirlo. Composer se agregó al PATH pero VS Code necesita reiniciarse para leerlo.

### Error: "PHP no está en el PATH"
- **Solución:** Agrega `C:\xampp\php` al PATH de Windows:
  1. Busca "Variables de entorno" en el menú de inicio
  2. Click en "Variables de entorno..."
  3. En "Variables del sistema", busca `Path`
  4. Click en "Editar" y agrega: `C:\xampp\php`
  5. Reinicia VS Code

### Error: "require(): Failed opening required 'vendor/autoload.php'"
- **Solución:** Asegúrate de que:
  1. Ejecutaste `composer require dompdf/dompdf` en la raíz del proyecto
  2. La carpeta `vendor/` existe en la raíz
  3. La ruta en `require` apunta correctamente (si el archivo está en `public/`, usa `../vendor/autoload.php`)

### Error: "Class 'Dompdf\Dompdf' not found"
- **Solución:** Verifica que `vendor/autoload.php` esté incluido al inicio del archivo PHP donde usas DomPDF.

### Las fuentes no se ven bien en el PDF
- **Solución:** DomPDF usa fuentes por defecto. Si necesitas soporte UTF-8 completo (tildes, ñ), incluye en el HTML:
  ```html
  <meta charset="UTF-8">
  ```
  Y usa fuentes que soporten Unicode, o descarga la fuente DejaVu en el HTML con CSS.

---

## Resumen rápido de comandos

```bash
# Verificar Composer
composer --version

# Ir al proyecto
cd C:\xampp\htdocs\proyecto_uptp_336-main

# Inicializar Composer (solo si no existe composer.json)
composer init

# Instalar DomPDF
composer require dompdf/dompdf

# Actualizar dependencias (si agregas más librerías)
composer update
```

---

## Nota final
Si tu proyecto usa un sistema de rutas (como el `.htaccess` que redirige todo a `public/index.php`), considera integrar la generación de PDF dentro de un **Controller** existente, en lugar de crear archivos PHP sueltos en `public/`. Esto mantiene la arquitectura MVC limpia.
