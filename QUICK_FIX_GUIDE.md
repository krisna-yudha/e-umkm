# Step-by-Step Fix for Login → Rating Submission

## Problem yang Terjadi
Pengguna non-UMKM login berhasil ✅, tapi saat submit rating mendapat error 401 "Sesi Anda telah berakhir"

## Root Cause
- Rating endpoint menggunakan `auth:sanctum` (token-based API auth)
- Frontend login menggunakan session-based auth (browser cookies)
- Mismatch antara authentication method

## Fixes Applied

### 1️⃣ Session Middleware untuk Rating Routes
Rating routes di-pindah dari Sanctum group ke session-based group dengan middleware:
- **StartSession** - Load session dari cookie `LARAVEL_SESSION`
- **VerifyCsrfTokenForApi** - Validate CSRF token
- **AuthenticateWithSession** - Check session auth (guard 'web')

### 2️⃣ Custom Middleware untuk Session Auth
Dibuat `AuthenticateWithSession.php` yang:
- Explicitly check `Auth::guard('web')->check()` (session auth)
- Return 401 jika session invalid
- Tidak expect Sanctum tokens

### 3️⃣ CSRF Validation untuk API
Dibuat `VerifyCsrfTokenForApi.php` yang:
- Validate token dari header `X-CSRF-TOKEN`
- Exempt public routes
- Return 419 jika token tidak match

## Langkah Testing

### A. Persiapan Server
Jalankan di terminal:
```bash
cd c:\xampp\htdocs\prokerv1\e-umkm-backendV1

# 1. Run migrations (ensure session table & user_type column exist)
php artisan migrate

# 2. Clear semua cache
php artisan cache:clear
php artisan config:clear
php artisan route:cache --force
```

### B. Test Login Flow

1. **Buka browser, buka DevTools (F12)**
   - Pilih tab **Network**

2. **Navigate ke login page**
   - URL: http://localhost:8000/login (atau sesuai APP_URL Anda)

3. **Fill form login**
   - Email: user regular (bukan UMKM)
   - Password: correct password
   - Pilih tab "👤 Pengguna" (bukan UMKM)

4. **Submit login**
   - Cari request **POST /login** di Network tab
   - Check Response Headers untuk `Set-Cookie`
   - Harus ada: `LARAVEL_SESSION=xxxxx`

5. **Check session di database**
   Di terminal MySQL:
   ```bash
   mysql> USE umkm_dbv2;
   mysql> SELECT COUNT(*) FROM sessions;
   # Seharusnya ada entries setelah login
   ```

### C. Test Rating Submission

1. **Navigate ke UMKM detail page**
   - URL: http://localhost:8000/umkm-public/{id}

2. **Check rating form**
   - Rating form seharusnya **AKTIF** (tidak ada "login required" message)
   - Stars bisa di-click

3. **Submit rating**
   - Click stars (pilih rating 1-5)
   - Optional: tulis review
   - Click "Kirim Rating" button

4. **Monitor Network tab**
   - Cari request **POST /api/v1/umkms/{id}/ratings**
   - Check **Request Headers**:
     ```
     Cookie: LARAVEL_SESSION=xxxxx
     X-CSRF-TOKEN: token_value
     X-Requested-With: XMLHttpRequest
     ```
   - Check **Response Status**:
     - 201 ✅ Success
     - 401 ❌ Session expired
     - 419 ❌ CSRF mismatch
     - 403 ❌ User tidak punya izin

### D. Expected Success Response (201)
```json
{
  "message": "Rating berhasil ditambahkan",
  "rating": {
    "id": 1,
    "umkm_id": 1,
    "user_id": 1,
    "rating": 5,
    "review": "Bagus!",
    "created_at": "2026-05-18T...",
    "user": {
      "id": 1,
      "name": "John",
      ...
    }
  }
}
```

## Jika Masih Error 401

### Debug di Server
Tail logs saat submit rating:
```bash
# Terminal 1
tail -f storage/logs/laravel.log

# Terminal 2 - Di browser, submit rating
# Cari output dari RATING STORE REQUEST
```

**Expected log output:**
```
=== RATING STORE REQUEST ===
{
  "auth_check": true,          ← IMPORTANT
  "user_id": 1,
  "user_name": "John",
  "user_type": "user",         ← MUST BE "user"
  "session_id": "EXISTS",      ← Session ada
  "request_headers_csrf": "PRESENT"
}
✅ User authenticated:
✅ New rating created
```

**Jika error:**
```
"auth_check": false,  ← Berarti session tidak di-load
"session_id": "MISSING"  ← Berarti session tidak ada
```

### Solutions untuk Common Issues

#### Problem: "auth_check": false, "session_id": "MISSING"
**Cause:** Session middleware tidak di-load
**Solution:**
```bash
php artisan route:cache --force
php artisan cache:clear
# Restart PHP
```

#### Problem: Database sessions table doesn't exist
```bash
# Check
mysql> SHOW TABLES LIKE 'sessions';

# If not exist, run migration
php artisan migrate --path=database/migrations/0001_01_01_000000_create_users_table.php
```

#### Problem: user_type column tidak ada
```bash
# Check
mysql> DESC users;  # Lihat apakah user_type ada

# Jika tidak ada, run migration
php artisan migrate --path=database/migrations/2025_10_01_000000_add_user_type_to_users_table.php
```

#### Problem: CSRF Token error (419)
```bash
# Clear cache
php artisan cache:clear
php artisan config:clear

# Ensure SetCsrfTokenHeader middleware exists
ls app/Http/Middleware/SetCsrfTokenHeader.php
```

## Verification Checklist

Sebelum test rating submission:

- [ ] Database migrations sudah run (`php artisan migrate`)
- [ ] Sessions table ada di database
- [ ] User_type column ada di users table
- [ ] Login page ada tab "Pengguna" dan "UMKM"
- [ ] Login form memiliki field `user_type`
- [ ] LARAVEL_SESSION cookie ada setelah login (DevTools → Cookies)
- [ ] /api/v1/umkms/{id}/ratings route ada di route list
- [ ] Middleware file ada:
  - app/Http/Middleware/AuthenticateWithSession.php
  - app/Http/Middleware/VerifyCsrfTokenForApi.php

## Files Changed

1. **routes/api.php** - Separated rating routes from Sanctum
2. **app/Http/Middleware/AuthenticateWithSession.php** - NEW
3. **app/Http/Middleware/VerifyCsrfTokenForApi.php** - NEW (already existed, verified)
4. **app/Http/Controllers/RatingController.php** - Enhanced logging

## Reference Documentation
- See `DEBUG_LOGIN_RATING.md` for detailed debugging guide
- See `RATING_SUBMISSION_FIX.md` for technical details

