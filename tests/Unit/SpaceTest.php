<?php

namespace Tests\Unit;

use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Space;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SpaceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function an_admin_can_create_a_space()
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $token = JWTAuth::fromUser($admin);

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->postJson('/api/spaces', [
                             'name' => 'Sala de Reuniones A',
                             'description' => 'Una sala para 10 personas',
                             'capacity' => 10,
                             'type' => 'meeting_room'
                         ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('spaces', ['name' => 'Sala de Reuniones A']);
    }

    /** @test */
    public function a_user_can_view_spaces()
    {
        Space::factory()->count(3)->create();

        $user = User::factory()->create(['role' => 'user']);
        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->getJson('/api/spaces');

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    /** @test */
    public function an_admin_can_update_a_space()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $space = Space::factory()->create();

        $token = JWTAuth::fromUser($admin);

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->putJson("/api/spaces/{$space->id}", [
                             'name' => 'Sala Actualizada',
                         ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('spaces', ['name' => 'Sala Actualizada']);
    }

    /** @test */
    public function an_admin_can_delete_a_space()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $space = Space::factory()->create();

        $token = JWTAuth::fromUser($admin);

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->deleteJson("/api/spaces/{$space->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('spaces', ['id' => $space->id]);
    }
}
