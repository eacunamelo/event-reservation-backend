<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use App\Models\Space;
class SpacesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Space::create([
            'name' => 'Sala de Reuniones A',
            'description' => 'Una sala pequeña para reuniones de 10 personas.',
            'capacity' => 10,
            'type' => 'meeting_room',
            'image_url' => Storage::disk('spaces')->url('club-house.jpg'),
        ]);

        Space::create([
            'name' => 'Sala de Reuniones B',
            'description' => 'Una sala pequeña para reuniones de 10 personas.',
            'capacity' => 10,
            'type' => 'meeting_room',
            'image_url' => Storage::disk('spaces')->url('meet.jpg'),
        ]);

        Space::create([
            'name' => 'Sala de Conferencias A',
            'description' => 'Sala equipada para conferencias de hasta 20 personas.',
            'capacity' => 20,
            'type' => 'conference_room',
            'image_url' => Storage::disk('spaces')->url('corporate-meeting.jpg'),
        ]);

        Space::create([
            'name' => 'Sala de Conferencias B',
            'description' => 'Sala equipada para conferencias de hasta 30 personas.',
            'capacity' => 30,
            'type' => 'conference_room',
            'image_url' => Storage::disk('spaces')->url('meeting-room.jpg'),
        ]);

        Space::create([
            'name' => 'Auditorio Principal',
            'description' => 'Un auditorio con capacidad para 20 personas.',
            'capacity' => 20,
            'type' => 'auditorium',
            'image_url' => Storage::disk('spaces')->url('meeting-room-old-house.jpg'),
        ]);

        Space::create([
            'name' => 'Auditorio Secundario',
            'description' => 'Un auditorio con capacidad para 10 personas.',
            'capacity' => 10,
            'type' => 'auditorium',
            'image_url' => Storage::disk('spaces')->url('living-room-old-house.jpg'),
        ]);
    }
}
