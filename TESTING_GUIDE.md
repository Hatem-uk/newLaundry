# 🧪 **Laravel Service System - Comprehensive Testing Guide**

## 📋 **Overview**

This guide provides comprehensive testing coverage for the entire Laravel Service System, including:
- **Authentication & Authorization** tests
- **Model & Relationship** tests  
- **API Controller** tests
- **Business Logic** tests
- **Integration & Workflow** tests
- **Middleware & Security** tests

---

## 🚀 **Quick Start**

### **1. Run All Tests**
```bash
php artisan test
```

### **2. Run Specific Test Categories**
```bash
# Authentication tests
php artisan test tests/Feature/AuthTest.php

# Service management tests
php artisan test tests/Feature/ServiceTest.php

# Company operations tests
php artisan test tests/Feature/CompanyTest.php

# Customer operations tests
php artisan test tests/Feature/CustomerTest.php

# Service purchase tests
php artisan test tests/Feature/ServicePurchaseTest.php

# Transaction system tests
php artisan test tests/Feature/TransactionTest.php

# Model tests
php artisan test tests/Feature/ModelTest.php

# Middleware tests
php artisan test tests/Feature/MiddlewareTest.php

# Integration tests
php artisan test tests/Feature/IntegrationTest.php
```

### **3. Run Tests with Coverage**
```bash
php artisan test --coverage --min=80
```

---

## 📊 **Test Coverage Breakdown**

### **🔐 Authentication & Authorization Tests (`AuthTest.php`)**
- **User Registration**: Tests for all user roles (admin, company, customer)
- **User Login**: Valid and invalid credential testing
- **Validation**: Input validation and error handling
- **Coin System**: Customer coin allocation on registration

**Test Methods:**
- `test_admin_user_can_register()`
- `test_company_user_can_register()`
- `test_customer_user_can_register()`
- `test_user_can_login_with_valid_credentials()`
- `test_user_cannot_login_with_invalid_credentials()`
- `test_registration_validation_errors()`
- `test_login_validation_errors()`
- `test_customer_gets_1000_coins_on_registration()`

---

### **🏢 Service Management Tests (`ServiceTest.php`)**
- **Company Service Operations**: Create, read, update, delete services
- **Admin Approval Workflow**: Service approval and rejection
- **Status Management**: Pending, approved, rejected service states
- **Validation**: Input validation and business rule enforcement

**Test Methods:**
- `test_company_can_create_service()`
- `test_company_can_view_their_services()`
- `test_company_can_update_their_service()`
- `test_company_cannot_update_approved_service()`
- `test_admin_can_approve_service()`
- `test_admin_can_reject_service()`
- `test_service_creation_validation()`
- `test_unauthorized_access_to_company_services()`

---

### **🏢 Company Operations Tests (`CompanyTest.php`)**
- **Profile Management**: View and update company information
- **Worker Management**: Add, view, and approve workers
- **Access Control**: Role-based permissions and data isolation
- **Validation**: Input validation and business rules

**Test Methods:**
- `test_company_can_view_profile()`
- `test_company_can_update_profile()`
- `test_company_can_add_worker()`
- `test_company_can_view_workers()`
- `test_company_can_approve_worker()`
- `test_worker_creation_validation()`
- `test_company_can_only_approve_their_own_workers()`

---

### **👤 Customer Operations Tests (`CustomerTest.php`)**
- **Profile Management**: View and update customer information
- **Coin System**: Balance checking and transaction history
- **Data Filtering**: Transaction filtering by type and status
- **Pagination**: Transaction history pagination

**Test Methods:**
- `test_customer_can_view_profile()`
- `test_customer_can_update_profile()`
- `test_customer_can_view_coin_balance()`
- `test_customer_can_view_transaction_history()`
- `test_customer_can_filter_transactions_by_type()`
- `test_customer_can_paginate_transactions()`
- `test_customer_can_only_see_their_own_transactions()`

---

### **🛍️ Service Purchase Tests (`ServicePurchaseTest.php`)**
- **Service Browsing**: View available services and details
- **Purchase Workflow**: Service purchase with coin deduction
- **Order Management**: Purchase history and order cancellation
- **Error Handling**: Insufficient coins and invalid service scenarios

