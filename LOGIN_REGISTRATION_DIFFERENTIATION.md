# Login & Registration Differentiation Guide

## Overview
The application now has completely differentiated login and registration forms for **regular users** (pembeli) and **UMKM owners** (pemilik UMKM).

---

## Authentication Routes

### User (Pembeli / Regular Users)
- **Login Page**: `/login/user`
  - File: `resources/js/Pages/Auth/UserLogin.vue`
  - Theme: Purple & Blue gradient
  - User Type: `user`
  - Features: Email, Password, Remember Me, Forgot Password

- **Register Page**: `/register/user`
  - File: `resources/js/Pages/Auth/UserRegister.vue`
  - Theme: Purple & Blue gradient
  - User Type: `user`
  - Features: Name, Email, Password, Terms Agreement

### UMKM (Pemilik UMKM / Business Owners)
- **Login Page**: `/login/umkm`
  - File: `resources/js/Pages/Auth/UmkmLogin.vue`
  - Theme: Blue & Indigo gradient
  - User Type: `umkm`
  - Features: Email, Password, Remember Me, Forgot Password
  - Additional Info: "👑 Portal Pelaku UMKM"

- **Register Page**: `/register/umkm`
  - File: `resources/js/Pages/Auth/UmkmRegister.vue`
  - Theme: Blue & Indigo gradient
  - User Type: `umkm`
  - Features: Name, Email, Password, Terms Agreement
  - Placeholder: "Email Bisnis" instead of just "Email"

---

## Key Differences

### Visual Differentiation
| Element | User | UMKM |
|---------|------|------|
| Primary Color | Purple (#667eea) | Blue (#2563eb) |
| Header Text | "Sebagai Pengguna Biasa" | "👑 Portal Pelaku UMKM" |
| Page Theme | Purple-Blue | Blue-Indigo |
| Form Fields | Standard | Business-focused |

### Form Fields
| Form | User | UMKM |
|------|------|------|
| Name Label | "Nama Lengkap" | "Nama Pemilik / Nama UMKM" |
| Email Label | "Email" | "Email Bisnis" |
| Password Label | Same | Same |
| Hidden Field | user_type: 'user' | user_type: 'umkm' |

### Navigation & Switching
- **From User Login**: Can switch to UMKM login via "Login sebagai UMKM" button
- **From User Register**: Can switch to UMKM register via "Daftar sebagai UMKM" button
- **From UMKM Login**: Can switch to User login via "Login Pengguna Biasa" button
- **From UMKM Register**: Can switch to User register via "Daftar Pengguna Biasa" button

---

## Database User Type
The `users` table has a `user_type` enum field:
```php
$table->enum('user_type', ['user', 'umkm'])->default('user');
```

This ensures:
- Users with `user_type='user'` can rate, review, and add to wishlist
- Users with `user_type='umkm'` can manage UMKM profiles and menus
- System can differentiate between roles for proper authorization

---

## API Endpoints & User Type Validation

### Rating System (User Only)
- `POST /api/v1/umkms/{id}/ratings` - Only `user_type='user'` can submit
- `DELETE /api/v1/ratings/{id}` - Only rating owner can delete
- `POST /api/v1/ratings/{id}/helpful` - Only authenticated users

### Wishlist System (User Only)
- `POST /api/v1/umkms/{id}/wishlist` - Only `user_type='user'` can add
- `DELETE /api/v1/umkms/{id}/wishlist` - Only `user_type='user'` can remove
- `GET /api/v1/wishlist` - Only `user_type='user'` can view
- `GET /api/v1/umkms/{id}/wishlist/check` - Check wishlist status

### UMKM Management
- `/umkm/create` - Only authenticated users
- `/umkm/{id}/edit` - Only UMKM owner
- `/umkm/{id}/menu/*` - Only UMKM owner

---

## Testing URLs

### User Authentication
```
https://localhost/login/user              → User login page
https://localhost/register/user           → User registration page
https://localhost/login                   → Redirects to /login/user
https://localhost/register                → Redirects to /register/user
```

### UMKM Authentication
```
https://localhost/login/umkm              → UMKM login page
https://localhost/register/umkm           → UMKM registration page
```

### Sample Test Cases
1. Register as User:
   - Navigate to `/register/user`
   - Fill form with user data
   - Redirect to login page after successful registration
   - Login at `/login/user`
   - Should see user dashboard

2. Register as UMKM:
   - Navigate to `/register/umkm`
   - Fill form with UMKM data
   - Redirect to login page after successful registration
   - Login at `/login/umkm`
   - Should see UMKM dashboard with menu management

3. Cross-Switching:
   - At `/login/user`, click "Login sebagai UMKM" → redirects to `/login/umkm`
   - At `/register/user`, click "Daftar sebagai UMKM" → redirects to `/register/umkm`
   - Verify same from UMKM pages to User pages

---

## Implementation Checklist

### ✅ Completed
- [x] Created separate UserLogin.vue component
- [x] Created separate UserRegister.vue component
- [x] Created separate UmkmLogin.vue component
- [x] Created separate UmkmRegister.vue component
- [x] Added routes: /login/user, /login/umkm, /register/user, /register/umkm
- [x] Added redirect routes: /login → /login/user, /register → /register/user
- [x] Updated navigation links between forms
- [x] Visual differentiation by color scheme

### ⏳ Next Steps
- [ ] Update AuthApiController to validate user_type in login/register endpoints
- [ ] Add user_type validation in RatingController (only allow 'user' type)
- [ ] Add user_type validation in WishlistController (only allow 'user' type)
- [ ] Test end-to-end authentication flows
- [ ] Update any other components linking to old /login?type=user URLs
- [ ] Run migrations to add user_type column if not exists

---

## File Locations

### Authentication Files
```
resources/js/Pages/Auth/
├── UserLogin.vue           (User login form)
├── UserRegister.vue        (User registration form)
├── UmkmLogin.vue           (UMKM login form - NEW)
├── UmkmRegister.vue        (UMKM registration form - NEW)
├── Login.vue               (Legacy - can be deprecated)
└── Register.vue            (Legacy - can be deprecated)
```

### Route Configuration
```
routes/
├── web.php                 (Includes new login/register routes)
├── auth.php                (Handles POST login/register)
└── api.php                 (API endpoints)
```

### Controllers
```
app/Http/Controllers/Auth/
├── AuthenticatedSessionController.php     (POST /login)
└── RegisteredUserController.php           (POST /register)
```

---

## Notes
- The `user_type` field is automatically set in the Vue form components
- Both forms POST to the same endpoint: `/login` and `/register`
- The controllers must validate and respect the `user_type` field
- All existing functionality (rating, wishlist) is restricted to `user_type='user'`
- UMKM management is available for `user_type='umkm'`
