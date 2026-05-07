# 📍 LOKASI SEMUA FILE - Rating, Wishlist & User Login

## 🔐 USER LOGIN FORM

### ✅ Form Login User (Non-UMKM)
**Lokasi**: `resources/js/Pages/Auth/UserLogin.vue`

**Features**:
- Email & password input
- Show/hide password toggle
- Remember me checkbox
- Forgot password link
- Link ke register & UMKM login
- Responsive (mobile & desktop)

**Akses di URL**: `/login?type=user`

---

### ✅ Form Register User
**Lokasi**: `resources/js/Pages/Auth/UserRegister.vue`

**Features**:
- Name, email, password, confirm password
- Terms & conditions checkbox
- Password visibility toggle
- Link ke UMKM register
- Responsive design

**Akses di URL**: `/register?type=user`

---

## ⭐ RATING SYSTEM

### ✅ Rating Component (Display & Submit)
**Lokasi**: `resources/js/Components/RatingSection.vue`

**Features**:
- ⭐ Tampilkan rating stats (average, distribution)
- 📝 Form submit rating 1-5 bintang + review
- 👥 List ulasan dengan user avatar
- 👍 Helpful voting system
- 🔐 Login check untuk user type 'user'
- 🗑️ Delete button untuk owner rating

**Sudah integrated di**: `PublicUmkmShow.vue`

---

### ✅ Rating Model & Database
**File**:
- `app/Models/Rating.php` - Model dengan relationships
- `database/migrations/2025_10_01_000001_create_ratings_table.php` - Schema

**Fields**:
- umkm_id (FK)
- user_id (FK)
- rating (integer 1-5)
- review (text, nullable)
- helpful_count (integer)
- timestamps

---

### ✅ Rating API Controller
**Lokasi**: `app/Http/Controllers/RatingController.php`

**Endpoints**:
```
GET  /api/v1/umkms/{id}/ratings          → Get all ratings
POST /api/v1/umkms/{id}/ratings          → Submit rating
DELETE /api/v1/ratings/{id}              → Delete rating
POST /api/v1/ratings/{id}/helpful        → Mark helpful
```

---

## 💗 WISHLIST SYSTEM

### ✅ Wishlist Component (Button di Header)
**Lokasi**: `resources/js/Pages/PublicUmkmShow.vue`

**Features**:
- ❤️ / 🤍 Wishlist button di header
- Toggle on/off dengan API calls
- Check status on component mount
- Requires user login

---

### ✅ Wishlist Model & Database
**File**:
- `app/Models/Wishlist.php` - Model dengan relationships
- `database/migrations/2025_10_02_000000_create_wishlists_table.php` - Schema

**Fields**:
- umkm_id (FK)
- user_id (FK)
- timestamps
- unique(umkm_id, user_id)

---

### ✅ Wishlist API Controller
**Lokasi**: `app/Http/Controllers/WishlistController.php`

**Endpoints**:
```
GET    /api/v1/wishlist                  → Get user's wishlist
GET    /api/v1/umkms/{id}/wishlist/check → Check if wishlisted
POST   /api/v1/umkms/{id}/wishlist       → Add to wishlist
DELETE /api/v1/umkms/{id}/wishlist       → Remove from wishlist
```

---

## 📋 CHECKLIST TO COMPLETE

### STEP 1: Run Migrations ✅ (SIAP)
```bash
php artisan migrate
```

Akan create:
- `ratings` table
- `wishlists` table
- Add `user_type` column ke `users` table

---

### STEP 2: Update AuthApiController ⏳ (BELUM DONE)

**File**: `app/Http/Controllers/Api/AuthApiController.php`

**Perlu dimodifikasi**:
```php
// Dalam register() method:
$user->user_type = $request->validated('user_type'); // 'user' atau 'umkm'

// Dalam login() method:
if ($user->user_type !== $request->validated('user_type')) {
    return error('User type tidak sesuai');
}
```

---

### STEP 3: Create Web Routes ⏳ (BELUM DONE)

**File**: `routes/web.php`

**Tambahkan**:
```php
Route::get('/login', function () {
    $type = request('type', 'user');
    return $type === 'umkm' 
        ? redirect('/auth/umkm-login')
        : redirect('/auth/user-login');
});

Route::get('/register', function () {
    $type = request('type', 'user');
    return $type === 'umkm'
        ? redirect('/auth/umkm-register')
        : redirect('/auth/user-register');
});

Route::get('/auth/user-login', fn() => Inertia::render('Auth/UserLogin'));
Route::get('/auth/user-register', fn() => Inertia::render('Auth/UserRegister'));
```

