@echo off
echo Starting Laravel Server...
start "Laravel Server" php artisan serve

echo Starting Vite...
start "Vite Server" npm run dev

echo Development environment started!
