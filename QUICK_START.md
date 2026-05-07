# ⚡ QUICK START GUIDE - Rating System

## 🚀 Start Here!

### Step 1: Run Database Migrations (2 minutes)
```bash
cd c:\xampp\htdocs\prokerv1\e-umkm-backendV1
php artisan migrate
```

### Step 2: Update AuthApiController (10 minutes)
**File**: `app/Http/Controllers/Api/AuthApiController.php`

Add `user_type` support in both `register()` and `login()` methods. 
👉 See full code in: **IMPLEMENTATION_STEPS.md** (lines 60-100)

### Step 3: Create UMKM Auth Forms (10 minutes)
Copy `UserLogin.vue` → `UmkmLogin.vue` and update:
- Title: "Login UMKM"
- Form field: `user_type: 'umkm'`
- Links: Point to UMKM register

Same for `UserRegister.vue` → `UmkmRegister.vue`

### Step 4: Update Web Routes (5 minutes)
**File**: `routes/web.php`

Copy routes block from: **IMPLEMENTATION_STEPS.md** (lines 100-130)

### Step 5: Integrate RatingSection (5 minutes)
**File**: `resources/js/Pages/Public/PublicUmkmShow.vue`

Add these 2 things:
```vue
<script setup>
import RatingSection from '@/Components/RatingSection.vue'; // Add this
</script>

<template>
  <!-- ... inside GuestLayout, before closing tag ... -->
  <RatingSection :umkm="umkm" :current-user="auth?.user" />
</template>
```

### Step 6: Test & Deploy (30 minutes)
- Register as user: http://localhost/register?type=user
- Login as user: http://localhost/login?type=user
- View UMKM and submit rating
- Test API with Postman

---

## ✅ Files Already Created For You

### Vue Components (Ready to Use)
- ✅ `resources/js/Components/RatingSection.vue` - Rating display & submission
- ✅ `resources/js/Pages/Auth/UserLogin.vue` - User login form
- ✅ `resources/js/Pages/Auth/UserRegister.vue` - User registration form

### Database & Models (Ready to Deploy)
- ✅ `database/migrations/2025_10_01_000000_add_user_type_to_users_table.php`
- ✅ `database/migrations/2025_10_01_000001_create_ratings_table.php`
- ✅ `app/Models/Rating.php`
- ✅ `app/Models/User.php` (updated)
- ✅ `app/Models/Umkm.php` (updated)

### API Controller (Ready to Use)
- ✅ `app/Http/Controllers/RatingController.php` - All rating endpoints

### API Routes (Already Updated)
- ✅ `routes/api.php` - Rating routes added

---

## 📋 Things You Still Need To Do

| # | Task | Time | Difficulty |
|---|------|------|------------|
| 1 | Run migrations | 1 min | ⭐ Easy |
| 2 | Update AuthApiController | 10 min | ⭐⭐ Medium |
| 3 | Create UmkmLogin.vue | 5 min | ⭐ Easy |
| 4 | Create UmkmRegister.vue | 5 min | ⭐ Easy |
| 5 | Add routes to web.php | 5 min | ⭐ Easy |
| 6 | Integrate RatingSection | 5 min | ⭐ Easy |
| 7 | Test everything | 30 min | ⭐⭐ Medium |

**Total Time**: ~60 minutes

---

## 🔗 Key Files Reference

| Need | File | What To Do |
|------|------|-----------|
| Code examples | IMPLEMENTATION_STEPS.md | Copy-paste ready code |
| File inventory | COMPLETE_FILE_INVENTORY.md | See what was created |
| API endpoints | RATING_IMPLEMENTATION_GUIDE.md | Test URLs and responses |
| Rating component | resources/js/Components/RatingSection.vue | Already complete, just use it |

---

## ✨ What This System Does

### For Regular Users
- 🔐 Separate login/register (not mixed with UMKM)
- ⭐ Give 1-5 star ratings to UMKMs
- 💬 Write reviews/comments (optional)
- 👍 Mark helpful reviews
- 👤 View profile of who rated

### For UMKM Owners
- 📊 See all ratings & reviews of their UMKM
- 📈 View rating statistics (average, distribution)
- 🔗 Separate login from regular users

### For Visitors (Not logged in)
- 👀 View all ratings & reviews
- 📊 See rating statistics
- 🔐 Login prompt when trying to rate

---

## 🛠️ Tools You'll Need

- **Text Editor**: VS Code (you have this)
- **Database Client**: phpMyAdmin (comes with XAMPP)
- **API Testing**: Postman (free download)
- **Terminal**: PowerShell or Command Prompt

---

## 🚨 Common Issues & Fixes

### "user_type column doesn't exist"
→ Run: `php artisan migrate`

### "RatingController not found"
→ Check import in routes/api.php

### "Vue component not found"
→ Check file path matches exactly

### Rating form won't submit
→ Check browser console (F12) for errors
→ Check Laravel logs: `storage/logs/laravel.log`

---

## 📞 Need Help?

1. **Check Documentation**: Read IMPLEMENTATION_STEPS.md
2. **Check Logs**: `storage/logs/laravel.log`
3. **Check Console**: Open DevTools (F12) in browser
4. **Check Database**: Open phpMyAdmin and verify tables exist

---

## 🎯 Success Criteria

You'll know it's working when:
✅ User can register as "user" type
✅ User can login and get token
✅ Ratings show on UMKM detail page
✅ User can submit a 5-star rating
✅ Rating appears in the list immediately
✅ Average rating updates correctly
✅ Other users can see the review

---

**Estimated Time to Complete: 1 hour**

Start with Step 1 now! 👇

```bash
php artisan migrate
```

Good luck! 🚀
