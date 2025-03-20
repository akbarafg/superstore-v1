# Step 1: Use PHP to install Laravel dependencies first
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

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Step 2: Use Node.js to build frontend assets
FROM node:20 as node-builder

WORKDIR /app

# Copy only package.json and package-lock.json first (for better caching)
COPY package.json package-lock.json ./

# Install Node.js dependencies
RUN npm install --legacy-peer-deps

# Copy the rest of the project files (now including Laravel with Ziggy)
COPY . .

# Ensure Laravel is ready before building Vite assets
RUN php artisan config:clear
RUN php artisan route:clear
RUN php artisan ziggy:generate

# Now build frontend assets
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
