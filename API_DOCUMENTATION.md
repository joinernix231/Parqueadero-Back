# API Documentation — Parking Management System

## Base URL

Typical local URL (adjust host/port to match your environment):

```
http://localhost:8080/api
```

## Authentication

The API uses **Laravel Sanctum** with Bearer tokens.

### Headers for protected routes

```
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

## Response envelope

Success responses generally include:

```json
{
  "success": true,
  "message": "Human-readable message in English",
  "data": { }
}
```

Paginated list responses may include `meta` with `current_page`, `last_page`, `per_page`, `total`.

Error responses:

```json
{
  "success": false,
  "message": "Error description"
}
```

## Endpoints

### Authentication

#### `POST /login`

Authenticate and receive a token.

**Request body**

```json
{
  "email": "admin@parking.com",
  "password": "password"
}
```

**200 response**

```json
{
  "success": true,
  "message": "Login successful",
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

#### `POST /logout`

Revoke the current token (requires authentication).

**200 response**

```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

#### `GET /me`

Return the authenticated user (requires authentication).

**200 response** — user payload under `data` (id, name, email, role, timestamps). Message example: `Authenticated user retrieved successfully`.

---

### Vehicles

#### `GET /vehicles`

List vehicles (pagination supported).

**Query parameters**

| Parameter | Description |
|-----------|-------------|
| `page` | Page number |
| `per_page` | Items per page (default: 15) |
| `plate` | Filter by license plate |
| `owner_name` | Filter by owner name |

**200** — `message`: e.g. `Vehicles retrieved successfully`.

#### `POST /vehicles`

Create a vehicle.

**Request body**

```json
{
  "plate": "XYZ789",
  "owner_name": "Jane Doe",
  "phone": "3009876543",
  "vehicle_type": "car"
}
```

**200** — `message`: `Vehicle created successfully`.

#### `GET /vehicles/{id}`

Get one vehicle by ID.

**404** — `Vehicle not found`.

#### `GET /vehicles/search-by-plate?plate=ABC123`

Search by license plate (query parameter `plate`).

---

### Parking lots

#### `GET /parking-lots`

List active parking lots.

**200** — `Parking lots retrieved successfully`.

#### `GET /parking-lots/{id}`

Get one lot.

**404** — `Parking lot not found`.

#### `POST /parking-lots` / `PUT /parking-lots/{id}`

Create or update a lot (operator/admin as per your route middleware).

**200** — `Parking lot created successfully` / `Parking lot updated successfully`.

---

### Parking spots

#### `GET /parking-spots/available?parking_lot_id=1`

List available spots for a lot.

**422** if `parking_lot_id` is missing — `parking_lot_id is required`.

**200** — `Available spots retrieved successfully`.

---

### Parking operations

#### `POST /parking/entry`

Register vehicle entry.

**Examples**

```json
{
  "vehicle_id": 1,
  "parking_lot_id": 1,
  "parking_spot_id": 1
}
```

Or with new vehicle data:

```json
{
  "plate": "ABC123",
  "parking_lot_id": 1,
  "parking_spot_id": 1,
  "vehicle_data": {
    "plate": "ABC123",
    "owner_name": "Jane Doe",
    "phone": "3001234567",
    "vehicle_type": "car"
  }
}
```

**200** — `Entry registered successfully`.

#### `POST /parking/exit`

Register exit (by `ticket_id` or `plate`).

**200** — `Exit registered successfully`.

#### `POST /parking/payment`

Process payment for a ticket.

```json
{
  "ticket_id": 1,
  "amount": 6.25,
  "payment_method": "cash"
}
```

**200** — `Payment processed successfully`.

#### `GET /parking/tickets/{id}`

**404** — `Ticket not found`.

**200** — `Ticket retrieved successfully`.

#### `GET /parking/tickets/by-plate/{plate}`

**404** — `No active ticket found for the given license plate`.

**200** — `Active ticket found successfully`.

#### `GET /parking/current`

Currently parked vehicles/tickets (supports filters; see `CurrentTicketsRequest`).

**200** — `Current tickets retrieved successfully`.

#### `GET /parking/history`

History with pagination and filters (`date_from`, `date_to`, `plate`, `parking_lot_id`, `status`, etc.).

**200** — `History retrieved successfully`.

#### `GET /parking/tickets/{id}/calculate-price`

Preview amount for an **active** ticket (no exit recorded yet).

**200** — `Price calculated successfully` (payload includes hours, rate, total, etc.).  
**404** — `Ticket not found`.  
**422** — e.g. `This ticket already has an exit recorded`.

#### Receipt downloads

- `GET /parking/tickets/{id}/receipt/entry` — stream entry PDF (`entry-receipt-{id}.pdf`).  
- `GET /parking/tickets/{id}/receipt/exit` — stream exit PDF (`exit-receipt-{id}.pdf`).  

**404** / **422** when the ticket is missing or exit receipt is requested before exit is recorded (`This ticket does not have an exit time recorded yet`).

---

### Dashboard

#### `GET /dashboard/stats` (path per your routes)

**200** — `Dashboard statistics retrieved successfully`. Includes metrics such as active vehicles, revenue, occupancy; weekday labels are **Mon–Sun** in English.

---

## HTTP status codes

| Code | Meaning |
|------|---------|
| 200 | Success |
| 401 | Not authenticated / invalid credentials (`Invalid credentials`) |
| 404 | Resource not found |
| 422 | Validation or business rule error |
| 500 | Server error |

## Validation errors (422)

Laravel validation failures return `message` (often `The given data was invalid.`) plus an `errors` object with field keys. Custom messages are in **English** (see `app/Http/Requests/**`).

Example:

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "plate": ["License plate is required."]
  }
}
```

---

## PDF receipts (async jobs)

When queue workers are enabled, PDFs are stored under:

- Entry: `storage/app/receipts/entry/entry-receipt-{id}.pdf`
- Exit: `storage/app/receipts/exit/exit-receipt-{id}.pdf`

Download filenames from HTTP responses: `entry-receipt-{id}.pdf`, `exit-receipt-{id}.pdf`.

---

## Angular example (environment)

```typescript
export const environment = {
  production: false,
  apiUrl: 'http://localhost:8080/api',
};
```

Use an HTTP interceptor to attach `Authorization: Bearer <token>` for protected routes.

---

## Demo credentials (seeders)

If your seeders create these users:

- **Admin:** `admin@parking.com` / `password`  
- **Operator:** `operator@parking.com` / `password`  
- **Guard:** `guard@parking.com` / `password`  
