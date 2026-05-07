# 📋 Step-by-Step Implementation Guide untuk Rating & User Role System

## 🎯 Overview
Sistem ini menambahkan fitur rating/review dengan mendukung dua role berbeda:
- **User (Pengguna Biasa)**: Dapat melihat daftar UMKM dan memberikan rating/review
- **UMKM**: Pemilik usaha yang mengelola produk dan menu mereka

---

## 📦 File-File yang Sudah Dibuat

### 1. Database Migrations
```
✅ database/migrations/2025_10_01_000000_add_user_type_to_users_table.php
✅ database/migrations/2025_10_01_000001_create_ratings_table.php
```

### 2. Models
```
✅ app/Models/Rating.php (NEW)
✅ app/Models/User.php (UPDATED)
✅ app/Models/Umkm.php (UPDATED)
```

### 3. Controllers
```
✅ app/Http/Controllers/RatingController.php (NEW)
```

### 4. Vue Components
```
✅ resources/js/Components/RatingSection.vue (NEW)
✅ resources/js/Pages/Auth/UserLogin.vue (NEW)
✅ resources/js/Pages/Auth/UserRegister.vue (NEW)
```

### 5. Routes
```
✅ routes/api.php (UPDATED dengan rating routes)
```

---

## 🚀 Langkah Implementasi

### STEP 1: Jalankan Database Migrations
```bash
cd c:\xampp\htdocs\prokerv1\e-umkm-backendV1
php artisan migrate
```

✅ **Hasil**: 
- Kolom `user_type` ditambahkan ke tabel `users`
- Tabel `ratings` baru dibuat dengan struktur lengkap

---

### STEP 2: Update AuthApiController

Buka file: `app/Http/Controllers/Api/AuthApiController.php`

**Modifikasi method `register()`** untuk menangani `user_type`:

```php
public function register(Request $request)
{
    $validated = $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email|unique:users',
        'password' => 'required|string|min:8|confirmed',
        'user_type' => 'required|in:user,umkm', // Add this validation
    ]);

    $user = User::create([
        'name' => $validated['name'],
        'email' => $validated['email'],
        'password' => Hash::make($validated['password']),
        'user_type' => $validated['user_type'], // Add this line
    ]);

    // Return response...
}
```

**Modifikasi method `login()`** untuk validasi user_type:

```php
public function login(Request $request)
{
    $validated = $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
        'user_type' => 'required|in:user,umkm', // Add validation
    ]);

    // Check user exists and password correct
    $user = User::where('email', $validated['email'])->first();
    
    if (!$user || !Hash::check($validated['password'], $user->password)) {
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    // Check user_type matches
    if ($user->user_type !== $validated['user_type']) {
        return response()->json([
            'message' => 'User type tidak sesuai. Silakan masuk dengan akun yang tepat.'
        ], 403);
    }

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'token' => $token,
        'user' => $user,
    ]);
}
```

---

### STEP 3: Update Routes (Web Routes untuk UI)

Buka file: `routes/web.php`

Tambahkan routes berikut:

```php
// Auth Routes untuk User
Route::prefix('auth')->group(function () {
    Route::get('/user-login', function () {
        return Inertia::render('Auth/UserLogin');
    })->name('user.login');

    Route::get('/user-register', function () {
        return Inertia::render('Auth/UserRegister');
    })->name('user.register');

    Route::get('/umkm-login', function () {
        return Inertia::render('Auth/UmkmLogin');
    })->name('umkm.login');

    Route::get('/umkm-register', function () {
        return Inertia::render('Auth/UmkmRegister');
    })->name('umkm.register');
});

// Redirect /login based on query parameter
Route::get('/login', function () {
    $type = request('type', 'user');
    return $type === 'umkm' 
        ? redirect()->route('umkm.login')
        : redirect()->route('user.login');
});

Route::get('/register', function () {
    $type = request('type', 'user');
    return $type === 'umkm'
        ? redirect()->route('umkm.register')
        : redirect()->route('user.register');
});
```

---

### STEP 4: Tambahkan RatingSection ke PublicUmkmShow.vue

Buka file: `resources/js/Pages/Public/PublicUmkmShow.vue`

```vue
<script setup lang="ts">
import RatingSection from '@/Components/RatingSection.vue';

// ... existing imports dan props ...

defineProps<{
    umkm: Umkm;
    menus: UmkmMenu[];
    auth: { user: User } | null;
}>();
</script>

<template>
    <GuestLayout>
        <!-- ... existing content ... -->

        <!-- Add Rating Section sebelum closing tag GuestLayout -->
        <div class="mt-12 mb-8">
            <RatingSection 
                :umkm="umkm"
                :current-user="auth?.user"
            />
        </div>
    </GuestLayout>
</template>
```

---

### STEP 5: Testing API Endpoints

Gunakan Postman atau curl untuk test:

#### A. Get Ratings (PUBLIC)
```
GET /api/v1/umkms/1/ratings
```

