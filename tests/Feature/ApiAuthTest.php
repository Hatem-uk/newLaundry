<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class ApiAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_company_register_and_receive_token()
    {
        $payload = [
            'name'=>'Comp Owner',
            'email'=>'comp@example.com',
            'password'=>'password',
            'role'=>'company',
            'company_name'=>'Builders',
            'address'=>'Addr',
            'phone'=>'123'
        ];

        $resp = $this->postJson('/api/register', $payload);
        $resp->assertStatus(201)->assertJsonStructure(['access_token','token_type']);

        $this->assertDatabaseHas('users',['email'=>'comp@example.com','role'=>'company']);
        $this->assertDatabaseHas('companies',['name'=>'Builders']);
    }

    public function test_company_can_create_worker()
    {
        // register company
        $companyResp = $this->postJson('/api/register', [
            'name'=>'Comp Owner', 'email'=>'comp2@example.com','password'=>'password',
            'role'=>'company','company_name'=>'Builders2','address'=>'A','phone'=>'1'
        ]);
        $token = $companyResp->json('access_token');

        $workerPayload = [
            'name'=>'Worker One','email'=>'w1@example.com','password'=>'pass123',
            'position'=>'Mason','salary'=>2000
        ];

        $resp = $this->withHeader('Authorization','Bearer '.$token)
                     ->postJson('/api/workers', $workerPayload);

        $resp->assertStatus(201);
        $this->assertDatabaseHas('users',['email'=>'w1@example.com','role'=>'worker']);
        $this->assertDatabaseHas('workers',['position'=>'Mason']);
    }
}
