FROM php:8.2-apache

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y unzip && rm -rf /var/lib/apt/lists/*
RUN docker-php-ext-install pdo pdo_mysql

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Install PHP dependencies first (better layer caching)
COPY composer.json ./
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Copy the rest of the backend
COPY . .

EXPOSE 80
