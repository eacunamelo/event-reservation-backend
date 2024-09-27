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
                Reservation::create([
                    'user_id' => $user->id,
                    'space_id' => $space->id,
                    'event_name' => 'Evento de ' . $user->name,
                    'reservation_date' => now()->addDays(rand(1, 30))->format('Y-m-d'),
                    'start_time' => now()->setTime(rand(8, 18), 0, 0)->format('H:i:s'),
                    'end_time' => now()->setTime(rand(8, 18), 0, 0)->addHours(2)->format('H:i:s'),
                ]);

                Reservation::create([
                    'user_id' => $user->id,
                    'space_id' => $space->id,
                    'event_name' => 'Evento de ' . $user->name . ' - 2',
                    'reservation_date' => now()->addDays(rand(1, 30))->format('Y-m-d'),
                    'start_time' => now()->setTime(rand(8, 18), 0, 0)->format('H:i:s'),
                    'end_time' => now()->setTime(rand(8, 18), 0, 0)->addHours(2)->format('H:i:s'),
                ]);
            }
        }
    }
}
