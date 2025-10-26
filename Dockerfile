# --- Imagen base de PHP 8.3 ---
FROM php:8.3-fpm

# --- Instalar dependencias de sistema ---
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev libzip-dev zip git unzip libpq-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_pgsql zip

# --- Instalar Composer ---
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# --- Establecer directorio de trabajo ---
WORKDIR /var/www/html

# --- Copiar proyecto ---
COPY . .

# --- Instalar dependencias de Laravel ---
RUN composer install --no-interaction --optimize-autoloader --no-scripts

# --- Limpiar caché de Laravel (opcional) ---
RUN php artisan config:clear || true
RUN php artisan cache:clear || true

# --- Exponer puerto (Railway usa variable PORT) ---
EXPOSE 8000

# --- Comando de inicio: servidor PHP embebido apuntando a public/ ---
CMD php -S 0.0.0.0:${PORT:-8000} -t public
