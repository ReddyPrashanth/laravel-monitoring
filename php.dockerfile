# Base image with PHP and Apache
FROM php:8-apache

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    # git \
    curl \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    supervisor \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd bcmath pdo pdo_mysql zip

# Install pcov for for laravel test coverage
RUN pecl install pcov && \
    docker-php-ext-enable pcov

# Enable Apache modules
RUN a2enmod rewrite

# Set working directory
WORKDIR /var/www/html

# Copy existing application directory contents
COPY ./src .

# Install Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Install application dependencies
# RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --optimize-autoloader --prefer-dist --no-scripts

# Set permissions for Laravel
# RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Copy custom Apache virtual host configuration
COPY conf/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Expose port 80
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
