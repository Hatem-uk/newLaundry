# Laundry Service API - Postman Collection Summary

## Overview
This Postman collection provides comprehensive coverage of all API endpoints in the Laundry Service system, covering Admin, Customer, Laundry, and Agent operations.

## Collection Structure

### 1. Authentication
- **Register**: Create new user accounts (customer, laundry, agent, worker, admin)
- **Login**: Authenticate and get access token
- **Logout**: Revoke access token
- **Check Status**: Verify authentication status
- **Refresh Token**: Get new access token
- **Get Profile**: Retrieve user profile information

### 2. Cities
- **Get All Cities**: List all cities with optional search
- **Get City by ID**: Get specific city details
- **Get All Regions**: List all available regions
- **Get Cities by Region**: Filter cities by specific region
- **Get Cities with Laundries**: Cities with active laundries count
- **Get Cities with Agents**: Cities with active agents count

### 3. Admin Services
- **Get All Services**: Retrieve all services with filters
- **Get Pending Services**: Get services awaiting approval
- **Get Service Details**: View specific service information
- **Approve Service**: Approve a service
- **Reject Service**: Reject a service with reason
- **Get Service Statistics**: View service analytics
- **Bulk Approve Services**: Approve multiple services at once
- **Bulk Reject Services**: Reject multiple services with reason

### 4. Admin Users
- **Get All Users**: List all users with filtering options
- **Update User Status**: Approve/reject user accounts

### 5. Admin Orders
- **Get All Orders**: View all system orders

### 6. Admin Statistics
- **Get Platform Statistics**: Comprehensive platform analytics

### 7. Customer Operations
- **Get Profile**: View customer profile with statistics
- **Update Profile**: Modify customer information
- **Get Nearby Laundries**: Find laundries within radius
- **Get Nearby Agents**: Find agents within radius
- **Get Favorite Services**: View frequently used services
- **Get Recent Searches**: View search history

### 8. Packages
- **Get All Packages**: List available packages
- **Get Packages by Type**: Filter packages by category
- **Get Package Details**: View specific package information
- **Purchase Package**: Buy package for self (increases coins)
- **Gift Package**: Send package to another customer

### 9. Orders
- **Get My Orders**: View user's order history
- **Get Order Details**: View specific order information
- **Purchase Service**: Buy service using coins (decreases coins)
- **Cancel Order**: Cancel pending orders (refunds coins)
- **Update Order Status**: Change order status (provider only)
- **Get Provider Orders**: View orders for service providers
- **Get Purchase History**: Customer purchase history
- **Get Order Statistics**: Order analytics for user

### 10. Ratings
- **Create Rating**: Submit new rating for laundry
- **Update Rating**: Modify existing rating (within 24 hours)
- **Delete Rating**: Remove rating
- **Get Rating Details**: View specific rating
- **Get Laundry Ratings**: All ratings for a laundry
- **Get My Ratings**: Customer's own ratings
- **Get Rating Statistics**: Rating analytics for laundry
- **Search Ratings**: Find ratings with filters

### 11. Laundry Operations
- **Get Profile**: View laundry profile
- **Update Profile**: Modify laundry information
- **Update Status**: Change online/offline status
- **Get Statistics**: View laundry analytics
- **Get Services**: List laundry's services
- **Get Nearby Agents**: Find nearby agents
- **Get Agents by City**: Filter agents by city
- **Purchase Agent Supply**: Buy supplies from agents
- **Get Workers**: List laundry workers
- **Add Worker**: Hire new worker
- **Get Pending Workers**: View workers awaiting approval
- **Approve Worker**: Approve worker account

### 12. Services Management
- **Get My Services**: List laundry's services
- **Create Service**: Add new service
- **Get Service Details**: View service information
- **Update Service**: Modify service details
- **Delete Service**: Remove service
- **Get Service Statistics**: Service analytics

### 13. Service Purchase
- **Get Available Services**: View services from nearby laundries
- **Get Services by Location**: Filter services by location
- **Get Service Details**: Detailed service information

## Key Features

### Coin System
- **Packages**: Increase coins when purchased
- **Services**: Decrease coins when purchased
- **Gifting**: Send packages to other customers
- **Refunds**: Coins refunded on order cancellation

### Role-Based Access
- **Admin**: Full system access, approve/reject services and users
- **Laundry**: Manage profile, services, workers, purchase supplies
- **Customer**: Browse services, make purchases, manage profile
- **Agent**: Provide supplies to laundries

### Business Logic
- **Service Approval**: Admin must approve new services
- **User Approval**: Admin approves laundry/agent accounts
- **Order Management**: Complete order lifecycle
- **Rating System**: Customer feedback and analytics
- **Location-Based**: Find nearby services and providers

## Environment Variables
- `base_url`: API base URL (default: http://localhost:8000)
- `access_token`: Authentication token (auto-populated from login)

## Pre-request Scripts
- Auto-sets Authorization header
- Sets Accept header for JSON responses
- Configures base URL

## Test Scripts
- Auto-saves access token from responses
- Logs responses for debugging

## Usage Instructions
1. Import the collection into Postman
2. Set the `base_url` environment variable
3. Use the Register endpoint to create an account
4. Use the Login endpoint to get an access token
5. The token will be automatically used for authenticated requests
6. Test different endpoints based on user roles

## Testing Scenarios
1. **Admin Flow**: Approve services, manage users, view statistics
2. **Customer Flow**: Browse services, purchase packages, order services
3. **Laundry Flow**: Create services, manage workers, purchase supplies
4. **Complete Flow**: End-to-end service purchase and delivery

This collection provides comprehensive coverage of your Laundry Service API, enabling thorough testing of all system functionality.
