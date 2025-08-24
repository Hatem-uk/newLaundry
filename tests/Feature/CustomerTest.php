<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use App\Models\Transaction;
use Laravel\Sanctum\Sanctum;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    protected $customerUser;
    protected $customer;

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
    }

    public function test_customer_can_view_profile()
    {
        Sanctum::actingAs($this->customerUser);

        $response = $this->getJson('/api/customer/profile');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'customer' => [
                        'id',
                        'user_id',
                        'phone',
                        'address',
                        'coins',
                        'user' => [
                            'id',
                            'name',
                            'email'
                        ]
                    ],
                    'transaction_summary' => [
                        'total_earned',
                        'total_spent',
                        'net_balance',
                        'current_balance'
                    ]
                ])
                ->assertJson([
                    'customer' => [
                        'id' => $this->customer->id,
                        'coins' => 1000
                    ]
                ]);
    }

    public function test_customer_can_update_profile()
    {
        Sanctum::actingAs($this->customerUser);

        $updateData = [
            'name' => 'John Smith Updated',
            'phone' => '01000000011',
            'address' => '789 Updated Customer Street, New Area, City'
        ];

        $response = $this->putJson('/api/customer/profile', $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Customer profile updated successfully',
                    'customer' => [
                        'name' => 'John Smith Updated',
                        'phone' => '01000000011',
                        'address' => '789 Updated Customer Street, New Area, City'
                    ]
                ]);

        $this->assertDatabaseHas('customers', [
            'id' => $this->customer->id,
            'name' => 'John Smith Updated',
            'phone' => '01000000011',
            'address' => '789 Updated Customer Street, New Area, City'
        ]);
    }

    public function test_customer_can_view_coin_balance()
    {
        Sanctum::actingAs($this->customerUser);

        $response = $this->getJson('/api/customer/coins');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'customer' => [
                        'id',
                        'name',
                        'coins'
                    ],
                    'transaction_summary' => [
                        'total_earned',
                        'total_spent',
                        'net_balance',
                        'current_balance'
                    ]
                ])
                ->assertJson([
                    'customer' => [
                        'coins' => 1000
                    ]
                ]);
    }

    public function test_customer_can_view_transaction_history()
    {
        Sanctum::actingAs($this->customerUser);

        // Create some transactions
        Transaction::factory()->count(5)->create([
            'customer_id' => $this->customer->id
        ]);

        $response = $this->getJson('/api/customer/transactions');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'transactions' => [
                        '*' => [
                            'id',
                            'type',
                            'amount',
                            'description',
                            'balance_after',
                            'status',
                            'created_at'
                        ]
                    ],
                    'pagination' => [
                        'current_page',
                        'per_page',
                        'total'
                    ]
                ]);

        $this->assertCount(5, $response->json('transactions'));
    }

    public function test_customer_can_view_recent_transactions()
    {
        Sanctum::actingAs($this->customerUser);

        // Create some transactions
        Transaction::factory()->count(10)->create([
            'customer_id' => $this->customer->id
        ]);

        $response = $this->getJson('/api/customer/transactions/recent');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'transactions' => [
                        '*' => [
                            'id',
                            'type',
                            'amount',
                            'description',
                            'balance_after',
                            'status',
                            'created_at'
                        ]
                    ]
                ]);

        // Should return only recent transactions (limited to 5)
        $this->assertLessThanOrEqual(5, count($response->json('transactions')));
    }

    public function test_customer_can_view_transaction_summary()
    {
        Sanctum::actingAs($this->customerUser);

        // Create transactions with different types
        Transaction::factory()->create([
            'customer_id' => $this->customer->id,
            'type' => 'credit',
            'amount' => 500,
            'balance_after' => 1500
        ]);

        Transaction::factory()->create([
            'customer_id' => $this->customer->id,
            'type' => 'debit',
            'amount' => 200,
            'balance_after' => 1300
        ]);

        $response = $this->getJson('/api/customer/transactions/summary');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'summary' => [
                        'total_credits',
                        'total_debits',
                        'current_balance',
                        'total_transactions'
                    ]
                ])
                ->assertJson([
                    'summary' => [
                        'total_credits' => 500,
                        'total_debits' => 200,
                        'current_balance' => 1300
                    ]
                ]);
    }

    public function test_customer_can_filter_transactions_by_type()
    {
        Sanctum::actingAs($this->customerUser);

        // Create transactions with different types
        Transaction::factory()->count(3)->create([
            'customer_id' => $this->customer->id,
            'type' => 'credit'
        ]);

        Transaction::factory()->count(2)->create([
            'customer_id' => $this->customer->id,
            'type' => 'debit'
        ]);

        $response = $this->getJson('/api/customer/transactions?type=credit');

        $response->assertStatus(200);

        $transactions = $response->json('transactions');
        $this->assertCount(3, $transactions);

        // Verify all returned transactions are credits
        foreach ($transactions as $transaction) {
            $this->assertEquals('credit', $transaction['type']);
        }
    }

    public function test_customer_can_filter_transactions_by_status()
    {
        Sanctum::actingAs($this->customerUser);

        // Create transactions with different statuses
        Transaction::factory()->count(4)->create([
            'customer_id' => $this->customer->id,
            'status' => 'completed'
        ]);

        Transaction::factory()->count(1)->create([
            'customer_id' => $this->customer->id,
            'status' => 'pending'
        ]);

        $response = $this->getJson('/api/customer/transactions?status=completed');

        $response->assertStatus(200);

        $transactions = $response->json('transactions');
        $this->assertCount(4, $transactions);

        // Verify all returned transactions are completed
        foreach ($transactions as $transaction) {
            $this->assertEquals('completed', $transaction['status']);
        }
    }

    public function test_customer_can_paginate_transactions()
    {
        Sanctum::actingAs($this->customerUser);

        // Create more transactions than default per page
        Transaction::factory()->count(15)->create([
            'customer_id' => $this->customer->id
        ]);

        $response = $this->getJson('/api/customer/transactions?page=1&per_page=10');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'transactions',
                    'pagination' => [
                        'current_page',
                        'per_page',
                        'total',
                        'last_page'
                    ]
                ]);

        $pagination = $response->json('pagination');
        $this->assertEquals(1, $pagination['current_page']);
        $this->assertEquals(10, $pagination['per_page']);
        $this->assertEquals(15, $pagination['total']);
        $this->assertEquals(2, $pagination['last_page']);

        // Should return 10 transactions on first page
        $this->assertCount(10, $response->json('transactions'));
    }

    public function test_customer_profile_update_validation()
    {
        Sanctum::actingAs($this->customerUser);

        $response = $this->putJson('/api/customer/profile', [
            'name' => '',
            'phone' => 'invalid-phone'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'phone']);
    }

    public function test_unauthorized_access_to_customer_profile()
    {
        $response = $this->getJson('/api/customer/profile');

        $response->assertStatus(401);
    }

    public function test_unauthorized_access_to_customer_coins()
    {
        $response = $this->getJson('/api/customer/coins');

        $response->assertStatus(401);
    }

    public function test_unauthorized_access_to_customer_transactions()
    {
        $response = $this->getJson('/api/customer/transactions');

        $response->assertStatus(401);
    }

    public function test_company_cannot_access_customer_endpoints()
    {
        $companyUser = User::factory()->create(['role' => 'company']);
        Sanctum::actingAs($companyUser);

        $response = $this->getJson('/api/customer/profile');

        $response->assertStatus(403);
    }

    public function test_customer_can_only_see_their_own_transactions()
    {
        Sanctum::actingAs($this->customerUser);

        // Create transactions for this customer
        Transaction::factory()->count(3)->create([
            'customer_id' => $this->customer->id
        ]);

        // Create transactions for another customer
        $otherCustomer = Customer::factory()->create();
        Transaction::factory()->count(2)->create([
            'customer_id' => $otherCustomer->id
        ]);

        $response = $this->getJson('/api/customer/transactions');

        $response->assertStatus(200);

        $transactions = $response->json('transactions');
        $this->assertCount(3, $transactions);

        // Verify all transactions belong to this customer
        foreach ($transactions as $transaction) {
            $this->assertEquals($this->customer->id, $transaction['customer_id']);
        }
    }

    public function test_customer_coin_balance_is_formatted_correctly()
    {
        Sanctum::actingAs($this->customerUser);

        $response = $this->getJson('/api/customer/coins');

        $response->assertStatus(200);

        $customer = $response->json('customer');
        $this->assertIsInt($customer['coins']);
        $this->assertEquals(1000, $customer['coins']);
    }

    public function test_transaction_amounts_are_formatted_correctly()
    {
        Sanctum::actingAs($this->customerUser);

        Transaction::factory()->create([
            'customer_id' => $this->customer->id,
            'type' => 'credit',
            'amount' => 1500
        ]);

        $response = $this->getJson('/api/customer/transactions');

        $response->assertStatus(200);

        $transaction = $response->json('transactions')[0];
        $this->assertIsInt($transaction['amount']);
        $this->assertEquals(1500, $transaction['amount']);
    }
}
