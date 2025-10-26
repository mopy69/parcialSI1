# --- Imagen base ---
FROM php:8.3-fpm

# --- Instalar dependencias del sistema ---
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev libpq-dev zip git unzip \
    && docker-php-ext-install pdo pdo_pgsql gd

# --- Instalar Composer ---
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# --- Establecer directorio de trabajo ---
WORKDIR /var/www/html

# --- Copiar archivos del proyecto ---
COPY . .

# --- Instalar dependencias PHP ---
RUN composer install --no-interaction --optimize-autoloader --no-scripts

RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev libpq-dev zip git unzip libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql gd zip

# --- Exponer puerto para Laravel ---
EXPOSE 8000

# --- Comando por defecto ---
CMD php artisan serve --host=0.0.0.0 --port=8000
