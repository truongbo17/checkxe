docker-compose up -d --build
docker exec -it laravel-app-cms /bin/sh -c "php artisan migrate --seed"
docker exec -it laravel-app-cms /bin/sh -c "php artisan bo:cms:install"
