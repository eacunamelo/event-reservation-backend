<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('space_id')->constrained('spaces')->onDelete('cascade');
            $table->string('event_name');
            $table->date('reservation_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();

            $table->unique(['space_id', 'reservation_date', 'start_time', 'end_time'], 'unique_reservation');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('reservations');
    }
};
