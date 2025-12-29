@echo off
setlocal

echo =====================================
echo   Composer.phar Installer
echo =====================================
echo.

:: ---------- Tải installer ----------
echo [1/6] Downloading composer installer...
php -r "@copy('https://getcomposer.org/installer', 'composer-setup.php');"
if %ERRORLEVEL% neq 0 (
	echo [ERROR] Installing composer-setup.php fail.
	echo Try to turn off anti-virus application
	pause
	exit /b 1
)


:: ---------- Tải chữ ký ----------
echo [2/6] Fetching installer signature...
set "EXPECTED_SIGNATURE="
for /f "usebackq delims=" %%i in (`php -r "echo @trim(file_get_contents('https://composer.github.io/installer.sig'));"`) do set "EXPECTED_SIGNATURE=%%i"
if "%EXPECTED_SIGNATURE%"=="" (
	echo [ERROR] Unable to download installer signature.
	pause
	exit /b 1
)


:: ---------- Xác thực install ----------
echo [3/6] Verifying composer-setup installer...
php -r "if (@hash_file('sha384', 'composer-setup.php') === '%EXPECTED_SIGNATURE%') { echo '[4/6] Installer verified' . PHP_EOL; } else { echo 'Installer corrupt' . PHP_EOL; unlink('composer-setup.php'); exit(1); }"
if %ERRORLEVEL% neq 0 (
	echo [ERROR] Verify fail
	pause
	exit /b 1
)


:: ---------- Cài bộ composer.phar ---------
echo [5/6] Running installer...
php composer-setup.php
if %ERRORLEVEL% neq 0 (
	echo [ERROR] Running installer got some wrong.
	pause
	exit /b 1
)


:: --------- Dọn dẹp ------------
echo [6/6] Cleaning installer...
php -r "@unlink('composer-setup.php');"
if %ERRORLEVEL% neq 0 (
	echo [WARN] Can't remove the installer temp, pls do it manually.
)

echo.
pause
endlocal