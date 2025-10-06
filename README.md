# 🏢 E-UMKM Management System v1.0

<div align="center">

![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)
![Vue.js](https://img.shields.io/badge/Vue.js-3.x-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white)
![TypeScript](https://img.shields.io/badge/TypeScript-5.x-3178C6?style=for-the-badge&logo=typescript&logoColor=white)
![Inertia.js](https://img.shields.io/badge/Inertia.js-1.x-9553E9?style=for-the-badge&logo=inertia&logoColor=white)

![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)
![Version](https://img.shields.io/badge/Version-1.0.0-blue?style=for-the-badge)
![Status](https://img.shields.io/badge/Status-Production%20Ready-success?style=for-the-badge)

**Platform manajemen UMKM yang komprehensif dengan teknologi modern - Production Ready**

[🚀 Quick Start](#quick-start) • [📚 API Documentation](./API_DOCUMENTATION_V1.md) • [🔧 Installation](#installation) • [📡 Features](#features)

</div>

</div>

---

## 📖 Tentang Proyek

**E-UMKM Management System** adalah platform digital yang dirancang khusus untuk membantu pengelolaan Usaha Mikro, Kecil, dan Menengah (UMKM) di Indonesia. Sistem ini menyediakan solusi terintegrasi untuk manajemen bisnis, pemetaan lokasi, dan analitik komprehensif.

### ✨ Fitur Utama

#### 🎯 **Manajemen UMKM**
- ✅ **Registrasi & Profil UMKM** - Pendaftaran mudah dengan validasi lengkap
- ✅ **Kategori Bisnis** - Klasifikasi berdasarkan jenis usaha
- ✅ **Status Management** - Monitoring status aktif/nonaktif UMKM
- ✅ **Upload Media** - Galeri foto produk dan tempat usaha

#### 🍽️ **Manajemen Menu & Produk**
- ✅ **Katalog Menu** - Daftar produk/layanan dengan foto
- ✅ **Manajemen Harga** - Sistem pricing yang fleksibel
- ✅ **Status Ketersediaan** - Real-time availability tracking
- ✅ **Kategori Produk** - Organisasi menu yang terstruktur

#### 🗺️ **Pemetaan & Lokasi**
- ✅ **Interactive Maps** - Integrasi Leaflet untuk pemetaan
- ✅ **Geolocation** - Pencarian berdasarkan koordinat
- ✅ **Nearby Search** - Pencarian UMKM terdekat dengan radius
- ✅ **Location Analytics** - Analisis sebaran geografis

#### 👥 **Multi-Role System**
- ✅ **Admin Dashboard** - Panel kontrol penuh untuk administrator
- ✅ **UMKM Owner** - Interface khusus pemilik usaha
- ✅ **Public Access** - Akses publik untuk pencarian UMKM

#### 📊 **Analytics & Reporting**
- ✅ **Dashboard Statistics** - Visualisasi data real-time
- ✅ **Monthly Reports** - Laporan bulanan pendaftaran
- ✅ **Category Analytics** - Analisis berdasarkan kategori bisnis
- ✅ **Export Functions** - Export data ke Excel/PDF

#### 🔐 **Security & Authentication**
- ✅ **Laravel Sanctum** - Token-based authentication
- ✅ **Role-based Access** - Kontrol akses berdasarkan peran
- ✅ **Secure File Upload** - Validasi dan enkripsi file
- ✅ **CSRF Protection** - Proteksi dari serangan CSRF

---

## 🛠️ Tech Stack

### Backend
- **Framework**: Laravel 11.x
- **Language**: PHP 8.2+
- **Database**: SQLite (dev) / MySQL (production)
- **Authentication**: Laravel Sanctum
- **Storage**: Laravel File Storage

### Frontend
- **Framework**: Vue.js 3.x
- **Language**: TypeScript 5.x
- **Build Tool**: Vite
- **SSR**: Inertia.js
- **Styling**: Tailwind CSS 3.x

### API & Integration
- **REST API**: Laravel API Resources
- **Documentation**: OpenAPI/Swagger
- **Testing**: Postman Collection
- **Maps**: Leaflet.js

---

## 🚀 Quick Start

### Prasyarat

- PHP 8.2 atau lebih tinggi
- Composer
- Node.js 18+ & NPM
- SQLite/MySQL

### Instalasi

1. **Clone Repository**
   ```bash
   git clone https://github.com/krisna-yudha/e-umkm.git
   cd e-umkm-backendV1
   ```

2. **Install Dependencies**
   ```bash
   # Backend dependencies
   composer install
   
   # Frontend dependencies
   npm install
   ```

3. **Environment Setup**
   ```bash
   # Copy environment file
   cp .env.example .env
   
   # Generate application key
   php artisan key:generate
   ```

4. **Database Setup**
   ```bash
   # Run migrations
   php artisan migrate
   
   # Seed database (optional)
   php artisan db:seed
   ```

5. **Build Assets**
   ```bash
   # Development build
   npm run dev
   
   # Production build
   npm run build
   ```

6. **Start Development Server**
   ```bash
   # Start Laravel server
   php artisan serve
   
   # Start Vite dev server (separate terminal)
   npm run dev
   ```

   Akses aplikasi di: `http://127.0.0.1:8000`

---

## 📡 API Documentation

### Base URL
```
http://127.0.0.1:8000/api
```

### Authentication
API menggunakan Laravel Sanctum dengan Bearer Token:
```bash
Authorization: Bearer {your-token}
```

### Endpoints Overview

| Category | Endpoints         | Description                       |
|----------|-------------------|-----------------------------------|
| **Auth** | `/v1/auth/*`      | Authentication & user management  |
| **UMKM** | `/v1/umkm/*`      | UMKM CRUD operations & search     |
| **Menu** | `/v1/menus/*`     | Menu/product management           |
| **Utils**| `/health`, `/user`| Utility endpoints                 |

#### Quick API Test
```bash
# Health check
curl -X GET "http://127.0.0.1:8000/api/health"

# Get all UMKM
curl -X GET "http://127.0.0.1:8000/api/v1/umkm"

# Register user
curl -X POST "http://127.0.0.1:8000/api/v1/auth/register" \
  -H "Content-Type: application/json" \
  -d '{"name":"Test User","email":"test@example.com","password":"password123","password_confirmation":"password123"}'
```

#### Postman Collection
Import file `E-UMKM-API.postman_collection.json` untuk testing lengkap.

---

## 📁 Project Structure

```
e-umkm-backendV1/
├── app/
│   ├── Http/Controllers/
│   │   ├── Api/              # API Controllers
│   │   ├── AdminController.php
│   │   ├── UmkmController.php
│   │   └── UmkmMenuController.php
│   ├── Models/               # Eloquent Models
│   └── Middleware/           # Custom Middleware
├── resources/
│   ├── js/
│   │   ├── Pages/            # Vue.js Pages
│   │   │   ├── Admin/        # Admin Dashboard
│   │   │   ├── Umkm/         # UMKM Management
│   │   │   └── Public/       # Public Pages
│   │   └── Components/       # Reusable Components
│   └── css/                  # Styling
├── routes/
│   ├── web.php               # Web Routes
│   └── api.php               # API Routes
├── database/
│   ├── migrations/           # Database Migrations
│   └── seeders/              # Database Seeders
└── public/                   # Static Assets
```

---

## 🎨 Screenshots

### Dashboard Admin
- **Overview Statistics**: Statistik real-time UMKM
- **Management Panel**: Kontrol penuh data UMKM
- **Analytics**: Grafik dan laporan visual

### UMKM Management
- **CRUD Operations**: Create, Read, Update, Delete UMKM
- **Media Upload**: Upload foto dan dokumen
- **Location Mapping**: Integrasi peta interaktif

### Public Interface
- **UMKM Directory**: Direktori publik UMKM
- **Search & Filter**: Pencarian dengan berbagai filter
- **Detail View**: Informasi lengkap UMKM dan menu

---

## 🧪 Testing

### Manual Testing
```bash
# Run PHP syntax check
php -l app/Http/Controllers/Api/*.php

# Test routes
php artisan route:list

# Clear cache
php artisan config:clear && php artisan route:clear
```

### API Testing
```bash
# Start server
php artisan serve

# Import Postman collection
# File: E-UMKM-API.postman_collection.json

# Test endpoints in order:
# 1. Health check
# 2. Register/Login
# 3. UMKM operations
# 4. Menu operations
```

---

## 📊 Database Schema

### Core Tables
- **users** - User accounts & roles
- **umkms** - UMKM business data
- **umkm_menus** - Menu/product items
- **personal_access_tokens** - API authentication

### Relationships
- User `hasMany` Umkm
- Umkm `hasMany` UmkmMenu
- User `hasMany` PersonalAccessToken

---

## 🤝 Contributing

1. Fork repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

### Development Guidelines
- Follow PSR-12 coding standards
- Write descriptive commit messages
- Add tests for new features
- Update documentation

---

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 🙏 Acknowledgments

- **Laravel Team** - Amazing PHP framework
- **Vue.js Team** - Reactive frontend framework
- **Inertia.js** - Modern monolith architecture
- **Tailwind CSS** - Utility-first CSS framework
- **Leaflet** - Open-source mapping library

---

## 📞 Support

- **Issues**: [GitHub Issues](https://github.com/krisna-yudha/e-umkm/issues)
- **Documentation**: [API Documentation](./README_API.md)
- **Email**: support@e-umkm.com

---

<div align="center">

**Made with ❤️ for Indonesian UMKM**

⭐ **Star this repo if you find it helpful!** ⭐

</div>

---

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Redberry](https://redberry.international/laravel-development)**
- **[Active Logic](https://activelogic.com)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
