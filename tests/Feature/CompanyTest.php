<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Company;
use App\Models\Worker;
use Laravel\Sanctum\Sanctum;

class CompanyTest extends TestCase
{
    use RefreshDatabase;

    protected $companyUser;
    protected $company;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->companyUser = User::factory()->create([
            'role' => 'company',
            'status' => 'approved'
        ]);
        
        $this->company = Company::factory()->create([
            'user_id' => $this->companyUser->id
        ]);
    }

    public function test_company_can_view_profile()
    {
        Sanctum::actingAs($this->companyUser);

        $response = $this->getJson('/api/company/profile');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'company' => [
                        'id',
                        'name',
                        'address',
                        'phone',
                        'user_id'
                    ]
                ])
                ->assertJson([
                    'company' => [
                        'id' => $this->company->id,
                        'name' => $this->company->name
                    ]
                ]);
    }

    public function test_company_can_update_profile()
    {
        Sanctum::actingAs($this->companyUser);

        $updateData = [
            'name' => 'Updated Company Name',
            'address' => '456 Updated Business Street, Downtown, City',
            'phone' => '01000000009'
        ];

        $response = $this->putJson('/api/company/profile', $updateData);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Company profile updated successfully',
                    'company' => [
                        'name' => 'Updated Company Name',
                        'address' => '456 Updated Business Street, Downtown, City',
                        'phone' => '01000000009'
                    ]
                ]);

        $this->assertDatabaseHas('companies', [
            'id' => $this->company->id,
            'name' => 'Updated Company Name',
            'address' => '456 Updated Business Street, Downtown, City',
            'phone' => '01000000009'
        ]);
    }

    public function test_company_can_add_worker()
    {
        Sanctum::actingAs($this->companyUser);

        $workerData = [
            'name' => 'Worker Name',
            'email' => 'worker@example.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            'phone' => '01000000010',
            'position' => 'Cleaner',
            'salary' => 2500,
            'status' => 'pending'
        ];

        $response = $this->postJson('/api/company/workers', $workerData);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'message',
                    'worker' => [
                        'id',
                        'name',
                        'email',
                        'phone',
                        'position',
                        'salary',
                        'status'
                    ]
                ]);

        $this->assertDatabaseHas('users', [
            'email' => 'worker@example.com',
            'role' => 'worker',
            'status' => 'pending'
        ]);

        $this->assertDatabaseHas('workers', [
            'name' => 'Worker Name',
            'position' => 'Cleaner',
            'salary' => 2500,
            'company_id' => $this->company->id
        ]);
    }

    public function test_company_can_view_workers()
    {
        Sanctum::actingAs($this->companyUser);

        // Create some workers
        Worker::factory()->count(3)->create([
            'company_id' => $this->company->id
        ]);

        $response = $this->getJson('/api/company/workers');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'workers' => [
                        '*' => [
                            'id',
                            'name',
                            'email',
                            'phone',
                            'position',
                            'salary',
                            'status'
                        ]
                    ]
                ]);

        $this->assertCount(3, $response->json('workers'));
    }

    public function test_company_can_approve_worker()
    {
        Sanctum::actingAs($this->companyUser);

        $worker = Worker::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'pending'
        ]);

        $response = $this->postJson("/api/company/workers/{$worker->id}/approve", [
            'workerId' => $worker->id
        ]);

        $response->assertStatus(200)
                ->assertJson([
                    'message' => 'Worker approved successfully',
                    'worker' => [
                        'id' => $worker->id,
                        'status' => 'approved'
                    ]
                ]);

        $this->assertDatabaseHas('workers', [
            'id' => $worker->id,
            'status' => 'approved'
        ]);

        // Check that user status is also updated
        $this->assertDatabaseHas('users', [
            'id' => $worker->user_id,
            'status' => 'approved'
        ]);
    }

    public function test_company_cannot_approve_already_approved_worker()
    {
        Sanctum::actingAs($this->companyUser);

        $worker = Worker::factory()->create([
            'company_id' => $this->company->id,
            'status' => 'approved'
        ]);

        $response = $this->postJson("/api/company/workers/{$worker->id}/approve", [
            'workerId' => $worker->id
        ]);

        $response->assertStatus(400)
                ->assertJson([
                    'message' => 'Worker is already approved'
                ]);
    }

    public function test_company_can_view_pending_workers()
    {
        Sanctum::actingAs($this->companyUser);

        // Create workers with different statuses
        Worker::factory()->count(2)->create([
            'company_id' => $this->company->id,
            'status' => 'pending'
        ]);
        
        Worker::factory()->count(3)->create([
            'company_id' => $this->company->id,
            'status' => 'approved'
        ]);

        $response = $this->getJson('/api/company/workers?status=pending');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'workers' => [
                        '*' => [
                            'id',
                            'name',
                            'email',
                            'phone',
                            'position',
                            'salary',
                            'status'
                        ]
                    ]
                ]);

        $this->assertCount(2, $response->json('workers'));
        
        // Verify all returned workers are pending
        foreach ($response->json('workers') as $worker) {
            $this->assertEquals('pending', $worker['status']);
        }
    }

    public function test_worker_creation_validation()
    {
        Sanctum::actingAs($this->companyUser);

        $response = $this->postJson('/api/company/workers', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => '123',
            'password_confirmation' => '456',
            'phone' => '',
            'position' => '',
            'salary' => 'invalid-salary'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors([
                    'name', 'email', 'password', 'phone', 'position', 'salary'
                ]);
    }

    public function test_worker_approval_validation()
    {
        Sanctum::actingAs($this->companyUser);

        $worker = Worker::factory()->create([
            'company_id' => $this->company->id
        ]);

        $response = $this->postJson("/api/company/workers/{$worker->id}/approve", [
            'workerId' => 'invalid-id'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['workerId']);
    }

    public function test_company_profile_update_validation()
    {
        Sanctum::actingAs($this->companyUser);

        $response = $this->putJson('/api/company/profile', [
            'name' => '',
            'phone' => 'invalid-phone'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'phone']);
    }

    public function test_unauthorized_access_to_company_profile()
    {
        $response = $this->getJson('/api/company/profile');

        $response->assertStatus(401);
    }

    public function test_unauthorized_access_to_company_workers()
    {
        $response = $this->getJson('/api/company/workers');

        $response->assertStatus(401);
    }

    public function test_customer_cannot_access_company_endpoints()
    {
        $customerUser = User::factory()->create(['role' => 'customer']);
        Sanctum::actingAs($customerUser);

        $response = $this->getJson('/api/company/profile');

        $response->assertStatus(403);
    }

    public function test_worker_belongs_to_correct_company()
    {
        Sanctum::actingAs($this->companyUser);

        // Create worker for this company
        $worker = Worker::factory()->create([
            'company_id' => $this->company->id
        ]);

        // Create another company and worker
        $otherCompany = Company::factory()->create();
        $otherWorker = Worker::factory()->create([
            'company_id' => $otherCompany->id
        ]);

        $response = $this->getJson('/api/company/workers');

        $response->assertStatus(200);

        $workers = $response->json('workers');
        $workerIds = collect($workers)->pluck('id')->toArray();

        // Should only see workers from this company
        $this->assertContains($worker->id, $workerIds);
        $this->assertNotContains($otherWorker->id, $workerIds);
    }

    public function test_company_can_only_approve_their_own_workers()
    {
        Sanctum::actingAs($this->companyUser);

        // Create worker for another company
        $otherCompany = Company::factory()->create();
        $otherWorker = Worker::factory()->create([
            'company_id' => $otherCompany->id,
            'status' => 'pending'
        ]);

        $response = $this->postJson("/api/company/workers/{$otherWorker->id}/approve", [
            'workerId' => $otherWorker->id
        ]);

        $response->assertStatus(404)
                ->assertJson([
                    'message' => 'Worker not found'
                ]);
    }
}