**Test Methods:**
- `test_customer_can_browse_available_services()`
- `test_customer_can_purchase_service()`
- `test_customer_cannot_purchase_service_with_insufficient_coins()`
- `test_customer_can_cancel_order()`
- `test_customer_cannot_cancel_completed_order()`
- `test_purchase_creates_correct_transaction_references()`
- `test_cancellation_creates_correct_refund_transaction()`

---

### **💰 Transaction System Tests (`TransactionTest.php`)**
- **Coin Management**: Add, deduct, refund, and bonus coins
- **Transaction Tracking**: Balance tracking and transaction history
- **Data Integrity**: Transaction validation and error handling
- **Performance**: Large amount handling and performance testing

**Test Methods:**
- `test_customer_can_add_coins()`
- `test_customer_can_deduct_coins()`
- `test_customer_cannot_deduct_more_coins_than_available()`
- `test_customer_can_refund_coins()`
- `test_transaction_balance_tracking()`
- `test_transaction_scopes()`
- `test_transaction_accessors()`
- `test_transaction_performance_with_large_amounts()`

---

### **🏗️ Model Tests (`ModelTest.php`)**
- **Relationships**: All model relationships and associations
- **Scopes**: Query scopes for filtering and data retrieval
- **Accessors**: Formatted data output and computed attributes
- **Methods**: Business logic methods and state management
- **Factory States**: Model factory states and data generation

**Test Methods:**
- `test_user_model_relationships()`
- `test_customer_model_relationships()`
- `test_company_model_relationships()`
- `test_service_model_scopes()`
- `test_transaction_model_scopes()`
- `test_customer_model_accessors()`
- `test_service_order_model_methods()`
- `test_model_factory_states()`

---

### **🔒 Middleware Tests (`MiddlewareTest.php`)**
- **Authentication**: Token validation and unauthorized access
- **Authorization**: Role-based access control for all endpoints
- **Route Protection**: Public vs. protected route access
- **Performance**: Middleware performance and concurrent request handling

**Test Methods:**
- `test_unauthenticated_user_cannot_access_protected_routes()`
- `test_customer_can_access_customer_routes()`
- `test_customer_cannot_access_company_routes()`
- `test_admin_can_access_all_routes()`
- `test_middleware_performance()`
- `test_concurrent_requests_with_middleware()`

---

### **🔄 Integration Tests (`IntegrationTest.php`)**
- **Complete Workflows**: End-to-end business process testing
- **Data Consistency**: Cross-model data integrity verification
- **Error Scenarios**: Comprehensive error handling testing
- **Performance**: System performance under load

**Test Methods:**
- `test_complete_workflow_from_registration_to_service_purchase()`
- `test_admin_service_management_workflow()`
- `test_company_worker_management_workflow()`
- `test_customer_transaction_workflow()`
- `test_error_handling_and_validation()`
- `test_data_integrity_and_consistency()`

---

## 🎯 **Running Specific Tests**

### **Run Single Test Method**
```bash
php artisan test --filter test_customer_can_purchase_service
```

### **Run Tests by Pattern**
```bash
# All customer tests
php artisan test --filter CustomerTest

# All authentication tests
php artisan test --filter AuthTest

# All tests containing "purchase"
php artisan test --filter purchase
```

### **Run Tests with Specific Output**
```bash
# Verbose output
php artisan test --verbose

# Stop on first failure
php artisan test --stop-on-failure

# Generate coverage report
php artisan test --coverage --min=80
```

---

## 📈 **Test Performance & Monitoring**

### **Performance Benchmarks**
- **Authentication**: < 100ms per request
- **Database Queries**: < 50ms for simple queries
- **API Responses**: < 200ms for standard operations
- **Concurrent Requests**: Support for 100+ simultaneous users

### **Memory Usage**
- **Test Execution**: < 128MB memory usage
- **Database Operations**: < 64MB per transaction
- **API Responses**: < 32MB per response

---

## 🚨 **Common Test Issues & Solutions**

