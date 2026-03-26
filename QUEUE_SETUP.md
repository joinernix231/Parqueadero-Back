# Queue setup — PDF receipts

The app uses Laravel **jobs**, **events**, and **listeners** to generate receipt PDFs asynchronously.

## Flow

1. **Events** — fired on entry/exit  
   - `VehicleEntryRegistered`  
   - `VehicleExitRegistered`  

2. **Listeners** — dispatch jobs  
   - `GenerateEntryReceiptListener`  
   - `GenerateExitReceiptListener`  

3. **Jobs** — build PDFs  
   - `GenerateEntryReceiptJob`  
   - `GenerateExitReceiptJob`  

## Configuration

### 1. Queue driver (`.env`)

```env
QUEUE_CONNECTION=database
# or
# QUEUE_CONNECTION=redis
```

### 2. Database driver tables

```bash
php artisan queue:table
php artisan migrate
```

For Redis, ensure the Redis service is running (e.g. Docker).

### 3. Run a worker

```bash
php artisan queue:work
```

In production, use a process manager (Supervisor, systemd, Kubernetes, etc.).

### 4. Docker example (`docker-compose`)

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

## Pipeline

1. User records entry/exit → `EntryVehicleUseCase` / `ExitVehicleUseCase`  
2. Ticket created/updated → domain event fired  
3. Listener dispatches `GenerateEntryReceiptJob` / `GenerateExitReceiptJob`  
4. Worker writes PDF under `storage/app/receipts/`  
5. HTTP controllers can also stream downloads directly when needed  

## PDF paths

- Entry: `storage/app/receipts/entry/entry-receipt-{id}.pdf`  
- Exit: `storage/app/receipts/exit/exit-receipt-{id}.pdf`  

## Logs

Check `storage/logs/laravel.log` for English messages such as:

- `Entry receipt generated successfully: ...`  
- `Exit receipt generated successfully: ...`  
- `Failed to generate entry/exit receipt for ticket ID: ...`  
- `... job failed permanently ...` (after retries)  

## Why queues

- Does not block the HTTP response  
- Automatic retries (e.g. 3 attempts)  
- Horizontally scalable workers  
- Clear separation from use cases  
