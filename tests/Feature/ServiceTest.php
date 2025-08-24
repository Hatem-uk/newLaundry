<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Service;
use Laravel\Sanctum\Sanctum;

class ServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $companyUser;
    protected $company;
    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create company user and company
        $this->companyUser = User::factory()->create([
            'role' => 'company',
            'status' => 'approved'
        ]);
        
        $this->company = Company::factory()->create([
            'user_id' => $this->companyUser->id
        ]);
        
        // Create admin user
        $this->adminUser = User::factory()->create([
            'role' => 'admin',
            'status' => 'approved'
        ]);
    }

    public function test_company_can_create_service()
    {
        Sanctum::actingAs($this->companyUser);

        $serviceData = [
            'name' => 'Premium Cleaning Service',
            'description' => 'High-quality cleaning service for offices and homes',
            'price' => 150.00,
            'image' => null
        ];

        $response = $this->postJson('/api/company/services', $serviceData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'message',
                    'service' => [
                        'id',
                        'name',
                        'description',
                        'price',
                        'status',
                        'company_id'
                    ]
                ]);

        $this->assertDatabaseHas('services', [
            'name' => 'Premium Cleaning Service',
            'company_id' => $this->company->id,
            'status' => 'pending'
        ]);
    }

    public function test_company_can_view_their_services()
    {
        Sanctum::actingAs($this->companyUser);

        // Create some services
        Service::factory()->count(3)->create([
            'company_id' => $this->company->id
        ]);

        $response = $this->getJson('/api/company/services');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'services' => [
                        '*' => [
                            'id',
                            'name',
                            'description',
                            'price',
                            'status',
                            'company_id'
                        ]
                    ]
                ]);

        $this->assertCount(3, $response->json('services'));
    }

    public function test_company_can_update_their_service()
    {
        Sanctum::actingAs($this->companyUser);

        $service = Service::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'pending'
        ]);

        $updateData = [
            'name' => 'Updated Service Name',
            'description' => 'Updated description',
            'price' => 200.00
        ];

        $response = $this->putJson("/api/company/services/{$service->id}", $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Service updated successfully',
                    'service' => [
                        'name' => 'Updated Service Name',
                        'price' => '200.00'
                    ]
                ]);

        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'name' => 'Updated Service Name',
            'price' => 200.00
        ]);
    }

    public function test_company_cannot_update_approved_service()
    {
        Sanctum::actingAs($this->companyUser);

        $service = Service::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'approved'
        ]);

        $updateData = [
            'name' => 'Updated Service Name',
            'price' => 200.00
        ];

        $response = $this->putJson("/api/company/services/{$service->id}", $updateData);

        $response->assertStatus(400)
                ->assertJson([
                    'message' => 'Only pending services can be updated'
                ]);
    }

    public function test_company_can_delete_pending_service()
    {
        Sanctum::actingAs($this->companyUser);

        $service = Service::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'pending'
        ]);

        $response = $this->deleteJson("/api/company/services/{$service->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Service deleted successfully'
                ]);

        $this->assertDatabaseMissing('services', [
            'id' => $service->id
        ]);
    }

    public function test_company_cannot_delete_approved_service()
    {
        Sanctum::actingAs($this->companyUser);

        $service = Service::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'approved'
        ]);

        $response = $this->deleteJson("/api/company/services/{$service->id}");

        $response->assertStatus(400)
                ->assertJson([
                    'message' => 'Only pending services can be deleted'
                ]);

        $this->assertDatabaseHas('services', [
            'id' => $service->id
        ]);
    }

    public function test_admin_can_view_all_services()
    {
        Sanctum::actingAs($this->adminUser);

        // Create services with different statuses
        Service::factory()->count(2)->create(['status' => 'pending']);
        Service::factory()->count(3)->create(['status' => 'approved']);
        Service::factory()->count(1)->create(['status' => 'rejected']);

        $response = $this->getJson('/api/admin/services');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'services' => [
                        '*' => [
                            'id',
                            'name',
                            'description',
                            'price',
                            'status',
                            'company_id'
                        ]
                    ]
                ]);

        $this->assertCount(6, $response->json('services'));
    }

    public function test_admin_can_view_pending_services()
    {
        Sanctum::actingAs($this->adminUser);

        // Create services with different statuses
        Service::factory()->count(3)->create(['status' => 'pending']);
        Service::factory()->count(2)->create(['status' => 'approved']);

        $response = $this->getJson('/api/admin/services/pending');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'services' => [
                        '*' => [
                            'id',
                            'name',
                            'description',
                            'price',
                            'status',
                            'company_id'
                        ]
                    ]
                ]);

        $this->assertCount(3, $response->json('services'));
        
        // Verify all returned services are pending
        foreach ($response->json('services') as $service) {
            $this->assertEquals('pending', $service['status']);
        }
    }

    public function test_admin_can_approve_service()
    {
        Sanctum::actingAs($this->adminUser);

        $service = Service::factory()->create(['status' => 'pending']);

        $response = $this->postJson("/api/admin/services/{$service->id}/approve", [
            'service_id' => $service->id
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Service approved successfully',
                    'service' => [
                        'id' => $service->id,
                        'status' => 'approved'
                    ]
                ]);

        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'status' => 'approved'
        ]);
    }

    public function test_admin_can_reject_service()
    {
        Sanctum::actingAs($this->adminUser);

        $service = Service::factory()->create(['status' => 'pending']);

        $response = $this->postJson("/api/admin/services/{$service->id}/reject", [
            'service_id' => $service->id
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Service rejected successfully',
                    'service' => [
                        'id' => $service->id,
                        'status' => 'rejected'
                    ]
                ]);

        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'status' => 'rejected'
        ]);
    }

    public function test_admin_cannot_approve_already_approved_service()
    {
        Sanctum::actingAs($this->adminUser);

        $service = Service::factory()->create(['status' => 'approved']);

        $response = $this->postJson("/api/admin/services/{$service->id}/approve", [
            'service_id' => $service->id
        ]);

        $response->assertStatus(400)
                ->assertJson([
                    'message' => 'Service is already approved'
                ]);
    }

    public function test_admin_cannot_reject_already_rejected_service()
    {
        Sanctum::actingAs($this->adminUser);

        $service = Service::factory()->create(['status' => 'rejected']);

        $response = $this->postJson("/api/admin/services/{$service->id}/reject", [
            'service_id' => $service->id
        ]);

        $response->assertStatus(400)
                ->assertJson([
                    'message' => 'Service is already rejected'
                ]);
    }

    public function test_service_creation_validation()
    {
        Sanctum::actingAs($this->companyUser);

        $response = $this->postJson('/api/company/services', [
            'name' => '',
            'description' => '',
            'price' => 'invalid-price'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'description', 'price']);
    }

    public function test_service_update_validation()
    {
        Sanctum::actingAs($this->companyUser);

        $service = Service::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'pending'
        ]);

        $response = $this->putJson("/api/company/services/{$service->id}", [
            'name' => '',
            'price' => 'invalid-price'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'price']);
    }

    public function test_unauthorized_access_to_company_services()
    {
        $response = $this->getJson('/api/company/services');

        $response->assertStatus(401);
    }

    public function test_unauthorized_access_to_admin_services()
    {
        $response = $this->getJson('/api/admin/services');

        $response->assertStatus(401);
    }

    public function test_customer_cannot_access_company_service_management()
    {
        $customerUser = User::factory()->create(['role' => 'customer']);
        Sanctum::actingAs($customerUser);

        $response = $this->postJson('/api/company/services', [
            'name' => 'Test Service',
            'description' => 'Test Description',
            'price' => 100
        ]);

        $response->assertStatus(403);
    }
}