### **Issue 1: Database Connection Errors**
```bash
# Solution: Clear database cache
php artisan config:clear
php artisan cache:clear

# Ensure database is accessible
php artisan migrate:status
```

### **Issue 2: Factory/Seeder Errors**
```bash
# Solution: Refresh database and re-seed
php artisan migrate:fresh --seed

# Or run specific seeders
php artisan db:seed --class=CustomerSeeder
```

### **Issue 3: Authentication Token Issues**
```bash
# Solution: Clear Sanctum cache
php artisan sanctum:clear

# Regenerate application key
php artisan key:generate
```

### **Issue 4: Test Timeout Issues**
```bash
# Solution: Increase PHP memory limit
php -d memory_limit=512M artisan test

# Or run tests individually
php artisan test tests/Feature/AuthTest.php
```

---

## 📊 **Test Results Interpretation**

### **✅ Successful Test Results**
```
PASS  Tests\Feature\AuthTest::test_customer_user_can_register
PASS  Tests\Feature\ServiceTest::test_company_can_create_service
PASS  Tests\Feature\CustomerTest::test_customer_can_view_profile
```

### **❌ Failed Test Results**
```
FAIL  Tests\Feature\ServiceTest::test_company_can_update_service
   Expected status code 200 but received 422.
   Failed asserting that false is true.
```

### **⚠️ Test Warnings**
```
WARN  Tests\Feature\MiddlewareTest::test_middleware_performance
   Test execution time exceeded 1 second threshold
```

---

## 🔧 **Custom Test Configuration**

### **Environment Variables**
```bash
# Test database configuration
DB_CONNECTION=sqlite
DB_DATABASE=:memory:

# Test mail configuration
MAIL_MAILER=array

# Test cache configuration
CACHE_DRIVER=array
```

### **PHPUnit Configuration**
```xml
<!-- phpunit.xml -->
<php>
    <env name="APP_ENV" value="testing"/>
    <env name="DB_CONNECTION" value="sqlite"/>
    <env name="DB_DATABASE" value=":memory:"/>
    <env name="CACHE_DRIVER" value="array"/>
    <env name="MAIL_MAILER" value="array"/>
</php>
```

---

## 📝 **Adding New Tests**

### **Test File Structure**
```php
<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

class NewFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_feature_functionality()
    {
        // Arrange
        $user = User::factory()->create();

        // Act
        Sanctum::actingAs($user);
        $response = $this->getJson('/api/new-feature');

        // Assert
        $response->assertStatus(200);
    }
}
```

### **Test Naming Conventions**
- **Method Names**: `test_what_is_being_tested()`
- **File Names**: `FeatureNameTest.php`
- **Class Names**: `FeatureNameTest`

---

## 🎉 **Test Completion Checklist**

- [ ] **Authentication Tests**: All user roles and scenarios
- [ ] **Authorization Tests**: Role-based access control
- [ ] **Model Tests**: Relationships, scopes, and methods
- [ ] **Controller Tests**: All API endpoints and responses
- [ ] **Middleware Tests**: Security and performance
- [ ] **Integration Tests**: End-to-end workflows
- [ ] **Error Handling**: Validation and edge cases
- [ ] **Performance Tests**: Response time and memory usage
- [ ] **Data Integrity**: Database consistency and relationships

---

## 🚀 **Next Steps**

1. **Run All Tests**: `php artisan test`
2. **Review Coverage**: `php artisan test --coverage`
3. **Fix Failures**: Address any failing tests
4. **Add Missing Tests**: Cover any uncovered functionality
5. **Performance Optimization**: Optimize slow-running tests
6. **Continuous Integration**: Set up automated testing pipeline

---

## 📚 **Additional Resources**

- **Laravel Testing Documentation**: https://laravel.com/docs/testing
- **PHPUnit Documentation**: https://phpunit.de/documentation.html
- **Laravel Sanctum**: https://laravel.com/docs/sanctum
- **Database Testing**: https://laravel.com/docs/database-testing

---

**🎯 Your Laravel Service System now has comprehensive testing coverage! Run the tests to ensure everything works correctly.**
