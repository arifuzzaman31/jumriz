FROM php:8.3-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    curl \
    git \
    zip \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    gnupg \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install -g npm@latest \
    && rm -rf /var/lib/apt/lists/*

# Set PHP runtime limits for file uploads
RUN { \
        echo "upload_max_filesize = 20M"; \
        echo "post_max_size = 20M"; \
        echo "memory_limit = 256M"; \
        echo "max_execution_time = 120"; \
    } > /usr/local/etc/php/conf.d/zz-custom-limits.ini

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache modules
RUN a2enmod rewrite

# Set the working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Update Apache configuration to allow .htaccess overrides
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Configure Apache to serve from /var/www/html/public
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Create and configure entrypoint script
RUN printf '#!/bin/sh\n\
if [ -f "package.json" ]; then\n\
    npm install\n\
    echo "Starting Vite development server..."\n\
    npm run dev -- --host 0.0.0.0 &\n\
fi\n\
exec apache2-foreground\n' > /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80 5173

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]