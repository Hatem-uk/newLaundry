# 🚀 Laravel Service System - Complete Postman Testing Guide

## 📋 **Overview**
This guide provides step-by-step instructions for testing the complete Laravel Service System using the provided Postman collection.

## 🎯 **What You'll Test**
- **Authentication System** (Registration & Login)
- **Admin Operations** (Service Management)
- **Company Operations** (Profile, Services, Workers)
- **Customer Operations** (Profile, Coins, Transactions)
- **Service Purchase System** (Browse, Purchase, Orders)
- **Error Handling & Validation**

---

## 🔧 **Setup Instructions**

### **1. Import Postman Collection**
1. Download `Laravel_Service_System_Postman_Collection.json`
2. Open Postman
3. Click **Import** → Select the JSON file
4. The collection will appear in your Postman sidebar

### **2. Set Environment Variables**
1. Click **Environment** in Postman
2. Create a new environment called "Laravel Service System"
3. Add these variables:
   ```
   base_url: http://localhost:8000
   admin_token: (leave empty - will auto-fill)
   company_token: (leave empty - will auto-fill)
   customer_token: (leave empty - will auto-fill)
   ```

### **3. Start Your Laravel Server**
```bash
php artisan serve
```

---

## 🧪 **Testing Sequence**

### **Phase 1: 🔐 Authentication & Setup**

#### **Step 1: Register Users**
1. **Register Admin User**
   - Method: `POST`
   - URL: `{{base_url}}/api/auth/register`
   - Body: Admin registration data
   - Expected: 201 Created

2. **Register Company User**
   - Method: `POST`
   - URL: `{{base_url}}/api/auth/register`
   - Body: Company registration data
   - Expected: 201 Created

3. **Register Customer User**
   - Method: `POST`
   - URL: `{{base_url}}/api/auth/register`
   - Body: Customer registration data
   - Expected: 201 Created

#### **Step 2: Login & Get Tokens**
1. **Login Admin**
   - Method: `POST`
   - URL: `{{base_url}}/api/auth/login`
   - Body: Admin credentials
   - **Result**: Token automatically saved to `{{admin_token}}`

2. **Login Company**
   - Method: `POST`
   - URL: `{{base_url}}/api/auth/login`
   - Body: Company credentials
   - **Result**: Token automatically saved to `{{company_token}}`

3. **Login Customer**
   - Method: `POST`
   - URL: `{{base_url}}/api/auth/login`
   - Body: Customer credentials
   - **Result**: Token automatically saved to `{{customer_token}}`

---

### **Phase 2: 👑 Admin Operations**

#### **Step 3: Admin Service Management**
1. **Get All Services (Admin)**
   - Method: `GET`
   - URL: `{{base_url}}/api/admin/services`
   - Headers: `Authorization: Bearer {{admin_token}}`
   - Expected: 200 OK with services list

2. **Get Pending Services (Admin)**
   - Method: `GET`
   - URL: `{{base_url}}/api/admin/services/pending`
   - Headers: `Authorization: Bearer {{admin_token}}`
   - Expected: 200 OK with pending services

3. **Approve Service (Admin)**
   - Method: `POST`
   - URL: `{{base_url}}/api/admin/services/1/approve`
   - Headers: `Authorization: Bearer {{admin_token}}`
   - Body: `{"service_id": 1}`
   - Expected: 200 OK

4. **Reject Service (Admin)**
   - Method: `POST`
   - URL: `{{base_url}}/api/admin/services/2/reject`
   - Headers: `Authorization: Bearer {{admin_token}}`
   - Body: `{"service_id": 2}`
   - Expected: 200 OK

---

### **Phase 3: 🏢 Company Operations**

#### **Step 4: Company Profile Management**
1. **Get Company Profile**
   - Method: `GET`
   - URL: `{{base_url}}/api/company/profile`
   - Headers: `Authorization: Bearer {{company_token}}`
   - Expected: 200 OK with company data

2. **Update Company Profile**
   - Method: `PUT`
   - URL: `{{base_url}}/api/company/profile`
   - Headers: `Authorization: Bearer {{company_token}}`
   - Body: Updated company data
   - Expected: 200 OK

