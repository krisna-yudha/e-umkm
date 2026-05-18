# Debug Login → Rating Submit Flow

## Masalah
Pengguna non-UMKM mendapat 401 Unauthorized saat submit rating, meski sudah login.

## Test Steps

### 1. Clear Cache & Restart Server
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:cache --force
```

### 2. Open Browser DevTools (F12) & Monitor

Saat login dengan akun regular user:

#### Step 2a: Check Login Success
1. Buka tab **Network**
2. Login dengan email regular user (user_type = 'user')
3. Cari request **POST /login** dan check:
   - Status: 302 (redirect) ✅
   - Response Headers: Lihat `Set-Cookie` header
   - Harus ada cookie dengan nama seperti `LARAVEL_SESSION`

#### Step 2b: Check Session Table
Setelah login berhasil:
```bash
mysql> SELECT id, user_id, payload FROM sessions LIMIT 1\G
```
- `id` harus ada (session ID)
- `user_id` harus ada (nomor ID user yang login)
- `payload` berisi data session

#### Step 2c: Check Session Cookie di Browser
1. DevTools → **Application** → **Cookies**
2. Lihat domain localhost atau 127.0.0.1
3. Cari cookie `LARAVEL_SESSION`
   - Harus ada setelah login
   - Harus hilang setelah logout

### 3. Navigate to UMKM Page & Check Auth

Setelah redirect ke UMKM page:

#### Step 3a: Check Page Props
Di browser console, ketik:
```javascript
// Inertia React component props
console.log('Page props:', window.location) // untuk lihat current URL

// Dari PublicUmkmShow.vue, currentUser should be:
// Jika ada yang print di console saat render
```

#### Step 3b: Check API Call untuk Check Auth
Dalam browser Network tab:
1. Cari request **GET /api/v1/auth/profile**
2. Check status:
   - Jika 200: User teridentifikasi ✅
   - Jika 401: Session tidak dikenali ❌

### 4. Submit Rating & Debug

Saat submit rating:

#### Step 4a: Check Network Request
1. Cari **POST /api/v1/umkms/{id}/ratings**
2. **Request Headers** harus memiliki:
   ```
   Cookie: LARAVEL_SESSION=abc123...
   X-CSRF-TOKEN: token_value_here
   X-Requested-With: XMLHttpRequest
   ```
3. **Response Status**:
   - 201: Rating berhasil ✅
   - 401: Session tidak valid ❌
   - 403: User adalah UMKM owner (error berbeda)

#### Step 4b: Check Response
Jika error 401, response seharusnya:
```json
{
  "error": "Sesi Anda telah berakhir. Silakan login terlebih dahulu",
  "debug": "Auth::check() = false"
}
```

### 5. Server-side Debugging

Cek Laravel logs:
```bash
tail -f storage/logs/laravel.log
```

Saat submit rating, cari:
```
=== RATING STORE REQUEST ===
"auth_check": true/false,
"user_id": <nomor>,
"session_id": "EXISTS" / "MISSING"
```

**Expected untuk user yang login:**
```json
{
  "auth_check": true,
  "user_id": 1,
  "user_name": "John Doe",
  "user_type": "user",
  "session_id": "EXISTS",
  "request_headers_csrf": "PRESENT"
}
```

## Possible Issues & Solutions

### Issue 1: "session_id": "MISSING"
**Cause**: Session middleware tidak di-load
**Solution**: 
```php
// Verify di routes/api.php:
Route::middleware([
    \Illuminate\Session\Middleware\StartSession::class,  // ← Harus ada
    ...
])
```

### Issue 2: "auth_check": false tetapi session_id EXISTS
**Cause**: Session ada tapi tidak berisi user auth
**Solution**: Cek apakah login sudah save auth ke session:
```bash
# Di MySQL:
SELECT * FROM sessions WHERE id = 'LARAVEL_SESSION_VALUE';
# Harus berisi user info dalam payload
```

### Issue 3: 419 Error (CSRF Mismatch)
**Cause**: CSRF token tidak match
**Solution**: 
- Cek X-CSRF-TOKEN header ada di request
- Cek token match dengan session token
- Clear CSRF cache: `php artisan cache:clear`

### Issue 4: Cookie tidak dikirim
**Cause**: axios tidak send credentials
**Solution**: Verify bootstrap.ts:
```javascript
// resources/js/bootstrap.ts harus memiliki:
window.axios.defaults.withCredentials = true;
```

## Verification Checklist

Sebelum submit rating:

- [ ] Login page menampilkan form dengan 2 tab (Pengguna / UMKM)
- [ ] Dipilih tab "Pengguna" (user_type = 'user')
- [ ] Email & password user yang ada di DB
- [ ] Login submit → 302 redirect
- [ ] Set-Cookie header ada di response
- [ ] LARAVEL_SESSION cookie ada di browser
- [ ] Redirect ke UMKM page berhasil
- [ ] Rating form aktif (tidak "login required")
- [ ] POST /api/v1/auth/profile return 200 (user recognized)
- [ ] POST rating submit memiliki X-CSRF-TOKEN header
- [ ] POST rating submit memiliki Cookie header

## Debug Code untuk Frontend

Tambahkan di RatingSection.vue sebelum submit:
```javascript
const submitRating = async () => {
    console.log('=== SUBMIT RATING DEBUG ===');
    console.log('User:', resolvedUser.value);
    console.log('Axios config:', {
        withCredentials: window.axios.defaults.withCredentials,
        CSRF: window.axios.defaults.headers.common['X-CSRF-TOKEN'] ? 'YES' : 'NO',
    });
    
    // Cek session cookie
    const cookies = document.cookie.split('; ').reduce((acc, c) => {
        const [key, val] = c.split('=');
        acc[key] = val;
        return acc;
    }, {});
    console.log('Cookies:', Object.keys(cookies));
    
    // ... submit code ...
};
```

## Logs yang Harus Anda Lihat

**Success Flow:**
1. POST /login → 302 (Set-Cookie: LARAVEL_SESSION)
2. GET /umkm-public/{id} → 200 (session recognized)
3. POST /api/v1/umkms/{id}/ratings → 201 (rating created)
   - Log: `=== RATING STORE REQUEST === "auth_check": true`
   - Log: `✅ User authenticated:`
   - Log: `✅ New rating created`

**Failure Flow (401 error):**
- Log: `=== RATING STORE REQUEST === "auth_check": false`
- Log: `❌ Auth::check() failed`
- Response: 401

## Next Steps untuk Report Issue

Jika masih 401 error, collect dan kirim:
1. Screenshot Network tab POST /api/v1/umkms/{id}/ratings request+response
2. Output dari: `SELECT COUNT(*) FROM sessions;`
3. Tail dari laravel.log saat submit rating
4. Browser console output dari debug code di atas

