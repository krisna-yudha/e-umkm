# E-UMKM API Endpoints - Postman Testing Guide

## Base URL
```
http://127.0.0.1:8000/api
```

## Authentication
Untuk endpoint yang protected, gunakan:
- **Authorization Type**: Bearer Token
- **Token**: Didapat dari login response

---

## 🔐 AUTHENTICATION ENDPOINTS

### 1. Register User
- **Method**: `POST`
- **URL**: `{{base_url}}/v1/auth/register`
- **Headers**: 
  ```
  Content-Type: application/json
  Accept: application/json
  ```
- **Body (JSON)**:
  ```json
  {
    "name": "Test User",
    "email": "test@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "umkm"
  }
  ```

### 2. Login User
- **Method**: `POST`
- **URL**: `{{base_url}}/v1/auth/login`
- **Headers**: 
  ```
  Content-Type: application/json
  Accept: application/json
  ```
- **Body (JSON)**:
  ```json
  {
    "email": "test@example.com",
    "password": "password123"
  }
  ```

### 3. Get User Profile
- **Method**: `GET`
- **URL**: `{{base_url}}/v1/auth/profile`
- **Headers**: 
  ```
  Authorization: Bearer {{token}}
  Accept: application/json
  ```

### 4. Update Profile
- **Method**: `PUT`
- **URL**: `{{base_url}}/v1/auth/profile`
- **Headers**: 
  ```
  Authorization: Bearer {{token}}
  Content-Type: application/json
  Accept: application/json
  ```
- **Body (JSON)**:
  ```json
  {
    "name": "Updated Name",
    "email": "updated@example.com"
  }
  ```

### 5. Logout
- **Method**: `POST`
- **URL**: `{{base_url}}/v1/auth/logout`
- **Headers**: 
  ```
  Authorization: Bearer {{token}}
  Accept: application/json
  ```

### 6. Logout All Devices
- **Method**: `POST`
- **URL**: `{{base_url}}/v1/auth/logout-all`
- **Headers**: 
  ```
  Authorization: Bearer {{token}}
  Accept: application/json
  ```

---

## 🏢 UMKM ENDPOINTS

### 7. Get All UMKM (Public)
- **Method**: `GET`
- **URL**: `{{base_url}}/v1/umkm`
- **Headers**: 
  ```
  Accept: application/json
  ```
- **Query Parameters** (Optional):
  ```
  ?status=active&kategori=Makanan&search=warung&per_page=10
  ```

### 8. Get UMKM by ID (Public)
- **Method**: `GET`
- **URL**: `{{base_url}}/v1/umkm/1`
- **Headers**: 
  ```
  Accept: application/json
  ```

### 9. Create UMKM (Protected)
- **Method**: `POST`
- **URL**: `{{base_url}}/v1/umkm`
- **Headers**: 
  ```
  Authorization: Bearer {{token}}
  Accept: application/json
  ```
- **Body (form-data)**:
  ```
  nama_umkm: Warung Test API
  pemilik: John Doe
  alamat: Jl. Test No. 123
  telepon: 08123456789
  email: test@warung.com
  kategori: Makanan & Minuman
  deskripsi: Warung test untuk API
  latitude: -6.200000
  longitude: 106.816666
  status: active
  jam_buka: 08:00
  jam_tutup: 22:00
  foto: [file upload - optional]
  ```

### 10. Update UMKM (Protected)
- **Method**: `PUT`
- **URL**: `{{base_url}}/v1/umkm/1`
- **Headers**: 
  ```
  Authorization: Bearer {{token}}
  Content-Type: application/json
  Accept: application/json
  ```
- **Body (JSON)**:
  ```json
  {
    "nama_umkm": "Warung Updated",
    "deskripsi": "Deskripsi yang sudah diupdate",
    "status": "active"
  }
  ```

### 11. Delete UMKM (Protected)
- **Method**: `DELETE`
- **URL**: `{{base_url}}/v1/umkm/1`
- **Headers**: 
  ```
  Authorization: Bearer {{token}}
  Accept: application/json
  ```

### 12. Get UMKM Statistics (Public)
- **Method**: `GET`
- **URL**: `{{base_url}}/v1/umkm/statistics/data`
- **Headers**: 
  ```
  Accept: application/json
  ```

### 13. Find Nearby UMKM (Public)
- **Method**: `POST`
- **URL**: `{{base_url}}/v1/umkm/nearby`
- **Headers**: 
  ```
  Content-Type: application/json
  Accept: application/json
  ```
- **Body (JSON)**:
  ```json
  {
    "latitude": -6.200000,
    "longitude": 106.816666,
    "radius": 10
  }
  ```

---

