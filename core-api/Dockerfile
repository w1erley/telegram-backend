# Use Laravel Sail's base image for PHP 8.3
FROM sail-8.3/app

# Set working directory
WORKDIR /var/www/html

# Copy all project files
COPY . .

# Install dependencies
RUN composer install --optimize-autoloader --no-dev

# Clear and cache config
RUN php artisan config:clear
RUN php artisan config:cache

# Set permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose application port
EXPOSE ${LARAVEL_APP_PORT}

# Start the Laravel application
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]

