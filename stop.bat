@echo off
chcp 65001 >nul
cls

echo =========================================
echo    E-UMKM Development Server Stopping...
echo =========================================
echo.

REM Kill npm dev server (node.exe)
echo [1/2] Stopping NPM development server...
taskkill /IM node.exe /F >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo   ✓ NPM server stopped
) else (
    echo   ✗ NPM server not found
)
echo.

REM Kill Laravel artisan serve (php.exe)
echo [2/2] Stopping Laravel API Server...
taskkill /IM php.exe /F >nul 2>&1
if %ERRORLEVEL% EQU 0 (
    echo   ✓ Laravel API server stopped
) else (
    echo   ✗ Laravel API server not found
)
echo.

REM Kill the cmd.exe windows created by start command
timeout /t 1 /nobreak >nul
taskkill /FI "WINDOWTITLE eq E-UMKM NPM Dev Server" /T /F >nul 2>&1
taskkill /FI "WINDOWTITLE eq E-UMKM API Server" /T /F >nul 2>&1
echo.

echo =========================================
echo    Servers Stopped Successfully!
echo =========================================
echo.
timeout /t 2 /nobreak
exit /b 0
