@echo off
cd /d "%~dp0"
set PATH=C:\xampp\php;%PATH%
echo Server berjalan di http://127.0.0.1:8000  (tekan Ctrl+C untuk berhenti)
start "" http://127.0.0.1:8000
php artisan serve
