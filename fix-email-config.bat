@echo off
REM This script will help fix your .env file for email configuration

echo ============================================
echo MyStore - Email Configuration Fixer
echo ============================================
echo.

cd /d "%~dp0"

echo Current directory: %CD%
echo.

echo Please choose an email option:
echo.
echo 1. Mailtrap (Testing - Recommended for Development)
echo 2. Gmail SMTP (Production - Requires Gmail App Password)
echo 3. Log Only (Emails will only appear in laravel.log)
echo.

set /p choice="Enter your choice (1, 2, or 3): "

if "%choice%"=="1" goto mailtrap
if "%choice%"=="2" goto gmail
if "%choice%"=="3" goto logonly
goto invalid

:mailtrap
echo.
echo You chose Mailtrap!
echo.
echo Please sign up at https://mailtrap.io if you haven't already.
echo Then get your SMTP credentials from your inbox settings.
echo.
set /p mt_user="Enter your Mailtrap username: "
set /p mt_pass="Enter your Mailtrap password: "

echo.
echo Creating .env.mail file with Mailtrap configuration...
(
echo MAIL_MAILER=smtp
echo MAIL_HOST=sandbox.smtp.mailtrap.io
echo MAIL_PORT=2525
echo MAIL_USERNAME=%mt_user%
echo MAIL_PASSWORD=%mt_pass%
echo MAIL_ENCRYPTION=tls
echo MAIL_FROM_ADDRESS=noreply@mystore.com
echo MAIL_FROM_NAME="MyStore"
) > .env.mail

echo.
echo ✓ Configuration saved to .env.mail
echo.
echo NEXT STEPS:
echo 1. Open your .env file
echo 2. Find the MAIL_ lines (around line 25-35)
echo 3. Replace them with the content from .env.mail
echo 4. Save the .env file
echo 5. Run: php artisan config:clear
echo.
pause
goto end

:gmail
echo.
echo You chose Gmail SMTP!
echo.
echo IMPORTANT: You need to enable 2FA and create an App Password
echo Visit: https://myaccount.google.com/apppasswords
echo.
set /p gmail_email="Enter your Gmail address: "
set /p gmail_pass="Enter your 16-character App Password: "

echo.
echo Creating .env.mail file with Gmail configuration...
(
echo MAIL_MAILER=smtp
echo MAIL_HOST=smtp.gmail.com
echo MAIL_PORT=587
echo MAIL_USERNAME=%gmail_email%
echo MAIL_PASSWORD=%gmail_pass%
echo MAIL_ENCRYPTION=tls
echo MAIL_FROM_ADDRESS=%gmail_email%
echo MAIL_FROM_NAME="MyStore"
) > .env.mail

echo.
echo ✓ Configuration saved to .env.mail
echo.
echo NEXT STEPS:
echo 1. Open your .env file
echo 2. Find the MAIL_ lines (around line 25-35)
echo 3. Replace them with the content from .env.mail
echo 4. Save the .env file
echo 5. Run: php artisan config:clear
echo.
pause
goto end

:logonly
echo.
echo You chose Log Only (emails will appear in laravel.log)
echo.
echo Creating .env.mail file with Log configuration...
(
echo MAIL_MAILER=log
echo MAIL_HOST=127.0.0.1
echo MAIL_PORT=2525
echo MAIL_USERNAME=null
echo MAIL_PASSWORD=null
echo MAIL_ENCRYPTION=null
echo MAIL_FROM_ADDRESS=hello@example.com
echo MAIL_FROM_NAME="MyStore"
) > .env.mail

echo.
echo ✓ Configuration saved to .env.mail
echo.
echo NEXT STEPS:
echo 1. Open your .env file
echo 2. Find the MAIL_ lines (around line 25-35)
echo 3. Replace them with the content from .env.mail
echo 4. Save the .env file
echo 5. Run: php artisan config:clear
echo.
pause
goto end

:invalid
echo.
echo Invalid choice! Please run the script again and choose 1, 2, or 3.
echo.
pause
goto end

:end
echo.
echo Script completed!
echo.
