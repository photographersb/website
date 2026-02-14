<?php
echo "=== DB Migration Helper ===\n\n";

// Change directory
chdir('/home/photographersb/htdocs/photographersb.com/public');

// Step 1: Update MySQL password for photouser
echo "Step 1: Updating photouser password...\n";
$result = shell_exec('sudo mysql -e "ALTER USER photouser@localhost IDENTIFIED BY \'PhotoPass2026\'; FLUSH PRIVILEGES;" 2>&1');
echo $result . "\n";

// Step 2: Update .env file
echo "Step 2: Updating .env file...\n";
$envFile = '/home/photographersb/htdocs/photographersb.com/public/.env';
$envContent = file_get_contents($envFile);
$envContent = preg_replace('/^DB_PASSWORD=.*/m', 'DB_PASSWORD=PhotoPass2026', $envContent);
file_put_contents($envFile, $envContent);
echo "Updated DB_PASSWORD in .env\n\n";

// Step 3: Clear config
echo "Step 3: Clearing config cache...\n";
system('php artisan config:clear');
echo "\n";

// Step 4: Run migrations
echo "Step 4: Running migrations...\n";
system('php artisan migrate --force');

echo "\n=== Complete ===\n";
?>
