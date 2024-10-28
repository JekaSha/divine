#!/bin/sh

echo "Starting transactions check at $(date)" >> /var/log/cron.log

/usr/local/bin/php /var/www/html/artisan app:transactions-check >> /var/log/cron.log 2>&1

echo "Completed transactions check at $(date)" >> /var/log/cron.log

