#!/bin/bash

# Crear archivo .env si no existe
if [ ! -f .env ]; then
    cat > .env << EOF
APP_NAME="Parking Management System"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8080
APP_TIMEZONE=America/Bogota

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=parking_management
DB_USERNAME=parking_user
DB_PASSWORD=password

BROADCAST_DRIVER=log
CACHE_DRIVER=redis
FILESYSTEM_DISK=local
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379

SANCTUM_STATEFUL_DOMAINS=localhost:8080,127.0.0.1:8080
EOF
fi

# Construir y levantar contenedores
docker-compose up -d --build

# Esperar a que MySQL esté listo
echo "Esperando a que MySQL esté listo..."
sleep 15

# Ejecutar migraciones y seeders dentro del contenedor
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate --force
docker-compose exec app php artisan db:seed --force

echo "¡Proyecto listo! Accede a http://localhost:8080"





