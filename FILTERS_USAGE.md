# Criteria filter system — usage guide

## Overview

Repository filters use a compact, safe string syntax so clients can query without ad-hoc query parameters for every field.

## Syntax

### Basic format

```
field|operator|value;field2|operator2|value2
```

### Operators

| Operator | Description | Example |
|----------|-------------|---------|
| `eq`, `=`, `==` | Equals | `plate|eq|ABC123` |
| `ne`, `!=`, `<>` | Not equal | `vehicle_type|ne|car` |
| `gt`, `>` | Greater than | `total_amount|gt|100` |
| `gte`, `>=` | Greater or equal | `total_amount|gte|100` |
| `lt`, `<` | Less than | `total_amount|lt|1000` |
| `lte`, `<=` | Less or equal | `total_amount|lte|1000` |
| `like` | Contains (wraps `%`) | `plate|like|ABC` |
| `ilike` | Case-insensitive contains | `owner_name|ilike|jane` |
| `in` | In list (comma-separated) | `vehicle_type|in|car,motorcycle` |
| `notIn` | Not in list | `vehicle_type|notIn|truck` |
| `between` | Between two values | `total_amount|between|100,500` |
| `null` | Is null | `exit_time|null|` |
| `notNull` | Is not null | `exit_time|notNull|` |
| `date` | Specific date | `entry_time|date|2024-01-15` |
| `dateBetween` | Date range | `entry_time|dateBetween|2024-01-01,2024-12-31` |

### Relation filters

```
relation.field|operator|value
```

Examples:

- `vehicle.plate|eq|ABC123` — tickets for a given plate  
- `parkingLot.name|like|Central` — filter by lot name  

## Using from the API

### Query string

```
GET /api/vehicles?filters=plate|like|ABC;vehicle_type|in|car,motorcycle
GET /api/parking/history?filters=vehicle.plate|eq|ABC123;entry_time|dateBetween|2024-01-01,2024-12-31
```

### JSON body (POST/PUT)

```json
{
  "filters": "plate|like|ABC;vehicle_type|eq|car"
}
```

### PHP array shorthand

```php
$filters = [
    'plate' => 'ABC123',
    'vehicle_type' => 'car,motorcycle', // interpreted as `in`
    'created_at' => '2024-01-01,2024-12-31', // interpreted as `dateBetween`
];
```

## Examples

### Vehicles

```bash
# Plates containing "ABC"
GET /api/vehicles?filters=plate|like|ABC

# car or motorcycle
GET /api/vehicles?filters=vehicle_type|in|car,motorcycle

# Created in January 2024
GET /api/vehicles?filters=created_at|dateBetween|2024-01-01,2024-01-31
```

### Parking tickets

```bash
# No exit time (active)
GET /api/parking/history?filters=exit_time|null|

# By vehicle plate
GET /api/parking/history?filters=vehicle.plate|eq|ABC123

# Entry date range
GET /api/parking/history?filters=entry_time|dateBetween|2024-01-01,2024-12-31

# Paid and amount > 50
GET /api/parking/history?filters=is_paid|eq|true;total_amount|gt|50
```

### Combining filters

```bash
GET /api/vehicles?filters=plate|like|ABC;vehicle_type|eq|car;created_at|dateBetween|2024-01-01,2024-12-31
```

## Repository implementation

### 1. Use the trait

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

### 2. Allow-list fields

```php
protected function getFilterableFields(): array
{
    return ['plate', 'owner_name', 'phone', 'vehicle_type', 'created_at', 'updated_at'];
}

protected function getFilterableRelations(): array
{
    return ['vehicle', 'parkingLot'];
}

protected function getDateFields(): array
{
    return ['created_at', 'updated_at', 'entry_time', 'exit_time'];
}
```

## Security

- Only `getFilterableFields()` may be filtered.  
- Only `getFilterableRelations()` may be used in relation paths.  
- Date fields are validated.  
- Queries use Laravel’s parameter binding (SQL injection safe).  

## Benefits

- One query parameter for many filters  
- Readable syntax  
- Field allow-listing  
- Extensible per repository  
- Works with Eloquent relations  
