#!/bin/bash
# Laravel Deployment - Final Migration Step
# Run this on the server: bash /tmp/deploy_migrations.sh

echo "=== Laravel Migration Deployment ===" 
cd /home/photographersb/htdocs/photographersb.com/public || exit 1

echo "1. Updating .env with correct database password..."
sed -i 's/^DB_PASSWORD=.*/DB_PASSWORD=Bangladesh#2026/' .env

echo "2. Verifying database configuration..."
grep "^DB_" .env

echo ""
echo "3. Clearing Laravel configuration cache..."
php artisan config:clear

echo ""
echo "4. Running database migrations..."
php artisan migrate --force

echo ""
echo "5. Checking migration status..."
php artisan migrate:status

echo ""
echo "=== Deployment Complete ==="
