FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www/greenalert

# Install system dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    unzip \
    git \
    curl \
    libmysqlclient-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install exif

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy application files
COPY . /var/www/greenalert

# Set permissions
RUN chown -R www-data:www-data /var/www/greenalert \
    && chmod -R 755 /var/www/greenalert/storage \
    && chmod -R 755 /var/www/greenalert/bootstrap/cache

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Expose port
EXPOSE 9000

# Start PHP-FPM
CMD ["php-fpm"]
