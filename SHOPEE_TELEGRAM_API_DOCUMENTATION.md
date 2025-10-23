# Shopee Accounts & Telegram API Documentation

## Overview

This API allows management of Shopee accounts and Telegram usernames for members. Members can have multiple Shopee accounts (each with a name and cookie) and one Telegram username.

## Base URL

```
Production: https://livekenceng.com/api
Development: http://localhost:8000/api
```

---

## API Endpoints

### 1. Get Member's Shopee Accounts

Retrieve all Shopee accounts for a specific member.

**Endpoint:** `POST /api/shopee/get-accounts`

**Headers:**
```http
Content-Type: application/json
```

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "userpassword"
}
```

**Success Response (200 OK):**
```json
{
  "success": true,
  "shopee_accounts": [
    {
      "id": 1,
      "member_id": 1,
      "name": "My Shopee Account",
      "cookie": "session_id=abc123; user_id=456; ...",
      "is_active": true,
      "created_at": "2025-10-23T15:30:00.000000Z",
      "updated_at": "2025-10-23T15:30:00.000000Z"
    }
  ]
}
```

**Error Responses:**
- `400`: Missing email or password
- `401`: Invalid credentials

---

### 2. Add Shopee Account

Add a new Shopee account to a member.

**Endpoint:** `POST /api/shopee/add-account`

**Headers:**
```http
Content-Type: application/json
```

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "userpassword",
  "name": "My Shopee Account",
  "cookie": "session_id=abc123; user_id=456; ...",
  "is_active": true
}
```

**Success Response (200 OK):**
```json
{
  "success": true,
  "message": "Shopee account added successfully",
  "shopee_account": {
    "id": 1,
    "member_id": 1,
    "name": "My Shopee Account",
    "cookie": "session_id=abc123; user_id=456; ...",
    "is_active": true,
    "created_at": "2025-10-23T15:30:00.000000Z",
    "updated_at": "2025-10-23T15:30:00.000000Z"
  }
}
```

**Error Responses:**
- `400`: Missing required fields
- `401`: Invalid credentials
- `422`: Validation errors

---

### 3. Update Shopee Account

Update an existing Shopee account.

**Endpoint:** `PUT /api/shopee/update-account/{shopeeAccount}`

**Headers:**
```http
Content-Type: application/json
```

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "userpassword",
  "name": "Updated Shopee Account",
  "cookie": "session_id=xyz789; user_id=789; ...",
  "is_active": false
}
```

**Success Response (200 OK):**
```json
{
  "success": true,
  "message": "Shopee account updated successfully",
  "shopee_account": {
    "id": 1,
    "member_id": 1,
    "name": "Updated Shopee Account",
    "cookie": "session_id=xyz789; user_id=789; ...",
    "is_active": false,
    "created_at": "2025-10-23T15:30:00.000000Z",
    "updated_at": "2025-10-23T15:35:00.000000Z"
  }
}
```

**Error Responses:**
- `400`: Missing required fields
- `401`: Invalid credentials
- `403`: Unauthorized access to Shopee account
- `422`: Validation errors

---

### 4. Delete Shopee Account

Delete a Shopee account.

**Endpoint:** `DELETE /api/shopee/delete-account/{shopeeAccount}`

**Headers:**
```http
Content-Type: application/json
```

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "userpassword"
}
```

**Success Response (200 OK):**
```json
{
  "success": true,
  "message": "Shopee account deleted successfully"
}
```

**Error Responses:**
- `400`: Missing email or password
- `401`: Invalid credentials
- `403`: Unauthorized access to Shopee account

---

### 5. Update Telegram Username

Update a member's Telegram username.

**Endpoint:** `POST /api/shopee/update-telegram`

