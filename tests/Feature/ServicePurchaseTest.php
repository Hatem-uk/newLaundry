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

class ServicePurchaseTest extends TestCase
{
    use RefreshDatabase;

    protected $customerUser;
    protected $customer;
    protected $company;
    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->customerUser = User::factory()->create([
            'role' => 'customer',
            'status' => 'approved'
        ]);
        
        $this->customer = Customer::factory()->create([
            'user_id' => $this->customerUser->id,
            'coins' => 1000
        ]);

        $this->company = Company::factory()->create();
        
        $this->service = Service::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'approved',
            'price' => 150.00
        ]);
    }

    public function test_customer_can_browse_available_services()
    {
        Sanctum::actingAs($this->customerUser);

        // Create multiple approved services
        Service::factory()->count(5)->create([
            'status' => 'approved'
        ]);

        $response = $this->getJson('/api/customer/services');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'services' => [
                        '*' => [
                            'id',
                            'name',
                            'description',
                            'price',
                            'status',
                            'company' => [
                                'id',
                                'name'
                            ]
                        ]
                    ]
                ]);

        $this->assertCount(6, $response->json('services')); // 5 + 1 from setUp
    }

    public function test_customer_can_view_service_details()
    {
        Sanctum::actingAs($this->customerUser);

        $response = $this->getJson("/api/customer/services/{$this->service->id}");

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'service' => [
                        'id',
                        'name',
                        'description',
                        'price',
                        'status',
                        'company' => [
                            'id',
                            'name',
                            'address'
                        ]
                    ]
                ])
                ->assertJson([
                    'service' => [
                        'id' => $this->service->id,
                        'name' => $this->service->name,
                        'price' => '150.00'
                    ]
                ]);
    }

    public function test_customer_can_purchase_service()
    {
        Sanctum::actingAs($this->customerUser);

        $purchaseData = [
            'service_id' => $this->service->id,
            'quantity' => 2,
            'notes' => 'Please clean thoroughly'
        ];

        $response = $this->postJson('/api/customer/services/purchase', $purchaseData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'message',
                    'order' => [
                        'id',
                        'customer_id',
                        'service_id',
                        'quantity',
                        'total_cost',
                        'status',
                        'notes'
                    ]
                ])
                ->assertJson([
                    'order' => [
                        'quantity' => 2,
                        'total_cost' => '300.00',
                        'status' => 'pending'
                    ]
                ]);

        // Verify order was created
        $this->assertDatabaseHas('service_orders', [
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id,
            'quantity' => 2,
            'total_cost' => 300.00,
            'status' => 'pending'
        ]);

        // Verify coins were deducted
        $this->assertDatabaseHas('customers', [
            'id' => $this->customer->id,
            'coins' => 700 // 1000 - 300
        ]);

        // Verify transaction was created
        $this->assertDatabaseHas('transactions', [
            'customer_id' => $this->customer->id,
            'type' => 'debit',
            'amount' => 300,
            'reference_type' => 'ServiceOrder'
        ]);
    }

    public function test_customer_cannot_purchase_service_with_insufficient_coins()
    {
        Sanctum::actingAs($this->customerUser);

        // Update customer to have fewer coins
        $this->customer->update(['coins' => 100]);

        $purchaseData = [
            'service_id' => $this->service->id,
            'quantity' => 2, // This would cost 300 coins
            'notes' => 'This should fail'
        ];

        $response = $this->postJson('/api/customer/services/purchase', $purchaseData);

        $response->assertStatus(400)
                ->assertJson([
                    'message' => 'Insufficient coins'
                ]);

        // Verify no order was created
        $this->assertDatabaseMissing('service_orders', [
            'customer_id' => $this->customer->id,
            'service_id' => $this->service->id
        ]);

        // Verify coins were not deducted
        $this->assertDatabaseHas('customers', [
            'id' => $this->customer->id,
            'coins' => 100
        ]);
    }

    public function test_customer_cannot_purchase_invalid_service()
    {
        Sanctum::actingAs($this->customerUser);

        $purchaseData = [
            'service_id' => 99999, // Non-existent service
            'quantity' => 1,
            'notes' => 'This should fail'
        ];

        $response = $this->postJson('/api/customer/services/purchase', $purchaseData);

        $response->assertStatus(404)
                ->assertJson([
                    'message' => 'Service not found'
                ]);
    }

    public function test_customer_cannot_purchase_unapproved_service()
    {
        Sanctum::actingAs($this->customerUser);

        $pendingService = Service::factory()->create([
            'status' => 'pending'
        ]);

        $purchaseData = [
            'service_id' => $pendingService->id,
            'quantity' => 1,
            'notes' => 'This should fail'
        ];

        $response = $this->postJson('/api/customer/services/purchase', $purchaseData);

        $response->assertStatus(400)
                ->assertJson([
                    'message' => 'Service is not available for purchase'
                ]);
    }

    public function test_customer_can_view_purchase_history()
    {
        Sanctum::actingAs($this->customerUser);

        // Create some orders
        ServiceOrder::factory()->count(3)->create([
            'customer_id' => $this->customer->id
        ]);

        $response = $this->getJson('/api/customer/orders');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'orders' => [
                        '*' => [
                            'id',
                            'customer_id',
                            'service_id',
                            'quantity',
                            'total_cost',
                            'status',
                            'notes',
                            'created_at',
                            'service' => [
                                'id',
                                'name',
                                'price'
                            ]
                        ]
                    ]
                ]);

        $this->assertCount(3, $response->json('orders'));
    }

    public function test_customer_can_cancel_order()
    {
        Sanctum::actingAs($this->customerUser);

        $order = ServiceOrder::factory()->create([
            'customer_id' => $this->customer->id,
            'status' => 'pending',
            'total_cost' => 200
        ]);

        $response = $this->postJson("/api/customer/orders/{$order->id}/cancel", [
            'orderId' => $order->id
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Order cancelled successfully',
                    'order' => [
                        'id' => $order->id,
                        'status' => 'cancelled'
                    ]
                ]);

        // Verify order status was updated
        $this->assertDatabaseHas('service_orders', [
            'id' => $order->id,
            'status' => 'cancelled'
        ]);

        // Verify coins were refunded
        $this->assertDatabaseHas('customers', [
            'id' => $this->customer->id,
            'coins' => 1200 // 1000 + 200 refund
        ]);

        // Verify refund transaction was created
        $this->assertDatabaseHas('transactions', [
            'customer_id' => $this->customer->id,
            'type' => 'refund',
            'amount' => 200,
            'reference_type' => 'ServiceOrder'
        ]);
    }

    public function test_customer_cannot_cancel_completed_order()
    {
        Sanctum::actingAs($this->customerUser);

        $order = ServiceOrder::factory()->create([
            'customer_id' => $this->customer->id,
            'status' => 'completed'
        ]);

        $response = $this->postJson("/api/customer/orders/{$order->id}/cancel", [
            'orderId' => $order->id
        ]);

        $response->assertStatus(400)
                ->assertJson([
                    'message' => 'Order cannot be cancelled'
                ]);

        // Verify order status was not changed
        $this->assertDatabaseHas('service_orders', [
            'id' => $order->id,
            'status' => 'completed'
        ]);
    }

    public function test_customer_can_view_spending_power()
    {
        Sanctum::actingAs($this->customerUser);

        $response = $this->getJson('/api/customer/spending-power');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'spending_analysis' => [
                        'current_balance',
                        'total_spent',
                        'total_orders',
                        'average_order_value',
                        'available_services_count'
                    ]
                ])
                ->assertJson([
                    'spending_analysis' => [
                        'current_balance' => 1000
                    ]
                ]);
    }

    public function test_purchase_validation_errors()
    {
        Sanctum::actingAs($this->customerUser);

        $response = $this->postJson('/api/customer/services/purchase', [
            'service_id' => '',
            'quantity' => 'invalid',
            'notes' => ''
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['service_id', 'quantity']);
    }

    public function test_order_cancellation_validation()
    {
        Sanctum::actingAs($this->customerUser);

        $order = ServiceOrder::factory()->create([
            'customer_id' => $this->customer->id
        ]);

        $response = $this->postJson("/api/customer/orders/{$order->id}/cancel", [
            'orderId' => 'invalid-id'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['orderId']);
    }

    public function test_unauthorized_access_to_service_purchase()
    {
        $response = $this->postJson('/api/customer/services/purchase', [
            'service_id' => 1,
            'quantity' => 1
        ]);

        $response->assertStatus(401);
    }

    public function test_unauthorized_access_to_purchase_history()
    {
        $response = $this->getJson('/api/customer/orders');

        $response->assertStatus(401);
    }

    public function test_company_cannot_access_customer_purchase_endpoints()
    {
        $companyUser = User::factory()->create(['role' => 'company']);
        Sanctum::actingAs($companyUser);

        $response = $this->getJson('/api/customer/services');

        $response->assertStatus(403);
    }

    public function test_customer_can_only_see_their_own_orders()
    {
        Sanctum::actingAs($this->customerUser);

        // Create orders for this customer
        ServiceOrder::factory()->count(2)->create([
            'customer_id' => $this->customer->id
        ]);

        // Create orders for another customer
        $otherCustomer = Customer::factory()->create();
        ServiceOrder::factory()->count(3)->create([
            'customer_id' => $otherCustomer->id
        ]);

        $response = $this->getJson('/api/customer/orders');

        $response->assertStatus(200);

        $orders = $response->json('orders');
        $this->assertCount(2, $orders);

        // Verify all orders belong to this customer
        foreach ($orders as $order) {
            $this->assertEquals($this->customer->id, $order['customer_id']);
        }
    }

    public function test_customer_can_only_cancel_their_own_orders()
    {
        Sanctum::actingAs($this->customerUser);

        // Create order for another customer
        $otherCustomer = Customer::factory()->create();
        $otherOrder = ServiceOrder::factory()->create([
            'customer_id' => $otherCustomer->id,
            'status' => 'pending'
        ]);

        $response = $this->postJson("/api/customer/orders/{$otherOrder->id}/cancel", [
            'orderId' => $otherOrder->id
        ]);

        $response->assertStatus(404)
                ->assertJson([
                    'message' => 'Order not found'
                ]);
    }

    public function test_purchase_creates_correct_transaction_references()
    {
        Sanctum::actingAs($this->customerUser);

        $purchaseData = [
            'service_id' => $this->service->id,
            'quantity' => 1,
            'notes' => 'Test purchase'
        ];

        $response = $this->postJson('/api/customer/services/purchase', $purchaseData);

        $response->assertStatus(201);

        $order = $response->json('order');

        // Verify transaction has correct reference
        $this->assertDatabaseHas('transactions', [
            'customer_id' => $this->customer->id,
            'type' => 'debit',
            'amount' => 150,
            'reference_type' => 'ServiceOrder',
            'reference_id' => $order['id']
        ]);
    }

    public function test_cancellation_creates_correct_refund_transaction()
    {
        Sanctum::actingAs($this->customerUser);

        $order = ServiceOrder::factory()->create([
            'customer_id' => $this->customer->id,
            'status' => 'pending',
            'total_cost' => 250
        ]);

        $response = $this->postJson("/api/customer/orders/{$order->id}/cancel", [
            'orderId' => $order->id
        ]);

        $response->assertStatus(200);

        // Verify refund transaction has correct reference
        $this->assertDatabaseHas('transactions', [
            'customer_id' => $this->customer->id,
            'type' => 'refund',
            'amount' => 250,
            'reference_type' => 'ServiceOrder',
            'reference_id' => $order->id
        ]);
    }
}
