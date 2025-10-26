# Subscription Management API Documentation

## Base URL
```
Production: https://livekenceng.com/api
Development: http://localhost:8000/api
```

## Overview

This API manages member subscriptions, license redemption, and authentication. All endpoints use JSON for request and response bodies.

---

## 1. Member Login (Subscription Check)

**Endpoint:** `POST /api/members/login`

**Request:**
```json
{
  "email": "member@example.com",
  "password": "yourpassword",
  "machine_id": "unique-machine-identifier-123"
}
```

**Response (200 OK):**
```json
{
  "success": true,
  "user": {
    "id": 1,
    "email": "member@example.com",
    "telegram_username": "@username",
    "expiry_date": "2025-11-14T12:00:00+00:00",
    "machine_id": "unique-machine-identifier-123",
    "created_at": "2025-10-14T12:00:00.000000Z",
    "updated_at": "2025-10-23T15:40:00.000000Z"
  }
}
```

**Error: Subscription Expired (401):**
```json
{
  "success": false,
  "message": "Subscription expired. Please contact support to renew."
}
```

---

## 2. Redeem License Key

**Endpoint:** `POST /api/members/redeem-license`

**Request:**
```json
{
  "email": "member@example.com",
  "license_key": "ABC123-XYZ789-DEF456"
}
```

**Response - Existing Member (200):**
```json
{
  "success": true,
  "message": "License key redeemed successfully",
  "expiry_date": "2025-12-14T12:00:00+00:00",
  "days_added": 30,
  "is_new_member": false
}
```

**Response - New Member (200):**
```json
{
  "success": true,
  "message": "New account created and license activated successfully",
  "expiry_date": "2025-11-14T12:00:00+00:00",
  "days_added": 30,
  "is_new_member": true,
  "password": "random12char",
  "email": "member@example.com"
}
```

---

## 3. Check Subscription Status

**Endpoint:** `POST /api/members/profile`

**Request:**
```json
{
  "email": "member@example.com",
  "password": "yourpassword"
}
```

**Response (200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "email": "member@example.com",
    "telegram_username": "@username",
    "expiry_date": "2025-11-14T12:00:00+00:00",
    "machine_id": "unique-machine-identifier-123",
    "created_at": "2025-10-14T12:00:00.000000Z",
    "updated_at": "2025-10-23T15:40:00.000000Z"
  }
}
```

**Subscription Status Logic:**
- **Active**: `expiry_date` is in the future
- **Expired**: `expiry_date` is null or in the past

---

## 4. Get Machine ID

**Endpoint:** `GET /api/members/machine-id/{email}`

**Response (200):**
```json
{
  "success": true,
  "email": "member@example.com",
  "machine_id": "unique-machine-identifier-123"
}
```

---

## 5. Update Machine ID

**Endpoint:** `POST /api/members/machine-id`

**Request:**
```json
{
  "email": "member@example.com",
  "machine_id": "new-machine-identifier-456"
}
```

---

## Common Use Cases

### First Time Member Setup
1. GET /api/members/machine-id/{email}
2. POST /api/members/redeem-license
3. POST /api/members/login

### Renewal Flow
1. POST /api/members/redeem-license (with new license key)
2. Days are added to current expiry

---

## Status Codes

| Code | Description |
|------|-------------|
| 200 | OK - Request successful |
| 400 | Bad Request |
| 401 | Unauthorized |
| 404 | Not Found |
| 422 | Validation Error |

---

**Last Updated:** October 25, 2025

