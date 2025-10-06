# E-UMKM API Documentation v1.0
**Production Ready API Documentation**

## Base Information
- **API Version**: 1.0
- **Base URL**: `http://127.0.0.1:8000/api/v1`
- **Authentication**: Bearer Token (Sanctum)
- **Content-Type**: `application/json`
- **Accept**: `application/json`

---

## 🔐 Authentication System

### Register
```http
POST /api/v1/auth/register
```

**Request Body:**
```json
{
  "name": "User Name",
  "email": "user@example.com", 
  "password": "password123",
  "password_confirmation": "password123",
  "role": "umkm" // or "admin"
}
```

**Response (Success - 201):**
```json
{
  "success": true,
  "message": "Registration successful",
  "data": {
    "user": {
      "id": 1,
      "name": "User Name",
      "email": "user@example.com",
      "role": "umkm",
      "email_verified_at": null,
      "created_at": "2025-10-06T12:00:00.000000Z",
      "updated_at": "2025-10-06T12:00:00.000000Z"
    },
    "token": "1|abc123def456..."
  }
}
```

### Login
```http
POST /api/v1/auth/login
```

**Request Body:**
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

**Response (Success - 200):**
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "User Name",
      "email": "user@example.com",
      "role": "umkm"
    },
    "token": "2|xyz789abc012..."
  }
}
```

### Logout
```http
POST /api/v1/auth/logout
Authorization: Bearer {token}
```

**Response (Success - 200):**
```json
{
  "success": true,
  "message": "Logout successful"
}
```

---

## 👤 User Profile Management

### Get Profile
```http
GET /api/v1/profile
Authorization: Bearer {token}
```

**Response (Success - 200):**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "User Name",
    "email": "user@example.com",
    "role": "umkm",
    "email_verified_at": null,
    "created_at": "2025-10-06T12:00:00.000000Z"
  }
}
```

### Update Profile
```http
PUT /api/v1/profile/update
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "name": "Updated Name",
  "email": "newemail@example.com"
}
```

