# API Documentation - Single Source of Truth

## Base URL
```
Production: https://livekenceng.com/api
Development: http://localhost:8000/api
```

## Standard Response Format

### Success Response
```json
{
  "success": true,
  "message": "Optional success message",
  "data": { ... }
}
```

### Error Response
```json
{
  "success": false,
  "message": "Error description",
  "errors": {
    "field": ["Validation error messages"]
  }
}
```

---

## 1. User Profile APIs

### Get User Profile
**Endpoint:** `POST /api/members/profile`

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

**Success Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "email": "user@example.com",
    "telegram_username": "@username",
    "expiry_date": "2025-11-14T12:00:00+00:00",
    "machine_id": "unique-machine-id",
    "created_at": "2025-10-14T12:00:00.000000Z",
    "updated_at": "2025-10-23T15:40:00.000000Z"
  }
}
```

---

### Update Telegram Username
**Endpoint:** `PUT /api/members/telegram`

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "password123",
  "telegram_username": "@username"
}
```

**Success Response (200 OK):**
```json
{
  "success": true,
  "message": "Telegram username updated successfully",
  "data": {
    "id": 1,
    "email": "user@example.com",
    "telegram_username": "@username",
    "updated_at": "2025-10-23T15:40:00.000000Z"
  }
}
```

---

## 2. Shopee Accounts APIs

### Get User's Shopee Accounts
**Endpoint:** `GET /api/members/shopee-accounts?email=user@example.com&password=password123`

**OR with POST:**
**Endpoint:** `GET /api/members/shopee-accounts`

**Request Body (if using POST):**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

**Success Response (200 OK):**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "member_id": 1,
      "name": "Account Name",
      "cookie": "session_data",
      "is_active": true,
      "created_at": "2025-10-23T15:30:00.000000Z",
      "updated_at": "2025-10-23T15:30:00.000000Z"
    }
  ]
}
```

---

### Add Shopee Account
**Endpoint:** `POST /api/members/shopee-accounts`

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "password123",
  "name": "Account Name",
  "cookie": "session_data",
  "is_active": true
}
```

**Success Response (200 OK):**
```json
{
  "success": true,
  "message": "Shopee account added successfully",
  "data": {
    "id": 1,
    "member_id": 1,
    "name": "Account Name",
    "cookie": "session_data",
    "is_active": true,
    "created_at": "2025-10-23T15:30:00.000000Z",
    "updated_at": "2025-10-23T15:30:00.000000Z"
  }
}
```

---

### Update Shopee Account
**Endpoint:** `PUT /api/members/shopee-accounts/{accountId}`

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "password123",
  "name": "Updated Name",
  "cookie": "new_session",
  "is_active": false
}
```

**Success Response (200 OK):**
```json
{
  "success": true,
  "message": "Shopee account updated successfully",
  "data": {
    "id": 1,
    "member_id": 1,
    "name": "Updated Name",
    "cookie": "new_session",
    "is_active": false,
    "created_at": "2025-10-23T15:30:00.000000Z",
    "updated_at": "2025-10-23T15:35:00.000000Z"
  }
}
```

---

### Delete Shopee Account
**Endpoint:** `DELETE /api/members/shopee-accounts/{accountId}`

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

**Success Response (200 OK):**
```json
{
  "success": true,
  "message": "Shopee account deleted successfully"
}
```

---

## 3. Settings APIs

### Get User Settings
**Endpoint:** `POST /api/members/settings`

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

**Success Response (200 OK):**
```json
{
  "success": true,
  "data": {
    "download_platform": null,
    "download_duration": null,
    "download_save_location": null,
    "split_duration": null,
    "part_delay_seconds": null,
    "custom_ffmpeg_path": null
  }
}
```

---

### Update User Settings
**Endpoint:** `PUT /api/members/settings`

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "password123",
  "settings": {
    "download_platform": "platform_name",
    "download_duration": 60,
    "download_save_location": "/path/to/save",
    "split_duration": 300,
    "part_delay_seconds": 5,
    "custom_ffmpeg_path": "/path/to/ffmpeg"
  }
}
```