## 🍽️ MENU ENDPOINTS

### 14. Get All Menus (Public)
- **Method**: `GET`
- **URL**: `{{base_url}}/v1/menus`
- **Headers**: 
  ```
  Accept: application/json
  ```
- **Query Parameters** (Optional):
  ```
  ?tersedia=true&search=ayam&min_price=10000&max_price=50000&sort_by_price=asc&per_page=10
  ```

### 15. Get Menus by UMKM (Public)
- **Method**: `GET`
- **URL**: `{{base_url}}/v1/menus/umkm/1`
- **Headers**: 
  ```
  Accept: application/json
  ```
- **Query Parameters** (Optional):
  ```
  ?tersedia=true&search=nasi&sort_by_price=desc
  ```

### 16. Get Menu Detail (Public)
- **Method**: `GET`
- **URL**: `{{base_url}}/v1/menus/umkm/1/1`
- **Headers**: 
  ```
  Accept: application/json
  ```

### 17. Create Menu (Protected)
- **Method**: `POST`
- **URL**: `{{base_url}}/v1/menus/umkm/1`
- **Headers**: 
  ```
  Authorization: Bearer {{token}}
  Accept: application/json
  ```
- **Body (form-data)**:
  ```
  nama_menu: Ayam Bakar Test
  deskripsi: Menu test untuk API
  harga: 25000
  tersedia: true
  foto_menu: [file upload - optional]
  ```

### 18. Update Menu (Protected)
- **Method**: `PUT`
- **URL**: `{{base_url}}/v1/menus/umkm/1/1`
- **Headers**: 
  ```
  Authorization: Bearer {{token}}
  Content-Type: application/json
  Accept: application/json
  ```
- **Body (JSON)**:
  ```json
  {
    "nama_menu": "Menu Updated",
    "harga": 30000,
    "tersedia": true
  }
  ```

### 19. Delete Menu (Protected)
- **Method**: `DELETE`
- **URL**: `{{base_url}}/v1/menus/umkm/1/1`
- **Headers**: 
  ```
  Authorization: Bearer {{token}}
  Accept: application/json
  ```

---

## 🏥 UTILITY ENDPOINTS

### 20. Health Check (Public)
- **Method**: `GET`
- **URL**: `{{base_url}}/health`
- **Headers**: 
  ```
  Accept: application/json
  ```

### 21. Get User Info (Protected)
- **Method**: `GET`
- **URL**: `{{base_url}}/user`
- **Headers**: 
  ```
  Authorization: Bearer {{token}}
  Accept: application/json
  ```

---

## 📝 TESTING WORKFLOW

### Step 1: Setup Environment
1. Start Laravel server: `php artisan serve`
2. Import Postman collection: `E-UMKM-API.postman_collection.json`
3. Set environment variable `base_url = http://127.0.0.1:8000/api`

### Step 2: Authentication Test
1. Test **Register** (#1) - Save the token
2. Test **Login** (#2) - Update token if needed
3. Test **Get Profile** (#3) - Verify token works

### Step 3: Public Endpoints Test
1. Test **Health Check** (#20)
2. Test **Get All UMKM** (#7)
3. Test **Get Statistics** (#12)

### Step 4: Protected Endpoints Test
1. Test **Create UMKM** (#9)
2. Test **Get UMKM by ID** (#8) with created UMKM
3. Test **Create Menu** (#17) for the UMKM
4. Test **Update** and **Delete** operations

### Step 5: Advanced Features Test
1. Test **Nearby Search** (#13)
2. Test **Menu Filtering** (#14, #15)
3. Test **File Upload** (UMKM photo, Menu photo)

---

## ⚡ QUICK TEST COMMANDS

### Curl Examples:

**Health Check:**
```bash
curl -X GET "http://127.0.0.1:8000/api/health" -H "Accept: application/json"
```

**Register:**
```bash
curl -X POST "http://127.0.0.1:8000/api/v1/auth/register" \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"name":"Test User","email":"test@example.com","password":"password123","password_confirmation":"password123","role":"umkm"}'
```

**Get UMKM:**
```bash
curl -X GET "http://127.0.0.1:8000/api/v1/umkm" -H "Accept: application/json"
```

---

## 🔍 RESPONSE EXAMPLES

### Success Response:
```json
{
  "status": "success",
  "message": "Operation successful",
  "data": {
    // response data
  }
}
```

### Error Response:
```json
{
  "status": "error",
  "message": "Error description",
  "errors": {
    // validation errors
  }
}
```

### Pagination Response:
```json
{
  "status": "success",
  "message": "Data retrieved successfully",
  "data": [...],
  "pagination": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 15,
    "total": 67
  }
}
```