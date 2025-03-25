<?php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;


class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Role::create(['name' => 'admin']);
        Role::create(['name' => 'user']);
    }
    public function test_admin_can_create_user()
    {
$admin = User::factory()->create(['role_id' => Role::where('name', 'admin')->first()->id]);

        $response = $this->actingAs($admin)->postJson('/api/users', [
            'name' => 'John Doe',
            'username' => 'johndoe',
            'bio' => 'Hello world!',
            'birthday' => '2000-05-10',
            'email' => 'johndoe@example.com',
            'phone' => '081234567890',
            'nik' => '3201234567890002',
            'gender' => 'male',
            'website' => 'https://johndoe.com',
            'password' => 'password123',
            'role_id' => 1

        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'username' => 'johndoe',
            'bio' => 'Hello world!',
            // 'birthday' =>   new DateTime('2000-05-10'),
            'email' => 'johndoe@example.com',
            'phone' => '081234567890',
            'nik' => '3201234567890002',
            'gender' => 'male',
            'website' => 'https://johndoe.com',
            'role_id' => 1
            ]
        );
        
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
