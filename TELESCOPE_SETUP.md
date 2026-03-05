# Configuración de Laravel Telescope

Telescope ha sido agregado al proyecto. Para completar la instalación, sigue estos pasos:

## Requisitos

- PHP 8.2 o superior (el proyecto requiere PHP 8.3 según composer.json)
- Composer instalado

## Pasos para activar Telescope

### Opción 1: Usando el script (Windows)
Ejecuta el archivo `install-telescope.bat` cuando tengas PHP 8.2+ disponible.

### Opción 2: Comandos manuales

1. **Actualizar composer.lock e instalar Telescope** (cuando tengas PHP 8.2+):
   ```bash
   composer require laravel/telescope --dev
   ```
   
   Si tienas problemas con la versión de PHP, puedes usar temporalmente:
   ```bash
   composer require laravel/telescope --dev --ignore-platform-reqs
   ```
   ⚠️ **Nota**: Solo usa `--ignore-platform-reqs` si estás seguro de que tu servidor web tiene PHP 8.2+

2. **Publicar la configuración de Telescope** (opcional, ya está creada manualmente):
   ```bash
   php artisan vendor:publish --tag=telescope-config
   ```

3. **Ejecutar las migraciones**:
   ```bash
   php artisan migrate
   ```
   Esto creará las tablas necesarias para Telescope (`telescope_entries`, `telescope_entries_tags`, `telescope_monitoring`).

4. **Configurar el acceso** (opcional):
   
   Edita `app/Providers/TelescopeServiceProvider.php` y agrega los emails de los usuarios que pueden acceder a Telescope en el método `gate()`:
   
   ```php
   protected function gate(): void
   {
       Gate::define('viewTelescope', function ($user = null) {
           return in_array($user->email ?? null, [
               'admin@example.com', // Agrega aquí los emails permitidos
           ]) || $this->app->environment('local');
   }
   ```

5. **Acceder a Telescope**:
   
   Una vez completados los pasos anteriores, puedes acceder a Telescope en:
   ```
   http://localhost:8080/telescope
   ```
   
   O la URL que corresponda según tu configuración.

## Configuración en .env

Puedes agregar estas variables opcionales a tu archivo `.env`:

```env
TELESCOPE_ENABLED=true
TELESCOPE_PATH=telescope
TELESCOPE_DOMAIN=null
```

## Nota sobre PHP

Si estás usando PHP 7.4 en la línea de comandos pero tu servidor web usa PHP 8.2+, puedes:

1. Usar la versión correcta de PHP en la línea de comandos:
   ```bash
   php8.2 artisan migrate
   # o
   php8.3 artisan migrate
   ```

2. O ejecutar los comandos directamente desde el servidor web si tienes acceso.

## Archivos creados

- `config/telescope.php` - Configuración de Telescope
- `app/Providers/TelescopeServiceProvider.php` - Service Provider de Telescope
- `database/migrations/2024_01_01_100000_create_telescope_entries_table.php` - Migración de las tablas

## Verificación

Una vez completada la instalación, puedes verificar que Telescope esté funcionando visitando:
- `http://localhost:8080/telescope` (o tu URL correspondiente)

Si ves la interfaz de Telescope, ¡está funcionando correctamente!

