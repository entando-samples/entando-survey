#! /usr/bin/env bash

docker-compose up -d

docker-compose exec app composer install --ignore-platform-reqs

docker-compose exec app composer dump-autoload

docker-compose exec app php artisan key:generate

docker-compose exec app php artisan storage:link

docker-compose exec app php artisan migrate --seed

