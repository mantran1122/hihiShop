<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DemoUserAndRolesSeeder;
use Database\Seeders\RolesSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserStatusTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed([DemoUserAndRolesSeeder::class, RolesSeeder::class]);

        $this->admin = User::where('email', 'admin@example.com')->firstOrFail();
        $this->customer = User::where('email', 'customer@example.com')->firstOrFail();
    }

    public function test_inactive_user_cannot_login(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
            'is_active' => false,
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_active_user_can_login(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_can_lock_and_unlock_user(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.users.toggleStatus', $this->customer->id))
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertFalse($this->customer->fresh()->is_active);

        // User bị khoá không đăng nhập được
        $this->post('/logout');
        $this->post('/login', [
            'email' => $this->customer->email,
            'password' => 'admin123',
        ])->assertSessionHasErrors('email');
        $this->assertGuest();

        // Mở khoá lại
        $this->actingAs($this->admin)
            ->post(route('admin.users.toggleStatus', $this->customer->id));

        $this->assertTrue($this->customer->fresh()->is_active);
    }

    public function test_admin_cannot_lock_their_own_account(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.users.toggleStatus', $this->admin->id))
            ->assertSessionHas('error');

        $this->assertTrue($this->admin->fresh()->is_active);
    }

    public function test_admin_users_page_shows_status_controls(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.users'))
            ->assertOk()
            ->assertSee('Trạng thái')
            ->assertSee('Khoá');
    }
}
