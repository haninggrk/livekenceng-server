# Bot Gacor API Documentation

**App Identifier:** `bot-gacor`

All endpoints require `email` and `password` in the request body unless specified otherwise.

---

## Member Authentication

### Login
```
POST /api/members/login
```
**Body:**
```json
{
  "email": "user@example.com",
  "password": "password123",
  "machine_id": "device-hash-123",
  "app_identifier": "bot-gacor"
}
```
**Note:** Always include `app_identifier: "bot-gacor"` for Bot Gacor app. If omitted, defaults to legacy `livekenceng` app.

**Response:**
```json
{
  "success": true,
  "user": {
    "id": 1,
    "email": "user@example.com",
    "telegram_username": "@username",
    "expiry_date": "2025-12-01T00:00:00Z",
    "machine_id": "device-hash-123"
  }
}
```

### Redeem License Key
```
POST /api/members/redeem-license
```
**Body:**
```json
{
  "email": "user@example.com",
  "license_key": "LK-ABCD-EFGH-IJKL"
}
```
**Note:** License key's `app_id` determines which app subscription is created/extended. No `app_identifier` needed in request.

**Response:**
```json
{
  "success": true,
  "message": "License key redeemed successfully",
  "expiry_date": "2025-12-08T00:00:00Z",
  "days_added": 7,
  "is_new_member": false
}
```

### Change Password
```
POST /api/members/change-password
```
**Body:**
```json
{
  "email": "user@example.com",
  "current_password": "oldpass123",
  "new_password": "newpass123",
  "machine_id": "device-hash-123"
}
```

### Get Machine ID
```
GET /api/members/machine-id/user@example.com?app_identifier=bot-gacor
```
**Query Params:**
- `app_identifier` (optional): App identifier. If omitted, defaults to `livekenceng` (legacy)
**Response:**
```json
{
  "success": true,
  "email": "user@example.com",
  "machine_id": "device-hash-123",
  "app_identifier": "bot-gacor"
}
```

### Update Machine ID
```
POST /api/members/machine-id
```
**Body:**
```json
{
  "email": "user@example.com",
  "machine_id": "new-device-hash-123",
  "app_identifier": "bot-gacor"
}
```
**Note:** `app_identifier` is optional - if omitted, defaults to `livekenceng` (legacy app).

**Response:**
```json
{
  "success": true,
  "message": "Machine ID updated successfully",
  "email": "user@example.com",
  "machine_id": "new-device-hash-123",
  "app_identifier": "bot-gacor"
}
```

---

## Shopee Accounts

### Get Shopee Accounts
```
GET /api/members/shopee-accounts
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
  "data": [
    {
      "id": 1,
      "name": "My Shopee Account",
      "is_active": true,
      "created_at": "2025-11-01T00:00:00Z"
    }
  ]
}
```

### Add Shopee Account
```
POST /api/members/shopee-accounts
```
**Body:**
```json
{
  "email": "user@example.com",
  "password": "password123",
  "name": "My Shopee Account",
  "cookie": "SPC_EC=...; SPC_F=...",
  "is_active": true
}
```
**Response:**
```json
{
  "success": true,
  "message": "Shopee account added successfully",
  "data": {
    "id": 1,
    "name": "My Shopee Account",
    "is_active": true
  }
}
```

### Update Shopee Account
```
PUT /api/members/shopee-accounts/1
```
**Body:**
```json
{
  "email": "user@example.com",
  "password": "password123",
  "name": "Updated Name",
  "cookie": "new-cookie-value",
  "is_active": false
}
```
**Note:** All fields (`name`, `cookie`) are required.

**Response:**
```json
{
  "success": true,
  "message": "Shopee account updated successfully",
  "shopee_account": {
    "id": 1,
    "name": "Updated Name",
    "is_active": false
  }
}
```

### Delete Shopee Account
```
DELETE /api/members/shopee-accounts/1
```
**Body:**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

---

## Niches

### List Niches
```
GET /api/members/niches
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
  "niches": [
    {
      "id": 1,
      "name": "Fashion",
      "description": "Fashion products",
      "product_sets": []
    }
  ]
}
```

### Create Niche
```
POST /api/members/niches
```
**Body:**
```json
{
  "email": "user@example.com",
  "password": "password123",
  "name": "Fashion",
  "description": "Fashion products"
}
```
**Response:**
```json
{
  "success": true,
  "message": "Niche created successfully",
  "niche": {
    "id": 1,
    "name": "Fashion",
    "description": "Fashion products"
  }
}
```

### Get Niche
```
GET /api/members/niches/1
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
  "niche": {
    "id": 1,
    "name": "Fashion",
    "description": "Fashion products",
    "product_sets": [
      {
        "id": 2,
        "name": "Summer Collection",
        "items": [...]
      }
    ]
  }
}
```

### Update Niche
```
PUT /api/members/niches/1
```
**Body:**
```json
{
  "email": "user@example.com",
  "password": "password123",
  "name": "Updated Fashion",
  "description": "Updated description"
}
```

### Delete Niche
```
DELETE /api/members/niches/1
```
**Body:**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

---

## Product Sets

### List Product Sets
```
GET /api/members/product-sets
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
  "product_sets": [
    {
      "id": 2,
      "name": "Summer Collection",
      "description": "Summer products",
      "niche_id": 1,
      "niche": {...},
      "items": [...]
    }
  ]
}
```

