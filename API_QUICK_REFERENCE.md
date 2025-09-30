# Quick API Reference Table

| No | Endpoint | Method | URL | Auth Required | Description |
|----|----------|--------|-----|---------------|-------------|
| 1 | Register | POST | `/api/v1/auth/register` | ❌ | Register new user |
| 2 | Login | POST | `/api/v1/auth/login` | ❌ | User login |
| 3 | Profile | GET | `/api/v1/auth/profile` | ✅ | Get user profile |
| 4 | Update Profile | PUT | `/api/v1/auth/profile` | ✅ | Update user profile |
| 5 | Logout | POST | `/api/v1/auth/logout` | ✅ | Logout current device |
| 6 | Logout All | POST | `/api/v1/auth/logout-all` | ✅ | Logout all devices |
| 7 | Get UMKM | GET | `/api/v1/umkm` | ❌ | Get all UMKM |
| 8 | Get UMKM by ID | GET | `/api/v1/umkm/{id}` | ❌ | Get specific UMKM |
| 9 | Create UMKM | POST | `/api/v1/umkm` | ✅ | Create new UMKM |
| 10 | Update UMKM | PUT | `/api/v1/umkm/{id}` | ✅ | Update UMKM |
| 11 | Delete UMKM | DELETE | `/api/v1/umkm/{id}` | ✅ | Delete UMKM |
| 12 | Statistics | GET | `/api/v1/umkm/statistics/data` | ❌ | Get UMKM statistics |
| 13 | Nearby UMKM | POST | `/api/v1/umkm/nearby` | ❌ | Find nearby UMKM |
| 14 | All Menus | GET | `/api/v1/menus` | ❌ | Get all menus |
| 15 | UMKM Menus | GET | `/api/v1/menus/umkm/{umkmId}` | ❌ | Get menus by UMKM |
| 16 | Menu Detail | GET | `/api/v1/menus/umkm/{umkmId}/{menuId}` | ❌ | Get specific menu |
| 17 | Create Menu | POST | `/api/v1/menus/umkm/{umkmId}` | ✅ | Create new menu |
| 18 | Update Menu | PUT | `/api/v1/menus/umkm/{umkmId}/{menuId}` | ✅ | Update menu |
| 19 | Delete Menu | DELETE | `/api/v1/menus/umkm/{umkmId}/{menuId}` | ✅ | Delete menu |
| 20 | Health Check | GET | `/api/health` | ❌ | API health status |
| 21 | User Info | GET | `/api/user` | ✅ | Get authenticated user |

## Postman Environment Variables
Set these in Postman Environment:
- `base_url`: `http://127.0.0.1:8000/api`
- `token`: `[token from login response]`

## Testing Priority Order:
1. **Health Check** (Test API is running)
2. **Register** (Create test user)
3. **Login** (Get authentication token)
4. **Get UMKM** (Test public endpoints)
5. **Create UMKM** (Test protected endpoints)
6. **Create Menu** (Test related data)
7. **Statistics** (Test aggregated data)

## Status Codes:
- `200` - Success
- `201` - Created
- `401` - Unauthorized
- `404` - Not Found
- `422` - Validation Error
- `500` - Server Error