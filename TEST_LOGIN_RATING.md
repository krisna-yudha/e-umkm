# Debug Login & Session Issue untuk Rating 401 Error

## Problem yang Terjadi
User regular (non-UMKM) login berhasil tapi saat beri rating dapat error 401 atau 419 (session/CSRF issue)

## Root Cause Analysis
1. **Login Request** - Tidak validate `user_type` dari tab yang dipilih
2. **Session Persistence** - `user_type` tidak disimpan di session
3. **Auth Check di API** - RatingController tidak double-check user_type
4. **Session Middleware Order** - StartSession harus PERTAMA sebelum auth check

## Fixes Applied

### 1. ✅ LoginRequest - Validate user_type Matching
- Check bahwa user_type di database cocok dengan tab yang dipilih
- Regular user tidak bisa login dari tab UMKM (dan sebaliknya)
- Reject login jika tidak match, dengan error message yang jelas

### 2. ✅ AuthenticatedSessionController - Store user_type to Session  
- Simpan `user_type`, `user_id`, `user_name` ke session saat login
- Enable persistent access dari API
- Log session info untuk debugging

### 3. ✅ RatingController - Better user_type Validation
- Check dari Auth::user() 
- Log session values juga untuk debug
- Detailed log untuk setiap step

### 4. ✅ Routes API - Fixed Middleware Order
- StartSession HARUS pertama
- AuthenticateWithSession langsung setelah
- Removed ShareErrorsFromSession (not needed for API)

## Critical Test Steps

### Step 1: Ensure Database & Migrations
```bash
cd c:\xampp\htdocs\prokerv1\e-umkm-backendV1

# Run ALL migrations
php artisan migrate

# Verify sessions table exists
mysql> USE umkm_dbv2;
mysql> DESC sessions;
```

Should show columns: `id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`

### Step 2: Clear ALL Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:cache --force
```

### Step 3: Test Login with Regular User

1. **Open fresh browser/incognito window**
2. **Go to login page**
   - URL: http://127.0.0.1:8000/login
3. **Choose tab "👤 Pengguna" (User)**
4. **Enter credentials**
   - Email: regular user email (user_type = 'user' in DB)
   - Password: correct password
5. **Click Login**
6. **Check result:**
   - ✅ Should redirect to dashboard
   - Check browser Network tab for POST /login
   - Check response headers for Set-Cookie

### Step 4: Verify Session Created
```bash
# While still logged in from Step 3:

mysql> SELECT id, user_id, created_at FROM sessions ORDER BY created_at DESC LIMIT 1;
# Should show: session_id | user_id=X | timestamp

mysql> SELECT id, email, user_type FROM users WHERE id = X;
# Verify user_type is 'user' or NULL
```

### Step 5: Navigate to UMKM Detail Page

1. **From dashboard or copy URL**
   - URL: http://127.0.0.1:8000/umkm-public/9
2. **Check DevTools → Application → Cookies**
   - Should have `LARAVEL_SESSION` cookie present
3. **Rating form should appear as ACTIVE** (not "login required")

### Step 6: Submit Rating

1. **Click stars (e.g., 5 stars)**
2. **Optional: type review**
3. **Click "Kirim Rating" button**

### Step 7: Monitor Network Request

In DevTools Network tab, find POST request to `/api/v1/umkms/9/ratings`:

**Expected Headers:**
```
Cookie: LARAVEL_SESSION=xxxxx
X-Requested-With: XMLHttpRequest
Content-Type: application/json
```

**Expected Status & Response:**
- ✅ **201 Created** → Success!
  ```json
  {
    "message": "Rating berhasil ditambahkan",
    "rating": { ... }
  }
  ```

- ❌ **401 Unauthorized** → Session not recognized
  ```json
  {
    "error": "Sesi Anda telah berakhir. Silakan login terlebih dahulu"
  }
  ```

- ❌ **403 Forbidden** → User is UMKM owner
  ```json
  {
    "error": "UMKM owner tidak dapat memberikan rating"
  }
  ```

- ~~419 CSRF~~ → Should NOT happen (middleware removed)

### Step 8: Check Server Logs

While doing Step 6-7:

```bash
tail -f storage/logs/laravel.log

# Should see these logs:
# "Login successful" - from AuthenticatedSessionController store
# "=== RATING STORE REQUEST ===" - from RatingController
# "✅ User authenticated:" - from RatingController
# "✅ New rating created" - from RatingController
```

## Troubleshooting

### Error: 401 Unauthorized with "Sesi Anda telah berakhir"

**Cause:** Session not being loaded properly

**Check:**
1. Sessions table exists: `mysql> SELECT COUNT(*) FROM sessions;`
2. LARAVEL_SESSION cookie present: DevTools → Cookies
3. Session lifecycle: `mysql> SELECT * FROM sessions WHERE user_id = 1;`
4. Restart PHP: Stop & start server

**Solution:**
```bash
php artisan migrate --force
php artisan cache:clear
# Restart PHP
```

### Error: 403 Forbidden "UMKM owner tidak dapat memberikan rating"

**Expected** if you're testing with UMKM user account

**Solution:** Test dengan regular user account instead

### Error: Login failed - "Akun ini adalah akun UMKM. Silakan login dari tab UMKM"

**Cause:** Trying to login with UMKM account from "Pengguna" tab

**Solution:** Choose correct tab matching account type

### Error: Still getting 419 CSRF

**Cause:** Route cache might not updated

**Solution:**
```bash
php artisan route:cache --force
php artisan config:clear
php artisan cache:clear
# Hard refresh browser: Ctrl+Shift+R
```

## Test Success Criteria

✅ All of these should work:

1. Login dari tab "Pengguna" dengan user regular → 302 redirect ke dashboard
2. Navigate ke UMKM detail page → session cookie masih ada
3. Rating form aktif (bukan "login required")
4. Submit rating → 201 Created response
5. Rating muncul di halaman
6. Server logs menunjukkan "✅ New rating created"

## Additional Debug Commands

### Check session serialization:
```bash
mysql> SELECT LEFT(payload, 200) FROM sessions LIMIT 1;
# Should contain auth data
```

### Check user_type values:
```bash
mysql> SELECT id, name, email, user_type FROM users LIMIT 10;
```

### Monitor real-time logs:
```bash
php artisan tinker

# In tinker:
>>> DB::table('sessions')->latest()->first();
>>> Auth::user();
```

## Files Modified
- ✅ `app/Http/Requests/Auth/LoginRequest.php` - Added user_type validation
- ✅ `app/Http/Controllers/Auth/AuthenticatedSessionController.php` - Store user_type to session
- ✅ `app/Http/Controllers/RatingController.php` - Enhanced logging & validation
- ✅ `routes/api.php` - Simplified middleware stack

