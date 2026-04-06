# 📦 PRODUCTION DEPLOYMENT - MAP & STORAGE

## Pre-Deployment Checklist

```bash
# 1. Setup storage symlink
php artisan storage:link

# 2. Copy leaflet icons (agar marker tidak broken di prod)
mkdir -p public/images/leaflet
cp node_modules/leaflet/dist/images/*.png public/images/leaflet/

# 3. Update .env untuk production
APP_ENV=production
APP_URL=https://yourdomain.com
APP_DEBUG=false

# 4. Build frontend
npm run build

# 5. Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

---

## Check Configuration

### .env (Verify these)
```env
APP_ENV=production
APP_URL=https://yourdomain.com
FILESYSTEM_DISK=public
```

### config/app.php
```php
'url' => env('APP_URL', 'https://yourdomain.com'),
```

### Storage Symlink ✅
```bash
# Verify symlink exists
ls -la public/storage    # Linux/Mac
dir public\storage       # Windows

# Should output: public/storage -> ../storage/app/public
```

---

## Force HTTPS

**public/.htaccess** - Add this as first rule inside `<IfModule mod_rewrite.c>`:
```apache
<IfModule mod_rewrite.c>
    # Force HTTPS
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>
    
    # ... rest of rewrite rules
</IfModule>
```

---

## Fix Map Icons for Production

**resources/js/Pages/Mapping.vue** - Update line ~278:

**FIND THIS:**
```javascript
const iconRetinaUrl = 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon-2x.png';
const iconUrl = 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon.png';
const shadowUrl = 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png';
```

**REPLACE WITH:**
```javascript
// Use local icons from built assets
const iconRetinaUrl = '/images/leaflet/marker-icon-2x.png';
const iconUrl = '/images/leaflet/marker-icon.png';
const shadowUrl = '/images/leaflet/marker-shadow.png';
```

Then rebuild:
```bash
npm run build
```

---

## Storage Config

**config/filesystems.php** - Verify `public` disk:
```php
'disks' => [
    'public' => [
        'driver' => 'local',
        'path' => 'storage/app/public',
        'url' => env('APP_URL').'/storage',
        'visibility' => 'public',
    ],
],
```

---

## Test Before Going Live

```bash
# Test 1: HTTPS redirect
curl -I http://yourdomain.com/mapping    # Should redirect to HTTPS

# Test 2: Map loads
curl https://yourdomain.com/mapping      # Should return 200

# Test 3: Images accessible
curl https://yourdomain.com/storage/[sample-image-path]    # Should return 200

# Test 4: Marker icons loaded
curl https://yourdomain.com/images/leaflet/marker-icon.png # Should return 200
```

---

## Server Configuration (if applicable)

### Nginx
```nginx
# Force HTTPS
if ($scheme != "https") {
    return 301 https://$server_name$request_uri;
}

# Public storage access
location /storage {
    alias /path/to/project/storage/app/public;
    expires 30d;
}
```

### Apache (via .htaccess)
```apache
# Already configured above
```

---

## Common Issues

| Issue | Fix |
|-------|-----|
| **Map blank/gray** | Check tile URL is HTTPS, check network in DevTools |
| **Markers missing icons** | Verify `/public/images/leaflet/` files exist |
| **Images not loading** | Check storage symlink: `php artisan storage:link` |
| **Mixed content warning** | Ensure all URLs are HTTPS |
| **CORS errors** | Check CSP headers allow openstreetmap.org |

---

## Post-Deployment

```bash
# Check storage folder size
du -sh storage/app/public

# Monitor logs
tail -f storage/logs/laravel.log

# Test critical endpoints
# - Map page: https://yourdomain.com/mapping
# - UMKM list: https://yourdomain.com/umkm-public
# - Images: inspect browser DevTools → Images tab
```

---

## Emergency Rollback

```bash
# If something breaks
php artisan cache:clear
php artisan storage:link
npm run build
```

---

**Status:** ✅ Ready to Deploy
