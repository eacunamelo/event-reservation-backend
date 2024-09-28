<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

use App\Models\User;
use App\Models\Space;
use App\Models\Reservation;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{

    protected $model = Reservation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $startTime = Carbon::createFromTime(rand(8, 16), 0, 0)->format('H:i:s');
        $endTime = Carbon::createFromTime(rand(8, 16), 0, 0)->addHours(2)->format('H:i:s');

        return [
            'user_id' => User::factory(),
            'space_id' => Space::factory(),
            'event_name' => $this->faker->sentence,
            'reservation_date' => now()->addDays(rand(1, 30))->format('Y-m-d'),
            'start_time' => $startTime,
            'end_time' => $endTime,
        ];
    }
}
