@echo off
title Vahla Mill Store - Offline POS with Real-Time Auto-Sync
cd /d "d:\vahla"
echo ====================================================================
echo   VAHLA MILL STORE - OFFLINE POS ^& REAL-TIME CLOUD SYNCHRONIZER
echo ====================================================================
echo.
echo [1/3] Ensuring MySQL database is running...
start "" /B "C:\xampp\mysql\bin\mysqld.exe" --defaults-file="C:\xampp\mysql\bin\my.ini" --standalone >nul 2>&1

echo [2/3] Starting Local Web Application (http://127.0.0.1:8000)...
start "" /B php artisan serve --host=127.0.0.1 --port=8000 >nul 2>&1

echo [3/3] Starting 5-Second Real-Time Auto-Sync Daemon...
start "" /B php artisan sync:daemon --interval=5

echo.
echo ====================================================================
echo   SUCCESS! Vahla ERP is running and continuously syncing.
echo   Local URL: http://127.0.0.1:8000
echo   Cloud URL: https://test.binsultansweet.com
echo ====================================================================
echo.
pause