**Headers:**
```http
Content-Type: application/json
```

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "userpassword",
  "telegram_username": "@myusername"
}
```

**Success Response (200 OK):**
```json
{
  "success": true,
  "message": "Telegram username updated successfully",
  "member": {
    "id": 1,
    "email": "user@example.com",
    "telegram_username": "@myusername",
    "expiry_date": "2025-11-14T12:00:00+00:00",
    "created_at": "2025-10-14T12:00:00.000000Z",
    "updated_at": "2025-10-23T15:40:00.000000Z"
  }
}
```

**Error Responses:**
- `400`: Missing required fields
- `401`: Invalid credentials
- `422`: Validation errors

---

### 6. Get Eligible Cookies

Get all cookies and Telegram usernames for active members (no authentication required).

**Endpoint:** `GET /api/shopee/eligible-cookies`

**Headers:**
```http
Content-Type: application/json
```

**Success Response (200 OK):**
```json
{
  "success": true,
  "eligible_members": [
    {
      "member_id": 1,
      "email": "user@example.com",
      "telegram_username": "@myusername",
      "shopee_accounts": [
        {
          "id": 1,
          "name": "My Shopee Account",
          "cookie": "session_id=abc123; user_id=456; ..."
        },
        {
          "id": 2,
          "name": "Another Shopee Account",
          "cookie": "session_id=def456; user_id=789; ..."
        }
      ]
    }
  ],
  "total_active_members": 1
}
```

---

## Admin Panel Endpoints

The following endpoints are available for admin panel management:

### Admin Shopee Account Management

- `GET /admin/shopee-accounts` - Get all Shopee accounts
- `GET /admin/members/{member}/shopee-accounts` - Get Shopee accounts for specific member
- `POST /admin/shopee-accounts` - Create new Shopee account
- `PUT /admin/shopee-accounts/{shopeeAccount}` - Update Shopee account
- `DELETE /admin/shopee-accounts/{shopeeAccount}` - Delete Shopee account
- `PUT /admin/members/{member}/telegram` - Update member's Telegram username

**Admin Request Example:**
```json
{
  "member_id": 1,
  "name": "Admin Created Account",
  "cookie": "session_id=admin123; user_id=999; ...",
  "is_active": true
}
```

---

## Desktop App Integration Examples

### JavaScript/Electron Examples

#### Get Member's Shopee Accounts
```javascript
async function getShopeeAccounts(email, password) {
  const response = await fetch('https://livekenceng.com/api/shopee/get-accounts', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      email: email,
      password: password
    })
  });
  
  const data = await response.json();
  
  if (data.success) {
    return data.shopee_accounts;
  } else {
    throw new Error(data.message);
  }
}
```

#### Add Shopee Account
```javascript
async function addShopeeAccount(email, password, name, cookie) {
  const response = await fetch('https://livekenceng.com/api/shopee/add-account', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      email: email,
      password: password,
      name: name,
      cookie: cookie,
      is_active: true
    })
  });
  
  const data = await response.json();
  
  if (data.success) {
    return data.shopee_account;
  } else {
    throw new Error(data.message);
  }
}
```

#### Update Telegram Username
```javascript
async function updateTelegram(email, password, telegramUsername) {
  const response = await fetch('https://livekenceng.com/api/shopee/update-telegram', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      email: email,
      password: password,
      telegram_username: telegramUsername
    })
  });
  
  const data = await response.json();
  
  if (data.success) {
    return data.member;
  } else {
    throw new Error(data.message);
  }
}
```

#### Get Eligible Cookies (No Authentication)
```javascript
async function getEligibleCookies() {
  const response = await fetch('https://livekenceng.com/api/shopee/eligible-cookies');
  const data = await response.json();
  
  if (data.success) {
    return data.eligible_members;
  } else {
    throw new Error(data.message);
  }
}
```

---

## Error Handling

### HTTP Status Codes

| Code | Description |
|------|-------------|
| 200 | OK - Request successful |
| 400 | Bad Request - Missing or invalid parameters |
| 401 | Unauthorized - Invalid credentials |
| 403 | Forbidden - Unauthorized access to resource |
| 422 | Unprocessable Entity - Validation errors |
| 500 | Internal Server Error - Server error |

### Common Error Scenarios

#### Invalid Credentials
```json
{
  "success": false,
  "message": "Invalid credentials"
}
```

#### Unauthorized Access
```json
{
  "success": false,
  "message": "Unauthorized access to Shopee account"
}
```

#### Validation Errors
```json
{
  "success": false,
  "errors": {
    "name": ["The name field is required."],
    "cookie": ["The cookie field is required."]
  }
}
```

---

## Best Practices

### 1. Cookie Security
- Store cookies securely in your application
- Never log or expose cookies in plain text
- Use HTTPS for all API communications

### 2. Error Handling
```javascript
try {
  const accounts = await getShopeeAccounts(email, password);
  // Process accounts
} catch (error) {
  if (error.message === 'Invalid credentials') {
    // Show login form
  } else {
    // Show generic error
  }
}
```

### 3. Data Validation
- Always validate cookie format before sending
- Check if Shopee account name is unique for the user
- Sanitize Telegram username input

---

## Testing

### cURL Examples

**Get Shopee Accounts:**
```bash
curl -X POST http://localhost:8000/api/shopee/get-accounts \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123"
  }'
```

**Add Shopee Account:**
```bash
curl -X POST http://localhost:8000/api/shopee/add-account \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123",
    "name": "Test Account",
    "cookie": "session_id=test123; user_id=456;"
  }'
```

**Get Eligible Cookies:**
```bash
curl -X GET http://localhost:8000/api/shopee/eligible-cookies \
  -H "Content-Type: application/json"
```

---

## Support

For issues or questions:
- **Email:** support@livekenceng.com
- **Telegram:** @livekenceng_support

---

**Last Updated:** October 23, 2025  
**API Version:** 1.0
