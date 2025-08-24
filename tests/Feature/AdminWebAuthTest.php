<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class AdminWebAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_registration_and_dashboard_access()
    {
        $resp = $this->post('/admin/register', [
            'name'=>'Admin','email'=>'admin2@example.com',
            'password'=>'secret123','password_confirmation'=>'secret123'
        ]);

        $resp->assertRedirect(route('admin.dashboard'));
        $this->assertDatabaseHas('users',['email'=>'admin2@example.com','role'=>'admin']);
        $this->assertAuthenticated('web');
    }
}
