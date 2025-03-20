# Step 1: Use Node.js to install dependencies and build frontend
FROM node:20 as node-builder

WORKDIR /app

# Copy only package.json and package-lock.json first (for better caching)
COPY package.json package-lock.json ./

# Install Node.js dependencies
RUN npm install --legacy-peer-deps

# Copy the full project (now including Ziggy & other Laravel files)
COPY . .

# Build Vite frontend assets (ensure Ziggy routes are available)
RUN npm run build

# Step 2: Use PHP for Laravel Backend
FROM php:8.2-fpm as php-builder

WORKDIR /var/www/html

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

# Copy Laravel project files
COPY . .

# Install Laravel backend dependencies
RUN composer install --no-dev --optimize-autoloader

# Copy the built frontend assets from Node.js stage
COPY --from=node-builder /app/public/build /var/www/html/public/build

# Set permissions for Laravel storage and cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose correct port (Render assigns $PORT dynamically)
EXPOSE 10000

# Start Laravel application
CMD php artisan serve --host 0.0.0.0 --port=$PORT
