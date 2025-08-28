<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function index_returns_all_users(): void
    {
        User::factory()->count(2)->create();

        $response = $this->getJson('/api/users');

        $response
            ->assertStatus(200)
            ->assertJsonCount(2);
    }

    /** @test */
    public function show_returns_single_user(): void
    {
        User::factory()->count(1)->create([
            'id' => 1,
            'name' => 'test',
            'email' => 'test@test.com',
            'password' => 'password',
        ]);

        $response = $this->getJson('/api/users/1');

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
            'id',
            'name',
            'email',
            'created_at',
            'updated_at',
            ])
            ->assertJsonFragment([
            'name' => 'test'
        ]);
    }

    /** @test */
    public function show_returns_404_if_no_user_found(): void
    {
        $response = $this->getJson('/api/users/1');

        $response->assertStatus(404);
    }

    /** @test */
    public function store_returns_created_user(): void
    {
        $body = [
            'name' => 'test',
            'email' => 'test@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson('/api/users', $body);
        $response->assertStatus(201);
    }

    /** @test */
    public function update_returns_updated_user(): void
    {
        $user = User::factory()->create([
            'name' => 'test',
            'email' => 'test@test.com',
            'password' => 'password',
        ]);

        $body = [
            'name' => 'test123',
        ];

        $response = $this->putJson("/api/users/$user->id", $body);
        $response
            ->assertStatus(200)
            ->assertJsonFragment([
                'name' => 'test123',
            ]);
    }

    /** @test */
    public function update_returns_404_if_no_user_found(): void
    {
        $body = [
            'name' => 'test',
        ];

        $response = $this->putJson('/api/users/1', $body);

        $response->assertStatus(404);
    }

    /** @test */
    public function update_returns_error_message_if_body_is_wrong(): void
    {

        $body = [];
        $response = $this->putJson('/api/users/1', $body);

        $response->assertStatus(422)
            ->assertInvalid([
                'name'
            ]);
    }

    /** @test */
    public function delete_returns_empty_json()
    {
        $user = User::factory()->create([
            'name' => 'test',
            'email' => 'test@test.com',
            'password' => 'password',
        ]);

        $response = $this->deleteJson("/api/users/$user->id");

        $response->assertStatus(204)
            ->assertNoContent();

        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    /** @test */
    public function get_username_by_id_returns_username()
    {
        $user = User::factory()->create([
            'name' => 'test',
            'email' => 'test@test.com',
            'password' => 'password',
        ]);

        $response = $this->getJson("/api/users/$user->id/username");

        $response->assertStatus(200)
            ->assertJson([
                'name' => 'test',
            ]);;
    }

    /** @test */
    public function get_username_by_id_cached_value()
    {
        $user = User::factory()->create();
        Cache::put("user:{$user->id}:name", $user->name, 600);
        $user->delete();

        $response = $this->getJson("/api/users/{$user->id}/username");

        $response->assertStatus(200)
            ->assertJson([
                'name' => $user->name,
            ]);
    }
}
