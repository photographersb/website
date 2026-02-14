#!/bin/bash
LOG="/tmp/migration_log.txt"
echo "=== Migration Script Started at $(date) ===" > $LOG

echo "Step 1: Setting root MySQL to use auth_socket plugin" | tee -a $LOG
mysql -u root <<EOF 2>&1 | tee -a $LOG
ALTER USER 'root'@'localhost' IDENTIFIED WITH auth_socket;
FLUSH PRIVILEGES;
EOF

echo "Step 2: Creating photouser with Bangladesh#2026 password" | tee -a $LOG
mysql -u root <<EOF 2>&1 | tee -a $LOG
DROP USER IF EXISTS 'photouser'@'localhost';
CREATE USER 'photouser'@'localhost' IDENTIFIED WITH mysql_native_password BY 'Bangladesh#2026';
GRANT ALL PRIVILEGES ON photodb.* TO 'photouser'@'localhost';
FLUSH PRIVILEGES;
SELECT user, host, plugin FROM mysql.user WHERE user='photouser';
EOF

echo "Step 3: Testing photouser connection" | tee -a $LOG
mysql -u photouser -p'Bangladesh#2026' photodb -e "SELECT 1 as test;" 2>&1 | tee -a $LOG

echo "Step 4: Updating .env file" | tee -a $LOG
cd /home/photographersb/htdocs/photographersb.com/public
sed -i 's/^DB_USERNAME=.*/DB_USERNAME=photouser/' .env
sed -i 's/^DB_PASSWORD=.*/DB_PASSWORD=Bangladesh#2026/' .env
grep "^DB_" .env | tee -a $LOG

echo "Step 5: Clearing Laravel config cache" | tee -a $LOG
php artisan config:clear 2>&1 | tee -a $LOG

echo "Step 6: Running migrations" | tee -a $LOG
php artisan migrate --force 2>&1 | tee -a $LOG

echo "Step 7: Checking migration status" | tee -a $LOG
php artisan migrate:status 2>&1 | head -30 | tee -a $LOG

echo "=== Migration Script Completed at $(date) ===" | tee -a $LOG
echo "Full log saved to $LOG"
