#!/bin/bash

# Photographar SB - VPS Deployment Setup
# Ubuntu 24.04 | MySQL 8.0 | Nginx | PHP 8.2

set -e

echo "🚀 Starting Photographar SB deployment setup..."
echo "=================================================="

# Update system
echo "📦 Updating system packages..."
apt update && apt upgrade -y

# Install PHP 8.2 and extensions
echo "📦 Installing PHP 8.2..."
apt install -y php8.2-cli php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-gd php8.2-curl php8.2-xml php8.2-zip php8.2-bcmath php8.2-intl php8.2-imagick

# Install MySQL 8.0
echo "📦 Installing MySQL 8.0..."
apt install -y mysql-server mysql-client
mysql_secure_installation

# Install Nginx
echo "📦 Installing Nginx..."
apt install -y nginx

# Install Composer
echo "📦 Installing Composer..."
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install Node.js & npm
echo "📦 Installing Node.js & npm..."
curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
apt install -y nodejs

# Install Git
echo "📦 Installing Git..."
apt install -y git

# Create application directory
echo "📂 Creating app directory..."
mkdir -p /var/www/photographersb
cd /var/www/photographersb

# Clone repository
echo "📥 Cloning repository..."
git clone https://github.com/photographersb/website.git .

# Install PHP dependencies
echo "📦 Installing PHP dependencies..."
composer install --no-dev --optimize-autoloader

# Install frontend dependencies
echo "📦 Installing frontend dependencies..."
npm install
npm run build

# Set permissions
echo "🔒 Setting permissions..."
chown -R www-data:www-data /var/www/photographersb
chmod -R 755 /var/www/photographersb
chmod -R 775 /var/www/photographersb/storage
chmod -R 775 /var/www/photographersb/bootstrap/cache

# Create .env file
echo "⚙️ Creating .env file..."
cp .env.production .env

# Generate APP_KEY
echo "🔑 Generating APP_KEY..."
php artisan key:generate

# Create MySQL database
echo "📊 Creating database..."
read -p "Enter database name (default: photographersb): " DB_NAME
DB_NAME=${DB_NAME:-photographersb}
read -p "Enter database user (default: phsb_user): " DB_USER
DB_USER=${DB_USER:-phsb_user}
read -sp "Enter database password: " DB_PASS
echo

mysql -e "CREATE DATABASE IF NOT EXISTS $DB_NAME;"
mysql -e "CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';"
mysql -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

# Update .env with database credentials
sed -i "s/DB_DATABASE=.*/DB_DATABASE=$DB_NAME/" .env
sed -i "s/DB_USERNAME=.*/DB_USERNAME=$DB_USER/" .env
sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$DB_PASS/" .env

# Run migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force

# Setup Nginx
echo "🌐 Configuring Nginx..."
cat > /etc/nginx/sites-available/photographersb << 'EOF'
server {
    listen 80;
    server_name photographersb.com www.photographersb.com;
    
    root /var/www/photographersb/public;
    index index.php index.html;

    client_max_body_size 100M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.ht {
        deny all;
    }

    gzip on;
    gzip_types text/plain text/css text/javascript application/json;
}
EOF

ln -sf /etc/nginx/sites-available/photographersb /etc/nginx/sites-enabled/

# Enable PHP-FPM and Nginx
echo "🔧 Enabling services..."
systemctl enable php8.2-fpm nginx mysql
systemctl restart php8.2-fpm nginx

# Install SSL (Let's Encrypt)
echo "🔐 Installing Certbot for SSL..."
apt install -y certbot python3-certbot-nginx
certbot --nginx -d photographersb.com -d www.photographersb.com --agree-tos --no-eff-email -m admin@photographersb.com

# Setup cron job for Laravel schedule
echo "⏰ Setting up Laravel scheduler..."
(crontab -l 2>/dev/null; echo "* * * * * cd /var/www/photographersb && php artisan schedule:run >> /dev/null 2>&1") | crontab -

echo ""
echo "✅ Deployment Complete!"
echo "=================================================="
echo "🌐 Visit: https://photographersb.com"
echo "📊 Database: $DB_NAME"
echo "👤 Database User: $DB_USER"
echo "=================================================="
