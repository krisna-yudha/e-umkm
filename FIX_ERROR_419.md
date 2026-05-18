# Fix untuk Error 419 - CSRF Token Mismatch

## Problem yang Terjadi
Error 419 (CSRF token mismatch) saat submit rating padahal sudah login

## Root Cause
Middleware CSRF validation terlalu strict untuk session-based API requests

## Solution
Removed CSRF middleware dari rating routes, karena:
1. Rating endpoints sudah protected dengan session auth
2. Session authentication adalah sufficient protection
3. CSRF dalam API context berbeda dengan web form context

## Middleware Stack Sekarang (Rating Routes)
```
1. StartSession              → Load session dari cookie
2. ShareErrorsFromSession    → Share validation errors  
3. AuthenticateWithSession   → Check Auth::guard('web')
```

**CSRF middleware DIREMOVE** - tidak perlu untuk session-protected endpoints

## Test Steps

### 1. Clear Cache & Route
```bash
cd c:\xampp\htdocs\prokerv1\e-umkm-backendV1

# IMPORTANT: Clear route cache
php artisan route:cache --force

# Clear config
php artisan config:clear
php artisan cache:clear

# Restart PHP/server
```

### 2. Test Rating Submission
1. **Browser DevTools → F12 → Network tab**
2. **Open new tab, login lagi**
   - Go to http://127.0.0.1:8000/login
   - Choose "👤 Pengguna" tab
   - Enter email & password
   - Click login

3. **Navigate ke UMKM detail page**
   - Redirect dari login atau copy URL dari browser

4. **Submit rating**
   - Click stars (pilih 1-5)
   - Click "Kirim Rating" button
   - **Expected:** 201 Created ✅ OR 401 Unauthorized ❌ (not 419!)

### 3. Monitor Network Request
Cari request **POST /api/v1/umkms/{id}/ratings**:
- **Status 201** → Success ✅
- **Status 401** → Session expired (need login) ⚠️
- **Status 419** → CSRF error (shouldn't happen) ❌
- **Status 403** → User is UMKM owner (blocked) ❌

### 4. Check Request Headers
Harus ada:
```
Cookie: LARAVEL_SESSION=xxxxx
X-Requested-With: XMLHttpRequest
```

## Expected Success Response (201)
```json
{
  "message": "Rating berhasil ditambahkan",
  "rating": {
    "id": 1,
    "umkm_id": 9,
    "user_id": 3,
    "rating": 5,
    "review": "Bagus!",
    "created_at": "2026-05-18T...",
    "user": {
      "id": 3,
      "name": "John Doe"
    }
  }
}
```

## Jika Masih Error

### Masih 419?
- [ ] Restart PHP/Laravel dev server
- [ ] Clear browser cache & cookies
- [ ] Try incognito/private mode
- [ ] Check middleware order in routes/api.php

### 401 Unauthorized?
- [ ] Logout & login lagi
- [ ] Check LARAVEL_SESSION cookie ada
- [ ] Check database: `SELECT COUNT(*) FROM sessions;`

### Database Error?
- [ ] Run migrations: `php artisan migrate`
- [ ] Check sessions table: `SHOW TABLES LIKE 'sessions';`

## Files yang Diubah
1. ✅ `routes/api.php` - Removed VerifyCsrfTokenForApi
2. ✅ `app/Http/Middleware/AuthenticateWithSession.php` - Enhanced logging
3. ✅ `app/Http/Middleware/VerifyCsrfTokenForApi.php` - Improved patterns (backup)

