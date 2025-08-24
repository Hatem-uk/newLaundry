# 🧺 Laundry Service API Documentation

## Overview
The Laundry Service API is a comprehensive RESTful API that provides a complete solution for managing laundry services, coin-based economy, package management, and customer interactions. The API supports multiple user roles including customers, laundries, agents, and administrators.

## 🔗 Base URL
```
http://localhost:8000/api
```

## 🔐 Authentication
The API uses Laravel Sanctum for authentication. Include the Bearer token in the Authorization header:
```
Authorization: Bearer {your_token}
```

## 📋 API Endpoints

### 🔓 Public Endpoints (No Authentication Required)

#### Cities & Locations
- `GET /cities` - Get all cities
- `GET /cities/regions` - Get all regions
- `GET /cities/with-laundries` - Get cities with available laundries
- `GET /cities/with-agents` - Get cities with available agents
- `GET /cities/region/{region}` - Get cities by specific region
- `GET /cities/{id}` - Get specific city details

#### Coin Packages
- `GET /packages` - Get all available coin packages
- `GET /packages/type/{type}` - Get packages by type (starter, premium, etc.)
- `GET /packages/{id}` - Get specific package details

#### Services (Public View)
- `GET /services` - Get all available services (limited information)

### 🔐 Authentication Endpoints

#### User Registration & Login
- `POST /auth/register` - Register new user (customer, laundry, agent)
- `POST /auth/login` - User login
- `POST /auth/logout` - User logout (authenticated)
- `GET /auth/status` - Check authentication status (authenticated)
- `POST /auth/refresh` - Refresh authentication token (authenticated)
- `GET /auth/profile` - Get user profile (authenticated)

### 👤 Customer Endpoints (Authenticated)

#### Profile Management
- `GET /customer/profile` - Get customer profile
- `PUT /customer/profile` - Update customer profile

#### Location & Discovery
- `GET /customer/nearby-laundries` - Find laundries near customer location
- `GET /customer/nearby-agents` - Find agents near customer location
- `GET /customer/favorite-services` - Get customer's favorite services
- `GET /customer/recent-searches` - Get customer's recent searches

#### Service Discovery
- `GET /customer/services` - Get available services for customer
- `GET /customer/services-by-location` - Get services by location
- `GET /customer/services/{serviceId}` - Get specific service details

#### Order Management
- `GET /customer/orders` - Get customer's order history
- `GET /customer/orders/{id}` - Get specific order details

### 📦 Package Management (Authenticated)

#### Package Purchases
- `POST /packages/purchase` - Purchase coin package for self
- `POST /packages/gift` - Gift coin package to another user

### 🛒 Order Management (Authenticated)

#### Service Orders
- `POST /orders/purchase-service` - Purchase laundry service
- `GET /orders` - Get user's orders with filtering
- `GET /orders/{id}` - Get specific order details
- `POST /orders/{id}/cancel` - Cancel order
- `PUT /orders/{id}/status` - Update order status (laundry only)

#### Order History & Analytics
- `GET /orders/history/purchases` - Get purchase history
- `GET /orders/statistics` - Get order statistics
- `GET /orders/provider/orders` - Get provider orders (laundry/agent)

### ⭐ Rating & Reviews (Authenticated)

#### Rating Management
- `POST /ratings` - Submit new rating
- `PUT /ratings/{id}` - Update existing rating
- `DELETE /ratings/{id}` - Delete rating
- `GET /ratings/{id}` - Get specific rating
- `GET /ratings/laundry/{laundryId}` - Get laundry ratings
- `GET /ratings/customer/me` - Get customer's own ratings
- `GET /ratings/stats/{laundryId}` - Get rating statistics
- `GET /ratings/search` - Search ratings

### 🏪 Laundry Management (Authenticated)

#### Profile & Settings
- `GET /laundry/profile` - Get laundry profile
- `PUT /laundry/profile` - Update laundry profile
- `PUT /laundry/status` - Update online/offline status
- `GET /laundry/statistics` - Get laundry statistics

#### Service Management
- `GET /laundry/services` - Get laundry's services
- `GET /laundry/nearby-agents` - Find nearby agents
- `GET /laundry/agents-by-city/{cityId}` - Get agents by city

#### Worker Management
- `GET /laundry/workers` - Get laundry workers
- `POST /laundry/workers` - Add new worker
- `GET /laundry/workers/pending` - Get pending worker applications
- `POST /laundry/workers/{worker}/approve` - Approve worker

