# --- Imagen base de PHP ---
FROM php:8.3-fpm

# --- Instalar dependencias del sistema ---
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev libzip-dev zip git unzip libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_pgsql zip

# --- Instalar Composer ---
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# --- Establecer el directorio de trabajo ---
WORKDIR /var/www/html

# --- Copiar archivos del proyecto ---
COPY . .

# --- Instalar dependencias de Laravel ---
RUN composer install --no-interaction --optimize-autoloader --no-scripts

# --- Generar caché de configuración y optimizar ---
RUN php artisan config:clear || true
RUN php artisan cache:clear || true

# --- Exponer el puerto de Laravel ---
EXPOSE 8000

# --- Comando de inicio ---
CMD php artisan serve --host=0.0.0.0 --port=8000
