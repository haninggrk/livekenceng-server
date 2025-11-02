# Device Metadata API Documentation

## App Identifier
Any app can use these endpoints.

---

## Device Metadata

### Get All Devices
```
GET /api/members/device-metadata
```
**Query Params or Body:**
```
email=user@example.com&password=password123
```
**Or POST body:**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```
**Response:**
```json
{
  "success": true,
  "devices": [
    {
      "id": 1,
      "manufacturer": "Samsung",
      "device_name": "SM-G998B",
      "device_model": "Galaxy S21 Ultra",
      "rom": "Android 13",
      "created_at": "2025-01-01T10:00:00.000000Z",
      "updated_at": "2025-01-01T10:00:00.000000Z"
    }
  ]
}
```

### Get Single Device
```
GET /api/members/device-metadata/1
```
**Query Params or Body:**
```
email=user@example.com&password=password123
```
**Or POST body:**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```
**Response:**
```json
{
  "success": true,
  "device": {
    "id": 1,
    "manufacturer": "Samsung",
    "device_name": "SM-G998B",
    "device_model": "Galaxy S21 Ultra",
    "rom": "Android 13",
    "created_at": "2025-01-01T10:00:00.000000Z",
    "updated_at": "2025-01-01T10:00:00.000000Z"
  }
}
```

### Create Device
```
POST /api/members/device-metadata
```
**Body:**
```json
{
  "email": "user@example.com",
  "password": "password123",
  "manufacturer": "Samsung",
  "device_name": "SM-G998B",
  "device_model": "Galaxy S21 Ultra",
  "rom": "Android 13"
}
```
**Response:**
```json
{
  "success": true,
  "message": "Device metadata created successfully",
  "device": {
    "id": 1,
    "manufacturer": "Samsung",
    "device_name": "SM-G998B",
    "device_model": "Galaxy S21 Ultra",
    "rom": "Android 13",
    "created_at": "2025-01-01T10:00:00.000000Z",
    "updated_at": "2025-01-01T10:00:00.000000Z"
  }
}
```
- All fields are optional

### Update Device
```
PUT /api/members/device-metadata/1
```
**Body:**
```json
{
  "email": "user@example.com",
  "password": "password123",
  "manufacturer": "Xiaomi",
  "device_name": "Mi 11",
  "device_model": "Mi 11 Pro",
  "rom": "MIUI 14"
}
```
**Response:**
```json
{
  "success": true,
  "message": "Device metadata updated successfully",
  "device": {
    "id": 1,
    "manufacturer": "Xiaomi",
    "device_name": "Mi 11",
    "device_model": "Mi 11 Pro",
    "rom": "MIUI 14",
    "created_at": "2025-01-01T10:00:00.000000Z",
    "updated_at": "2025-01-01T12:00:00.000000Z"
  }
}
```
- All fields are optional
- Only provided fields are updated

### Delete Device
```
DELETE /api/members/device-metadata/1
```
**Body:**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```
**Response:**
```json
{
  "success": true,
  "message": "Device metadata deleted successfully"
}
```

---

## Common HTTP Error Codes

| Code | Description |
|------|-------------|
| 400 | Bad Request - Missing or invalid parameters |
| 401 | Unauthorized - Invalid credentials |
| 404 | Not Found - Resource not found or doesn't belong to you |