#### Agent Supply
- `POST /laundry/purchase-agent-supply` - Purchase supplies from agents

### 🔧 Service Management (Laundry Only)

#### Service CRUD
- `GET /services` - Get laundry's services
- `POST /services` - Create new service
- `GET /services/{id}` - Get specific service
- `PUT /services/{id}` - Update service
- `DELETE /services/{id}` - Delete service
- `GET /services/statistics` - Get service statistics

### 👨‍💼 Admin Management (Admin Only)

#### Service Approval
- `GET /admin/services` - Get all services with filtering
- `GET /admin/services/pending` - Get pending services
- `GET /admin/services/{id}` - Get specific service
- `POST /admin/services/{id}/approve` - Approve service
- `POST /admin/services/{id}/reject` - Reject service
- `POST /admin/services/bulk-approve` - Bulk approve services
- `POST /admin/services/bulk-reject` - Bulk reject services
- `GET /admin/services/statistics` - Get service approval statistics

#### User Management
- `GET /admin/users` - Get all users with filtering
- `PUT /admin/users/{id}/status` - Approve/reject user

#### Order Management
- `GET /admin/orders` - Get all orders with filtering

#### Platform Statistics
- `GET /admin/statistics` - Get comprehensive platform statistics

## 📊 Response Format

### Success Response
```json
{
    "success": true,
    "message": "Operation completed successfully",
    "data": { ... },
    "status": 200
}
```

### Error Response
```json
{
    "success": false,
    "message": "Error description",
    "data": null,
    "status": 400,
    "debug": "Detailed error information (development only)"
}
```

## 🔑 User Roles

### Customer
- Purchase coin packages
- Buy laundry services
- Rate and review services
- Manage profile and preferences

### Laundry
- Manage services and pricing
- Handle customer orders
- Manage workers
- Purchase supplies from agents

### Agent
- Provide supplies to laundries
- Manage inventory
- Handle supply orders

### Admin
- Approve/reject services and users
- Monitor platform statistics
- Manage system-wide settings

## 💰 Coin System

The platform uses a virtual coin economy:
- **Packages**: Predefined coin bundles that customers can purchase
- **Services**: Laundry services that can be purchased with coins or cash
- **Gifting**: Customers can gift coin packages to other users
- **Balance Tracking**: All coin transactions are tracked and audited

## 🌍 Multilingual Support

The API supports both English and Arabic:
- User names and descriptions
- Service names and descriptions
- City and region names
- Error messages and responses

## 📱 Features

### Location-Based Services
- Find nearby laundries and agents
- City and region-based filtering
- Distance calculation and sorting

### Real-Time Updates
- Order status tracking
- Email notifications for key events
- Push notifications (FCM support)

### Advanced Filtering
- Service type filtering
- Price range filtering
- Rating-based sorting
- Status-based filtering

## 🧪 Testing

### Postman Collection
Import the `Laundry_Service_API.postman_collection.json` file into Postman for comprehensive API testing.

### Test Scripts
- `test_all_api.php` - Comprehensive API endpoint testing
- `test_customer_lifecycle.php` - Complete customer journey testing

### Environment Variables
Set these variables in Postman:
- `base_url`: API base URL
- `customer_token`: Customer authentication token
- `laundry_token`: Laundry authentication token
- `admin_token`: Admin authentication token

## 🚀 Getting Started

1. **Install Dependencies**
   ```bash
   composer install
   ```

2. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. **Database Setup**
   ```bash
   php artisan migrate:fresh --seed
   ```

4. **Start Server**
   ```bash
   php artisan serve
   ```

5. **Test API**
   ```bash
   php test_all_api.php
   ```

## 📝 Notes

- All timestamps are in UTC
- File uploads support images (jpg, png, gif)
- Pagination is supported on list endpoints
- Rate limiting is implemented for security
- Email notifications are sent for key business events
- The API follows RESTful conventions
- Comprehensive error handling and validation
- Support for both JSON and form data

## 🔒 Security Features

- JWT-based authentication with Sanctum
- Role-based access control (RBAC)
- Input validation and sanitization
- SQL injection protection
- XSS protection
- CSRF protection
- Rate limiting
- Secure file uploads

## 📞 Support

For API support and questions:
- Check the test scripts for usage examples
- Review the Postman collection for endpoint details
- Examine the controller code for implementation details
