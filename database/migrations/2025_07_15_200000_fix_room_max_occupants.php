<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FixRoomMaxOccupants extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        foreach (\App\Models\Room::all() as $room) {
            $roomType = $room->roomType;
            if ($roomType && $room->max_occupants != $roomType->capacity) {
                $room->max_occupants = $roomType->capacity;
                $room->save();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        // No rollback needed
    }
} 