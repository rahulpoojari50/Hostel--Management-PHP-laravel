<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hostel;
use App\Models\RoomType;

class RoomTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $boysHostel = Hostel::where('name', 'Boys Hostel')->first();
        $girlsHostel = Hostel::where('name', 'Girls Hostel')->first();

        if ($boysHostel) {
            RoomType::create([
                'hostel_id' => $boysHostel->id,
                'type' => 'single',
                'capacity' => 1,
                'price_per_month' => 5000,
                'total_rooms' => 10,
                'available_rooms' => 10,
                'facilities' => json_encode(['AC', 'WiFi', 'Attached Bathroom']),
            ]);
            RoomType::create([
                'hostel_id' => $boysHostel->id,
                'type' => 'double',
                'capacity' => 2,
                'price_per_month' => 3500,
                'total_rooms' => 20,
                'available_rooms' => 20,
                'facilities' => json_encode(['WiFi']),
            ]);
        }
        if ($girlsHostel) {
            RoomType::create([
                'hostel_id' => $girlsHostel->id,
                'type' => 'single',
                'capacity' => 1,
                'price_per_month' => 5200,
                'total_rooms' => 8,
                'available_rooms' => 8,
                'facilities' => json_encode(['AC', 'WiFi', 'Balcony']),
            ]);
            RoomType::create([
                'hostel_id' => $girlsHostel->id,
                'type' => 'double',
                'capacity' => 2,
                'price_per_month' => 3700,
                'total_rooms' => 15,
                'available_rooms' => 15,
                'facilities' => json_encode(['WiFi', 'Balcony']),
            ]);
        }
    }
}
