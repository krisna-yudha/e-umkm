# E-UMKM API Documentation

## Overview

E-UMKM API adalah RESTful API yang menyediakan akses ke data UMKM (Usaha Mikro, Kecil, dan Menengah) untuk platform eksternal. API ini memungkinkan integrasi dengan aplikasi mobile, website, atau sistem lain yang membutuhkan data UMKM.

## Base URL

```
http://127.0.0.1:8000/api
```

## Version

Current API Version: **v1**

## Authentication

API menggunakan Laravel Sanctum untuk autentikasi. Setelah login, Anda akan mendapatkan token yang harus disertakan dalam header request.

### Headers

```
Authorization: Bearer {your-token}
Content-Type: application/json
Accept: application/json
```

## Response Format

Semua response menggunakan format JSON dengan struktur standar:

### Success Response
```json
{
    "status": "success",
    "message": "Operation successful",
    "data": {
        // response data
    }
}
```

### Error Response
```json
{
    "status": "error",
    "message": "Error description",
    "errors": {
        // validation errors (if any)
    }
}
```

## API Endpoints

### 1. Authentication

#### Register User
- **POST** `/api/v1/auth/register`
- **Description**: Registrasi user baru
- **Access**: Public

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "umkm"
}
```

**Response:**
```json
{
    "status": "success",
    "message": "User registered successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "role": "umkm"
        },
        "token": "1|xxxxxxxxxxxxxxxxxxxxx",
        "token_type": "Bearer"
    }
}
```

#### Login
- **POST** `/api/v1/auth/login`
- **Description**: Login user
- **Access**: Public

**Request Body:**
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

#### Get Profile
- **GET** `/api/v1/auth/profile`
- **Description**: Mendapatkan profil user yang sedang login
- **Access**: Protected

#### Update Profile
- **PUT** `/api/v1/auth/profile`
- **Description**: Update profil user
- **Access**: Protected

**Request Body:**
```json
{
    "name": "John Doe Updated",
    "email": "john.updated@example.com",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
}
```

#### Logout
- **POST** `/api/v1/auth/logout`
- **Description**: Logout dari device saat ini
- **Access**: Protected

#### Logout All
- **POST** `/api/v1/auth/logout-all`
- **Description**: Logout dari semua device
- **Access**: Protected

### 2. UMKM Management

#### Get All UMKM
- **GET** `/api/v1/umkm`
- **Description**: Mendapatkan daftar semua UMKM
- **Access**: Public

**Query Parameters:**
- `status` (optional): Filter berdasarkan status (active/inactive)
- `kategori` (optional): Filter berdasarkan kategori
- `search` (optional): Search berdasarkan nama UMKM
- `per_page` (optional): Jumlah data per halaman (default: 15)

**Example Request:**
```
GET /api/v1/umkm?status=active&kategori=Makanan&search=warung&per_page=10
```

**Response:**
```json
{
    "status": "success",
    "message": "UMKM data retrieved successfully",
    "data": [
        {
            "id": 1,
            "nama_umkm": "Warung Makan Sederhana",
            "pemilik": "Budi Santoso",
            "alamat": "Jl. Merdeka No. 123",
            "telepon": "08123456789",
            "email": "budi@warung.com",
            "kategori": "Makanan & Minuman",
            "deskripsi": "Warung makan dengan menu tradisional",
            "latitude": -6.200000,
            "longitude": 106.816666,
            "status": "active",
            "jam_buka": "08:00",
            "jam_tutup": "22:00",
            "foto": "umkm/1234567890_warung.jpg",
            "created_at": "2025-09-30T10:00:00.000000Z",
            "updated_at": "2025-09-30T10:00:00.000000Z",
            "user": {
                "id": 1,
                "name": "John Doe",
                "email": "john@example.com"
            }
        }
    ],
    "pagination": {
        "current_page": 1,
        "last_page": 5,
        "per_page": 15,
        "total": 67
    }
}
```

#### Get UMKM by ID
- **GET** `/api/v1/umkm/{id}`
- **Description**: Mendapatkan detail UMKM berdasarkan ID
- **Access**: Public

**Response:**
```json
{
    "status": "success",
    "message": "UMKM data retrieved successfully",
    "data": {
        "id": 1,
        "nama_umkm": "Warung Makan Sederhana",
        // ... other UMKM fields
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com"
        },
        "menus": [
            {
                "id": 1,
                "nama_menu": "Nasi Gudeg",
                "deskripsi": "Nasi gudeg dengan lauk lengkap",
                "harga": 15000,
                "tersedia": true,
                "foto_menu": "menu/1234567890_gudeg.jpg"
            }
        ]
    }
}
```

#### Create UMKM
- **POST** `/api/v1/umkm`
- **Description**: Membuat UMKM baru
- **Access**: Protected

**Request Body (multipart/form-data):**
```json
{
    "nama_umkm": "Warung Baru",
    "pemilik": "Jane Doe",
    "alamat": "Jl. Sudirman No. 456",
    "telepon": "08987654321",
    "email": "jane@warungbaru.com",
    "kategori": "Fashion & Tekstil",
    "deskripsi": "Toko pakaian modern",
    "latitude": -6.195000,
    "longitude": 106.820000,
    "status": "active",
    "jam_buka": "09:00",
    "jam_tutup": "21:00",
    "foto": "image_file.jpg"
}
```

#### Update UMKM
- **PUT** `/api/v1/umkm/{id}`
- **Description**: Update data UMKM
- **Access**: Protected

#### Delete UMKM
- **DELETE** `/api/v1/umkm/{id}`
- **Description**: Hapus UMKM
- **Access**: Protected

#### Get UMKM Statistics
- **GET** `/api/v1/umkm/statistics/data`
- **Description**: Mendapatkan statistik UMKM
- **Access**: Public

**Response:**
```json
{
    "status": "success",
    "message": "Statistics retrieved successfully",
    "data": {
        "total_umkm": 67,
        "active_umkm": 58,
        "inactive_umkm": 9,
        "category_stats": [
            {
                "kategori": "Makanan & Minuman",
                "count": 25
            },
            {
                "kategori": "Fashion & Tekstil",
                "count": 15
            }
        ],
        "monthly_stats": [
            {
                "year": 2025,
                "month": 9,
                "count": 12
            }
        ]
    }
}
```

#### Find Nearby UMKM
- **POST** `/api/v1/umkm/nearby`
- **Description**: Mencari UMKM terdekat berdasarkan koordinat
- **Access**: Public

**Request Body:**
```json
{
    "latitude": -6.200000,
    "longitude": 106.816666,
    "radius": 5
}
```

### 3. Menu Management

#### Get All Menus
- **GET** `/api/v1/menus`
- **Description**: Mendapatkan semua menu dari semua UMKM
- **Access**: Public

**Query Parameters:**
- `tersedia` (optional): Filter berdasarkan ketersediaan (true/false)
- `search` (optional): Search berdasarkan nama menu
- `min_price` (optional): Harga minimum
- `max_price` (optional): Harga maksimum
- `sort_by_price` (optional): Sort berdasarkan harga (asc/desc)
- `per_page` (optional): Jumlah data per halaman

#### Get Menus by UMKM
- **GET** `/api/v1/menus/umkm/{umkmId}`
- **Description**: Mendapatkan menu berdasarkan UMKM
- **Access**: Public

#### Get Menu Detail
- **GET** `/api/v1/menus/umkm/{umkmId}/{menuId}`
- **Description**: Mendapatkan detail menu spesifik
- **Access**: Public

#### Create Menu
- **POST** `/api/v1/menus/umkm/{umkmId}`
- **Description**: Membuat menu baru untuk UMKM
- **Access**: Protected

**Request Body (multipart/form-data):**
```json
{
    "nama_menu": "Ayam Bakar",
    "deskripsi": "Ayam bakar bumbu kecap",
    "harga": 25000,
    "tersedia": true,
    "foto_menu": "image_file.jpg"
}
```

#### Update Menu
- **PUT** `/api/v1/menus/umkm/{umkmId}/{menuId}`
- **Description**: Update menu
- **Access**: Protected

#### Delete Menu
- **DELETE** `/api/v1/menus/umkm/{umkmId}/{menuId}`
- **Description**: Hapus menu
- **Access**: Protected

### 4. Utility

#### Health Check
- **GET** `/api/health`
- **Description**: Cek status API
- **Access**: Public

**Response:**
```json
{
    "status": "success",
    "message": "E-UMKM API is running",
    "timestamp": "2025-09-30T12:00:00.000000Z",
    "version": "1.0.0"
}
```

## Error Codes

| Code | Message | Description |
|------|---------|-------------|
| 200 | OK | Request berhasil |
| 201 | Created | Resource berhasil dibuat |
| 400 | Bad Request | Request tidak valid |
| 401 | Unauthorized | Token tidak valid atau expired |
| 403 | Forbidden | Tidak memiliki akses |
| 404 | Not Found | Resource tidak ditemukan |
| 422 | Validation Error | Error validasi input |
| 500 | Internal Server Error | Error server |

## Rate Limiting

API memiliki rate limiting:
- **Authenticated requests**: 60 requests per minute
- **Guest requests**: 30 requests per minute

## File Upload

### Supported Formats
- **Images**: JPEG, PNG, JPG, GIF
- **Max Size**: 2MB per file

### Upload Fields
- `foto` (UMKM photo)
- `foto_menu` (Menu photo)

## Examples

### Using cURL

#### Login
```bash
curl -X POST http://127.0.0.1:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"john@example.com","password":"password123"}'
```

#### Get UMKM with Token
```bash
curl -X GET http://127.0.0.1:8000/api/v1/umkm \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxx" \
  -H "Accept: application/json"
