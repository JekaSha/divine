# Use the official PHP 8.3 FPM image as the base
FROM php:8.2-fpm

#RUN apt clean && apt update --allow-unauthenticated

#RUN apt-get update && apt-get install -y debian-archive-keyring

#COPY sources.list /etc/apt/sources.list

# Update package lists and install cron and other dependencies
RUN apt-get update && apt-get install -y \
    cron \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Composer from the latest version
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set the working directory to /var/www/html
WORKDIR /var/www/html

# Copy the application code into the container
COPY . /var/www/html

# Install PHP dependencies using Composer
RUN composer install

# Copy custom php.ini configuration file
COPY ./php/php.ini /usr/local/etc/php/

# Create a directory for logs and a log file for cron
RUN mkdir /var/log/cron && \
    touch /var/log/cron.log && \
    chmod 666 /var/log/cron.log
RUN apt-get update && apt-get install -y default-mysql-client

# Set permissions for the application directory and its subdirectories
RUN chmod -R 775 /var/www/html/storage && \
    chmod -R 775 /var/www/html/bootstrap/cache

# Copy the cron job definition file to the cron.d directory
COPY cronfile /etc/cron.d/laravel-scheduler

# Set the appropriate permissions for the cron job file
RUN chmod 0644 /etc/cron.d/laravel-scheduler

# Generate the application key for Laravel
RUN php artisan key:generate

# Expose port 9000 for PHP-FPM
EXPOSE 9000

# Copy the start script to the container
COPY start.sh /start.sh

# Make the start script executable
RUN chmod +x /start.sh

# Set the command to run the start script
CMD ["/start.sh"]
