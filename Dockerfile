# Dockerfile para Symfony + PHP + Composer
FROM php:8.2-fpm

# Instala dependencias del sistema y Nginx
RUN apt-get update \
    && apt-get install -y nginx supervisor git unzip libicu-dev libpq-dev libzip-dev libxml2-dev libonig-dev libssl-dev pkg-config zlib1g-dev libcurl4-openssl-dev libpng-dev build-essential zip \
    && docker-php-ext-install intl pdo pdo_pgsql zip mbstring xml

# Verifica que la extensión pdo_pgsql está habilitada
RUN php -m | grep -i pdo_pgsql || (echo 'ERROR: pdo_pgsql no está habilitado' && exit 1)

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Limpia TODAS las configuraciones por defecto de Nginx
RUN rm -rf /etc/nginx/sites-enabled/* /etc/nginx/conf.d/* && \
    echo "=== Limpieza de configuraciones por defecto completada ==="

# Permite a Composer ejecutarse como root
ENV COMPOSER_ALLOW_SUPERUSER=1

# Instala symfony CLI para los auto-scripts
RUN curl -sS https://get.symfony.com/cli/installer | bash && \
    mv /root/.symfony*/bin/symfony /usr/local/bin/symfony

# Variable dummy para evitar error en cache:clear durante el buildd
ENV DATABASE_URL=sqlite:///%kernel.project_dir%/var/data.db

# Especifica la versión de plataforma PHP para Symfony/Composer
ENV SYMFONY_PHP_VERSION=8.2.0

WORKDIR /var/www/html

# 1. Copia composer.json y composer.lock primero (mejor caché)
COPY backend/composer.json backend/composer.lock ./

# 2. Copia el resto del backend (incluyendo bin/console, src, etc)
COPY backend/ .

# 3. Instala dependencias (esto genera vendor/autoload_runtime.php y ejecuta los auto-scripts)
RUN composer install --no-dev --optimize-autoloader --no-interaction && \
    echo "=== Dependencias de Composer instaladas correctamente ===" && \
    ls -la vendor/

# Verifica que el archivo autoload_runtime.php existe
RUN test -f vendor/autoload_runtime.php || (echo "ERROR: autoload_runtime.php no encontrado" && exit 1)

# Copia y verifica el frontend
COPY frontend/ /var/www/frontend/
RUN echo "=== Verificando contenido del frontend ===" && \
    ls -la /var/www/frontend && \
    test -f /var/www/frontend/index.html || (echo "ERROR: index.html no encontrado" && exit 1)

# Copia y verifica la configuración de Nginx
COPY nginx/default.conf /etc/nginx/conf.d/default.conf
RUN echo "=== Verificando configuración de Nginx ===" && \
    nginx -t && \
    echo "=== Contenido de la configuración de Nginx ===" && \
    cat /etc/nginx/conf.d/default.conf

# Configura supervisord
COPY .docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
RUN echo "=== Verificando configuración de Supervisord ===" && \
    cat /etc/supervisor/conf.d/supervisord.conf

# Permisos
RUN chown -R www-data:www-data /var/www/html /var/www/frontend && \
    chmod -R 755 /var/www/frontend && \
    echo "=== Permisos establecidos correctamente ==="

# Verifica la configuración final
RUN echo "=== Verificación final de archivos y configuraciones ===" && \
    ls -la /var/www/frontend && \
    ls -la /etc/nginx/conf.d/ && \
    nginx -t

EXPOSE 80

# Asegura que los logs van a stdout/stderr
RUN ln -sf /dev/stdout /var/log/nginx/access.log && \
    ln -sf /dev/stderr /var/log/nginx/error.log

# Asegurar que la carpeta de logs existe y tiene permisos correctos
RUN mkdir -p /var/www/html/var/log && chown -R www-data:www-data /var/www/html/var

# Comando de inicio con verificaciones
CMD echo "=== Iniciando contenedor $(date) ===" && \
    echo "=== Verificando archivos antes de iniciar ===" && \
    ls -la /var/www/frontend && \
    ls -la /var/www/html/vendor/ && \
    nginx -t && \
    echo "=== Iniciando servicios ===" && \
    /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf 

# Asegura que PHP-FPM exponga las variables de entorno (fix para Render)
RUN sed -i 's/;clear_env = no/clear_env = no/' /usr/local/etc/php-fpm.d/www.conf || true
RUN sed -i 's/clear_env = yes/clear_env = no/' /usr/local/etc/php-fpm.d/www.conf || true 