FROM php:8.3-apache

# 1. Instalar dependencias del sistema requeridas para librerías comunes y SQLite
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libsqlite3-dev \
    zip \
    unzip \
    git \
    && rm -rf /var/lib/apt/lists/*

# 2. Instalar extensiones de PHP fundamentales (PDO, SQLite, MySQL, GD para imágenes)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite gd bcmath

# 3. Activar el módulo rewrite de Apache (Crucial para rutas de Laravel o APIs)
RUN a2enmod rewrite

# 4. Cambiar el DocumentRoot de Apache a /var/www/html/public
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# 5. Copiar Composer (El gestor de paquetes de PHP) directamente desde su imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 6. Establecer directorio de trabajo y copiar el código de "Proyecto Modificado"
WORKDIR /var/www/html
COPY ["Proyecto Modificado/Proyecto Modificado/proyecto_uptp_336-main", "."]

# 7. Instalar dependencias de Composer para asegurar Dompdf
RUN composer install --no-interaction --no-dev --optimize-autoloader

# 8. Dar permisos correctos a las carpetas para que Apache pueda escribir (especialmente para microbiologia.db)
RUN chown -R www-data:www-data /var/www/html

EXPOSE 80



# COMANDO PARA EL CONTAINER:
# # 1. Borrar contenedor viejo
# docker rm -f uptp336_container

# # 2. Reconstruir la imagen con el nuevo código (Paso clave)
# docker build -t uptp336_image .

# # 3. Crear y correr el nuevo contenedor
# docker run -d -p 8080:80 --name uptp336_container uptp336_image



# Opcion 2:
# # 1. Borrar contenedor viejo
# docker rm -f uptp336_container

# # 2. Correr vinculando tu carpeta local de desarrollo directamente al contenedor
# docker run -d -p 8080:80 --name uptp336_container -v "${PWD}/Proyecto Modificado/Proyecto Modificado/proyecto_uptp_336-main:/var/www/html" uptp336_image

