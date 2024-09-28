<?php

namespace Tests\Unit;

use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Space;
use App\Models\User;
use App\Models\Reservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
class ReservationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_user_can_create_a_reservation()
    {
        $user = User::factory()->create(['role' => 'user']);
        $space = Space::factory()->create();

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->postJson('/api/reservations', [
                             'space_id' => $space->id,
                             'event_name' => 'Evento de Prueba',
                             'reservation_date' => '2024-10-10',
                             'start_time' => '09:00:00',
                             'end_time' => '11:00:00',
                         ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('reservations', ['event_name' => 'Evento de Prueba']);
    }

    /** @test */
    public function a_user_cannot_create_a_reservation_with_overlapping_times()
    {
        $user = User::factory()->create(['role' => 'user']);
        $space = Space::factory()->create();

        Reservation::create([
            'user_id' => $user->id,
            'space_id' => $space->id,
            'event_name' => 'Primera Reserva',
            'reservation_date' => '2024-10-10',
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
        ]);

        $token = JWTAuth::fromUser($user);

        $response = $this->withHeader('Authorization', "Bearer $token")
                         ->postJson('/api/reservations', [
                             'space_id' => $space->id,
                             'event_name' => 'Reserva Conflictiva',
                             'reservation_date' => '2024-10-10',
                             'start_time' => '10:00:00',
                             'end_time' => '12:00:00',
                         ]);

        $response->assertStatus(409);
    }
}
