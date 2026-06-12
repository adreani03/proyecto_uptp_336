# Guía de Clean Architecture

¡Hola! Si estás leyendo esto, probablemente estés dando tus primeros pasos en programación o estés intentando entender cómo está organizado este proyecto. ¡No te preocupes! Todo tiene una razón de ser, y esta guía está diseñada para explicártelo de forma sencilla y paso a paso.

---

## 1. ¿Por qué usamos tantas carpetas? (Arquitectura Limpia)

Cuando hacemos aplicaciones pequeñas, solemos meter todo en un solo archivo (la base de datos, el diseño visual y la lógica del negocio). Pero cuando el proyecto crece (como este Sistema de Inventario), ese archivo se vuelve inmanejable. 

Para resolver esto, usamos la **Arquitectura Limpia (Clean Architecture)**. Esta arquitectura divide nuestro código como si fuera una cebolla, en diferentes **capas**. La regla principal es que las capas internas (el núcleo del negocio) no deben depender ni saber de las capas externas (base de datos o interfaces visuales).

---

## 2. Explicación de cada Carpeta

A continuación te detallo qué hace cada una de las carpetas que verás en este proyecto:

```text
/proyecto_uptp_336
│
├── /public                 <-- 🌐 Tu "Puerta Principal" (Punto de entrada de todo)
│   ├── index.php           <-- Front Controller. Todas las peticiones web pasan por aquí primero.
│   └── /assets             <-- Archivos públicos: CSS (estilos), JS y fotos.
│
├── /src                    <-- ⚙️ Tu Código Fuente Principal (El corazón del proyecto)
│   │
│   ├── /Domain             <-- 🧠 Capa 1 (El Núcleo): Reglas del negocio Puras
│   │   ├── /Entities       <-- Tus Objetos (Ej. Usuario.php, Inventario.php). Solo datos, nada de BD.
│   │   └── /Repositories   <-- Interfaces (Contratos). Solo dicen "qué se debe hacer" pero no "cómo".
│   │
│   ├── /Application        <-- 🔧 Capa 2 (Casos de Uso): Las "Acciones" que hace tu sistema
│   │   └── /UseCases       <-- Ej. LoginUseCase.php (Verificar credenciales y devolver si todo está ok).
│   │
│   ├── /Infrastructure     <-- 🛠️ Capa 3 (Herramientas): Cómo nos conectamos al mundo exterior
│   │   ├── /Database       <-- Todo lo que tiene que ver con conexiones (DatabaseConnection.php)
│   │   └── /Persistence    <-- El "Cómo" real. Aquí van las consultas SQL (`SELECT * FROM...`) a SQLite.
│   │
│   └── /Presentation       <-- 🖼️ Capa 4 (La Cara del Sistema): Lo que ve e interactúa el usuario
│       ├── /Controllers    <-- Reciben los "clics" del usuario, llaman a los Casos de Uso y muestran Vistas.
│       └── /Views          <-- Plantillas visuales (.php) con HTML donde se dibuja el sistema para el usuario.
│
├── /doc                    <-- 📖 Documentación (Estás leyendo esto ahora).
│
├── init_db.php             <-- Script para crear la base de datos limpia de producción.
├── init_test_db.php        <-- Script para crear una base de datos con datos de prueba cargados.
└── microbiologia.db        <-- (Se creará) El archivo de base de datos SQLite donde se guardará todo.
```

---

## 3. El Flujo de Trabajo (El Viaje de un Dato)

Imagina que un usuario quiere iniciar sesión. Este es el viaje de los datos a través de las carpetas:

1. **Presentation (Vista)**: El usuario llena el formulario en `src/Presentation/Views/auth/login.php` y da click en "Ingresar".
2. **Public (Front Controller)**: El formulario envía los datos a `public/index.php`. Este archivo enruta la petición al Controlador.
3. **Presentation (Controller)**: `AuthController.php` recibe el usuario y contraseña. No sabe cómo chequear la base de datos, así que se los pasa a la capa Application.
4. **Application (Use Case)**: `LoginUseCase.php` dice "Ok, tengo esta cédula y contraseña, necesito buscar si existen". Para hacerlo, le pide a Infrastructure que busque la cédula.
5. **Infrastructure (Persistence)**: `UsuarioRepositorySQLite.php` ejecuta la consulta `SELECT * FROM usuarios` en la base de datos y le devuelve una Entidad Usuario (de Domain) al Caso de Uso.
6. **Application (Use Case)**: El Caso de Uso verifica si la contraseña es correcta usando los métodos del Domain (`Usuario->verifyPassword()`). Responde al Controlador si fue exitoso o no.
7. **Presentation (Controller)**: Si fue exitoso, redirige al usuario a su dashboard. Si falló, le muestra el mensaje de error en la vista.

---

## 4. ¿Cómo Iniciar y Correr el Proyecto?

### Requisitos
Necesitas tener instalado **PHP** en tu computadora. Puedes usar XAMPP, Laragon, o instalar PHP independiente.

### Pasos
1. **Crear la Base de Datos**:
   Abre una terminal en la carpeta raíz del proyecto (`proyecto_uptp_336`) y ejecuta:
   ```bash
   php init_test_db.php
   ```
   *Esto creará el archivo `microbiologia.db` con usuarios de prueba (password por defecto: `password123`).*

2. **Iniciar el Servidor Web**:
   Debemos decirle a PHP que sirva la carpeta `public` como raíz de nuestra web:
   ```bash
   php -S localhost:8000 -t public
   ```

3. **Verlo en el Navegador**:
   Abre tu navegador (Chrome, Firefox, etc.) y visita:
   `http://localhost:8000`

---

## 5. Mini-Tutorial: ¿Cómo añadir algo nuevo? (Ej. "Categorías")

Si tu jefe te dice: *"Necesito poder listar, crear y borrar categorías del inventario"*, debes seguir este orden:

1. **Domain**: 
   - Crea `src/Domain/Entities/Categoria.php` (con id y nombre).
   - Crea `src/Domain/Repositories/CategoriaRepository.php` (interfaz con métodos como `getAll()`, `save()`, `delete()`).
2. **Infrastructure**:
   - Crea `src/Infrastructure/Persistence/CategoriaRepositorySQLite.php` e implementa la interfaz (escribe los `SELECT`, `INSERT` y `DELETE` ahí adentro).
3. **Application**:
   - Crea Casos de Uso para cada acción: `ObtenerCategoriasUseCase.php`, `CrearCategoriaUseCase.php`, etc. (Estos llaman al Repositorio).
4. **Presentation**:
   - Crea `src/Presentation/Controllers/CategoriaController.php`. (Para manejar las llamadas web).
   - Crea tus vistas HTML en `src/Presentation/Views/categorias/listar.php`, `crear.php`, etc.
5. **Enrutador**:
   - Ve a `public/index.php` y añade las nuevas rutas (Ej. `if ($request_uri === '/categorias') { ... }`).

Y listo! Siguiendo ese orden, nunca te perderás y mantendrás todo el código ordenado.
