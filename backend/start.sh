#!/bin/sh


# Navigate to the application directory
cd /var/www/html

# Clear Laravel cache 
/usr/local/bin/php /var/www/html/artisan cache:clear
/usr/local/bin/php /var/www/html/artisan config:clear
/usr/local/bin/php /var/www/html/artisan route:clear
/usr/local/bin/php /var/www/html/artisan view:clear

# Rebuild the configuration cache
/usr/local/bin/php /var/www/html/artisan config:cache

/usr/local/bin/php /var/www/html/artisan queue:work &

# Start cron in the background
cron -f &

# Start php-fpm in the foreground
php-fpm
