@echo off
title Blood Bank - Local Server
cd /d "%~dp0"
set PORT=8000

rem ---- find PHP (PATH first, then XAMPP) ----
set "PHPEXE="
where php >nul 2>nul && set "PHPEXE=php"
if not defined PHPEXE if exist "C:\xampp\php\php.exe" set "PHPEXE=C:\xampp\php\php.exe"
if not defined PHPEXE goto nophp

rem ---- check if port is already listening ----
netstat -ano -p tcp | findstr /C:":%PORT% " | findstr LISTENING >nul
if not errorlevel 1 (
  echo [INFO] Server is already running on port %PORT%. Opening browser...
  start "" "http://localhost:%PORT%/"
  goto :eof
)

rem ---- start MySQL (XAMPP) if it is not running ----
netstat -ano -p tcp | findstr /C:":3306 " | findstr LISTENING >nul
if errorlevel 1 (
  if exist "C:\xampp\mysql\bin\mysqld.exe" (
    echo [INFO] Starting MySQL daemon...
    start "MySQL" /min /d "C:\xampp" "C:\xampp\mysql\bin\mysqld.exe" --defaults-file="C:\xampp\mysql\bin\my.ini" --standalone
    set /a attempts=0
    :wait_mysql
    set /a attempts+=1
    timeout /t 1 /nobreak >nul
    netstat -ano -p tcp | findstr /C:":3306 " | findstr LISTENING >nul
    if errorlevel 1 (
      if %attempts% lss 15 goto wait_mysql
      echo.
      echo [ERROR] MySQL could not be started automatically on port 3306.
      echo Please start MySQL manually from the XAMPP Control Panel and run start.bat again.
      pause
      goto :eof
    )
    echo [INFO] MySQL started successfully.
  ) else (
    echo.
    echo [ERROR] MySQL is not running on port 3306 and XAMPP was not found at C:\xampp.
    echo Please start your MySQL / MariaDB server and run start.bat again.
    pause
    goto :eof
  )
)

rem ---- create database + sample data on first run ----
echo [INFO] Checking database setup...
"%PHPEXE%" tools\setup_db.php
if errorlevel 1 goto dberr

echo.
echo [INFO] Starting Blood Bank web server on port %PORT%...
start "Blood Bank Web Server (Port %PORT%)" "%PHPEXE%" -S 0.0.0.0:%PORT% -t src

set /a p_attempts=0
:wait_php
set /a p_attempts+=1
timeout /t 1 /nobreak >nul
netstat -ano -p tcp | findstr /C:":%PORT% " | findstr LISTENING >nul
if errorlevel 1 (
  if %p_attempts% lss 6 goto wait_php
  echo.
  echo [ERROR] Failed to start PHP server on port %PORT%.
  echo Port %PORT% may be blocked or occupied by another application.
  pause
  goto :eof
)

echo.
echo ==========================================================
echo   Blood Bank is live at: http://localhost:%PORT%/
echo   The browser will now open automatically.
echo   To stop the server, close the "Blood Bank Web Server" window.
echo ==========================================================
start "" "http://localhost:%PORT%/"
timeout /t 3 /nobreak >nul
goto :eof

:nophp
echo.
echo [ERROR] PHP was not found in PATH or at C:\xampp\php\php.exe.
echo Please install XAMPP from https://www.apachefriends.org/ or add PHP to your PATH.
pause
goto :eof

:dberr
echo.
echo [ERROR] Database setup failed. Please check the error above, ensure MySQL is running, then run start.bat again.
pause
goto :eof
