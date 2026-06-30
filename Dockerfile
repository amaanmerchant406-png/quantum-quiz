FROM php:8.2-fpm-alpine

# Install system dependencies and PHP extensions
RUN apk add --no-cache nginx wget supervisor bash \
    && docker-php-ext-install pdo pdo_mysql

# Install Composer
RUN curl -sS https://getcomposer.org | php -- --install-dir=/usr/local/bin --filename=composer

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Install project dependencies
RUN composer install --no-dev --optimize-autoloader

# Setup Nginx configuration
RUN mkdir -p /run/nginx
COPY ./nginx.conf /etc/nginx/http.d/default.conf

# Expose port and start script
EXPOSE 80
CMD nginx && php-fpm