```

#### Create UMKM with File Upload
```bash
curl -X POST http://127.0.0.1:8000/api/v1/umkm \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxx" \
  -H "Accept: application/json" \
  -F "nama_umkm=Warung Test" \
  -F "pemilik=Test Owner" \
  -F "alamat=Test Address" \
  -F "telepon=08123456789" \
  -F "kategori=Makanan & Minuman" \
  -F "foto=@/path/to/image.jpg"
```

### Using JavaScript (Fetch)

#### Login
```javascript
const response = await fetch('http://127.0.0.1:8000/api/v1/auth/login', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  },
  body: JSON.stringify({
    email: 'john@example.com',
    password: 'password123'
  })
});

const data = await response.json();
const token = data.data.token;
```

#### Get UMKM
```javascript
const response = await fetch('http://127.0.0.1:8000/api/v1/umkm', {
  headers: {
    'Authorization': `Bearer ${token}`,
    'Accept': 'application/json'
  }
});

const umkmData = await response.json();
```

## Development Notes

1. **CORS**: API sudah dikonfigurasi untuk menerima request dari domain manapun dalam development
2. **File Storage**: File upload disimpan di `storage/app/public/`
3. **Database**: Menggunakan SQLite untuk development, dapat diganti ke MySQL/PostgreSQL untuk production
4. **Validation**: Semua input divalidasi menggunakan Laravel Validation
5. **Security**: API menggunakan HTTPS di production dan Laravel Sanctum untuk authentication

## Support

Untuk pertanyaan atau issue terkait API, silakan hubungi tim development atau buat issue di repository GitHub.

---

**API Version**: 1.0.0  
**Last Updated**: September 30, 2025