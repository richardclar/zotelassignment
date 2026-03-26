FROM php:8.3-cli

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
    libzip-dev

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy entire application
COPY . .

# Create public directory if not exists
RUN mkdir -p /app/public

# Create .env manually
RUN echo "APP_NAME=Zotel" >> .env && \
    echo "APP_ENV=production" >> .env && \
    echo "APP_KEY=" >> .env && \
    echo "APP_DEBUG=false" >> .env && \
    echo "APP_URL=https://zotel-assignment.onrender.com" >> .env && \
    echo "LOG_CHANNEL=stack" >> .env && \
    echo "LOG_LEVEL=debug" >> .env && \
    echo "DB_CONNECTION=sqlite" >> .env && \
    echo "DB_DATABASE=/app/database/database.sqlite" >> .env

# Install dependencies
RUN composer install --no-dev --optimize-autoloader

# Generate application key
RUN php artisan key:generate --force

# Create SQLite database and run migrations
RUN mkdir -p /app/database && \
    touch /app/database/database.sqlite && \
    php artisan migrate --force && \
    php artisan db:seed --force

# Set permissions
RUN chmod -R 775 storage bootstrap/cache public

# Expose port
EXPOSE 10000

# Start server
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=10000"]
