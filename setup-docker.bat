@echo off
echo Configurando proyecto Parking Management System con Docker...

REM Crear archivo .env si no existe
if not exist .env (
    echo Creando archivo .env...
    (
        echo APP_NAME="Parking Management System"
        echo APP_ENV=local
        echo APP_KEY=
        echo APP_DEBUG=true
        echo APP_URL=http://localhost:8080
        echo APP_TIMEZONE=America/Bogota
        echo.
        echo LOG_CHANNEL=stack
        echo LOG_LEVEL=debug
        echo.
        echo DB_CONNECTION=mysql
        echo DB_HOST=db
        echo DB_PORT=3306
        echo DB_DATABASE=parking_management
        echo DB_USERNAME=parking_user
        echo DB_PASSWORD=password
        echo.
        echo BROADCAST_DRIVER=log
        echo CACHE_DRIVER=redis
        echo FILESYSTEM_DISK=local
        echo QUEUE_CONNECTION=redis
        echo SESSION_DRIVER=redis
        echo SESSION_LIFETIME=120
        echo.
        echo REDIS_HOST=redis
        echo REDIS_PASSWORD=null
        echo REDIS_PORT=6379
        echo.
        echo SANCTUM_STATEFUL_DOMAINS=localhost:8080,127.0.0.1:8080
    ) > .env
)

echo Construyendo y levantando contenedores Docker...
docker-compose up -d --build

echo Esperando a que MySQL este listo...
timeout /t 20 /nobreak >nul

echo Ejecutando migraciones y seeders...
docker-compose exec -T app php artisan key:generate
docker-compose exec -T app php artisan migrate --force
docker-compose exec -T app php artisan db:seed --force

echo.
echo ========================================
echo ¡Proyecto listo!
echo ========================================
echo API: http://localhost:8080/api
echo phpMyAdmin: http://localhost:8081
echo ========================================

pause





