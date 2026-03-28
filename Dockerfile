FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    wget \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    json \
    openssl

# Enable Apache modules
RUN a2enmod rewrite
RUN a2enmod headers

# Set working directory
WORKDIR /var/www/html

# Copy project files
COPY . .

# Set proper permissions
RUN chown -R www-data:www-data /var/www/html

# Create necessary directories
RUN mkdir -p /var/www/html/logs
RUN mkdir -p /var/www/html/uploads
RUN mkdir -p /var/www/html/backups
RUN chmod 755 /var/www/html/logs
RUN chmod 755 /var/www/html/uploads
RUN chmod 755 /var/www/html/backups

# Configure Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Set PHP configuration
RUN echo "upload_max_filesize = 10M" >> /usr/local/etc/php/conf.d/uploads.ini
RUN echo "post_max_size = 10M" >> /usr/local/etc/php/conf.d/uploads.ini
RUN echo "memory_limit = 256M" >> /usr/local/etc/php/conf.d/memory.ini

# Expose port 80
EXPOSE 80

# Start Apache
CMD ["apache2-foreground"]
