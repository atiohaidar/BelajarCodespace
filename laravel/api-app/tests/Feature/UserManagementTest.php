<?php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);
    }

    public function test_admin_can_view_all_users()
    {
        $admin = User::factory()->create(['role_id' => Role::where('name', 'admin')->first()->id]);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->getJson('/api/users');

        $response->assertStatus(200);
    }

    public function test_user_cannot_view_all_users()
    {
        $user = User::factory()->create(['role_id' => Role::where('name', 'user')->first()->id]);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->getJson('/api/users');

        $response->assertStatus(403);
    }

    public function test_user_can_view_themselves()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->getJson("/api/users/{$user->id}");

        $response->assertStatus(200);
    }

    public function test_user_cannot_view_others()
    {
        $user1 = User::factory()->create(['role_id' => Role::where('name', 'user')->first()->id]);
        $user2 = User::factory()->create(['role_id' => Role::where('name', 'user')->first()->id]);

        $token = $user1->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->getJson("/api/users/{$user2->id}");

        $response->assertStatus(403);
    }

    public function test_admin_can_delete_user()
    {
        $admin = User::factory()->create(['role_id' => Role::where('name', 'admin')->first()->id]);
        $user = User::factory()->create();
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->deleteJson("/api/users/{$user->id}");

        $response->assertStatus(200);
    }

    public function test_user_cannot_delete_another_user()
    {
        $user1 = User::factory()->create(['role_id' => Role::where('name', 'user')->first()->id]);
        $user2 = User::factory()->create();
        $token = $user1->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->deleteJson("/api/users/{$user2->id}");

        $response->assertStatus(403);
    }
}