### Create Product Set
```
POST /api/members/product-sets
```
**Body:**
```json
{
  "email": "user@example.com",
  "password": "password123",
  "name": "Summer Collection",
  "description": "Summer products",
  "niche_id": 1
}
```
**Note:** `niche_id` is optional. If provided, must belong to the member.

**Response:**
```json
{
  "success": true,
  "message": "Product set created successfully",
  "product_set": {
    "id": 2,
    "name": "Summer Collection",
    "niche_id": 1
  }
}
```

### Get Product Set
```
GET /api/members/product-sets/2
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
  "product_set": {
    "id": 2,
    "name": "Summer Collection",
    "niche_id": 1,
    "niche": {...},
    "items": [
      {
        "id": 5,
        "url": "https://shopee.co.id/product/123/456",
        "shop_id": 123,
        "item_id": 456
      }
    ]
  }
}
```

### Update Product Set
```
PUT /api/members/product-sets/2
```
**Body:**
```json
{
  "email": "user@example.com",
  "password": "password123",
  "name": "Updated Collection",
  "description": "Updated description",
  "niche_id": 1
}
```

### Delete Product Set
```
DELETE /api/members/product-sets/2
```
**Body:**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

---

## Product Set Items

### Add Items
```
POST /api/members/product-sets/2/items
```
**Body:**
```json
{
  "email": "user@example.com",
  "password": "password123",
  "items": [
    {
      "url": "https://shopee.co.id/product/123/456"
    },
    {
      "url": "https://shopee.co.id/product/789/012",
      "shop_id": 789,
      "item_id": 12
    }
  ]
}
```
- Max 100 items per product set
- URLs auto-parsed if `shop_id`/`item_id` not provided
- Duplicate URLs skipped (same URL in same product set)

**Response:**
```json
{
  "success": true,
  "message": "Items processed",
  "added": 2,
  "skipped": 0,
  "added_items": [
    {
      "id": 5,
      "url": "https://shopee.co.id/product/123/456",
      "shop_id": 123,
      "item_id": 456
    }
  ],
  "skipped_items": []
}
```

### Remove Item
```
DELETE /api/members/product-sets/2/items/5
```
**Body:**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```
**Note:** `5` is the item ID (numeric).

### Clear All Items
```
DELETE /api/members/product-sets/2/items
```
**Body:**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

---

## Shopee Live Stream

### Get Session IDs
```
POST /api/shopee-live/session-ids
```
**Body:**
```json
{
  "email": "user@example.com",
  "password": "password123",
  "shopee_account_id": 1
}
```
**Response:**
```json
{
  "success": true,
  "session_ids": ["session-123", "session-456"],
  "count": 2
}
```

### Get Active Session
```
POST /api/shopee-live/active-session
```
**Body:**
```json
{
  "email": "user@example.com",
  "password": "password123",
  "shopee_account_id": 1
}
```
**Response (with active session):**
```json
{
  "success": true,
  "session_id": "171945454"
}
```
**Response (no active session):**
```json
{
  "success": true,
  "session_id": null
}
```
**Note:** Returns only the first session ID where `status = 1` (active/live). Returns `null` if no active session found.

### Replace Products
```
POST /api/shopee-live/replace-products
```
**Body:**
```json
{
  "email": "user@example.com",
  "password": "password123",
  "shopee_account_id": 1,
  "session_id": "session-123",
  "product_set_id": 2
}
```
**Response:**
```json
{
  "success": true,
  "message": "Products replaced successfully",
  "items_count": 5
}
```

### Clear Products
```
POST /api/shopee-live/clear-products
```
**Body:**
```json
{
  "email": "user@example.com",
  "password": "password123",
  "shopee_account_id": 1,
  "session_id": "session-123"
}
```
**Response:**
```json
{
  "success": true,
  "message": "Products cleared successfully"
}
```

---

## Important Notes

### App Identifier
- **Always use** `app_identifier: "bot-gacor"` in login requests for Bot Gacor app
- If `app_identifier` is omitted, it defaults to legacy `livekenceng` app
- License redemption uses the license key's `app_id` automatically (no `app_identifier` needed)

### IDs in URLs
- Use **numeric IDs** in URLs: `/niches/1`, `/product-sets/2`, `/shopee-accounts/1`, `/items/5`
- IDs are returned in create/list responses
- Route model binding automatically validates ownership

### Authentication
- All endpoints require member `email` and `password`
- Shopee account endpoints also validate account belongs to member
- All resource endpoints validate ownership before operations

---

## Constraints

- **Product Set**: Max 100 items per set
- **Product Set Items**: No duplicate URLs per set (enforced by unique constraint)
- **URL Format**: Must contain `/product/{shop_id}/{item_id}` pattern
- **All endpoints**: Require valid member email/password
- **Shopee endpoints**: Require active Shopee account (`is_active: true`)
- **Ownership**: All resources (niches, product sets, shopee accounts) are member-specific

---

## Error Responses

```json
{
  "success": false,
  "message": "Error description"
}
```

**Common HTTP codes:**
- `400` - Bad request / Validation error / Account inactive
- `401` - Invalid credentials / Machine ID mismatch
- `403` - Unauthorized access (resource doesn't belong to member)
- `404` - Resource not found
- `422` - Validation errors (array of field errors)
