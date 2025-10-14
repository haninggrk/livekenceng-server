# Livekenceng Desktop App API Documentation

## Overview

This API allows desktop applications to authenticate members, validate machine IDs, check subscription status, and redeem license keys. All endpoints use JSON for request and response bodies.

## Base URL

```
Production: https://livekenceng.com/api
Development: http://localhost:8000/api
```

## Authentication Flow

The API uses machine ID-based authentication to ensure one subscription per device.

### Flow Diagram

```
1. First Time User:
   User enters email/password → Check if machine_id exists → 
   Login with machine_id → Machine ID gets stored → Access granted

2. Returning User:
   User enters email/password → Login with stored machine_id → 
   Validate expiry → Access granted/denied

3. Machine Changed:
   User enters email/password → Machine ID mismatch → 
   Contact support (or admin resets machine_id)
```

---

## API Endpoints

### 1. Member Login

Authenticate a member with email, password, and machine ID.

**Endpoint:** `POST /api/members/login`

**Headers:**
```http
Content-Type: application/json
```

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "userpassword",
  "machine_id": "UNIQUE-MACHINE-ID-123"
}
```

**Success Response (200 OK):**
```json
{
  "success": true,
  "user": {
    "id": 1,
    "email": "user@example.com",
    "machine_id": "UNIQUE-MACHINE-ID-123",
    "expiry_date": "2025-11-14T12:00:00+00:00"
  }
}
```

**Error Responses:**

*Missing Fields (400 Bad Request):*
```json
{
  "success": false,
  "message": "Email, password, and machine_id are required"
}
```

*Invalid Credentials (401 Unauthorized):*
```json
{
  "success": false,
  "message": "Invalid credentials"
}
```

*Subscription Expired (401 Unauthorized):*
```json
{
  "success": false,
  "message": "Subscription expired. Please contact support to renew."
}
```

*Machine ID Mismatch (401 Unauthorized):*
```json
{
  "success": false,
  "message": "Machine ID mismatch"
}
```

**Desktop App Example (JavaScript/Electron):**
```javascript
const { machineIdSync } = require('node-machine-id');

async function login(email, password) {
  const machineId = machineIdSync({ original: true });
  
  const response = await fetch('https://livekenceng.com/api/members/login', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      email: email,
      password: password,
      machine_id: machineId
    })
  });
  
  const data = await response.json();
  
  if (data.success) {
    // Store user data locally
    localStorage.setItem('user', JSON.stringify(data.user));
    return data.user;
  } else {
    throw new Error(data.message);
  }
}
```

---

### 2. Get Machine ID by Email

Retrieve the machine ID associated with a member's email.

**Endpoint:** `GET /api/members/machine-id/{email}`

**Headers:**
```http
Content-Type: application/json
```

**URL Parameters:**
- `email` (required): Member's email address

**Example Request:**
```
GET /api/members/machine-id/user@example.com
```

**Success Response (200 OK):**
```json
{
  "success": true,
  "email": "user@example.com",
  "machine_id": "UNIQUE-MACHINE-ID-123"
}
```

**Error Response (404 Not Found):**
```json
{
  "success": false,
  "message": "User not found"
}
```

**Desktop App Example:**
```javascript
async function checkMachineId(email) {
  const response = await fetch(`https://livekenceng.com/api/members/machine-id/${encodeURIComponent(email)}`);
  const data = await response.json();
  
  if (data.success && data.machine_id) {
    return data.machine_id;
  }
  return null;
}
```

---

### 3. Update Machine ID

Update or reset the machine ID for a member. **Note:** This should typically be done by admins through the dashboard, not by the app.

**Endpoint:** `POST /api/members/machine-id`

**Headers:**
```http
Content-Type: application/json
```

**Request Body:**
```json
{
  "email": "user@example.com",
  "machine_id": "NEW-MACHINE-ID-456"
}
```

**Success Response (200 OK):**
```json
{
  "success": true,
  "message": "Machine ID updated successfully",
  "email": "user@example.com",
  "machine_id": "NEW-MACHINE-ID-456"
}
```

**Error Responses:**

*Missing Fields (400 Bad Request):*
```json
{
  "success": false,
  "message": "Email and machine_id are required"
}
```

*User Not Found (404 Not Found):*
```json
{
  "success": false,
  "message": "User not found"
}
```

---

### 4. Redeem License Key

Redeem a license key to extend or activate subscription. If the email does not exist, a new member will be automatically created and the license duration applied. The generated temporary password will be returned in the response for first-time users.

**Endpoint:** `POST /api/members/redeem-license`

**Headers:**
```http
Content-Type: application/json
```

**Request Body:**
```json
{
  "email": "user@example.com",
  "license_key": "LK-ABCD-EFGH-IJKL"
}
```

**Success Response (200 OK):**
```json
{
  "success": true,
  "message": "License key redeemed successfully",
  "expiry_date": "2025-11-21T12:00:00+00:00",
  "days_added": 7,
  "is_new_member": false
}
```

**Success Response (New Member Created - 200 OK):**
```json
{
  "success": true,
  "message": "New account created and license activated successfully",
  "expiry_date": "2025-11-21T12:00:00+00:00",
  "days_added": 7,
  "is_new_member": true,
  "email": "user@example.com",
  "password": "TEMP-PASS-123"
}
```

**Error Responses:**

*Missing Fields (400 Bad Request):*
```json
{
  "success": false,
  "message": "Email and license_key are required"
}
```

Note: If the user does not exist, the API will create one automatically and return a temporary password.

*Invalid License Key (404 Not Found):*
```json
{
  "success": false,
  "message": "Invalid license key"
}
```

*Already Used (400 Bad Request):*
```json
{
  "success": false,
  "message": "License key already used"
}
```

**Desktop App Example:**
```javascript
async function redeemLicense(email, licenseKey) {
  const response = await fetch('https://livekenceng.com/api/members/redeem-license', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
    },
    body: JSON.stringify({
      email: email,
      license_key: licenseKey
    })
  });
  
  const data = await response.json();
  
  if (data.success) {
    // Update local user data
    const user = JSON.parse(localStorage.getItem('user'));
    user.expiry_date = data.expiry_date;
    localStorage.setItem('user', JSON.stringify(user));
    
    return {
      expiryDate: data.expiry_date,
      daysAdded: data.days_added
    };
  } else {
    throw new Error(data.message);
  }
}
```

---

## Implementation Guide

### Getting Machine ID

#### Windows (Node.js/Electron)
```javascript
const { machineIdSync } = require('node-machine-id');

