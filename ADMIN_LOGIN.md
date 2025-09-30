# Admin Login Guide

## Login Credentials
- **Email**: `admin@admin.com`
- **Password**: `admin`
- **Role**: `admin`

## API Endpoint
```
POST /api/v1/auth/login
```

## Request Format
```json
{
    "email": "admin@admin.com",
    "password": "admin"
}
```

## Headers Required
```
Content-Type: application/json
Accept: application/json
```

## Response Success (200)
```json
{
    "status": "success",
    "message": "Login successful",
    "data": {
        "user": {
            "id": 5,
            "name": "Administrator",
            "email": "admin@admin.com",
            "profile_photo": null,
            "role": "admin",
            "email_verified_at": "2025-09-30T16:38:14.000000Z",
            "created_at": "2025-09-30T16:38:14.000000Z",
            "updated_at": "2025-09-30T16:38:14.000000Z"
        },
        "token": "1|SkkOaKQAJ5YoAkqSWzMQvDgb1HOydkCqgJxZgSjTdba3a7ba",
        "token_type": "Bearer"
    }
}
```

## Using the Token
Include the token in Authorization header for protected endpoints:
```
Authorization: Bearer 1|SkkOaKQAJ5YoAkqSWzMQvDgb1HOydkCqgJxZgSjTdba3a7ba
```

## Testing in Postman
1. **Method**: POST
2. **URL**: `http://yourdomain.com/api/v1/auth/login`
3. **Headers**:
   - `Content-Type: application/json`
   - `Accept: application/json`
4. **Body** (raw JSON):
   ```json
   {
       "email": "admin@admin.com",
       "password": "admin"
   }
   ```

## Testing via cURL
```bash
curl -X POST http://yourdomain.com/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "admin@admin.com",
    "password": "admin"
  }'
```

## All Available Users
After seeding, these users are available:

### Admin User
- **Email**: `admin@admin.com`
- **Password**: `admin`
- **Role**: `admin`

### UMKM Users
- **Email**: `sari.dewi@example.com`
- **Password**: `password`
- **Role**: `umkm`

- **Email**: `budi.santoso@example.com`
- **Password**: `password`
- **Role**: `umkm`

- **Email**: `rina.kartika@example.com`
- **Password**: `password`
- **Role**: `umkm`

### Regular User
- **Email**: `user@example.com`
- **Password**: `password`
- **Role**: `user`

## Common Issues

### 1. Route Not Found (404)
Make sure you're using the correct URL with `/api/v1/` prefix.

### 2. Validation Failed (422)
Ensure you're sending data as JSON in the request body, not as form data.

### 3. Invalid Credentials (401)
Double-check the email and password. Default admin credentials are:
- Email: `admin@admin.com`
- Password: `admin`

### 4. Internal Server Error (500)
Check that:
- Database is properly configured
- Admin user exists in database (run `php artisan db:seed --class=AdminUserSeeder`)
- Laravel is properly configured

## Reset Admin User
If you need to recreate the admin user:
```bash
php artisan db:seed --class=AdminUserSeeder
```