<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Room;

class FixRoomMaxOccupants extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:room-max-occupants';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all rooms so that max_occupants matches the room type capacity';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = 0;
        foreach (Room::all() as $room) {
            $roomType = $room->roomType;
            if ($roomType && $room->max_occupants != $roomType->capacity) {
                $room->max_occupants = $roomType->capacity;
                $room->save();
                $count++;
            }
        }
        $this->info("Updated $count rooms to correct max_occupants.");
    }
}