### Change Password
```http
PUT /api/v1/profile/password
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "current_password": "oldpassword123",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

---

## 🏢 UMKM Management

### Get All UMKM (Public)
```http
GET /api/v1/umkm
```

**Query Parameters:**
- `search` (optional): Search by name, kategori, alamat
- `kategori` (optional): Filter by category
- `status` (optional): Filter by status (active/inactive)
- `page` (optional): Page number (default: 1)
- `per_page` (optional): Items per page (default: 15)

**Response (Success - 200):**
```json
{
  "success": true,
  "data": {
    "current_page": 1,
    "data": [
      {
        "id": 1,
        "user_id": 1,
        "nama_usaha": "Warung Makan Berkah",
        "deskripsi": "Warung makan dengan masakan tradisional",
        "kategori": "Kuliner",
        "alamat": "Jl. Raya No. 123, Semarang",
        "telepon": "081234567890",
        "email": "berkah@gmail.com",
        "website": "https://warungberkah.com",
        "jam_operasional": "08:00-21:00",
        "harga_rata_rata": "25000",
        "latitude": -6.9664,
        "longitude": 110.4204,
        "status": "active",
        "created_at": "2025-10-06T12:00:00.000000Z",
        "user": {
          "id": 1,
          "name": "Pemilik Warung"
        }
      }
    ],
    "total": 50,
    "per_page": 15,
    "current_page": 1,
    "last_page": 4
  }
}
```

### Get UMKM by ID
```http
GET /api/v1/umkm/{id}
```

### Get User's UMKM
```http
GET /api/v1/umkm/my
Authorization: Bearer {token}
```

### Create UMKM
```http
POST /api/v1/umkm
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "nama_usaha": "Toko Kerajinan Indah",
  "deskripsi": "Menjual berbagai kerajinan tangan tradisional",
  "kategori": "Kerajinan",
  "alamat": "Jl. Kerajinan No. 45, Semarang",
  "telepon": "085123456789",
  "email": "kerajinan@gmail.com",
  "website": "https://kerajinanindah.com",
  "jam_operasional": "09:00-17:00",
  "harga_rata_rata": "50000",
  "latitude": -6.9775,
  "longitude": 110.4315,
  "instagram": "@kerajinanindah",
  "facebook": "Kerajinan Indah",
  "whatsapp": "6285123456789"
}
```

### Update UMKM
```http
PUT /api/v1/umkm/{id}
Authorization: Bearer {token}
```

### Delete UMKM
```http
DELETE /api/v1/umkm/{id}
Authorization: Bearer {token}
```

---

## 🍽️ Menu Management

### Get UMKM Menus
```http
GET /api/v1/umkm/{umkm_id}/menus
```

### Create Menu Item
```http
POST /api/v1/umkm/{umkm_id}/menus
Authorization: Bearer {token}
```

**Request Body:**
```json
{
  "nama_menu": "Nasi Gudeg Komplit",
  "deskripsi": "Nasi gudeg dengan ayam, telur, dan sambal krecek",
  "harga": 25000,
  "kategori": "Makanan Utama",
  "status": "active"
}
```

### Update Menu Item
```http
PUT /api/v1/menus/{menu_id}
Authorization: Bearer {token}
```

### Delete Menu Item
```http
DELETE /api/v1/menus/{menu_id}
Authorization: Bearer {token}
```

---

## 🗺️ Location & Mapping

### Search UMKM by Location
```http
GET /api/v1/umkm/nearby
```

**Query Parameters:**
- `latitude` (required): Latitude coordinate
- `longitude` (required): Longitude coordinate  
- `radius` (optional): Search radius in km (default: 5)

**Example:**
```
GET /api/v1/umkm/nearby?latitude=-6.9664&longitude=110.4204&radius=3
```

### Get All UMKM Locations (for mapping)
```http
GET /api/v1/umkm/locations
```

**Response:**
```json
{
  "success": true,
  "data": {
    "total": 50,
    "locations": [
      {
        "id": 1,
        "nama_usaha": "Warung Makan Berkah",
        "kategori": "Kuliner", 
        "latitude": -6.9664,
        "longitude": 110.4204,
        "alamat": "Jl. Raya No. 123, Semarang",
        "status": "active"
      }
    ]
  }
}
```

---

## 👥 Admin Endpoints

### Admin Dashboard Stats
```http
GET /api/v1/admin/stats
Authorization: Bearer {admin_token}
```

**Response:**
```json
{
  "success": true,
  "data": {
    "total_umkm": 50,
    "active_umkm": 45,
    "inactive_umkm": 5,
    "total_users": 30,
    "monthly_registrations": [
      {"month": "2025-09", "count": 8},
      {"month": "2025-10", "count": 12}
    ],
    "category_distribution": [
      {"kategori": "Kuliner", "count": 25},
      {"kategori": "Fashion", "count": 15}
    ]
  }
}
```

### Get All Users (Admin)
```http
GET /api/v1/admin/users
Authorization: Bearer {admin_token}
```

### Toggle UMKM Status (Admin)
```http
POST /api/v1/admin/umkm/{id}/toggle
Authorization: Bearer {admin_token}
```

---

## 🔄 Password Reset System

### Request Password Reset
```http
POST /api/v1/auth/password-reset/request
```

**Request Body:**
```json
{
  "email": "user@example.com",
  "message": "Lupa password, mohon bantuan reset"
}
```

### Verify Reset Code
```http
POST /api/v1/auth/password-reset/verify
```

**Request Body:**
```json
{
  "email": "user@example.com",
  "verification_code": "ABC123"
}
```

### Reset Password
```http
POST /api/v1/auth/password-reset/reset
```

**Request Body:**
```json
{
  "email": "user@example.com",
  "verification_code": "ABC123",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

---

## ❌ Error Responses

### Validation Error (422)
```json
{
  "success": false,
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password field is required."]
  }
}
```

### Unauthorized (401)
```json
{
  "success": false,
  "message": "Unauthenticated."
}
```

### Forbidden (403)
```json
{
  "success": false,
  "message": "This action is unauthorized."
}
```

### Not Found (404)
```json
{
  "success": false,
  "message": "Resource not found."
}
```

### Server Error (500)
```json
{
  "success": false,
  "message": "Internal server error."
}
```

---

## 📝 Notes

### Production Considerations
1. **Security**: All endpoints use CSRF protection and rate limiting
2. **Authentication**: Bearer tokens expire after 24 hours
3. **File Uploads**: Maximum file size 2MB for images
4. **Rate Limiting**: 60 requests per minute per IP
5. **Pagination**: Default 15 items per page, maximum 100

### Status Codes
- `200` - Success
- `201` - Created
- `204` - No Content (successful deletion)
- `400` - Bad Request
- `401` - Unauthorized
- `403` - Forbidden
- `404` - Not Found
- `422` - Validation Error
- `500` - Internal Server Error

### Available Categories
- Kuliner
- Fashion
- Kerajinan
- Teknologi
- Jasa
- Retail
- Lainnya

---

**Last Updated**: October 6, 2025  
**API Version**: 1.0  
**Environment**: Production Ready