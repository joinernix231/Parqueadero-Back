# API Documentation - Parking Management System

## Base URL
```
http://localhost:8080/api
```

## Autenticación

La API utiliza Laravel Sanctum para autenticación mediante tokens Bearer.

### Headers requeridos para rutas protegidas:
```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

## Endpoints

### Autenticación

#### POST /login
Iniciar sesión y obtener token de autenticación.

**Request:**
```json
{
  "email": "admin@parking.com",
  "password": "password"
}
```

**Response (200):**
```json
{
  "data": {
    "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
    "user": {
      "id": 1,
      "name": "Administrator",
      "email": "admin@parking.com",
      "role": "admin"
    }
  }
}
```

#### POST /logout
Cerrar sesión (requiere autenticación).

**Response (200):**
```json
{
  "message": "Sesión cerrada exitosamente"
}
```

#### GET /user
Obtener información del usuario autenticado.

**Response (200):**
```json
{
  "data": {
    "id": 1,
    "name": "Administrator",
    "email": "admin@parking.com",
    "role": "admin",
    "created_at": "2024-01-01T00:00:00.000000Z"
  }
}
```

### Vehículos

#### GET /vehicles
Listar vehículos con paginación.

**Query Parameters:**
- `page` (opcional): Número de página
- `per_page` (opcional): Elementos por página (default: 15)
- `plate` (opcional): Filtrar por placa
- `owner_name` (opcional): Filtrar por nombre del propietario

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "plate": "ABC123",
      "owner_name": "Juan Pérez",
      "phone": "3001234567",
      "vehicle_type": "car",
      "created_at": "2024-01-01T00:00:00.000000Z"
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 1
  }
}
```

#### POST /vehicles
Registrar un nuevo vehículo.

**Request:**
```json
{
  "plate": "XYZ789",
  "owner_name": "María García",
  "phone": "3009876543",
  "vehicle_type": "car"
}
```

**Response (200):**
```json
{
  "data": {
    "id": 2,
    "plate": "XYZ789",
    "owner_name": "María García",
    "phone": "3009876543",
    "vehicle_type": "car",
    "created_at": "2024-01-01T00:00:00.000000Z"
  }
}
```

#### GET /vehicles/{id}
Obtener un vehículo por ID.

**Response (200):**
```json
{
  "data": {
    "id": 1,
    "plate": "ABC123",
    "owner_name": "Juan Pérez",
    "phone": "3001234567",
    "vehicle_type": "car",
    "created_at": "2024-01-01T00:00:00.000000Z"
  }
}
```

#### GET /vehicles/search/plate?plate=ABC123
Buscar vehículo por placa.

**Response (200):**
```json
{
  "data": {
    "id": 1,
    "plate": "ABC123",
    "owner_name": "Juan Pérez",
    "phone": "3001234567",
    "vehicle_type": "car",
    "created_at": "2024-01-01T00:00:00.000000Z"
  }
}
```

### Estacionamientos

#### GET /parking-lots
Listar estacionamientos activos.

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Central Parking",
      "address": "123 Main St, City",
      "total_spots": 100,
      "hourly_rate_day": "2.50",
      "hourly_rate_night": "3.00",
      "day_start_time": "06:00",
      "day_end_time": "20:00",
      "is_active": true
    }
  ]
}
```

#### GET /parking-lots/{id}
Obtener un estacionamiento por ID.

**Response (200):**
```json
{
  "data": {
    "id": 1,
    "name": "Central Parking",
    "address": "123 Main St, City",
    "total_spots": 100,
    "hourly_rate_day": "2.50",
    "hourly_rate_night": "3.00",
    "day_start_time": "06:00",
    "day_end_time": "20:00",
    "is_active": true
  }
}
```

### Espacios de Estacionamiento

#### GET /parking-spots/available?parking_lot_id=1
Obtener espacios disponibles de un estacionamiento.

**Query Parameters:**
- `parking_lot_id` (requerido): ID del estacionamiento

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "spot_number": "A01",
      "spot_type": "regular",
      "is_occupied": false,
      "is_active": true,
      "is_available": true
    }
  ]
}
```

### Gestión de Estacionamiento

#### POST /parking/entry
Registrar entrada de vehículo.

**Request:**
```json
{
  "vehicle_id": 1,
  "parking_lot_id": 1,
  "parking_spot_id": 1
}
```

**O alternativamente:**
```json
{
  "plate": "ABC123",
  "parking_lot_id": 1,
  "parking_spot_id": 1
}
```

**Response (200):**
```json
{
  "data": {
    "id": 1,
    "vehicle_id": 1,
    "parking_spot_id": 1,
    "parking_lot_id": 1,
    "entry_time": "2024-01-01T10:00:00.000000Z",
    "exit_time": null,
    "entry_guard_id": 1,
    "exit_guard_id": null,
    "total_hours": 0,
    "hourly_rate_applied": 0,
    "total_amount": 0,
    "is_paid": false,
    "payment_method": null,
    "payment_time": null,
    "is_active": true
  }
}
```

#### POST /parking/exit
Registrar salida de vehículo.

**Request:**
```json
{
  "ticket_id": 1
}
```

