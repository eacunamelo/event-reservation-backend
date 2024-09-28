<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Space;
use Carbon\Carbon;

class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::where('role', 'user')->get();
        $spaces = Space::all();

        if ($users->count() === 0 || $spaces->count() === 0) {
            $this->command->info('No users or spaces found. Please run UsersTableSeeder and SpacesTableSeeder first.');
            return;
        }

        foreach ($users as $user) {
            foreach ($spaces as $space) {
                $reservationDate = now()->addDays(rand(1, 30))->format('Y-m-d');
                $startTime = Carbon::createFromTime(rand(8, 16), 0, 0);
                $endTime = $startTime->copy()->addHours(2);

                Reservation::create([
                    'user_id' => $user->id,
                    'space_id' => $space->id,
                    'event_name' => 'Evento de ' . $user->name,
                    'reservation_date' => $reservationDate,
                    'start_time' => $startTime->format('H:i:s'),
                    'end_time' => $endTime->format('H:i:s'),
                ]);

                $startTimeSecondEvent = $endTime->copy()->addHours(rand(1, 3));
                $endTimeSecondEvent = $startTimeSecondEvent->copy()->addHours(2);

                if ($startTimeSecondEvent->hour <= 18) {
                    Reservation::create([
                        'user_id' => $user->id,
                        'space_id' => $space->id,
                        'event_name' => 'Evento de ' . $user->name . ' - 2',
                        'reservation_date' => $reservationDate,
                        'start_time' => $startTimeSecondEvent->format('H:i:s'),
                        'end_time' => $endTimeSecondEvent->format('H:i:s'),
                    ]);
                }
            }
        }
    }
}
