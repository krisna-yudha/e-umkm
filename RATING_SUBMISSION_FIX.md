# Rating Submission Fix - Testing Guide

## Changes Made

### 1. **Separated Rating Routes from Sanctum Auth** (`routes/api.php`)
   - Moved rating/wishlist routes out of `auth:sanctum` group
   - Created dedicated route group with **session middleware** for web-based users

### 2. **Added Session Middleware Stack**
   Routes now use:
   - `StartSession::class` - Loads session from cookie
   - `ShareErrorsFromSession::class` - Shares validation errors
   - `VerifyCsrfTokenForApi::class` - Verifies CSRF token
   - `auth:web` - Authenticates user via session

### 3. **Created Custom CSRF Middleware** (`app/Http/Middleware/VerifyCsrfTokenForApi.php`)
   - Validates CSRF token in `X-CSRF-TOKEN` header or `_token` field
   - Exempts public routes from CSRF verification
   - Returns 419 error if token doesn't match

### 4. **Enhanced Logging** (`app/Http/Controllers/RatingController.php`)
   - Logs session ID status
   - Logs CSRF header presence
   - Better error messages mentioning session expiry

## Testing Steps

### Step 1: Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:cache
```

### Step 2: Verify Session Table
Ensure the `sessions` table exists in database:
```bash
php artisan migrate
```

### Step 3: Test Complete Flow
1. **Open browser console** (F12) to check for errors
2. **Login** via the login page
3. **Return to UMKM page** (should redirect back automatically)
4. **Check** if rating form appears as active (not "Login required" message)
5. **Submit a rating** with 1-5 stars

### Step 4: Monitor Logs
While testing, check the logs for debug info:
```bash
tail -f storage/logs/laravel.log
```

Look for these log entries:
- `=== RATING STORE REQUEST ===` - Shows auth status
- `✅ User authenticated:` - Indicates session was recognized
- `✅ New rating created` - Indicates successful submission

### Step 5: Browser Developer Tools
1. **Network Tab**: Check `/api/v1/umkms/*/ratings` POST request
   - Should have `Cookie` header with `LARAVEL_SESSION`
   - Should have `X-CSRF-TOKEN` header

2. **Console Tab**: Look for any JavaScript errors

## Expected Results

### Success Scenario
- **Status**: 201 Created (for new rating) or 200 OK (for update)
- **Response**: 
  ```json
  {
    "message": "Rating berhasil ditambahkan",
    "rating": { ... }
  }
  ```
- **Alert**: "✅ Rating berhasil disimpan!"

### Session Expired Error
- **Status**: 401 Unauthorized
- **Response**: 
  ```json
  {
    "error": "Sesi Anda telah berakhir. Silakan login terlebih dahulu"
  }
  ```

## Troubleshooting

### If still getting 401 error:

1. **Check session cookie exists**
   - In browser DevTools → Application → Cookies
   - Look for `LARAVEL_SESSION` cookie
   - Should be present after login

2. **Check session is being created**
   ```bash
   # In database
   mysql> SELECT COUNT(*) FROM sessions;
   # Should have entries after login
   ```

3. **Verify CSRF token**
   - In Network tab, check that POST request includes `X-CSRF-TOKEN` header
   - Value should match what's in the page meta tag

4. **Check auth guard**
   - Logs should show `"auth_check": true` in RATING STORE REQUEST
   - If false, the session is not being recognized

### If getting CSRF mismatch error:

1. **Clear CSRF cache**
   ```bash
   php artisan route:cache --force
   php artisan cache:clear
   ```

2. **Check CSRF token generation**
   - Verify the `X-CSRF-TOKEN` meta tag exists in the page HTML
   - Verify axios is reading it from the meta tag

3. **Verify SetCsrfTokenHeader middleware is running**
   - Check that response headers include `X-CSRF-TOKEN`
   - This updates the token after each request

## Configuration Files Reviewed
- `.env` - SESSION_DRIVER=database, SESSION_LIFETIME=120
- `config/session.php` - Database driver configured
- `resources/js/bootstrap.ts` - Axios withCredentials=true, CSRF token handling

## Middleware Stack Order (Important)
1. StartSession - MUST be first to load session
2. ShareErrorsFromSession - Optional but helpful
3. VerifyCsrfTokenForApi - CSRF validation
4. auth:web - Authenticates using session

This order ensures:
- Session is loaded before any auth checks
- CSRF is verified before data is processed
- User is authenticated using session

