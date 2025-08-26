# Laundry Service Application - Detailed Business Logic

## Table of Contents
1. [Business Model Overview](#business-model-overview)
2. [Core Business Rules](#core-business-rules)
3. [Workflow Decision Trees](#workflow-decision-trees)
4. [Business Logic Implementation](#business-logic-implementation)
5. [Validation Rules](#validation-rules)
6. [Business Constraints](#business-constraints)
7. [Exception Handling](#exception-handling)
8. [Business Metrics](#business-metrics)

---

## Business Model Overview

### Revenue Streams
1. **Service Commission**: Platform takes percentage from service transactions
2. **Package Sales**: Coin packages for customers
3. **Premium Features**: Advanced analytics for laundries
4. **Agent Commission**: From supply transactions

### Value Proposition
- **For Customers**: Convenient access to nearby laundry services
- **For Laundries**: Increased customer reach and business management tools
- **For Agents**: Expanded market for supplies
- **For Platform**: Network effect and transaction fees

---

## Core Business Rules

### 1. User Registration & Approval Rules

#### Customer Registration
```
IF role = 'customer' THEN
    status = 'approved' (automatic)
    initial_coins = 100
    profile_creation = required
    city_setup = optional
END IF
```

#### Laundry/Agent Registration
```
IF role IN ('laundry', 'agent') THEN
    status = 'pending' (requires admin approval)
    initial_coins = 0
    profile_creation = required
    city_setup = required
    business_verification = required
END IF
```

#### Worker Registration
```
IF role = 'worker' THEN
    status = 'pending' (requires laundry approval)
    initial_coins = 0
    laundry_assignment = required
    position_definition = required
    salary_setting = required
END IF
```

### 2. Service Creation & Management Rules

#### Service Validation Rules
```
VALIDATE service:
    - provider_role IN ('laundry', 'agent')
    - provider_status = 'approved'
    - name_ar OR name_en required
    - description_ar OR description_en required
    - quantity > 0
    - type IN ('washing', 'ironing', 'cleaning', 'agent_supply', 'other')
    - price XOR coin_cost required (not both, not neither)
    - image_size <= 2MB
    - image_type IN ('jpeg', 'png', 'jpg', 'gif')
END VALIDATE
```

#### Service Status Transitions
```
Service Status Flow:
pending → approved (admin action)
pending → rejected (admin action)
rejected → pending (laundry update)
approved → [locked for modifications]
```

#### Service Modification Rules
```
IF service.status = 'pending' THEN
    ALLOW: update, delete, modify_all_fields
ELSE IF service.status = 'approved' THEN
    ALLOW: none (requires admin intervention)
ELSE IF service.status = 'rejected' THEN
    ALLOW: update (moves back to pending)
END IF
```

### 3. Order Processing Rules

#### Order Creation Validation
```
VALIDATE order_creation:
    - customer_coins >= service.coin_cost * quantity
    - service.status = 'approved'
    - service.provider.status = 'online'
    - customer.city_id IS NOT NULL
    - service.provider.city_id IS NOT NULL
    - distance <= max_search_radius
END VALIDATE
```

#### Order Status Transition Rules
```
Order Status Flow:
pending → in_process (provider action)
pending → canceled (customer action, within time limit)
in_process → completed (provider action)
in_process → canceled (provider action, with refund)
completed → [final state]
canceled → [final state]
```

#### Order Cancellation Rules
```
IF order.status = 'pending' THEN
    ALLOW_CANCELLATION: true
    REFUND_TYPE: automatic_coin_refund
    REFUND_AMOUNT: abs(order.coins)
    CANCELLATION_FEE: 0
ELSE IF order.status = 'in_process' THEN
    ALLOW_CANCELLATION: false (provider discretion)
    REFUND_TYPE: manual_review
    REFUND_AMOUNT: variable
    CANCELLATION_FEE: possible
END IF
```

### 4. Coin System Rules

#### Coin Balance Rules
```
Coin Balance Constraints:
- minimum_balance = 0
- maximum_balance = unlimited
- negative_balance = not_allowed
- decimal_precision = 0 (whole numbers only)
```

#### Coin Transaction Rules
```
Coin Transaction Types:
1. SERVICE_PURCHASE:
   - from: customer
   - to: laundry
   - amount: service.coin_cost * quantity
   - direction: negative for customer, positive for laundry

2. PACKAGE_PURCHASE:
   - from: customer
   - to: customer
   - amount: package.coins_amount
   - direction: positive (addition)

3. GIFT_PACKAGE:
   - from: sender
   - to: recipient
   - amount: package.coins_amount
   - direction: positive for recipient

4. ORDER_REFUND:
   - from: laundry
   - to: customer
   - amount: original_purchase_amount
   - direction: positive for customer
```

#### Coin Transfer Validation
```
VALIDATE coin_transfer:
    - sender_coins >= transfer_amount
    - transfer_amount > 0
    - sender_id != recipient_id
    - sender.status = 'approved'
    - recipient.status = 'approved'
END VALIDATE
```

### 5. Location-Based Search Rules

#### Search Radius Logic
```
Search Parameters:
- default_radius = 50km
- max_radius = 100km
- min_radius = 5km
- radius_step = 5km
```

#### Distance Calculation
```
Distance Algorithm:
1. Get customer.city.coordinates
2. Get laundry.city.coordinates
3. Apply Haversine formula
4. Filter by max_distance
5. Sort by proximity (closest first)
```

#### Location Filtering Rules
```
Location Filters:
- customer.city_id must be set
- laundry.status = 'online'
- laundry.city_id must be set
- distance <= search_radius
- service.status = 'approved'
```

### 6. Rating System Rules

#### Rating Creation Rules
```
Rating Validation:
- customer_id must exist
- laundry_id must exist
- rating_value IN (1, 2, 3, 4, 5)
- comment_length <= 1000 characters
- service_type IN ('washing', 'ironing', 'cleaning', 'agent_supply', 'other')
- order_id optional but recommended
```

#### Rating Modification Rules
```
Rating Update Constraints:
- time_limit = 24_hours_from_creation
- allowed_operations = update, delete
- restricted_fields = customer_id, laundry_id, order_id
- editable_fields = rating_value, comment, service_type
```

#### Rating Uniqueness Rules
```
Rating Uniqueness:
- one_rating_per_order_per_customer
- one_rating_per_laundry_per_customer (if no order)
- duplicate_rating_prevention = enforced
```

---

## Workflow Decision Trees

### 1. Service Purchase Decision Tree

```
START: Customer wants to purchase service
├── Is customer logged in?
│   ├── NO → Redirect to login
│   └── YES → Continue
├── Does customer have city set?
│   ├── NO → Prompt to set city
│   └── YES → Continue
├── Is service available in customer's area?
│   ├── NO → Show "no services available" message
│   └── YES → Continue
├── Does customer have sufficient coins?
│   ├── NO → Show "insufficient coins" + package options
│   └── YES → Continue
├── Is service provider online?
│   ├── NO → Show "provider offline" message
│   └── YES → Continue
├── Create order
├── Deduct coins from customer
├── Send notification to provider
└── END: Order created successfully
```

### 2. Service Approval Decision Tree

```
START: Admin reviews service
├── Is service complete (all required fields)?
│   ├── NO → Reject with "incomplete information" reason
│   └── YES → Continue
├── Does service meet quality standards?
│   ├── NO → Reject with specific reason
│   └── YES → Continue
├── Is pricing reasonable?
│   ├── NO → Reject with "pricing issue" reason
│   └── YES → Continue
├── Are images appropriate?
│   ├── NO → Reject with "inappropriate content" reason
│   └── YES → Continue
├── Approve service
├── Send approval notification to provider
├── Make service visible to customers
└── END: Service approved
```

### 3. Order Status Update Decision Tree

```
START: Provider updates order status
├── Is provider authorized for this order?
│   ├── NO → Return "unauthorized" error
│   └── YES → Continue
├── What is current order status?
│   ├── pending → Allow: in_process, canceled
│   ├── in_process → Allow: completed, canceled
│   ├── completed → No changes allowed
│   └── canceled → No changes allowed
├── Is status transition valid?
│   ├── NO → Return "invalid transition" error
│   └── YES → Continue
├── Update order status
├── Send notification to customer
├── If completed: transfer coins to provider
└── END: Status updated successfully
```

---

## Business Logic Implementation

### 1. Service Availability Check

```php
public function isServiceAvailable(Service $service, User $customer): bool
{
    // Check service status
    if ($service->status !== 'approved') {
        return false;
    }
    
    // Check provider status
    if ($service->provider->status !== 'online') {
        return false;
    }
    
    // Check customer coins
    if ($service->coin_cost && $customer->coins < $service->coin_cost) {
        return false;
    }
    
    // Check location availability
    if (!$this->isServiceInCustomerArea($service, $customer)) {
        return false;
    }
    
    return true;
}
```

### 2. Coin Balance Validation

```php
public function canUserAffordService(User $user, Service $service, int $quantity): bool
{
    $totalCost = $service->coin_cost * $quantity;
    
    // Check if user has sufficient coins
    if ($user->coins < $totalCost) {
        return false;
    }
    
    // Check if user is approved
    if ($user->status !== 'approved') {
        return false;
    }
    
    return true;
}
```

### 3. Order Status Transition Validation

```php
public function canTransitionOrderStatus(Order $order, string $newStatus): bool
{
    $allowedTransitions = [
        'pending' => ['in_process', 'canceled'],
        'in_process' => ['completed', 'canceled'],
        'completed' => [],
        'canceled' => []
    ];
    
    $currentStatus = $order->status;
    
    if (!isset($allowedTransitions[$currentStatus])) {
        return false;
    }
    
    return in_array($newStatus, $allowedTransitions[$currentStatus]);
}
```

---

## Validation Rules

### 1. User Registration Validation

```php
'name' => 'required|string|max:255',
'email' => 'required|email|unique:users',
'password' => 'required|min:6|confirmed',
'role' => 'required|in:laundry,worker,customer,admin',
'phone' => 'nullable|string|max:20',
'city_id' => 'nullable|exists:cities,id',
'address' => 'nullable|string|max:500',
'laundry_name' => 'nullable|string|max:255|required_if:role,laundry',
'position' => 'nullable|string|max:255|required_if:role,worker',
'salary' => 'nullable|numeric|min:0|required_if:role,worker',
'laundry_id' => 'nullable|exists:laundries,id|required_if:role,worker'
```

### 2. Service Creation Validation

```php
'name_ar' => 'required|string|max:255',
'name_en' => 'required|string|max:255',
'description_ar' => 'required|string',
'description_en' => 'required|string',
'quantity' => 'required|integer|min:1',
'type' => 'required|in:washing,ironing,agent_supply,cleaning,other',
'coin_cost' => 'nullable|integer|min:1|required_without:price',
'price' => 'nullable|numeric|min:0|required_without:coin_cost',
'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
```

### 3. Order Creation Validation

```php
'service_id' => 'required|exists:services,id',
'quantity' => 'required|integer|min:1',
'recipient_id' => 'nullable|exists:users,id'
```

---

## Business Constraints

### 1. Time-Based Constraints

```
Rating Modifications: 24 hours from creation
Order Cancellation: While status is 'pending' only
Service Updates: While status is 'pending' only
Worker Approval: No time limit (laundry discretion)
Admin Approval: No time limit (admin discretion)
```

### 2. Quantity Constraints

```
Service Quantity: Minimum 1, no maximum
Order Quantity: Minimum 1, maximum based on service availability
Coin Amounts: Whole numbers only, no decimals
Rating Values: 1-5 integers only
Search Radius: 5-100 km, 5km increments
```

### 3. Relationship Constraints

```
User Roles: One role per user, fixed at registration
Service Provider: One provider per service
Order Assignment: One provider per order
Worker Assignment: One laundry per worker
City Assignment: One city per user profile
```

---

## Exception Handling

### 1. Business Logic Exceptions

```php
class InsufficientCoinsException extends Exception
{
    public function __construct($required, $available)
    {
        $this->message = "Insufficient coins. Required: {$required}, Available: {$available}";
        $this->code = 400;
    }
}

class InvalidStatusTransitionException extends Exception
{
    public function __construct($currentStatus, $requestedStatus)
    {
        $this->message = "Cannot transition from {$currentStatus} to {$requestedStatus}";
        $this->code = 422;
    }
}

class ServiceNotAvailableException extends Exception
{
    public function __construct($reason)
    {
        $this->message = "Service not available: {$reason}";
        $this->code = 400;
    }
}
```

### 2. Error Response Format

```php
{
    "success": false,
    "message": "Business logic error message",
    "error_code": "BUSINESS_RULE_VIOLATION",
    "details": {
        "field": "affected_field_name",
        "rule": "violated_rule",
        "current_value": "current_value",
        "expected_value": "expected_value"
    },
    "status_code": 422
}
```

---

## Business Metrics

### 1. Key Performance Indicators (KPIs)

```
Customer Metrics:
- Registration rate
- Active customer count
- Average coins spent per customer
- Customer retention rate
- Average rating given

Laundry Metrics:
- Service approval rate
- Average order completion time
- Customer satisfaction score
- Revenue per laundry
- Service utilization rate

Platform Metrics:
- Total transactions
- Platform revenue
- Service availability by area
- User engagement rate
- System uptime
```

### 2. Business Intelligence Queries

```sql
-- Customer engagement by city
SELECT 
    c.name as city_name,
    COUNT(DISTINCT u.id) as customer_count,
    AVG(u.coins) as avg_coins,
    COUNT(o.id) as total_orders
FROM cities c
JOIN customers cust ON c.id = cust.city_id
JOIN users u ON cust.user_id = u.id
LEFT JOIN orders o ON u.id = o.user_id
GROUP BY c.id, c.name
ORDER BY customer_count DESC;

-- Service performance by type
SELECT 
    s.type,
    COUNT(s.id) as total_services,
    AVG(s.coin_cost) as avg_coin_cost,
    COUNT(o.id) as total_orders,
    AVG(r.rating) as avg_rating
FROM services s
LEFT JOIN orders o ON s.id = o.target_id
LEFT JOIN ratings r ON s.provider_id = r.laundry_id
WHERE s.status = 'approved'
GROUP BY s.type
ORDER BY total_orders DESC;
```

### 3. Monitoring Alerts

```
Business Alerts:
- Service approval rate drops below 80%
- Customer registration rate drops by 20%
- Average order completion time exceeds 48 hours
- Coin balance errors detected
- Rating system abuse detected

Technical Alerts:
- API response time exceeds 2 seconds
- Database connection failures
- File upload errors
- Email delivery failures
- Authentication failures
```

---

## Business Rule Enforcement

### 1. Database Constraints

```sql
-- Ensure coin balance never goes negative
ALTER TABLE users ADD CONSTRAINT check_positive_coins CHECK (coins >= 0);

-- Ensure service has either price or coin_cost
ALTER TABLE services ADD CONSTRAINT check_pricing CHECK (
    (price IS NOT NULL AND coin_cost IS NULL) OR 
    (price IS NULL AND coin_cost IS NOT NULL)
);

-- Ensure order status transitions are valid
ALTER TABLE orders ADD CONSTRAINT check_status_transition CHECK (
    status IN ('pending', 'in_process', 'completed', 'canceled')
);
```

### 2. Application-Level Enforcement

```php
// Middleware for business rule enforcement
class BusinessRuleMiddleware
{
    public function handle($request, Closure $next)
    {
        // Check user status before allowing access
        if (auth()->check() && auth()->user()->status !== 'approved') {
            return response()->json([
                'message' => 'Account not approved',
                'status' => auth()->user()->status
            ], 403);
        }
        
        return $next($request);
    }
}
```

### 3. Event-Driven Validation

```php
// Event listener for business rule validation
class OrderCreatedListener
{
    public function handle(OrderCreated $event)
    {
        $order = $event->order;
        
        // Validate business rules
        $this->validateOrderBusinessRules($order);
        
        // Send notifications
        $this->sendOrderNotifications($order);
        
        // Update metrics
        $this->updateBusinessMetrics($order);
    }
}
```

---

This detailed business logic document provides the foundation for implementing and maintaining the laundry service application. All business rules should be implemented consistently across the application, with proper validation, error handling, and monitoring to ensure business integrity.

