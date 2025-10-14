# Livekenceng - Software Automasi Shopee Indonesia

A comprehensive Laravel-based platform combining a modern landing page, member authentication system, and admin panel for managing Shopee automation software licenses.

## 🚀 Features

### Public Website
- **Modern Landing Page**: Beautiful Tailwind CSS design showcasing software features
- **Responsive Design**: Mobile-first approach with seamless user experience
- **SEO Optimized**: Meta tags, Open Graph, and structured data for better visibility

### Member System
- **Desktop App Authentication**: Email, password, and machine ID validation
- **Subscription Management**: Expiry date tracking with automatic status checking
- **License Key Redemption**: Members can extend their subscription with license keys
- **Machine ID Locking**: One subscription per machine for security

### Admin Panel
- **Member Management**: Create, edit, delete members with expiry dates
- **Machine ID Control**: Admins can change member machine IDs
- **License Key Generation**: Generate keys for 1, 3, 7, 14, or 30 days
- **Beautiful Dashboard**: Modern Tailwind UI with real-time statistics
- **Secure Authentication**: Session-based admin login system

## 📋 Requirements

- PHP >= 8.2
- Composer
- SQLite (or MySQL/PostgreSQL)
- Node.js & NPM (optional, for asset compilation)

## 🛠️ Installation

1. **Clone or navigate to the project:**
```bash
cd /Users/hgalih/CursorProject/LiveKencengLaravelWeb/LivekencengNew
```

2. **Install dependencies:**
```bash
composer install
```

3. **Set up environment:**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure database in `.env`:**
```env
DB_CONNECTION=sqlite
# For SQLite, create the database file:
touch database/database.sqlite
```

5. **Run migrations and seed:**
```bash
php artisan migrate
php artisan db:seed
```

6. **Start the server:**
```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

## 🔐 Default Admin Credentials

After seeding, you can access the admin panel at `/admin/login`:

- **Email:** admin@livekenceng.com
- **Password:** admin123

⚠️ **Important:** Change these credentials in production!

## 📚 API Documentation

### Base URLs
- **Web:** `http://localhost:8000`
- **API:** `http://localhost:8000/api`

### Member API Endpoints

#### 1. Member Login
Authenticate member with email, password, and machine ID.

**Endpoint:** `POST /api/members/login`

**Request:**
```json
{
  "email": "member@example.com",
  "password": "password123",
  "machine_id": "MACHINE-ABC-123"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "user": {
    "id": 1,
    "email": "member@example.com",
    "machine_id": "MACHINE-ABC-123",
    "expiry_date": "2025-11-14T12:00:00+00:00"
  }
}
```

**Error Responses:**
- `400`: Missing fields
- `401`: Invalid credentials / Expired subscription / Machine ID mismatch

#### 2. Get Machine ID
Retrieve machine ID associated with an email.

**Endpoint:** `GET /api/members/machine-id/{email}`

**Example:** `GET /api/members/machine-id/member@example.com`

**Success Response (200):**
```json
{
  "success": true,
  "email": "member@example.com",
  "machine_id": "MACHINE-ABC-123"
}
```

#### 3. Update Machine ID
Update the machine ID for a member.

**Endpoint:** `POST /api/members/machine-id`

**Request:**
```json
{
  "email": "member@example.com",
  "machine_id": "NEW-MACHINE-ID"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "Machine ID updated successfully",
  "email": "member@example.com",
  "machine_id": "NEW-MACHINE-ID"
}
```

#### 4. Redeem License Key
Redeem a license key to extend subscription.

**Endpoint:** `POST /api/members/redeem-license`

**Request:**
```json
{
  "email": "member@example.com",
  "license_key": "LK-ABCD-EFGH-IJKL"
}
```

**Success Response (200):**
```json
{
  "success": true,
  "message": "License key redeemed successfully",
  "expiry_date": "2025-11-21T12:00:00+00:00",
  "days_added": 7
}
```

**Error Responses:**
- `400`: License already used
- `404`: Invalid license key or user not found

## 🎨 Admin Panel Features

### Access
Navigate to `/admin/login` and use admin credentials.

