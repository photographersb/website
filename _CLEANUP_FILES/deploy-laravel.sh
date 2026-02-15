#!/bin/bash
set -e

APP_PATH="/home/photographersb/htdocs/photographersb.com/public"
DB_NAME="photographar_db"
DB_USER="photouser"
DB_PASS=$(openssl rand -base64 16)

echo "========================================="
echo "Laravel Deployment Script"
echo "========================================="

# Step 1: Reset MySQL root password
echo "Step 1: Setting up MySQL root access..."
systemctl stop mysql
sleep 2

# Start MySQL with skip-grant-tables
mysqld_safe --skip-grant-tables &
sleep 3

# Reset root password
mysql -u root -e "FLUSH PRIVILEGES;"
mysql -u root -e "ALTER USER 'root'@'localhost' IDENTIFIED BY 'RootPass2026';"
mysql -u root -e "FLUSH PRIVILEGES;"

# Kill the skip-grant-tables instance
killall mysqld_safe 2>/dev/null || true
sleep 2

# Restart normally
systemctl start mysql
sleep 3

echo "✓ MySQL root password reset"

# Step 2: Create database and user
echo "Step 2: Creating database and user..."
mysql -u root -p'RootPass2026' -e "CREATE DATABASE IF NOT EXISTS $DB_NAME;"
mysql -u root -p'RootPass2026' -e "CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';"
mysql -u root -p'RootPass2026' -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';"
mysql -u root -p'RootPass2026' -e "FLUSH PRIVILEGES;"

echo "✓ Database and user created"
echo "  Database: $DB_NAME"
echo "  User: $DB_USER" 
echo "  Password: $DB_PASS"

# Step 3: Update .env with database credentials
echo "Step 3: Updating .env..."
cd $APP_PATH

# Update .env with database credentials
sed -i "s/DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=$DB_USER/" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASS/" .env
sed -i "s/APP_URL=.*/APP_URL=https:\/\/photographersb.com/" .env

echo "✓ .env updated with database credentials"

# Step 4: Run migrations
echo "Step 4: Running Laravel migrations..."
sudo -u photographersb php artisan migrate --force 2>&1 | tail -20

echo "✓ Migrations completed"

# Step 5: Set final permissions
echo "Step 5: Setting final permissions..."
chmod -R 755 $APP_PATH
chmod -R 775 storage bootstrap/cache
chown -R photographersb:photographersb $APP_PATH

echo "✓ Permissions set"

# Step 6: Test site
echo ""
echo "========================================="
echo "Deployment Complete!"
echo "========================================="
echo "Site URL: https://photographersb.com"
echo "Database Name: $DB_NAME"
echo "Database User: $DB_USER"
echo ""
echo "Testing site accessibility..."
curl -I https://photographersb.com 2>&1 | head -5 || echo "SSL/DNS may not be configured yet"

echo ""
echo "All done!"
