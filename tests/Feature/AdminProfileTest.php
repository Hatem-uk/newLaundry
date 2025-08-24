<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProfileTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'role' => 'admin',
            'password' => 'password', // سيُخزن مشفر تلقائياً
        ]);

        $this->admin = Admin::factory()->create([
            'user_id' => $this->user->id,
            'phone' => '0123456789',
            'address' => 'Test address',
            'image' => null,
        ]);
    }

    public function test_can_show_profile_as_json()
    {
        $this->actingAs($this->user);

        $response = $this->getJson(route('admin.profile.show'));

        $response->assertOk()
                 ->assertJson([
                     'phone' => $this->admin->phone,
                     'address' => $this->admin->address,
                 ]);
    }

    public function test_can_view_edit_form()
    {
        $this->actingAs($this->user);

        $response = $this->get(route('admin.profile.edit'));

        $response->assertOk();
        $response->assertViewIs('admin.profile.edit');
        $response->assertViewHas('admin');
    }

    public function test_can_update_profile_without_image()
    {
        $this->actingAs($this->user);

        $data = [
            'phone' => '0987654321',
            'address' => 'New Address',
        ];

        $response = $this->put(route('admin.profile.update'), $data);

        $response->assertRedirect(route('admin.profile.show'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('admins', [
            'user_id' => $this->user->id,
            'phone' => '0987654321',
            'address' => 'New Address',
        ]);
    }

    public function test_can_update_profile_with_image()
    {
        Storage::fake('public');
        $this->actingAs($this->user);

        $file = UploadedFile::fake()->image('avatar.jpg');

        $data = [
            'phone' => '0987654321',
            'address' => 'New Address',
            'image' => $file,
        ];

        $response = $this->put(route('admin.profile.update'), $data);

        $response->assertRedirect(route('admin.profile.show'));
        $response->assertSessionHas('success');

        $admin = Admin::where('user_id', $this->user->id)->first();

        $this->assertNotNull($admin->image);
        Storage::disk('public')->assertExists($admin->image);
    }

    public function test_can_destroy_profile_image()
    {
        Storage::fake('public');
        $this->actingAs($this->user);

        $admin = $this->admin;
        $admin->image = 'admin_profiles/test_image.webp';
        $admin->save();

        Storage::disk('public')->put('admin_profiles/test_image.webp', 'fake content');

        $response = $this->delete(route('admin.profile.destroy'));

        $response->assertRedirect(route('admin.profile.show'));
        $response->assertSessionHas('success');

        $admin->refresh();
        $this->assertNull($admin->image);
        Storage::disk('public')->assertMissing('admin_profiles/test_image.webp');
    }
}
