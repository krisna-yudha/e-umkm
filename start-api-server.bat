@echo off
echo =========================================
echo    E-UMKM API Server Starting...
echo =========================================
echo.
echo API will be available at:
echo http://127.0.0.1:8000/api
echo.
echo Available endpoints:
echo - Health Check: GET /api/health
echo - Authentication: /api/v1/auth/*
echo - UMKM: /api/v1/umkm/*  
echo - Menus: /api/v1/menus/*
echo.
echo Press Ctrl+C to stop the server
echo =========================================
echo.

php artisan serve --host=127.0.0.1 --port=8000