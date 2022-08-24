#!/bin/bash
echo "Lauch survey app"
php artisan migrate --force
apache2-foreground
