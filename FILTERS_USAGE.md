# Sistema de Filtros Criteria - Guía de Uso

## Descripción

Sistema moderno de filtros para repositorios que permite filtrar datos de forma flexible y segura usando una sintaxis simple y poderosa.

## Sintaxis

### Formato Básico
```
campo|operador|valor;campo2|operador2|valor2
```

### Operadores Disponibles

| Operador | Descripción | Ejemplo |
|----------|-------------|---------|
| `eq`, `=`, `==` | Igual a | `plate|eq|ABC123` |
| `ne`, `!=`, `<>` | Diferente de | `vehicle_type|ne|car` |
| `gt`, `>` | Mayor que | `total_amount|gt|100` |
| `gte`, `>=` | Mayor o igual que | `total_amount|gte|100` |
| `lt`, `<` | Menor que | `total_amount|lt|1000` |
| `lte`, `<=` | Menor o igual que | `total_amount|lte|1000` |
| `like` | Contiene (añade % alrededor) | `plate|like|ABC` |
| `ilike` | Contiene (case-insensitive) | `owner_name|ilike|juan` |
| `in` | En lista (valores separados por coma) | `vehicle_type|in|car,motorcycle` |
| `notIn` | No está en lista | `vehicle_type|notIn|truck` |
| `between` | Entre dos valores | `total_amount|between|100,500` |
| `null` | Es nulo | `exit_time|null|` |
| `notNull` | No es nulo | `exit_time|notNull|` |
| `date` | Fecha específica | `entry_time|date|2024-01-15` |
| `dateBetween` | Rango de fechas | `entry_time|dateBetween|2024-01-01,2024-12-31` |

### Filtros en Relaciones

Para filtrar por campos de modelos relacionados:

```
relacion.campo|operador|valor
```

**Ejemplos:**
- `vehicle.plate|eq|ABC123` - Filtrar tickets por placa del vehículo
- `parkingLot.name|like|Central` - Filtrar por nombre del estacionamiento

## Uso en API

### Query String

```
GET /api/vehicles?filters=plate|like|ABC;vehicle_type|in|car,motorcycle
GET /api/parking/history?filters=vehicle.plate|eq|ABC123;entry_time|dateBetween|2024-01-01,2024-12-31
```

### Body (POST/PUT)

```json
{
  "filters": "plate|like|ABC;vehicle_type|eq|car"
}
```

### Array de Filtros

También puedes pasar un array directamente:

```php
$filters = [
    'plate' => 'ABC123',
    'vehicle_type' => 'car,motorcycle', // Se interpreta como 'in'
    'created_at' => '2024-01-01,2024-12-31' // Se interpreta como 'dateBetween'
];
```

## Ejemplos Prácticos

### Vehículos

```bash
# Buscar vehículos por placa que contenga "ABC"
GET /api/vehicles?filters=plate|like|ABC

# Buscar vehículos de tipo car o motorcycle
GET /api/vehicles?filters=vehicle_type|in|car,motorcycle

# Buscar vehículos creados en enero 2024
GET /api/vehicles?filters=created_at|dateBetween|2024-01-01,2024-01-31
```

### Tickets de Estacionamiento

```bash
# Tickets activos (sin salida)
GET /api/parking/history?filters=exit_time|null|

# Tickets con vehículo específico
GET /api/parking/history?filters=vehicle.plate|eq|ABC123

# Tickets en un rango de fechas
GET /api/parking/history?filters=entry_time|dateBetween|2024-01-01,2024-12-31

# Tickets pagados con monto mayor a $50
GET /api/parking/history?filters=is_paid|eq|true;total_amount|gt|50
```

### Combinación de Filtros

```bash
# Múltiples filtros separados por punto y coma
GET /api/vehicles?filters=plate|like|ABC;vehicle_type|eq|car;created_at|dateBetween|2024-01-01,2024-12-31
```

## Implementación en Repositorios

### 1. Usar el Trait

```php
use App\Infrastructure\Repositories\Traits\AppliesFilters;

class EloquentVehicleRepository implements VehicleRepositoryInterface
{
    use AppliesFilters;
    
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = VehicleModel::query();
        $query = $this->applyFilters($query, $filters);
        return $query->paginate($perPage);
    }
}
```

### 2. Definir Campos Permitidos

```php
protected function getFilterableFields(): array
{
    return ['plate', 'owner_name', 'phone', 'vehicle_type', 'created_at', 'updated_at'];
}

protected function getFilterableRelations(): array
{
    return ['vehicle', 'parkingLot']; // Para tickets
}

protected function getDateFields(): array
{
    return ['created_at', 'updated_at', 'entry_time', 'exit_time'];
}
```

## Seguridad

- Solo los campos definidos en `getFilterableFields()` pueden ser filtrados
- Solo las relaciones definidas en `getFilterableRelations()` pueden ser usadas
- Los campos de fecha se validan automáticamente
- Previene SQL injection usando parámetros preparados de Laravel

## Ventajas

✅ **Una sola URL** para todos los filtros  
✅ **Sintaxis simple** y fácil de entender  
✅ **Type-safe** con validación de campos  
✅ **Seguro** contra SQL injection  
✅ **Extensible** fácilmente  
✅ **Compatible** con relaciones Eloquent  
✅ **Moderno** usando PHP 8+ features  



