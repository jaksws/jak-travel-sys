#!/bin/bash

# Script to clean Laravel cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan optimize:clear

# Additional cleanup for storage and logs
rm -rf storage/framework/cache/*
rm -rf storage/framework/sessions/*
rm -rf storage/framework/views/*
rm -rf storage/logs/*.log

echo "✅ All caches and temporary files have been cleared."