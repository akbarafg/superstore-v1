# Use a multi-stage build to install Node.js dependencies
FROM node:18 as node-builder

WORKDIR /app

# Copy only package.json and package-lock.json for caching
COPY package.json package-lock.json ./

# Install Node.js dependencies
RUN npm install

# Copy the rest of the project files and build Vite assets
COPY . .
RUN npm run build

# Now use a PHP image for Laravel
FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    git \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libzip-dev \
    zip \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql gd mbstring zip exif pcntl bcmath

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy Laravel project files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy built assets from Node.js stage
COPY --from=node-builder /app/public/build /var/www/html/public/build

# Set permissions for Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose the correct port (Render assigns $PORT dynamically)
EXPOSE 10000

# Start Laravel application using PHP's built-in server
CMD php artisan serve --host 0.0.0.0 --port=$PORT
