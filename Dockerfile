# Using a PHP base image with Apache
FROM php:8.1-apache

# Installs necessary extensions and system dependencies
RUN apt-get update && apt-get install -y \
    git unzip zip libzip-dev libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-install pdo_mysql gd \
    && docker-php-ext-configure gd --with-freetype --with-jpeg

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# CSet the working directory
WORKDIR /var/www/html

# Create an empty composer.json file
RUN echo '{}' > composer.json

# Install firebase/php-jwt
RUN composer require firebase/php-jwt

# Copy the project files to the container
COPY . .

# Adjust permissions for uploads
RUN chown -R www-data:www-data /var/www/html/uploads && chmod -R 775 /var/www/html/uploads

# Enable Apache rewrite module
RUN a2enmod rewrite

# Expose port 80
EXPOSE 80
