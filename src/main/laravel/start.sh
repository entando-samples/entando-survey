#! /usr/bin/env bash

docker-compose up -d

docker-compose exec app php artisan storage:link

docker-compose exec app php artisan migrate