function getMachineId() {
  return machineIdSync({ original: true });
}
```

#### Alternative Method (Cross-platform)
```javascript
const os = require('os');
const crypto = require('crypto');

function getMachineId() {
  const networkInterfaces = os.networkInterfaces();
  const cpus = os.cpus();
  
  // Get MAC address of first network interface
  const mac = Object.values(networkInterfaces)
    .flat()
    .find(i => !i.internal)?.mac || '';
  
  // Create unique ID from MAC + hostname
  const uniqueString = mac + os.hostname();
  
  return crypto
    .createHash('sha256')
    .update(uniqueString)
    .digest('hex')
    .substring(0, 32);
}
```

### Complete Login Flow

```javascript
class LivekencengAuth {
  constructor(apiBaseUrl = 'https://livekenceng.com/api') {
    this.apiBaseUrl = apiBaseUrl;
    this.machineId = this.getMachineId();
  }

  getMachineId() {
    const { machineIdSync } = require('node-machine-id');
    return machineIdSync({ original: true });
  }

  async login(email, password) {
    try {
      const response = await fetch(`${this.apiBaseUrl}/members/login`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          email,
          password,
          machine_id: this.machineId
        })
      });

      const data = await response.json();

      if (!data.success) {
        throw new Error(data.message);
      }

      // Store user data
      localStorage.setItem('user', JSON.stringify(data.user));
      
      return {
        user: data.user,
        isActive: this.checkSubscriptionActive(data.user.expiry_date)
      };
    } catch (error) {
      throw error;
    }
  }

  checkSubscriptionActive(expiryDate) {
    if (!expiryDate) return false;
    return new Date(expiryDate) > new Date();
  }

  async redeemLicense(email, licenseKey) {
    try {
      const response = await fetch(`${this.apiBaseUrl}/members/redeem-license`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          email,
          license_key: licenseKey
        })
      });

      const data = await response.json();

      if (!data.success) {
        throw new Error(data.message);
      }

      // Update stored user
      const user = JSON.parse(localStorage.getItem('user'));
      user.expiry_date = data.expiry_date;
      localStorage.setItem('user', JSON.stringify(user));

      return data;
    } catch (error) {
      throw error;
    }
  }

  async checkMachineId(email) {
    const response = await fetch(`${this.apiBaseUrl}/members/machine-id/${encodeURIComponent(email)}`);
    const data = await response.json();
    return data.success ? data.machine_id : null;
  }

  getStoredUser() {
    const user = localStorage.getItem('user');
    return user ? JSON.parse(user) : null;
  }

  isLoggedIn() {
    const user = this.getStoredUser();
    if (!user) return false;
    
    // Check if subscription is active
    return this.checkSubscriptionActive(user.expiry_date);
  }

  logout() {
    localStorage.removeItem('user');
  }
}

// Usage
const auth = new LivekencengAuth();

// Login
auth.login('user@example.com', 'password123')
  .then(result => {
    if (result.isActive) {
      console.log('Login successful, subscription active');
      // Open main app window
    } else {
      console.log('Login successful but subscription expired');
      // Show redeem license dialog
    }
  })
  .catch(error => {
    console.error('Login failed:', error.message);
  });

// Redeem license
auth.redeemLicense('user@example.com', 'LK-ABCD-EFGH-IJKL')
  .then(result => {
    console.log(`License redeemed! ${result.days_added} days added`);
    console.log(`New expiry: ${result.expiry_date}`);
  })
  .catch(error => {
    console.error('Redeem failed:', error.message);
  });