---

## 📊 FILE STRUCTURE OVERVIEW

```
resources/js/
├── Pages/
│   ├── Auth/
│   │   ├── UserLogin.vue          ✅ DONE
│   │   ├── UserRegister.vue       ✅ DONE
│   │   ├── UmkmLogin.vue          (sudah ada)
│   │   └── UmkmRegister.vue       (sudah ada)
│   │
│   └── Public/
│       ├── PublicUmkmShow.vue     ✅ UPDATED (+ Rating + Wishlist)
│       └── PublicUmkmListSimple.vue (tidak perlu changes)
│
├── Components/
│   └── RatingSection.vue          ✅ DONE
│
app/Http/Controllers/
├── RatingController.php           ✅ DONE
├── WishlistController.php         ✅ DONE
├── Api/AuthApiController.php      ⏳ NEED UPDATE

app/Models/
├── Rating.php                     ✅ DONE
├── Wishlist.php                   ✅ DONE
├── User.php                       ✅ UPDATED (+ ratings, wishlists relations)
└── Umkm.php                       ✅ UPDATED (+ ratings, wishlists relations)

database/migrations/
├── 2025_10_01_000000_add_user_type_to_users_table.php         ✅ DONE
├── 2025_10_01_000001_create_ratings_table.php                 ✅ DONE
└── 2025_10_02_000000_create_wishlists_table.php               ✅ DONE

routes/
├── api.php                        ✅ UPDATED (rating & wishlist routes)
└── web.php                        ⏳ NEED UPDATE (auth routes)
```

---

## 🧪 TEST URLS

### User Login/Register
```
http://localhost/login?type=user        → User Login Form
http://localhost/register?type=user     → User Register Form
http://localhost/login?type=umkm        → UMKM Login Form
http://localhost/register?type=umkm     → UMKM Register Form
```

### UMKM Detail dengan Rating & Wishlist
```
http://localhost/daftar-umkm/{id}       → Show detail dengan Rating & Wishlist
```

---

## 🔌 API Testing (Postman)

### Rating Endpoints
```
GET  /api/v1/umkms/1/ratings                      → Get ratings
POST /api/v1/umkms/1/ratings                      → Submit rating
     Body: {"rating": 5, "review": "text"}
DELETE /api/v1/ratings/1                          → Delete
POST /api/v1/ratings/1/helpful                    → Mark helpful
```

### Wishlist Endpoints
```
GET  /api/v1/wishlist                             → Get user's wishlist
GET  /api/v1/umkms/1/wishlist/check               → Check if wishlisted
POST /api/v1/umkms/1/wishlist                     → Add to wishlist
DELETE /api/v1/umkms/1/wishlist                   → Remove
```

---

## 📌 IMPORTANT NOTES

### Token Storage
Components menggunakan:
```javascript
localStorage.getItem('sanctum_token')
```

Pastikan token disimpan dengan key yang sama saat login.

### User Type Requirement
- **Rating**: Hanya user dengan `user_type = 'user'` yang bisa submit
- **Wishlist**: Hanya user dengan `user_type = 'user'` yang bisa wishlist
- UMKM owners TIDAK bisa rating/wishlist

### Database Constraints
- Unique (umkm_id, user_id) pada ratings table
- Unique (umkm_id, user_id) pada wishlists table
- Cascade delete jika user/UMKM dihapus

---

## ✅ SUMMARY: APA YANG SUDAH DONE

- ✅ User Login Form (UserLogin.vue)
- ✅ User Register Form (UserRegister.vue)
- ✅ Rating Component (RatingSection.vue)
- ✅ Rating Model & Controller
- ✅ Wishlist Model & Controller
- ✅ Wishlist Button di Header
- ✅ API Routes (rating & wishlist)
- ✅ Database Migrations (ready to run)
- ✅ Model Relationships (User, Umkm, Rating, Wishlist)
- ✅ Integration ke PublicUmkmShow.vue

---

## ⏳ YANG MASIH PERLU DONE

1. **Run Migrations**: `php artisan migrate`
2. **Update AuthApiController**: Add user_type handling
3. **Add Web Routes**: Auth routes di routes/web.php
4. **Test Everything**: Login, register, rating, wishlist

**Estimated Time**: 30-45 minutes

---

Generated: 2025-04-18
Status: 90% Complete