#### **Step 5: Company Service Management**
1. **Get Company Services**
   - Method: `GET`
   - URL: `{{base_url}}/api/company/services`
   - Headers: `Authorization: Bearer {{company_token}}`
   - Expected: 200 OK with services list

2. **Create New Service**
   - Method: `POST`
   - URL: `{{base_url}}/api/company/services`
   - Headers: `Authorization: Bearer {{company_token}}`
   - Body: New service data
   - Expected: 201 Created

3. **Update Service**
   - Method: `PUT`
   - URL: `{{base_url}}/api/company/services/1`
   - Headers: `Authorization: Bearer {{company_token}}`
   - Body: Updated service data
   - Expected: 200 OK

#### **Step 6: Company Worker Management**
1. **Get Company Workers**
   - Method: `GET`
   - URL: `{{base_url}}/api/company/workers`
   - Headers: `Authorization: Bearer {{company_token}}`
   - Expected: 200 OK with workers list

2. **Add New Worker**
   - Method: `POST`
   - URL: `{{base_url}}/api/company/workers`
   - Headers: `Authorization: Bearer {{company_token}}`
   - Body: New worker data
   - Expected: 201 Created

3. **Approve Worker**
   - Method: `POST`
   - URL: `{{base_url}}/api/company/workers/1/approve`
   - Headers: `Authorization: Bearer {{company_token}}`
   - Body: `{"workerId": 1}`
   - Expected: 200 OK

---

### **Phase 4: 👤 Customer Operations**

#### **Step 7: Customer Profile & Coins**
1. **Get Customer Profile**
   - Method: `GET`
   - URL: `{{base_url}}/api/customer/profile`
   - Headers: `Authorization: Bearer {{customer_token}}`
   - Expected: 200 OK with customer data

2. **Update Customer Profile**
   - Method: `PUT`
   - URL: `{{base_url}}/api/customer/profile`
   - Headers: `Authorization: Bearer {{customer_token}}`
   - Body: Updated customer data
   - Expected: 200 OK

3. **Get Customer Coins Balance**
   - Method: `GET`
   - URL: `{{base_url}}/api/customer/coins`
   - Headers: `Authorization: Bearer {{customer_token}}`
   - Expected: 200 OK with coins data

#### **Step 8: Customer Transaction History**
1. **Get Customer Transaction History**
   - Method: `GET`
   - URL: `{{base_url}}/api/customer/transactions`
   - Headers: `Authorization: Bearer {{customer_token}}`
   - Expected: 200 OK with transactions list

2. **Get Recent Transactions**
   - Method: `GET`
   - URL: `{{base_url}}/api/customer/transactions/recent`
   - Headers: `Authorization: Bearer {{customer_token}}`
   - Expected: 200 OK with recent transactions

3. **Get Transaction Summary**
   - Method: `GET`
   - URL: `{{base_url}}/api/customer/transactions/summary`
   - Headers: `Authorization: Bearer {{customer_token}}`
   - Expected: 200 OK with transaction summary

---

### **Phase 5: 🛍️ Service Purchase System**

#### **Step 9: Service Browsing & Purchase**
1. **Browse Available Services**
   - Method: `GET`
   - URL: `{{base_url}}/api/customer/services`
   - Headers: `Authorization: Bearer {{customer_token}}`
   - Expected: 200 OK with available services

2. **Get Service Details**
   - Method: `GET`
   - URL: `{{base_url}}/api/customer/services/1`
   - Headers: `Authorization: Bearer {{customer_token}}`
   - Expected: 200 OK with service details

3. **Purchase Service**
   - Method: `POST`
   - URL: `{{base_url}}/api/customer/services/purchase`
   - Headers: `Authorization: Bearer {{customer_token}}`
   - Body: Purchase data
   - Expected: 201 Created with order details

#### **Step 10: Order Management**
1. **Get Purchase History**
   - Method: `GET`
   - URL: `{{base_url}}/api/customer/orders`
   - Headers: `Authorization: Bearer {{customer_token}}`
   - Expected: 200 OK with orders list