```

### Subscription Status Checking

```javascript
// Check subscription on app startup
function checkSubscriptionOnStartup() {
  const user = JSON.parse(localStorage.getItem('user'));
  
  if (!user) {
    // Show login screen
    return { status: 'logged_out' };
  }

  if (!user.expiry_date) {
    // No subscription
    return { status: 'no_subscription', user };
  }

  const expiryDate = new Date(user.expiry_date);
  const now = new Date();

  if (expiryDate > now) {
    const daysRemaining = Math.ceil((expiryDate - now) / (1000 * 60 * 60 * 24));
    
    if (daysRemaining <= 3) {
      // Show warning: expiring soon
      return { status: 'expiring_soon', daysRemaining, user };
    }
    
    // Active subscription
    return { status: 'active', daysRemaining, user };
  } else {
    // Expired
    return { status: 'expired', user };
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
| 401 | Unauthorized - Invalid credentials, expired subscription, or machine ID mismatch |
| 404 | Not Found - Resource (user/license) not found |
| 500 | Internal Server Error - Server error |

### Common Error Scenarios

#### 1. Subscription Expired
```javascript
// Response: 401 Unauthorized
{
  "success": false,
  "message": "Subscription expired. Please contact support to renew."
}

// Action: Show license redemption dialog or contact support
```

#### 2. Machine ID Mismatch
```javascript
// Response: 401 Unauthorized
{
  "success": false,
  "message": "Machine ID mismatch"
}

// Action: Show message to contact support for machine reset
```

#### 3. Invalid License Key
```javascript
// Response: 404 Not Found
{
  "success": false,
  "message": "Invalid license key"
}

// Action: Show error, allow user to re-enter key
```

---

## Best Practices

### 1. Secure Storage
- Never store passwords locally
- Store user data encrypted if possible
- Clear sensitive data on logout

### 2. Offline Handling
- Cache user and expiry data locally
- Allow grace period for network issues
- Sync with server when online

### 3. User Experience
```javascript
// Example: Grace period for network issues
function canAccessApp() {
  const user = JSON.parse(localStorage.getItem('user'));
  const lastSync = localStorage.getItem('lastSync');
  
  if (!user) return false;
  
  const expiryDate = new Date(user.expiry_date);
  const now = new Date();
  
  // If expired, check if we need to re-verify
  if (expiryDate < now) {
    const lastSyncDate = new Date(lastSync);
    const hoursSinceSync = (now - lastSyncDate) / (1000 * 60 * 60);
    
    // Allow 24-hour grace period if can't reach server
    if (hoursSinceSync < 24) {
      return true; // Trust local data
    }
    return false;
  }
  
  return true;
}
```

### 4. Periodic Validation
```javascript
// Re-validate subscription every 6 hours
setInterval(async () => {
  const user = JSON.parse(localStorage.getItem('user'));
  if (user) {
    try {
      const result = await auth.login(user.email, ''); // Use saved session
      localStorage.setItem('lastSync', new Date().toISOString());
    } catch (error) {
      // Handle error
    }
  }
}, 6 * 60 * 60 * 1000);
```

---

## UI Flow Examples

### Login Screen
```
┌─────────────────────────────────┐
│   Livekenceng Desktop App       │
├─────────────────────────────────┤
│                                 │
│  Email:    [________________]   │
│                                 │
│  Password: [________________]   │
│                                 │
│         [  Login  ]             │
│                                 │
│  Don't have an account?         │
│  Contact: @livekenceng_support  │
└─────────────────────────────────┘
```

### License Redemption Dialog
```
┌─────────────────────────────────┐
│   Redeem License Key            │
├─────────────────────────────────┤
│                                 │
│  Your subscription has expired. │
│  Enter a license key to renew:  │
│                                 │
│  License Key:                   │
│  [____-____-____-____]          │
│                                 │
│  [  Redeem  ]  [  Cancel  ]     │
│                                 │
│  Get license keys from your     │
│  reseller or admin.             │
└─────────────────────────────────┘
```

### Subscription Status
```
┌─────────────────────────────────┐
│   Subscription Active           │
├─────────────────────────────────┤
│                                 │
│  Email: user@example.com        │
│                                 │
│  Expires: 2025-11-14            │
│  Days Remaining: 30 days        │
│                                 │
│  [  Extend License  ]           │
└─────────────────────────────────┘
```

---

## Testing

### Test Credentials (Development)
Create test users through admin panel or use seeded data.

### cURL Examples

**Login:**
```bash
curl -X POST http://localhost:8000/api/members/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password123",
    "machine_id": "TEST-MACHINE-001"
  }'
```

**Check Machine ID:**
```bash
curl -X GET http://localhost:8000/api/members/machine-id/test@example.com \
  -H "Content-Type: application/json"
```

**Redeem License:**
```bash
curl -X POST http://localhost:8000/api/members/redeem-license \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "license_key": "LK-ABCD-EFGH-IJKL"
  }'
```

---

## Support

For issues or questions:
- **Email:** support@livekenceng.com
- **Telegram:** @livekenceng_support

---

**Last Updated:** October 14, 2025  
**API Version:** 1.0