**O alternativamente:**
```json
{
  "plate": "ABC123"
}
```

**Response (200):**
```json
{
  "data": {
    "id": 1,
    "vehicle_id": 1,
    "parking_spot_id": 1,
    "parking_lot_id": 1,
    "entry_time": "2024-01-01T10:00:00.000000Z",
    "exit_time": "2024-01-01T12:30:00.000000Z",
    "entry_guard_id": 1,
    "exit_guard_id": 1,
    "total_hours": 2.5,
    "hourly_rate_applied": 2.5,
    "total_amount": 6.25,
    "is_paid": false,
    "payment_method": null,
    "payment_time": null,
    "is_active": false
  }
}
```

#### POST /parking/payment
Procesar pago de ticket.

**Request:**
```json
{
  "ticket_id": 1,
  "amount": 6.25,
  "payment_method": "cash"
}
```

**Response (200):**
```json
{
  "data": {
    "id": 1,
    "vehicle_id": 1,
    "parking_spot_id": 1,
    "parking_lot_id": 1,
    "entry_time": "2024-01-01T10:00:00.000000Z",
    "exit_time": "2024-01-01T12:30:00.000000Z",
    "entry_guard_id": 1,
    "exit_guard_id": 1,
    "total_hours": 2.5,
    "hourly_rate_applied": 2.5,
    "total_amount": 6.25,
    "is_paid": true,
    "payment_method": "cash",
    "payment_time": "2024-01-01T12:35:00.000000Z",
    "is_active": false
  }
}
```

#### GET /parking/current
Obtener vehículos actualmente estacionados.

**Query Parameters:**
- `parking_lot_id` (opcional): Filtrar por estacionamiento

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "vehicle_id": 1,
      "parking_spot_id": 1,
      "parking_lot_id": 1,
      "entry_time": "2024-01-01T10:00:00.000000Z",
      "exit_time": null,
      "entry_guard_id": 1,
      "exit_guard_id": null,
      "total_hours": 0,
      "hourly_rate_applied": 0,
      "total_amount": 0,
      "is_paid": false,
      "payment_method": null,
      "payment_time": null,
      "is_active": true
    }
  ]
}
```

#### GET /parking/history
Obtener historial de estacionamiento con paginación.

**Query Parameters:**
- `page` (opcional): Número de página
- `per_page` (opcional): Elementos por página (default: 15)
- `date_from` (opcional): Fecha desde (formato: YYYY-MM-DD)
- `date_to` (opcional): Fecha hasta (formato: YYYY-MM-DD)
- `plate` (opcional): Filtrar por placa
- `parking_lot_id` (opcional): Filtrar por estacionamiento
- `status` (opcional): Filtrar por estado (current, completed, paid, unpaid)

**Response (200):**
```json
{
  "data": [
    {
      "id": 1,
      "vehicle_id": 1,
      "parking_spot_id": 1,
      "parking_lot_id": 1,
      "entry_time": "2024-01-01T10:00:00.000000Z",
      "exit_time": "2024-01-01T12:30:00.000000Z",
      "entry_guard_id": 1,
      "exit_guard_id": 1,
      "total_hours": 2.5,
      "hourly_rate_applied": 2.5,
      "total_amount": 6.25,
      "is_paid": true,
      "payment_method": "cash",
      "payment_time": "2024-01-01T12:35:00.000000Z",
      "is_active": false
    }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 1,
    "per_page": 15,
    "total": 1
  }
}
```

## Códigos de Estado HTTP

- `200` - Éxito
- `401` - No autenticado / Credenciales inválidas
- `404` - Recurso no encontrado
- `422` - Error de validación
- `500` - Error del servidor

## Errores

Las respuestas de error siguen este formato:

```json
{
  "message": "Mensaje de error descriptivo"
}
```

Para errores de validación (422):

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "plate": ["La placa es obligatoria."],
    "owner_name": ["El nombre del propietario es obligatorio."]
  }
}
```

## Configuración para Angular

### Variables de entorno en Angular

```typescript
export const environment = {
  production: false,
  apiUrl: 'http://localhost:8080/api',
};
```

### Servicio HTTP con Interceptor

```typescript
import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class ApiService {
  private apiUrl = 'http://localhost:8080/api';

  constructor(private http: HttpClient) {}

  private getHeaders(): HttpHeaders {
    const token = localStorage.getItem('token');
    return new HttpHeaders({
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'Authorization': token ? `Bearer ${token}` : ''
    });
  }

  login(email: string, password: string): Observable<any> {
    return this.http.post(`${this.apiUrl}/login`, { email, password });
  }

  logout(): Observable<any> {
    return this.http.post(`${this.apiUrl}/logout`, {}, {
      headers: this.getHeaders()
    });
  }

  getVehicles(): Observable<any> {
    return this.http.get(`${this.apiUrl}/vehicles`, {
      headers: this.getHeaders()
    });
  }

  // ... más métodos
}
```

## Credenciales de Prueba

- **Admin**: `admin@parking.com` / `password`
- **Operator**: `operator@parking.com` / `password`
- **Guard**: `guard@parking.com` / `password`




