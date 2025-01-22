# Usa una imagen base de PHP con Apache
FROM php:8.1-apache

# Instala extensiones necesarias y dependencias del sistema
RUN apt-get update && apt-get install -y \
    git unzip zip libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-install pdo_mysql gd \
    && docker-php-ext-configure gd --with-freetype --with-jpeg

# Instala Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configura el directorio de trabajo
WORKDIR /var/www/html

# Crea un archivo composer.json vacío
RUN echo '{}' > composer.json

# Instala firebase/php-jwt
RUN composer require firebase/php-jwt

# Copia los archivos del proyecto al contenedor
COPY . .

# Ajusta permisos para uploads
RUN chown -R www-data:www-data /var/www/html/uploads && chmod -R 775 /var/www/html/uploads

# Habilita el módulo rewrite de Apache
RUN a2enmod rewrite

# Expone el puerto 80
EXPOSE 80
