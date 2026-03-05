# Parking Management System - Backend

Sistema de gestión de estacionamientos construido con Laravel 11 y Clean Architecture.

## Arquitectura

El proyecto sigue Clean Architecture con separación clara de capas:

- **Domain/**: Entidades, Interfaces de Repositories, Domain Services, DTOs
- **Application/**: Use Cases y Actions
- **Infrastructure/**: Implementaciones de Repositories (Eloquent)
- **Http/**: Controllers, Requests, Resources

## Stack Tecnológico

- Laravel 11
- PHP 8.3
- MySQL 8.0
- Laravel Sanctum (Autenticación)
- Docker & Docker Compose

## Instalación

1. Clonar el repositorio
2. Copiar `.env.example` a `.env` y configurar variables
3. Ejecutar `docker-compose up -d`
4. Ejecutar migraciones: `php artisan migrate`
5. Ejecutar seeders: `php artisan db:seed`

## Estructura del Proyecto

```
app/
├── Domain/              # Capa de Dominio
│   ├── Entities/       # Entidades de negocio
│   ├── Repositories/   # Interfaces
│   ├── Services/       # Domain Services
│   └── DTOs/          # Data Transfer Objects
├── Application/        # Capa de Aplicación
│   ├── UseCases/      # Casos de uso
│   └── Actions/        # Orquestación
├── Infrastructure/     # Capa de Infraestructura
│   └── Repositories/   # Implementaciones Eloquent
└── Http/              # Capa de Presentación
    ├── Controllers/   # Controladores API
    ├── Requests/      # Validación
    └── Resources/     # Transformación de respuestas
```

## Endpoints API

### Autenticación
- `POST /api/login` - Iniciar sesión
- `POST /api/logout` - Cerrar sesión

### Vehículos
- `GET /api/vehicles` - Listar vehículos
- `POST /api/vehicles` - Registrar vehículo
- `GET /api/vehicles/{id}` - Obtener vehículo
- `GET /api/vehicles/search/plate` - Buscar por placa

### Estacionamientos
- `GET /api/parking-lots` - Listar estacionamientos
- `GET /api/parking-lots/{id}` - Obtener estacionamiento
- `GET /api/parking-spots/available` - Espacios disponibles

### Parqueadero
- `POST /api/parking/entry` - Registrar entrada
- `POST /api/parking/exit` - Registrar salida
- `POST /api/parking/payment` - Procesar pago
- `GET /api/parking/current` - Vehículos actuales
- `GET /api/parking/history` - Historial

## Principios SOLID Aplicados

- **Single Responsibility**: Cada clase tiene una única responsabilidad
- **Open/Closed**: Abierto para extensión, cerrado para modificación
- **Liskov Substitution**: Interfaces intercambiables
- **Interface Segregation**: Interfaces específicas y pequeñas
- **Dependency Inversion**: Dependencias de abstracciones

## Testing

Ejecutar tests:
```bash
php artisan test
```

## Documentación

La documentación completa de la API se encuentra en `/api/documentation` (cuando se configure Swagger).

## Licencia

MIT
