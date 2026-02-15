#!/bin/bash
cd /home/photographersb/htdocs/photographersb.com/public
php artisan migrate --force 2>&1 | tee /tmp/migration_output.txt
echo "Exit code: $?" >> /tmp/migration_output.txt
