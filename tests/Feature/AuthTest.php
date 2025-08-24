<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_user_can_register()
    {
        $response = $this->postJson('/api/admin/register', [
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            'role' => 'admin',
            'phone' => '01000000001',
            'address' => 'Admin Address',
            'image' => 'dummy_image.jpg'
        ]);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'message',
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'role',
                        'status'
                    ],
                    'token'
                ]);

        $this->assertDatabaseHas('users', [
            'email' => 'admin@example.com',
            'role' => 'admin',
            'status' => 'approved'
        ]);
    }

    public function test_company_user_can_register()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'CleanPro Services',
            'email' => 'company@example.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            'role' => 'company',
            'phone' => '01000000002',
            'address' => '123 Business Street, Downtown, City'
        ]);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'message',
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'role',
                        'status'
                    ],
                    'token'
                ]);

        $this->assertDatabaseHas('users', [
            'email' => 'company@example.com',
            'role' => 'company'
        ]);

        $this->assertDatabaseHas('companies', [
            'name' => 'CleanPro Services',
            'address' => '123 Business Street, Downtown, City'
        ]);
    }

    public function test_customer_user_can_register()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'John Smith',
            'email' => 'customer@example.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            'role' => 'customer',
            'phone' => '01000000004',
            'address' => '456 Customer Ave, Residential Area, City'
        ]);

        $response->assertStatus(201)
                ->assertJsonStructure([
                    'message',
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'role',
                        'status'
                    ],
                    'token'
                ]);

        $this->assertDatabaseHas('users', [
            'email' => 'customer@example.com',
            'role' => 'customer'
        ]);

        // Get the created user to check the customer record
        $user = User::where('email', 'customer@example.com')->first();
        
        $this->assertDatabaseHas('customers', [
            'user_id' => $user->id,
            'address' => '456 Customer Ave, Residential Area, City',
            'coins' => 1000
        ]);
    }

    public function test_user_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('123456'),
            'role' => 'customer',
            'status' => 'approved'
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => '123456'
        ]);

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'message',
                    'user' => [
                        'id',
                        'name',
                        'email',
                        'role'
                    ],
                    'token'
                ]);
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('123456'),
            'role' => 'customer',
            'status' => 'approved'
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword'
        ]);

        $response->assertStatus(401)
                ->assertJson([
                    'message' => 'Invalid credentials'
                ]);
    }

    public function test_registration_validation_errors()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => '123',
            'password_confirmation' => '456'
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['name', 'email', 'password']);
    }

    public function test_login_validation_errors()
    {
        $response = $this->postJson('/api/auth/login', [
            'email' => '',
            'password' => ''
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['email', 'password']);
    }

    public function test_customer_gets_1000_coins_on_registration()
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test Customer',
            'email' => 'testcustomer@example.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            'role' => 'customer',
            'phone' => '01000000005',
            'address' => 'Test Address'
        ]);

        $response->assertStatus(201);

        // Get the created user to check the customer record
        $user = User::where('email', 'testcustomer@example.com')->first();
        
        $this->assertDatabaseHas('customers', [
            'user_id' => $user->id,
            'coins' => 1000
        ]);
    }
}
