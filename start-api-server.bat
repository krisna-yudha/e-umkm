@echo off
chcp 65001 >nul
cls

echo =========================================
echo    E-UMKM Development Server Starting...
echo =========================================
echo.

REM Start npm development server in a new window
echo [1/2] Starting NPM development server...
echo.
start "E-UMKM NPM Dev Server" cmd /k npm run dev
echo.

REM Wait a moment for npm to start
timeout /t 3 /nobreak

REM Start Laravel artisan serve in a new window
echo [2/2] Starting Laravel API Server...
echo.
start "E-UMKM API Server" cmd /k php artisan serve --host=127.0.0.1 --port=8000

echo.
echo =========================================
echo    Servers Started Successfully!
echo =========================================
echo.
echo Frontend (Vite):
echo   http://localhost:5173
echo.
echo Backend API:
echo   http://127.0.0.1:8000/api
echo.
echo Available endpoints:
echo   - Health Check: GET /api/health
echo   - Authentication: /api/v1/auth/*
echo   - UMKM: /api/v1/umkm/*
echo   - Menus: /api/v1/menus/*
echo.
echo Note: Close both terminal windows to stop the servers
echo =========================================
echo.