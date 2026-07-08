@echo off
title Laravel Project Setup
setlocal enabledelayedexpansion

echo =====================================================
echo Starting Laravel Project Setup...
echo =====================================================
echo.

:: -------------------------
:: 1. Composer install
:: -------------------------
echo Installing Composer packages...
composer install
IF ERRORLEVEL 1 (
    echo [ERROR] Composer install failed!
    pause
    exit /b 1
)
echo Composer packages installed.
echo.

:: -------------------------
:: 2. Copy .env if missing
:: -------------------------
IF NOT EXIST ".env" (
    echo Creating .env from .env.example...
    copy .env.example .env
) ELSE (
    echo .env already exists, skipping.
)
echo.

:: -------------------------
:: 3. Configure Database
:: -------------------------
set /p DB_NAME=Enter Database name (e.g., veggie): 
set /p DB_USER=Enter Database user (e.g., root): 
set /p DB_PASS=Enter Database password (leave empty if none): 

:: -------------------------
:: 4. Update .env safely using batch
:: -------------------------
(for /f "usebackq tokens=1,* delims==" %%A in (`type .env`) do (
    set "line=%%A=%%B"
    if "%%A"=="DB_DATABASE" set "line=DB_DATABASE=!DB_NAME!"
    if "%%A"=="DB_USERNAME" set "line=DB_USERNAME=!DB_USER!"
    if "%%A"=="DB_PASSWORD" set "line=DB_PASSWORD=!DB_PASS!"
    echo !line!
)) > .env.temp
move /Y .env.temp .env
echo .env updated with database info.
echo.

:: -------------------------
:: 5. Generate APP_KEY
:: -------------------------
echo Generating APP_KEY...
php artisan key:generate
IF ERRORLEVEL 1 (
    echo [ERROR] php artisan key:generate failed!
    pause
    exit /b 1
)
echo APP_KEY generated.
echo.

:: -------------------------
:: 6. Run migrations and seeders
:: -------------------------
echo Running migrations & seeders...
php artisan migrate --seed
IF ERRORLEVEL 1 (
    echo [ERROR] Migration or seeder failed!
    pause
    exit /b 1
)
echo Migration & seed completed.
echo.

:: -------------------------
:: 7. Start Laravel server in new CMD
:: -------------------------
echo Starting Laravel server in a new window...
start cmd /k "php artisan serve"
echo Laravel server should be running.
echo.

echo Setup complete! Press any key to exit this window.
pause
