# Step 1: Base PHP Image for Laravel
FROM php:8.2-fpm as php-builder

WORKDIR /var/www/html

# Install system dependencies and PHP extensions
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

# ✅ Ensure Laravel is installed before running artisan commands
RUN composer install --no-dev --optimize-autoloader

# ✅ Fix permissions for Laravel storage
RUN chmod -R 777 storage bootstrap/cache

# ✅ Run artisan commands inside the PHP container
RUN php artisan config:clear && php artisan cache:clear
RUN php artisan route:clear && php artisan view:clear
RUN php artisan ziggy:generate

# Step 2: Node.js for Vite Build
FROM node:20 as node-builder

WORKDIR /app

# Copy only package.json and package-lock.json first (for caching)
COPY package.json package-lock.json ./

# Install Node.js dependencies
RUN npm install --legacy-peer-deps

# Copy full project
COPY . .

# ✅ Ensure Laravel is ready before building Vite assets
RUN php artisan config:clear
RUN php artisan ziggy:generate

# ✅ Now build frontend assets
RUN npm run build

# Step 3: Final PHP Image
FROM php:8.2-fpm as final

WORKDIR /var/www/html

# Copy Laravel project from previous PHP stage
COPY --from=php-builder /var/www/html /var/www/html

# Copy frontend assets from Node.js stage
COPY --from=node-builder /app/public/build /var/www/html/public/build

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose the correct port
EXPOSE 10000

# Start Laravel application
CMD php artisan serve --host 0.0.0.0 --port=$PORT