2. **Cancel Order**
   - Method: `POST`
   - URL: `{{base_url}}/api/customer/orders/1/cancel`
   - Headers: `Authorization: Bearer {{customer_token}}`
   - Body: `{"orderId": 1}`
   - Expected: 200 OK with cancellation details

3. **Get Spending Power**
   - Method: `GET`
   - URL: `{{base_url}}/api/customer/spending-power`
   - Headers: `Authorization: Bearer {{customer_token}}`
   - Expected: 200 OK with spending analysis

---

### **Phase 6: 🧪 Testing & Validation**

#### **Step 11: Error Handling Tests**
1. **Test Invalid Token**
   - Method: `GET`
   - URL: `{{base_url}}/api/customer/profile`
   - Headers: `Authorization: Bearer invalid_token_here`
   - Expected: 401 Unauthorized

2. **Test Insufficient Coins**
   - Method: `POST`
   - URL: `{{base_url}}/api/customer/services/purchase`
   - Headers: `Authorization: Bearer {{customer_token}}`
   - Body: Service with high quantity
   - Expected: 400 Bad Request with insufficient coins message

3. **Test Invalid Service ID**
   - Method: `POST`
   - URL: `{{base_url}}/api/customer/services/purchase`
   - Headers: `Authorization: Bearer {{customer_token}}`
   - Body: Non-existent service ID
   - Expected: 404 Not Found or 422 Validation Error

---

## 📊 **Expected Test Results**

### **Successful Responses**
- **200 OK**: Successful GET, PUT, DELETE operations
- **201 Created**: Successful POST operations
- **204 No Content**: Successful operations with no response body

### **Error Responses**
- **400 Bad Request**: Validation errors, insufficient coins
- **401 Unauthorized**: Invalid or missing authentication
- **403 Forbidden**: Insufficient permissions
- **404 Not Found**: Resource doesn't exist
- **422 Unprocessable Entity**: Validation errors
- **500 Internal Server Error**: Server errors

---

## 🔍 **Testing Tips**

### **1. Token Management**
- Tokens are automatically saved after successful login
- Check the console for "token saved" messages
- Verify tokens are properly set in environment variables

### **2. Data Consistency**
- Test in the correct order (setup → operations → validation)
- Use the same service/worker IDs throughout testing
- Check that coin balances update correctly after purchases

### **3. Error Scenarios**
- Test with invalid data to ensure proper validation
- Verify error messages are user-friendly
- Test edge cases (e.g., purchasing more than available coins)

### **4. Performance Testing**
- Test with multiple concurrent requests
- Monitor response times
- Check database performance during heavy operations

---

## 🚨 **Common Issues & Solutions**

### **Issue 1: Token Not Saving**
- **Solution**: Check if the login response contains `token` field
- **Verify**: Look for "token saved" in console

### **Issue 2: 401 Unauthorized**
- **Solution**: Ensure token is properly set in environment
- **Verify**: Check `Authorization: Bearer {{token}}` header

### **Issue 3: 404 Not Found**
- **Solution**: Verify the resource ID exists
- **Verify**: Check if the resource was created in previous steps

### **Issue 4: 422 Validation Error**
- **Solution**: Check request body format
- **Verify**: Ensure all required fields are provided

---

## 📝 **Test Checklist**

- [ ] All users registered successfully
- [ ] All users logged in and tokens saved
- [ ] Admin can view and manage services
- [ ] Company can manage profile, services, and workers
- [ ] Customer can view profile and coin balance
- [ ] Customer can browse and purchase services
- [ ] Customer can view transaction history
- [ ] Customer can cancel orders
- [ ] Error handling works correctly
- [ ] Authentication middleware works
- [ ] Role-based access control works

---

## 🎉 **Congratulations!**

You've successfully tested the complete Laravel Service System! The system includes:
- ✅ User authentication and role management
- ✅ Company service management
- ✅ Customer coin system
- ✅ Service purchase workflow
- ✅ Transaction tracking
- ✅ Order management
- ✅ Proper error handling and validation

Your system is now ready for production use! 🚀

