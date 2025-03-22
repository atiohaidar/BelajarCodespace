<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);
    }

    public function test_admin_can_access_protected_route()
    {
        $admin = User::factory()->create([
            'role_id' => Role::where('name', 'admin')->first()->id
        ]);
        $token = $admin->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->getJson('/api/admin-only');

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Welcome Admin']);
    }

    public function test_user_cannot_access_admin_route()
    {
        $user = User::factory()->create([
            'role_id' => Role::where('name', 'user')->first()->id
        ]);
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->getJson('/api/admin-only');

        $response->assertStatus(403)
                 ->assertJson(['message' => 'Unauthorized']);
    }
}
