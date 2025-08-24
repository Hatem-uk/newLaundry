# Testing Guide for Laundry Service API Fixes

This guide covers testing the two main issues that were fixed:

1. **Email notifications for laundry/agent registration**
2. **Order status transition validation**

## Prerequisites

- Laravel application running on `http://localhost:8000`
- Mail configuration set up (check `.env` file)
- Database seeded with cities and admin users
- Postman or similar API testing tool

## Issue 1: Email Notifications for Registration

### What Was Fixed

- Added email notifications when laundry or agent users register
- Admin users receive notification emails about new registrations
- New users receive welcome emails
- Emails are sent for both API and web registration endpoints

### Testing Steps

#### 1. Test Laundry Registration

```bash
POST /api/auth/register
Content-Type: application/json

{
  "name": "CleanPro Laundry",
  "email": "laundry@example.com",
  "password": "123456",
  "password_confirmation": "123456",
  "role": "laundry",
  "phone": "+966500000001",
  "city_id": 1,
  "address": "123 Business Street, Riyadh",
  "laundry_name": "CleanPro Premium Laundry"
}
```

**Expected Results:**
- User created successfully with status "pending"
- Admin users receive notification email about new laundry registration
- Laundry user receives welcome email
- Check mail logs or configured mail service

#### 2. Test Agent Registration

```bash
POST /api/admin/register
Content-Type: application/json

{
  "name": "Fast Agent Services",
  "email": "agent@example.com",
  "password": "123456",
  "password_confirmation": "123456",
  "role": "agent",
  "phone": "+966500000002",
  "address": "456 Agent Ave, Jeddah"
}
```

**Expected Results:**
- User created successfully
- Admin users receive notification email about new agent registration
- Agent user receives welcome email

#### 3. Verify Email Templates

Check that these email templates exist and are properly formatted:
- `resources/views/emails/registration/laundry.blade.php`
- `resources/views/emails/registration/agent.blade.php`
- `resources/views/emails/welcome/laundry.blade.php`
- `resources/views/emails/welcome/agent.blade.php`

### Email Configuration

Ensure your `.env` file has proper mail settings:

```env
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME="Laundry Service System"
```

For testing, you can use:
```env
MAIL_MAILER=log
```

This will log emails to `storage/logs/laravel.log` instead of sending them.

## Issue 2: Order Status Transition Validation

### What Was Fixed

- Improved order status transition validation logic
- Better error messages with current and requested status
- More flexible status transitions (pending → in_process, pending → completed, in_process → completed)

### Testing Steps

#### 1. Create Test Order

First, create a service and purchase it to create an order:

```bash
# Login as customer
POST /api/auth/login
{
  "email": "customer@example.com",
  "password": "123456"
}

# Purchase service (requires existing service with ID 1)
POST /api/orders/purchase-service
Authorization: Bearer {customer_token}
{
  "service_id": 1,
  "quantity": 1
}
```

#### 2. Test Valid Status Transitions

**Pending → In Process:**
```bash
PUT /api/orders/{order_id}/status
Authorization: Bearer {laundry_token}
{
  "status": "in_process"
}
```

**Expected Result:** Status updated successfully

**In Process → Completed:**
```bash
PUT /api/orders/{order_id}/status
Authorization: Bearer {laundry_token}
{
  "status": "completed"
}
```

**Expected Result:** Status updated successfully

**Pending → Completed (Direct):**
```bash
PUT /api/orders/{order_id}/status
Authorization: Bearer {laundry_token}
{
  "status": "completed"
}
```

**Expected Result:** Status updated successfully (this was the fix)

#### 3. Test Invalid Status Transitions

**Completed → In Process (Invalid):**
```bash
PUT /api/orders/{order_id}/status
Authorization: Bearer {laundry_token}
{
  "status": "in_process"
}
```

**Expected Result:** 
```json
{
  "message": "Invalid status transition. Order must be pending to start processing.",
  "current_status": "completed",
  "requested_status": "in_process"
}
```

### Status Transition Rules

The fixed validation now allows these transitions:

- `pending` → `in_process` ✅
- `pending` → `completed` ✅ (This was the main fix)
- `in_process` → `completed` ✅
- `completed` → `in_process` ❌
- `completed` → `pending` ❌
- `canceled` → `in_process` ❌

## Using the Postman Collection

1. Import `Laundry_Service_API_Testing.postman_collection.json` into Postman
2. Set the `base_url` variable to your Laravel app URL
3. Run the tests in sequence:
   - Registration tests
   - Authentication tests  
   - Order status update tests

## Troubleshooting

### Email Issues

1. **No emails sent:**
   - Check mail configuration in `.env`
   - Verify mail service is running
   - Check Laravel logs for errors

2. **Email templates not found:**
   - Ensure all email template files exist
   - Check file permissions
   - Clear view cache: `php artisan view:clear`

### Order Status Issues

1. **Still getting "Invalid status transition":**
   - Check if the order exists and has correct status
   - Verify the user is the provider of the order
   - Check Laravel logs for detailed error messages

2. **Permission denied:**
   - Ensure the user is authenticated
   - Verify the user is the provider of the order
   - Check if the order exists

### General Issues

1. **Database errors:**
   - Run migrations: `php artisan migrate`
   - Seed database: `php artisan db:seed`
   - Check database connection

2. **Route not found:**
   - Clear route cache: `php artisan route:clear`
   - Check if routes are properly defined in `routes/api.php`

## Verification Checklist

- [ ] Laundry registration sends admin notification email
- [ ] Laundry registration sends welcome email to user
- [ ] Agent registration sends admin notification email
- [ ] Agent registration sends welcome email to user
- [ ] Order status can be updated from pending to in_process
- [ ] Order status can be updated from in_process to completed
- [ ] Order status can be updated from pending to completed (main fix)
- [ ] Invalid status transitions return proper error messages
- [ ] Error messages include current and requested status

## Notes

- The email system is designed to fail gracefully - if emails fail to send, registration will still succeed
- All email failures are logged for debugging
- The order status validation now provides more informative error messages
- The system maintains data integrity while being more flexible with status transitions