**Success Response (200 OK):**
```json
{
  "success": true,
  "message": "Settings updated successfully",
  "data": {
    "download_platform": "platform_name",
    "download_duration": 60,
    "download_save_location": "/path/to/save",
    "split_duration": 300,
    "part_delay_seconds": 5,
    "custom_ffmpeg_path": "/path/to/ffmpeg"
  }
}
```

---

## 4. Authentication

### Login (Enhanced)
**Endpoint:** `POST /api/members/login`

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "password123",
  "machine_id": "unique-id"
}
```

**Success Response (200 OK):**
```json
{
  "success": true,
  "user": {
    "id": 1,
    "email": "user@example.com",
    "telegram_username": "@username",
    "expiry_date": "2025-11-14T12:00:00+00:00",
    "machine_id": "unique-id",
    "created_at": "2025-10-14T12:00:00.000000Z",
    "updated_at": "2025-10-23T15:40:00.000000Z"
  }
}
```

---

## Error Responses

### Invalid Credentials (401)
```json
{
  "success": false,
  "message": "Invalid credentials"
}
```

### Missing Required Fields (400)
```json
{
  "success": false,
  "message": "Email and password are required"
}
```

### Validation Errors (422)
```json
{
  "success": false,
  "errors": {
    "name": ["The name field is required."],
    "cookie": ["The cookie field is required."]
  }
}
```

### Unauthorized Access (403)
```json
{
  "success": false,
  "message": "Unauthorized access to Shopee account"
}
```

---

## Response Data Types

### User Object
- `id`: integer
- `email`: string
- `telegram_username`: string (nullable)
- `expiry_date`: string (ISO 8601 date, nullable)
- `machine_id`: string (nullable)
- `created_at`: string (ISO 8601 datetime)
- `updated_at`: string (ISO 8601 datetime)

### Shopee Account Object
- `id`: integer
- `member_id`: integer
- `name`: string
- `cookie`: string
- `is_active`: boolean
- `created_at`: string (ISO 8601 datetime)
- `updated_at`: string (ISO 8601 datetime)

### Settings Object
- `download_platform`: string (nullable)
- `download_duration`: integer (nullable)
- `download_save_location`: string (nullable)
- `split_duration`: integer (nullable)
- `part_delay_seconds`: integer (nullable)
- `custom_ffmpeg_path`: string (nullable)

---

## Status Codes

| Code | Description |
|------|-------------|
| 200 | OK - Request successful |
| 400 | Bad Request - Missing or invalid parameters |
| 401 | Unauthorized - Invalid credentials |
| 403 | Forbidden - Unauthorized access to resource |
| 422 | Unprocessable Entity - Validation errors |
| 500 | Internal Server Error - Server error |

---

## Examples

### JavaScript/Electron Example

```javascript
// Get user profile
async function getUserProfile(email, password) {
  const response = await fetch('https://livekenceng.com/api/members/profile', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email, password })
  });
  
  const data = await response.json();
  return data.success ? data.data : null;
}

// Update Telegram
async function updateTelegram(email, password, telegramUsername) {
  const response = await fetch('https://livekenceng.com/api/members/telegram', {
    method: 'PUT',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email, password, telegram_username: telegramUsername })
  });
  
  return await response.json();
}

// Get Shopee accounts
async function getShopeeAccounts(email, password) {
  const params = new URLSearchParams({ email, password });
  const response = await fetch(`https://livekenceng.com/api/members/shopee-accounts?${params}`);
  const data = await response.json();
  return data.success ? data.data : [];
}

// Add Shopee account
async function addShopeeAccount(email, password, name, cookie) {
  const response = await fetch('https://livekenceng.com/api/members/shopee-accounts', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email, password, name, cookie, is_active: true })
  });
  
  return await response.json();
}
```

---

**Last Updated:** October 25, 2025  
**API Version:** 2.0
