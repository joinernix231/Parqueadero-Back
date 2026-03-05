# Configuración de Colas para Generación de Recibos

Este proyecto utiliza Jobs, Events y Listeners de Laravel para generar los recibos PDF de forma asíncrona.

## Arquitectura

1. **Events**: Se disparan cuando se registra una entrada o salida de vehículo
   - `VehicleEntryRegistered`: Se dispara cuando se registra una entrada
   - `VehicleExitRegistered`: Se dispara cuando se registra una salida

2. **Listeners**: Escuchan los eventos y despachan los Jobs
   - `GenerateEntryReceiptListener`: Escucha `VehicleEntryRegistered`
   - `GenerateExitReceiptListener`: Escucha `VehicleExitRegistered`

3. **Jobs**: Procesan la generación de PDFs de forma asíncrona
   - `GenerateEntryReceiptJob`: Genera el PDF de entrada
   - `GenerateExitReceiptJob`: Genera el PDF de salida

## Configuración

### 1. Configurar el driver de cola en `.env`

```env
QUEUE_CONNECTION=database
# O usar Redis si prefieres:
# QUEUE_CONNECTION=redis
```

### 2. Crear las tablas de colas

Si usas `database` como driver:

```bash
php artisan queue:table
php artisan migrate
```

Si usas `redis`, asegúrate de que Redis esté corriendo (ya está configurado en Docker).

### 3. Ejecutar el worker de colas

En desarrollo (sincrónico para testing):
```bash
php artisan queue:work
```

En producción, ejecuta el worker como servicio o usando Supervisor.

### 4. Para Docker

Agrega un servicio worker en `docker-compose.yml`:

```yaml
queue-worker:
  build:
    context: .
    dockerfile: Dockerfile
  container_name: parking-management-queue-worker
  restart: unless-stopped
  working_dir: /var/www/html
  volumes:
    - ./:/var/www/html
  command: php artisan queue:work --sleep=3 --tries=3 --max-time=3600
  networks:
    - parking-network
  depends_on:
    - db
    - redis
```

## Flujo de Trabajo

1. Usuario registra entrada/salida → `EntryVehicleUseCase` / `ExitVehicleUseCase`
2. UseCase crea/actualiza el ticket → Dispara Event (`VehicleEntryRegistered` / `VehicleExitRegistered`)
3. Listener escucha el evento → Despacha Job (`GenerateEntryReceiptJob` / `GenerateExitReceiptJob`)
4. Worker procesa el Job → Genera PDF y lo guarda en `storage/app/receipts/`
5. PDF disponible para descarga desde el controlador

## Ubicación de los PDFs

Los PDFs se guardan en:
- Entrada: `storage/app/receipts/entry/recibo-entrada-{id}.pdf`
- Salida: `storage/app/receipts/exit/recibo-salida-{id}.pdf`

## Monitoreo

Los logs de los Jobs se registran en `storage/logs/laravel.log`:
- Éxito: "Recibo generado exitosamente"
- Error: "Error al generar recibo" (con detalles)
- Fallo definitivo: "Job falló definitivamente" (después de 3 intentos)

## Ventajas

- ✅ No bloquea la respuesta HTTP al usuario
- ✅ Reintentos automáticos en caso de fallo (3 intentos)
- ✅ Escalable: múltiples workers pueden procesar jobs
- ✅ Logs detallados para debugging
- ✅ Separación de responsabilidades (Clean Architecture)

