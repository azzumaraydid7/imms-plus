<?php

namespace Tests\Feature\Auth;

use App\Modules\Identity\Models\Permission;
use App\Modules\Identity\Models\Role;
use App\Modules\Identity\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiLoginTest extends TestCase
{
    use RefreshDatabase;

    private function userWithRole(): User
    {
        $permission = Permission::create(['name' => 'Dashboard View', 'slug' => 'dashboard.view']);
        $role = Role::create(['name' => 'Student', 'slug' => 'student']);
        $role->permissions()->attach($permission);

        $user = User::factory()->create(['email' => 'mobile@imjpj.my']);
        $user->roles()->attach($role);

        return $user;
    }

    public function test_user_can_login_and_receive_access_token(): void
    {
        $user = $this->userWithRole();

        $this->postJson('http://api.localhost/login', [
            'email' => 'mobile@imjpj.my',
            'password' => 'password',
            'device_name' => 'pixel-8',
            'device_uuid' => 'device-uuid-123',
            'platform' => 'android',
        ])
            ->assertOk()
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonPath('user.email', 'mobile@imjpj.my')
            ->assertJsonPath('user.roles', ['student'])
            ->assertJsonPath('user.permissions', ['dashboard.view'])
            ->assertJsonStructure(['token', 'token_type', 'user' => ['id', 'uuid', 'email', 'roles', 'permissions']]);

        $this->assertDatabaseHas('user_devices', [
            'user_id' => $user->id,
            'device_uuid' => 'device-uuid-123',
            'platform' => 'android',
        ]);

        $this->assertDatabaseHas('personal_access_tokens', ['tokenable_id' => $user->id]);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        User::factory()->create(['email' => 'mobile@imjpj.my']);

        $this->postJson('http://api.localhost/login', [
            'email' => 'mobile@imjpj.my',
            'password' => 'wrong-password',
        ])
            ->assertStatus(422)
            ->assertJsonValidationErrors('email');
    }

    public function test_user_endpoint_requires_a_token(): void
    {
        $this->getJson('http://api.localhost/user')->assertStatus(401);
    }

    public function test_authenticated_user_can_fetch_profile_and_logout(): void
    {
        $user = $this->userWithRole();
        $token = $user->createToken('test-client')->plainTextToken;

        $this->withToken($token)
            ->getJson('http://api.localhost/user')
            ->assertOk()
            ->assertJsonPath('user.email', 'mobile@imjpj.my');

        $this->withToken($token)
            ->postJson('http://api.localhost/logout')
            ->assertOk();

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }
}
