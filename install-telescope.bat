@echo off
REM Script para instalar Telescope cuando tengas PHP 8.2+

echo Instalando Laravel Telescope...
composer require laravel/telescope --dev

echo Publicando configuracion de Telescope...
php artisan vendor:publish --tag=telescope-config

echo Ejecutando migraciones...
php artisan migrate

echo ¡Telescope instalado correctamente!
echo Accede a Telescope en: http://localhost:8080/telescope

pause




