#!/bin/bash

php artisan clear
php artisan view:clear
php artisan route:clear
php artisan config:clear
php artisan config:cache
php artisan cache:clear
php artisan optimize:clear
composer dump-autoload

