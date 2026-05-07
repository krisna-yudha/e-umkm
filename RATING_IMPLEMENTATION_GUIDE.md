# Implementasi Rating & Wishlist + Role User Baru

## ✅ Yang Sudah Dibuat

### 1. **Database Migrations**
- ✅ `2025_10_01_000000_add_user_type_to_users_table.php` - Menambahkan kolom `user_type` (enum: 'user', 'umkm')
- ✅ `2025_10_01_000001_create_ratings_table.php` - Tabel untuk menyimpan rating dan review

### 2. **Models**
- ✅ `Rating.php` - Model untuk rating dengan relationships
- ✅ `User.php` - Diupdate dengan `user_type` dan `ratings()` relationship
- ✅ `Umkm.php` - Diupdate dengan `ratings()` relationship dan helper methods

### 3. **Controllers**
- ✅ `RatingController.php` - API endpoints untuk rating:
  - `store()` - POST rating baru atau update existing
  - `index()` - GET ratings dengan stats
  - `destroy()` - DELETE rating
  - `markHelpful()` - POST mark as helpful

### 4. **Vue Components**
- ✅ `RatingSection.vue` - Complete rating & review component dengan:
  - Tampilan rating stats (average & distribution)
  - Form untuk submit/edit rating (hanya untuk user tertentu)
  - List ulasan dengan user info
  - Helpful button

### 5. **API Routes**
- ✅ Public: `GET /api/v1/umkms/{umkm}/ratings` - Get ratings
- ✅ Protected: `POST /api/v1/umkms/{umkm}/ratings` - Store rating
- ✅ Protected: `DELETE /api/v1/ratings/{rating}` - Delete rating
- ✅ Protected: `POST /api/v1/ratings/{rating}/helpful` - Mark helpful

---

## 🔧 Yang Perlu Dilakukan Manual

### 1. **Jalankan Migrations**
```bash
php artisan migrate
```

### 2. **Update AuthApiController untuk Handle 2 Role**
Perbarui register & login untuk mendukung `user_type`:

```php
// Dalam register method
$user->user_type = request('user_type'); // 'user' atau 'umkm'
```

### 3. **Buat Login Form Terpisah (Opsional UI)**
Di file login form, perlu ada:
- Toggle/Tab untuk memilih "Masuk sebagai User" atau "Masuk sebagai UMKM"
- Atau form terpisah dengan URL berbeda: `/login?type=user` dan `/login?type=umkm`

### 4. **Tambahkan RatingSection ke PublicUmkmShow.vue**
Di file `PublicUmkmShow.vue`, tambahkan import dan penggunaan:

```vue
<script setup>
import RatingSection from '@/Components/RatingSection.vue';
// ... props
</script>

<template>
  <!-- ... existing content ... -->
  
  <!-- Tambahkan sebelum closing GuestLayout -->
  <RatingSection 
    :umkm="umkm" 
    :currentUser="auth?.user" 
  />
</template>
```

### 5. **Update Sanctum Configuration (Jika Perlu)**
Pastikan CORS dan token handling sudah benar untuk axios requests.

### 6. **Seed Data (Opsional)**
Buat seeder untuk test:
```bash
php artisan tinker

# Buat user biasa
User::create(['name' => 'John', 'email' => 'john@example.com', 'password' => Hash::make('pass'), 'user_type' => 'user']);

# Buat rating
Rating::create(['umkm_id' => 1, 'user_id' => 1, 'rating' => 5, 'review' => 'Keren!']);
```

---

## 📋 Checklist Implementasi

- [ ] Run migrations
- [ ] Update AuthApiController (register/login)
- [ ] Buat/Update login form untuk support user_type
- [ ] Import RatingSection di PublicUmkmShow.vue
- [ ] Test rating functionality
- [ ] Test authorization (user vs umkm)
- [ ] Styling adjustments jika perlu

---

## 🔒 Security Notes

1. **Authorization**: Rating hanya bisa dibuat oleh user dengan `user_type = 'user'`
2. **Validation**: Rating harus 1-5, review max 1000 char
3. **Unique Constraint**: Satu user hanya bisa memberikan satu rating per UMKM
4. **Delete Permission**: User hanya bisa delete rating milik mereka sendiri

---

## 📊 Database Schema

### ratings table
```
id (PK)
umkm_id (FK)
user_id (FK)
rating (int 1-5)
review (text, nullable)
helpful_count (int, default 0)
created_at
updated_at
unique(umkm_id, user_id)
```

### users table
```
+ user_type (enum: 'user', 'umkm')
```

---

## 🚀 Next Steps

1. Implement separate login forms (UI)
2. Update authentication to handle user_type
3. Add admin panel untuk moderate reviews (optional)
4. Add email notifications untuk UMKM owner (optional)
5. Add more rating filters/sorting (optional)

---

Generated: 2025-04-18
