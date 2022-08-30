#!/bin/bash
echo "Lauch survey app"
sleep 5
php artisan migrate --force
apache2-foreground