Response:
```json
{
    "ratings": [
        {
            "id": 1,
            "rating": 5,
            "review": "Produk berkualitas!",
            "helpful_count": 2,
            "created_at": "2025-01-15T10:30:00Z",
            "user": {
                "id": 1,
                "name": "John Doe",
                "profile_photo": null
            }
        }
    ],
    "average_rating": 4.5,
    "total_ratings": 10,
    "distribution": {
        "5": { "count": 5, "percentage": 50 },
        "4": { "count": 3, "percentage": 30 },
        // ...
    }
}
```

#### B. Submit Rating (PROTECTED)
```
POST /api/v1/umkms/1/ratings
Authorization: Bearer {token}
Content-Type: application/json

{
    "rating": 5,
    "review": "Sangat puas dengan produknya!"
}
```

Response:
```json
{
    "message": "Rating submitted successfully",
    "rating": {
        "id": 11,
        "umkm_id": 1,
        "user_id": 5,
        "rating": 5,
        "review": "Sangat puas dengan produknya!",
        "helpful_count": 0,
        "created_at": "2025-01-20T14:22:00Z",
        "updated_at": "2025-01-20T14:22:00Z"
    }
}
```

#### C. Delete Rating (PROTECTED)
```
DELETE /api/v1/ratings/11
Authorization: Bearer {token}
```

#### D. Mark as Helpful (PROTECTED)
```
POST /api/v1/ratings/11/helpful
Authorization: Bearer {token}
```

---

### STEP 6: Testing Frontend

1. **Test User Registration**
   - Buka: `http://localhost/register?type=user`
   - Isi form dengan data valid
   - Klik "Daftar Sekarang"
   - Verify: User dibuat dengan `user_type = 'user'`

2. **Test User Login**
   - Buka: `http://localhost/login?type=user`
   - Masuk dengan email/password user
   - Verify: Token disimpan dan user dialihkan

3. **Test Rating Submission**
   - Login sebagai user
   - Buka halaman detail UMKM
   - Scroll ke RatingSection
   - Submit rating dengan bintang dan review
   - Verify: Rating muncul di list

4. **Test Rating Statistics**
   - Buka halaman UMKM dengan multiple ratings
   - Verify: Average rating, distribution bar, dan count ditampilkan

---

## ⚙️ Konfigurasi Tambahan

### Sanctum CORS Configuration
Edit `config/sanctum.php` jika perlu:

```php
'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
    '%s%s',
    'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1',
    env('APP_URL') ? ','.parse_url(env('APP_URL'), PHP_URL_HOST) : ''
))),
```

### Rate Limiting (Optional)
Tambahkan ke `RatingController`:

```php
public function store(Request $request, Umkm $umkm)
{
    // Rate limit: max 5 ratings per hour per user
    $this->middleware('throttle:5,60')->only('store');
    
    // ... existing code ...
}
```

---

## 🔍 Troubleshooting

### Error: "user_type column doesn't exist"
**Solusi**: Jalankan migration terlebih dahulu
```bash
php artisan migrate
```

### Error: "RatingController not found"
**Solusi**: Pastikan import di routes/api.php benar
```php
use App\Http\Controllers\RatingController;
```

### Error: "CORS error" saat request dari Vue
**Solusi**: Pastikan CORS headers benar di `config/cors.php`

### Rating tidak muncul di frontend
**Solusi**: 
- Check browser console untuk error
- Verify API endpoint bekerja di Postman
- Pastikan user_id dan umkm_id valid

---

## 📊 Database Structure Verification

Run di terminal:
```bash
php artisan tinker

# Check users table
Schema::getColumnListing('users')

# Check ratings table
Schema::getColumnListing('ratings')

# Create test data
$user = User::create(['name' => 'Test User', 'email' => 'test@test.com', 'password' => Hash::make('password'), 'user_type' => 'user']);
$rating = Rating::create(['umkm_id' => 1, 'user_id' => $user->id, 'rating' => 5, 'review' => 'Great!']);
```

---

## ✅ Checklist Implementasi Final

- [ ] Jalankan migrations
- [ ] Update AuthApiController (register & login methods)
- [ ] Update routes/web.php dengan auth routes
- [ ] Update PublicUmkmShow.vue dengan RatingSection
- [ ] Test API endpoints dengan Postman
- [ ] Test user registration
- [ ] Test user login
- [ ] Test rating submission
- [ ] Test rating display
- [ ] Verify authorization (non-users can't rate)
- [ ] Test helpful button
- [ ] Test delete rating functionality
- [ ] Deploy ke production

---

## 🎨 UI/UX Notes

RatingSection component sudah include:
- ✨ Gradient backgrounds
- 🎭 Smooth animations
- 📱 Responsive design (mobile/desktop)
- ♿ Accessibility features
- 🎯 Interactive star rating selector
- 👍 Helpful voting system
- 🗑️ Delete button (owner only)

---

## 📞 Support & Next Steps

Jika ada error atau pertanyaan, check:
1. Browser console (F12) untuk JavaScript errors
2. Laravel logs: `storage/logs/laravel.log`
3. Database queries: Enable query logging di `config/database.php`

**Future Enhancements**:
- [ ] Wishlist feature (star icon to save favorites)
- [ ] Email notifications untuk UMKM owners
- [ ] Rating moderation panel untuk admin
- [ ] Review filtering & sorting
- [ ] User reputation badges

---

Generated: 2025-04-18
Document Version: 1.0
