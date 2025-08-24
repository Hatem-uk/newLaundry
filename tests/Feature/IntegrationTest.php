<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use App\Models\Company;
use App\Models\Service;
use App\Models\ServiceOrder;
use App\Models\Transaction;
use Laravel\Sanctum\Sanctum;

class IntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_workflow_from_registration_to_service_purchase()
    {
        // Step 1: Register a customer
        $customerResponse = $this->postJson('/api/auth/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            'role' => 'customer',
            'phone' => '1234567890',
            'address' => '123 Main St'
        ]);

        $customerResponse->assertStatus(201);
        $customerToken = $customerResponse->json('token');

        // Step 2: Register a company
        $companyResponse = $this->postJson('/api/auth/register', [
            'name' => 'CleanPro Services',
            'email' => 'company@example.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            'role' => 'company',
            'phone' => '0987654321',
            'address' => '456 Business Ave'
        ]);

        $companyResponse->assertStatus(201);
        $companyToken = $companyResponse->json('token');

        // Step 3: Company creates a service
        Sanctum::actingAs(User::where('email', 'company@example.com')->first());

        $serviceResponse = $this->postJson('/api/company/services', [
            'name' => 'Premium Cleaning',
            'description' => 'High-quality cleaning service',
            'price' => 200.00,
            'image' => null
        ]);

        $serviceResponse->assertStatus(201);
        $serviceId = $serviceResponse->json('service.id');

        // Step 4: Admin approves the service
        $adminUser = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($adminUser);

        $approvalResponse = $this->postJson("/api/admin/services/{$serviceId}/approve", [
            'service_id' => $serviceId
        ]);

        $approvalResponse->assertStatus(200);

        // Step 5: Customer browses services
        Sanctum::actingAs(User::where('email', 'john@example.com')->first());

        $servicesResponse = $this->getJson('/api/customer/services');
        $servicesResponse->assertStatus(200);
        $this->assertCount(1, $servicesResponse->json('services'));

        // Step 6: Customer purchases the service
        $purchaseResponse = $this->postJson('/api/customer/services/purchase', [
            'service_id' => $serviceId,
            'quantity' => 1,
            'notes' => 'Please clean thoroughly'
        ]);

        $purchaseResponse->assertStatus(201);
        $orderId = $purchaseResponse->json('order.id');

        // Step 7: Verify the purchase was recorded
        $this->assertDatabaseHas('service_orders', [
            'id' => $orderId,
            'customer_id' => Customer::where('email', 'john@example.com')->first()->id,
            'service_id' => $serviceId,
            'status' => 'pending'
        ]);

        // Step 8: Verify coins were deducted
        $customer = Customer::where('email', 'john@example.com')->first();
        $this->assertEquals(800, $customer->coins); // 1000 - 200

        // Step 9: Verify transaction was created
        $this->assertDatabaseHas('transactions', [
            'customer_id' => $customer->id,
            'type' => 'debit',
            'amount' => 200,
            'reference_type' => 'ServiceOrder'
        ]);

        // Step 10: Customer views purchase history
        $ordersResponse = $this->getJson('/api/customer/orders');
        $ordersResponse->assertStatus(200);
        $this->assertCount(1, $ordersResponse->json('orders'));

        // Step 11: Customer cancels the order
        $cancelResponse = $this->postJson("/api/customer/orders/{$orderId}/cancel", [
            'orderId' => $orderId
        ]);

        $cancelResponse->assertStatus(200);

        // Step 12: Verify order was cancelled
        $this->assertDatabaseHas('service_orders', [
            'id' => $orderId,
            'status' => 'cancelled'
        ]);

        // Step 13: Verify coins were refunded
        $customer->refresh();
        $this->assertEquals(1000, $customer->coins); // 800 + 200 refund

        // Step 14: Verify refund transaction was created
        $this->assertDatabaseHas('transactions', [
            'customer_id' => $customer->id,
            'type' => 'refund',
            'amount' => 200,
            'reference_type' => 'ServiceOrder'
        ]);
    }

    public function test_admin_service_management_workflow()
    {
        // Create company and admin
        $companyUser = User::factory()->create(['role' => 'company']);
        $company = Company::factory()->create(['user_id' => $companyUser->id]);
        
        $adminUser = User::factory()->create(['role' => 'admin']);

        // Company creates multiple services
        Sanctum::actingAs($companyUser);

        $service1 = Service::factory()->create([
            'company_id' => $company->id,
            'status' => 'pending'
        ]);

        $service2 = Service::factory()->create([
            'company_id' => $company->id,
            'status' => 'pending'
        ]);

        $service3 = Service::factory()->create([
            'company_id' => $company->id,
            'status' => 'pending'
        ]);

        // Admin views all services
        Sanctum::actingAs($adminUser);

        $allServicesResponse = $this->getJson('/api/admin/services');
        $allServicesResponse->assertStatus(200);
        $this->assertCount(3, $allServicesResponse->json('services'));

        // Admin views pending services
        $pendingServicesResponse = $this->getJson('/api/admin/services/pending');
        $pendingServicesResponse->assertStatus(200);
        $this->assertCount(3, $pendingServicesResponse->json('services'));

        // Admin approves one service
        $approveResponse = $this->postJson("/api/admin/services/{$service1->id}/approve", [
            'service_id' => $service1->id
        ]);

        $approveResponse->assertStatus(200);

        // Admin rejects another service
        $rejectResponse = $this->postJson("/api/admin/services/{$service2->id}/reject", [
            'service_id' => $service2->id
        ]);

        $rejectResponse->assertStatus(200);

        // Verify service statuses
        $this->assertDatabaseHas('services', [
            'id' => $service1->id,
            'status' => 'approved'
        ]);

        $this->assertDatabaseHas('services', [
            'id' => $service2->id,
            'status' => 'rejected'
        ]);

        $this->assertDatabaseHas('services', [
            'id' => $service3->id,
            'status' => 'pending'
        ]);

        // Admin views pending services again (should be 1 now)
        $pendingServicesResponse2 = $this->getJson('/api/admin/services/pending');
        $pendingServicesResponse2->assertStatus(200);
        $this->assertCount(1, $pendingServicesResponse2->json('services'));
    }

    public function test_company_worker_management_workflow()
    {
        // Create company
        $companyUser = User::factory()->create(['role' => 'company']);
        $company = Company::factory()->create(['user_id' => $companyUser->id]);

        Sanctum::actingAs($companyUser);

        // Company adds workers
        $worker1Response = $this->postJson('/api/company/workers', [
            'name' => 'Worker One',
            'email' => 'worker1@example.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            'phone' => '1111111111',
            'position' => 'Cleaner',
            'salary' => 2500,
            'status' => 'pending'
        ]);

        $worker1Response->assertStatus(201);
        $worker1Id = $worker1Response->json('worker.id');

        $worker2Response = $this->postJson('/api/company/workers', [
            'name' => 'Worker Two',
            'email' => 'worker2@example.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            'phone' => '2222222222',
            'position' => 'Supervisor',
            'salary' => 3500,
            'status' => 'pending'
        ]);

        $worker2Response->assertStatus(201);
        $worker2Id = $worker2Response->json('worker.id');

        // Company views workers
        $workersResponse = $this->getJson('/api/company/workers');
        $workersResponse->assertStatus(200);
        $this->assertCount(2, $workersResponse->json('workers'));

        // Company approves one worker
        $approveResponse = $this->postJson("/api/company/workers/{$worker1Id}/approve", [
            'workerId' => $worker1Id
        ]);

        $approveResponse->assertStatus(200);

        // Verify worker status
        $this->assertDatabaseHas('workers', [
            'id' => $worker1Id,
            'status' => 'approved'
        ]);

        // Verify user status was also updated
        $worker1 = \App\Models\Worker::find($worker1Id);
        $this->assertDatabaseHas('users', [
            'id' => $worker1->user_id,
            'status' => 'approved'
        ]);

        // Company views pending workers (should be 1 now)
        $pendingWorkersResponse = $this->getJson('/api/company/workers?status=pending');
        $pendingWorkersResponse->assertStatus(200);
        $this->assertCount(1, $pendingWorkersResponse->json('workers'));
    }

    public function test_customer_transaction_workflow()
    {
        // Create customer
        $customerUser = User::factory()->create(['role' => 'customer']);
        $customer = Customer::factory()->create([
            'user_id' => $customerUser->id,
            'coins' => 1000
        ]);

        Sanctum::actingAs($customerUser);

        // Customer adds bonus coins
        $customer->addCoins(500, 'Welcome bonus');

        // Customer deducts coins for purchase
        $customer->deductCoins(200, 'Service purchase');

        // Customer gets refund
        $customer->refundCoins(100, 'Partial refund');

        // Customer adds loyalty bonus
        $customer->addBonusCoins(50, 'Loyalty reward');

        // Verify final balance
        $customer->refresh();
        $this->assertEquals(1350, $customer->coins); // 1000 + 500 - 200 + 100 + 50

        // Verify all transactions were created
        $this->assertDatabaseHas('transactions', [
            'customer_id' => $customer->id,
            'type' => 'credit',
            'amount' => 500,
            'description' => 'Welcome bonus'
        ]);

        $this->assertDatabaseHas('transactions', [
            'customer_id' => $customer->id,
            'type' => 'debit',
            'amount' => 200,
            'description' => 'Service purchase'
        ]);

        $this->assertDatabaseHas('transactions', [
            'customer_id' => $customer->id,
            'type' => 'refund',
            'amount' => 100,
            'description' => 'Partial refund'
        ]);

        $this->assertDatabaseHas('transactions', [
            'customer_id' => $customer->id,
            'type' => 'bonus',
            'amount' => 50,
            'description' => 'Loyalty reward'
        ]);

        // Customer views transaction history
        $transactionsResponse = $this->getJson('/api/customer/transactions');
        $transactionsResponse->assertStatus(200);
        $this->assertCount(4, $transactionsResponse->json('transactions'));

        // Customer views transaction summary
        $summaryResponse = $this->getJson('/api/customer/transactions/summary');
        $summaryResponse->assertStatus(200);
        
        $summary = $summaryResponse->json('summary');
        $this->assertEquals(550, $summary['total_credits']); // 500 + 50
        $this->assertEquals(200, $summary['total_debits']);
        $this->assertEquals(1350, $summary['current_balance']);
        $this->assertEquals(4, $summary['total_transactions']);
    }

    public function test_error_handling_and_validation()
    {
        // Test invalid registration data
        $invalidRegistrationResponse = $this->postJson('/api/auth/register', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => '123',
            'password_confirmation' => '456'
        ]);

        $invalidRegistrationResponse->assertStatus(422)
                                  ->assertJsonValidationErrors(['name', 'email', 'password']);

        // Test invalid login data
        $invalidLoginResponse = $this->postJson('/api/auth/login', [
            'email' => '',
            'password' => ''
        ]);

        $invalidLoginResponse->assertStatus(422)
                            ->assertJsonValidationErrors(['email', 'password']);

        // Test insufficient coins scenario
        $customerUser = User::factory()->create(['role' => 'customer']);
        $customer = Customer::factory()->create([
            'user_id' => $customerUser->id,
            'coins' => 100
        ]);

        $company = Company::factory()->create();
        $service = Service::factory()->create([
            'company_id' => $company->id,
            'status' => 'approved',
            'price' => 200.00
        ]);

        Sanctum::actingAs($customerUser);

        $insufficientCoinsResponse = $this->postJson('/api/customer/services/purchase', [
            'service_id' => $service->id,
            'quantity' => 1,
            'notes' => 'This should fail'
        ]);

        $insufficientCoinsResponse->assertStatus(400)
                                 ->assertJson([
                                     'message' => 'Insufficient coins'
                                 ]);

        // Test invalid service ID
        $invalidServiceResponse = $this->postJson('/api/customer/services/purchase', [
            'service_id' => 99999,
            'quantity' => 1,
            'notes' => 'This should fail'
        ]);

        $invalidServiceResponse->assertStatus(404)
                              ->assertJson([
                                  'message' => 'Service not found'
                              ]);
    }

    public function test_data_integrity_and_consistency()
    {
        // Create customer and service
        $customerUser = User::factory()->create(['role' => 'customer']);
        $customer = Customer::factory()->create([
            'user_id' => $customerUser->id,
            'coins' => 1000
        ]);

        $company = Company::factory()->create();
        $service = Service::factory()->create([
            'company_id' => $company->id,
            'status' => 'approved',
            'price' => 150.00
        ]);

        Sanctum::actingAs($customerUser);

        // Make multiple purchases
        $purchase1 = $this->postJson('/api/customer/services/purchase', [
            'service_id' => $service->id,
            'quantity' => 1,
            'notes' => 'First purchase'
        ]);

        $purchase1->assertStatus(201);

        $purchase2 = $this->postJson('/api/customer/services/purchase', [
            'service_id' => $service->id,
            'quantity' => 2,
            'notes' => 'Second purchase'
        ]);

        $purchase2->assertStatus(201);

        // Verify customer balance is correct
        $customer->refresh();
        $expectedBalance = 1000 - 150 - (150 * 2); // 1000 - 150 - 300 = 550
        $this->assertEquals($expectedBalance, $customer->coins);

        // Verify all transactions sum up correctly
        $transactions = Transaction::where('customer_id', $customer->id)->get();
        $totalDebits = $transactions->where('type', 'debit')->sum('amount');
        $totalCredits = $transactions->where('type', 'credit')->sum('amount');
        
        $this->assertEquals(450, $totalDebits); // 150 + 300
        $this->assertEquals(1000, $totalCredits); // Initial coins
        $this->assertEquals($expectedBalance, $customer->coins);

        // Verify service orders are linked correctly
        $orders = ServiceOrder::where('customer_id', $customer->id)->get();
        $this->assertCount(2, $orders);

        foreach ($orders as $order) {
            $this->assertEquals($service->id, $order->service_id);
            $this->assertEquals($customer->id, $order->customer_id);
        }
    }
}
