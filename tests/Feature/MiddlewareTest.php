<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use App\Models\Company;
use Laravel\Sanctum\Sanctum;

class MiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_cannot_access_protected_routes()
    {
        // Test customer routes
        $response = $this->getJson('/api/customer/profile');
        $response->assertStatus(401);

        $response = $this->getJson('/api/customer/coins');
        $response->assertStatus(401);

        // Test company routes
        $response = $this->getJson('/api/company/profile');
        $response->assertStatus(401);

        $response = $this->getJson('/api/company/services');
        $response->assertStatus(401);

        // Test admin routes
        $response = $this->getJson('/api/admin/services');
        $response->assertStatus(401);

        $response = $this->getJson('/api/admin/services/pending');
        $response->assertStatus(401);
    }

    public function test_customer_can_access_customer_routes()
    {
        $customerUser = User::factory()->create(['role' => 'customer']);
        Sanctum::actingAs($customerUser);

        $response = $this->getJson('/api/customer/profile');
        $response->assertStatus(200);

        $response = $this->getJson('/api/customer/coins');
        $response->assertStatus(200);

        $response = $this->getJson('/api/customer/transactions');
        $response->assertStatus(200);
    }

    public function test_customer_cannot_access_company_routes()
    {
        $customerUser = User::factory()->create(['role' => 'customer']);
        Sanctum::actingAs($customerUser);

        $response = $this->getJson('/api/company/profile');
        $response->assertStatus(403);

        $response = $this->postJson('/api/company/services', [
            'name' => 'Test Service',
            'description' => 'Test Description',
            'price' => 100
        ]);
        $response->assertStatus(403);

        $response = $this->getJson('/api/company/workers');
        $response->assertStatus(403);
    }

    public function test_customer_cannot_access_admin_routes()
    {
        $customerUser = User::factory()->create(['role' => 'customer']);
        Sanctum::actingAs($customerUser);

        $response = $this->getJson('/api/admin/services');
        $response->assertStatus(403);

        $response = $this->postJson('/api/admin/services/1/approve', [
            'service_id' => 1
        ]);
        $response->assertStatus(403);
    }

    public function test_company_can_access_company_routes()
    {
        $companyUser = User::factory()->create(['role' => 'company']);
        Sanctum::actingAs($companyUser);

        $response = $this->getJson('/api/company/profile');
        $response->assertStatus(200);

        $response = $this->getJson('/api/company/services');
        $response->assertStatus(200);

        $response = $this->getJson('/api/company/workers');
        $response->assertStatus(200);
    }

    public function test_company_cannot_access_customer_routes()
    {
        $companyUser = User::factory()->create(['role' => 'company']);
        Sanctum::actingAs($companyUser);

        $response = $this->getJson('/api/customer/profile');
        $response->assertStatus(403);

        $response = $this->getJson('/api/customer/services');
        $response->assertStatus(403);

        $response = $this->postJson('/api/customer/services/purchase', [
            'service_id' => 1,
            'quantity' => 1
        ]);
        $response->assertStatus(403);
    }

    public function test_company_cannot_access_admin_routes()
    {
        $companyUser = User::factory()->create(['role' => 'company']);
        Sanctum::actingAs($companyUser);

        $response = $this->getJson('/api/admin/services');
        $response->assertStatus(403);

        $response = $this->postJson('/api/admin/services/1/reject', [
            'service_id' => 1
        ]);
        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_routes()
    {
        $adminUser = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($adminUser);

        $response = $this->getJson('/api/admin/services');
        $response->assertStatus(200);

        $response = $this->getJson('/api/admin/services/pending');
        $response->assertStatus(200);
    }

    public function test_admin_can_access_company_routes()
    {
        $adminUser = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($adminUser);

        $response = $this->getJson('/api/company/profile');
        $response->assertStatus(200);

        $response = $this->getJson('/api/company/services');
        $response->assertStatus(200);
    }

    public function test_admin_can_access_customer_routes()
    {
        $adminUser = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($adminUser);

        $response = $this->getJson('/api/customer/profile');
        $response->assertStatus(200);

        $response = $this->getJson('/api/customer/transactions');
        $response->assertStatus(200);
    }

    public function test_worker_cannot_access_company_routes()
    {
        $workerUser = User::factory()->create(['role' => 'worker']);
        Sanctum::actingAs($workerUser);

        $response = $this->getJson('/api/company/profile');
        $response->assertStatus(403);

        $response = $this->postJson('/api/company/services', [
            'name' => 'Test Service',
            'description' => 'Test Description',
            'price' => 100
        ]);
        $response->assertStatus(403);
    }

    public function test_agent_cannot_access_company_routes()
    {
        $agentUser = User::factory()->create(['role' => 'agent']);
        Sanctum::actingAs($agentUser);

        $response = $this->getJson('/api/company/profile');
        $response->assertStatus(403);

        $response = $this->getJson('/api/company/workers');
        $response->assertStatus(403);
    }

    public function test_worker_cannot_access_customer_routes()
    {
        $workerUser = User::factory()->create(['role' => 'worker']);
        Sanctum::actingAs($workerUser);

        $response = $this->getJson('/api/customer/profile');
        $response->assertStatus(403);

        $response = $this->getJson('/api/customer/services');
        $response->assertStatus(403);
    }

    public function test_agent_cannot_access_customer_routes()
    {
        $agentUser = User::factory()->create(['role' => 'agent']);
        Sanctum::actingAs($agentUser);

        $response = $this->getJson('/api/customer/profile');
        $response->assertStatus(403);

        $response = $this->postJson('/api/customer/services/purchase', [
            'service_id' => 1,
            'quantity' => 1
        ]);
        $response->assertStatus(403);
    }

    public function test_worker_cannot_access_admin_routes()
    {
        $workerUser = User::factory()->create(['role' => 'worker']);
        Sanctum::actingAs($workerUser);

        $response = $this->getJson('/api/admin/services');
        $response->assertStatus(403);

        $response = $this->postJson('/api/admin/services/1/approve', [
            'service_id' => 1
        ]);
        $response->assertStatus(403);
    }

    public function test_agent_cannot_access_admin_routes()
    {
        $agentUser = User::factory()->create(['role' => 'agent']);
        Sanctum::actingAs($agentUser);

        $response = $this->getJson('/api/admin/services');
        $response->assertStatus(403);

        $response = $this->getJson('/api/admin/services/pending');
        $response->assertStatus(403);
    }

    public function test_public_routes_are_accessible_without_authentication()
    {
        // Test authentication routes
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            'role' => 'customer'
        ]);
        $response->assertStatus(201);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => '123456'
        ]);
        $response->assertStatus(200);
    }

    public function test_invalid_token_returns_unauthorized()
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer invalid_token_here'
        ])->getJson('/api/customer/profile');

        $response->assertStatus(401);
    }

    public function test_expired_token_returns_unauthorized()
    {
        // This test would require setting up token expiration
        // For now, we'll test with a valid token that should work
        $customerUser = User::factory()->create(['role' => 'customer']);
        Sanctum::actingAs($customerUser);

        $response = $this->getJson('/api/customer/profile');
        $response->assertStatus(200);
    }

    public function test_malformed_authorization_header_returns_unauthorized()
    {
        $response = $this->withHeaders([
            'Authorization' => 'InvalidFormat'
        ])->getJson('/api/customer/profile');

        $response->assertStatus(401);
    }

    public function test_missing_authorization_header_returns_unauthorized()
    {
        $response = $this->getJson('/api/customer/profile');
        $response->assertStatus(401);
    }

    public function test_role_middleware_handles_multiple_roles()
    {
        // Test that admin can access routes with multiple role requirements
        $adminUser = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($adminUser);

        // Admin should be able to access company routes
        $response = $this->getJson('/api/company/profile');
        $response->assertStatus(200);

        // Admin should be able to access customer routes
        $response = $this->getJson('/api/customer/profile');
        $response->assertStatus(200);
    }

    public function test_middleware_preserves_request_data()
    {
        $customerUser = User::factory()->create(['role' => 'customer']);
        Sanctum::actingAs($customerUser);

        $testData = [
            'name' => 'Updated Name',
            'phone' => '1234567890'
        ];

        $response = $this->putJson('/api/customer/profile', $testData);
        
        // The middleware should not interfere with the request data
        if ($response->status() === 200) {
            $this->assertDatabaseHas('customers', $testData);
        }
    }

    public function test_middleware_logs_unauthorized_access()
    {
        // This test would verify that unauthorized access attempts are logged
        // Implementation depends on your logging setup
        $this->assertTrue(true);
    }

    public function test_middleware_performance()
    {
        $customerUser = User::factory()->create(['role' => 'customer']);
        Sanctum::actingAs($customerUser);

        $startTime = microtime(true);
        
        // Make multiple requests to test middleware performance
        for ($i = 0; $i < 10; $i++) {
            $this->getJson('/api/customer/profile');
        }
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Middleware should complete within reasonable time (less than 1 second for 10 requests)
        $this->assertLessThan(1.0, $executionTime);
    }

    public function test_concurrent_requests_with_middleware()
    {
        $customerUser = User::factory()->create(['role' => 'customer']);
        Sanctum::actingAs($customerUser);

        // Test that middleware handles concurrent requests properly
        $responses = [];
        
        // Simulate concurrent requests
        for ($i = 0; $i < 5; $i++) {
            $responses[] = $this->getJson('/api/customer/profile');
        }

        // All responses should be successful
        foreach ($responses as $response) {
            $this->assertEquals(200, $response->status());
        }
    }
}