### Member Management
- **View All Members**: See complete member list with status
- **Add Member**: Create new member with email, password, and optional expiry date
- **Edit Member**: Update email, password, machine ID, or expiry date
- **Delete Member**: Remove member from system
- **Status Tracking**: Visual indicators for active/expired subscriptions

### License Key Management
- **Generate Keys**: Create license keys for various durations (1, 3, 7, 14, 30 days)
- **Bulk Generation**: Generate multiple keys at once (up to 100)
- **Track Usage**: See which member used which key
- **Copy Keys**: One-click copy to clipboard
- **Delete Unused**: Remove unused license keys

### Dashboard Statistics
- Total Members count
- Active Members count
- Total License Keys count

## 🗄️ Database Structure

### Members Table
```sql
- id
- email (unique)
- password (hashed)
- machine_id (nullable)
- expiry_date (nullable timestamp)
- created_at
- updated_at
```

### License Keys Table
```sql
- id
- code (unique, format: LK-XXXX-XXXX-XXXX)
- duration_days (1, 3, 7, 14, 30)
- is_used (boolean)
- used_by (foreign key to members)
- created_by (foreign key to users/admins)
- used_at (nullable timestamp)
- created_at
- updated_at
```

### Users Table (Admins)
```sql
- id
- name
- email (unique)
- password (hashed)
- created_at
- updated_at
```

## 💻 Desktop App Integration

### Example Login Flow (JavaScript/Electron):

```javascript
const API_URL = 'http://localhost:8000/api';

async function loginMember(email, password, machineId) {
  try {
    const response = await fetch(`${API_URL}/members/login`, {
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
      console.log('Login successful!', data.user);
      // Store user data and proceed
      return data.user;
    } else {
      console.error('Login failed:', data.message);
      throw new Error(data.message);
    }
  } catch (error) {
    console.error('Error:', error);
    throw error;
  }
}

// Get machine ID (platform-specific)
function getMachineId() {
  // Windows: Use WMIC or Registry
  // macOS: Use system_profiler
  // Linux: Use /etc/machine-id
  return 'YOUR-MACHINE-ID';
}

// Usage
const machineId = getMachineId();
loginMember('user@example.com', 'password', machineId)
  .then(user => {
    // Start application
  })
  .catch(err => {
    // Show error to user
  });
```

### Example License Redemption:

```javascript
async function redeemLicense(email, licenseKey) {
  try {
    const response = await fetch(`${API_URL}/members/redeem-license`, {
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
      console.log('License redeemed!', data);
      alert(`Subscription extended by ${data.days_added} days!`);
      return data;
    } else {
      throw new Error(data.message);
    }
  } catch (error) {
    console.error('Error:', error);
    throw error;
  }
}
```

## 🔒 Security Features

1. **Password Hashing**: All passwords are hashed using Laravel's bcrypt
2. **CSRF Protection**: All forms protected with CSRF tokens
3. **Machine ID Validation**: Prevents account sharing
4. **Session-based Admin Auth**: Secure admin authentication
5. **Input Validation**: All inputs validated before processing
6. **Expiry Checking**: Automatic subscription status validation

## 🎯 Pricing Tiers

As displayed on the landing page:

- **Free Trial**: 1 Day - Basic features
- **Weekly Plan**: Rp 40,000 - All features for 7 days
- **Monthly Plan**: Rp 139,000 - All features for 30 days (13% savings)

## 🚀 Deployment

### Production Checklist

1. **Update environment:**
```bash
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
```

2. **Change default admin credentials**

3. **Set up proper database** (MySQL/PostgreSQL recommended for production)

4. **Configure web server** (Nginx/Apache)

5. **Enable HTTPS** with SSL certificate

6. **Set up queue workers** if needed:
```bash
php artisan queue:work --daemon
```

7. **Configure cron for scheduled tasks:**
```bash
* * * * * cd /path-to-project && php artisan schedule:run >> /dev/null 2>&1
```

## 📞 Support

For issues or questions:
- Email: support@livekenceng.com
- Telegram: @livekenceng_support

## 📄 License

This project is proprietary software. All rights reserved.

## 🙏 Credits

- **Laravel Framework**: Backend framework
- **Tailwind CSS**: UI styling
- **Design**: Based on original Livekenceng frontend

---

**Built with ❤️ for Shopee Sellers in Indonesia**
