# Usa una imagen base de PHP con Apache
FROM php:8.1-apache

# Instala las extensiones necesarias (pdo_mysql para MySQL)
RUN docker-php-ext-install pdo_mysql

# Copia el contenido del proyecto al contenedor
COPY . /var/www/html

# Establece permisos adecuados para la carpeta de subida
RUN chown -R www-data:www-data /var/www/html/uploads && chmod -R 775 /var/www/html/uploads

# Habilita el módulo de reescritura de Apache
RUN a2enmod rewrite

# Expone el puerto 80
EXPOSE 80
