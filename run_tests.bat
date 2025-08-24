@echo off
setlocal enabledelayedexpansion

echo 🚀 Laravel Test Runner
echo =====================

if "%1"=="" (
    echo.
    echo Usage: run_tests.bat [category]
    echo.
    echo Available categories:
    echo   auth              Run authentication tests only
    echo   service           Run service management tests only
    echo   laundry            Run laundry management tests only
    echo   customer          Run customer management tests only
    echo   purchase          Run service purchase tests only
    echo   model             Run model tests only
    echo   middleware       Run middleware tests only
    echo   integration      Run integration tests only
    echo   all              Run all tests
    echo.
    echo Examples:
    echo   run_tests.bat auth
    echo   run_tests.bat service
    echo   run_tests.bat all
    echo.
    goto :end
)

if "%1"=="auth" goto run_auth
if "%1"=="service" goto run_service
if "%1"=="laundry" goto run_laundry
if "%1"=="customer" goto run_customer
if "%1"=="purchase" goto run_purchase
if "%1"=="model" goto run_model
if "%1"=="middleware" goto run_middleware
if "%1"=="integration" goto run_integration
if "%1"=="all" goto run_all

echo ❌ Invalid category: %1
echo.
echo Available categories: auth, service, laundry, customer, purchase, model, middleware, integration
goto :end

:run_auth
echo 🔐 Running Authentication tests...
php artisan test tests/Feature/ApiAuthTest.php
php artisan test tests/Feature/AdminWebAuthTest.php
goto :end

:run_service
echo 🛠️ Running Service Management tests...
php artisan test tests/Feature/ServiceTest.php
goto :end

:run_laundry
echo 🧺 Running Laundry Management tests...
php artisan test tests/Feature/LaundryTest.php
goto :end

:run_customer
echo 👤 Running Customer Management tests...
php artisan test tests/Feature/CustomerTest.php
goto :end

:run_purchase
echo 🛒 Running Service Purchase tests...
php artisan test tests/Feature/ServicePurchaseTest.php
goto :end

:run_model
echo 🗃️ Running Model tests...
php artisan test tests/Unit/ExampleTest.php
goto :end

:run_middleware
echo 🚧 Running Middleware tests...
php artisan test tests/Feature/MiddlewareTest.php
goto :end

:run_integration
echo 🔗 Running Integration tests...
php artisan test tests/Feature/IntegrationTest.php
goto :end

:run_all
echo 🎯 Running ALL tests...
echo.
echo 🔐 Authentication tests...
php artisan test tests/Feature/ApiAuthTest.php
php artisan test tests/Feature/AdminWebAuthTest.php
echo.
echo 🛠️ Service Management tests...
php artisan test tests/Feature/ServiceTest.php
echo.
echo 🧺 Laundry Management tests...
php artisan test tests/Feature/LaundryTest.php
echo.
echo 👤 Customer Management tests...
php artisan test tests/Feature/CustomerTest.php
echo.
echo 🛒 Service Purchase tests...
php artisan test tests/Feature/ServicePurchaseTest.php
echo.
echo 🗃️ Model tests...
php artisan test tests/Unit/ExampleTest.php
echo.
echo 🚧 Middleware tests...
php artisan test tests/Feature/MiddlewareTest.php
echo.
echo 🔗 Integration tests...
php artisan test tests/Feature/IntegrationTest.php
echo.
echo ✅ All tests completed!
goto :end

:end
echo.
echo 🎉 Test runner finished!
echo.
echo Available test files:
if exist "tests\Feature\ApiAuthTest.php" (
    echo ✅ tests\Feature\ApiAuthTest.php
) else (
    echo ❌ tests\Feature\ApiAuthTest.php (missing)
)
if exist "tests\Feature\AdminWebAuthTest.php" (
    echo ✅ tests\Feature\AdminWebAuthTest.php
) else (
    echo ❌ tests\Feature\AdminWebAuthTest.php (missing)
)
if exist "tests\Feature\ServiceTest.php" (
    echo ✅ tests\Feature\ServiceTest.php
) else (
    echo ❌ tests\Feature\ServiceTest.php (missing)
)
if exist "tests\Feature\LaundryTest.php" (
    echo ✅ tests\Feature\LaundryTest.php
) else (
    echo ❌ tests\Feature\LaundryTest.php (missing)
)
if exist "tests\Feature\CustomerTest.php" (
    echo ✅ tests\Feature\CustomerTest.php
) else (
    echo ❌ tests\Feature\CustomerTest.php (missing)
)
if exist "tests\Feature\ServicePurchaseTest.php" (
    echo ✅ tests\Feature\ServicePurchaseTest.php
) else (
    echo ❌ tests\Feature\ServicePurchaseTest.php (missing)
)
if exist "tests\Unit\ExampleTest.php" (
    echo ✅ tests\Unit\ExampleTest.php
) else (
    echo ❌ tests\Unit\ExampleTest.php (missing)
)
if exist "tests\Feature\MiddlewareTest.php" (
    echo ✅ tests\Feature\MiddlewareTest.php
) else (
    echo ❌ tests\Feature\MiddlewareTest.php (missing)
)
if exist "tests\Feature\IntegrationTest.php" (
    echo ✅ tests\Feature\IntegrationTest.php
) else (
    echo ❌ tests\Feature\IntegrationTest.php (missing)
)
echo.
echo 💡 Tip: Use 'run_tests.bat [category]' to run specific test categories
echo.
pause
