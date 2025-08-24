#!/bin/bash

echo "🚀 Laravel Test Runner"
echo "====================="

if [ $# -eq 0 ]; then
    echo ""
    echo "Usage: ./run_tests.sh [category]"
    echo ""
    echo "Available categories:"
    echo "  auth              Run authentication tests only"
    echo "  service           Run service management tests only"
    echo "  laundry            Run laundry management tests only"
    echo "  customer          Run customer management tests only"
    echo "  purchase          Run service purchase tests only"
    echo "  model             Run model tests only"
    echo "  middleware        Run middleware tests only"
    echo "  integration       Run integration tests only"
    echo "  all               Run all tests"
    echo ""
    echo "Examples:"
    echo "  ./run_tests.sh auth"
    echo "  ./run_tests.sh service"
    echo "  ./run_tests.sh all"
    echo ""
    exit 1
fi

case $1 in
    "auth")
        echo "🔐 Running Authentication tests..."
        php artisan test tests/Feature/ApiAuthTest.php
        php artisan test tests/Feature/AdminWebAuthTest.php
        ;;
    "service")
        echo "🛠️ Running Service Management tests..."
        php artisan test tests/Feature/ServiceTest.php
        ;;
    "laundry")
        echo "🧺 Running Laundry Management tests..."
        php artisan test tests/Feature/LaundryTest.php
        ;;
    "customer")
        echo "👤 Running Customer Management tests..."
        php artisan test tests/Feature/CustomerTest.php
        ;;
    "purchase")
        echo "🛒 Running Service Purchase tests..."
        php artisan test tests/Feature/ServicePurchaseTest.php
        ;;
    "model")
        echo "🗃️ Running Model tests..."
        php artisan test tests/Unit/ExampleTest.php
        ;;
    "middleware")
        echo "🚧 Running Middleware tests..."
        php artisan test tests/Feature/MiddlewareTest.php
        ;;
    "integration")
        echo "🔗 Running Integration tests..."
        php artisan test tests/Feature/IntegrationTest.php
        ;;
    "all")
        echo "🎯 Running ALL tests..."
        echo ""
        echo "🔐 Authentication tests..."
        php artisan test tests/Feature/ApiAuthTest.php
        php artisan test tests/Feature/AdminWebAuthTest.php
        echo ""
        echo "🛠️ Service Management tests..."
        php artisan test tests/Feature/ServiceTest.php
        echo ""
        echo "🧺 Laundry Management tests..."
        php artisan test tests/Feature/LaundryTest.php
        echo ""
        echo "👤 Customer Management tests..."
        php artisan test tests/Feature/CustomerTest.php
        echo ""
        echo "🛒 Service Purchase tests..."
        php artisan test tests/Feature/ServicePurchaseTest.php
        echo ""
        echo "🗃️ Model tests..."
        php artisan test tests/Unit/ExampleTest.php
        echo ""
        echo "🚧 Middleware tests..."
        php artisan test tests/Feature/MiddlewareTest.php
        echo ""
        echo "🔗 Integration tests..."
        php artisan test tests/Feature/IntegrationTest.php
        echo ""
        echo "✅ All tests completed!"
        ;;
    *)
        echo "❌ Invalid category: $1"
        echo ""
        echo "Available categories: auth, service, laundry, customer, purchase, model, middleware, integration"
        exit 1
        ;;
esac

echo ""
echo "🎉 Test runner finished!"
echo ""
echo "Available test files:"
echo "  tests/Feature/ApiAuthTest.php"
echo "  tests/Feature/AdminWebAuthTest.php"
echo "  tests/Feature/ServiceTest.php"
echo "  tests/Feature/LaundryTest.php"
echo "  tests/Feature/CustomerTest.php"
echo "  tests/Feature/ServicePurchaseTest.php"
echo "  tests/Unit/ExampleTest.php"
echo "  tests/Feature/MiddlewareTest.php"
echo "  tests/Feature/IntegrationTest.php"
echo ""
echo "💡 Tip: Use './run_tests.sh [category]' to run specific test categories"
