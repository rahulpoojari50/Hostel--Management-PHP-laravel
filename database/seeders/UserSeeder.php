<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default warden
        User::create([
            'name' => 'Default Warden',
            'email' => 'warden@example.com',
            'usn' => 'WARDEN001',
            'password' => Hash::make('password'),
            'role' => 'warden',
            'phone' => '1234567890',
            'address' => 'Warden Hostel Block, City',
        ]);
        // Create sample wardens
        User::create([
            'name' => 'Warden One',
            'email' => 'warden1@example.com',
            'usn' => 'WARDEN002',
            'password' => Hash::make('password'),
            'role' => 'warden',
            'phone' => '1234567890',
            'address' => 'Warden Hostel Block, City',
        ]);
        User::create([
            'name' => 'Warden Two',
            'email' => 'warden2@example.com',
            'usn' => 'WARDEN003',
            'password' => Hash::make('password'),
            'role' => 'warden',
            'phone' => '1234567891',
            'address' => 'Warden Hostel Block 2, City',
        ]);

        // Create sample students
        User::create([
            'name' => 'Student One',
            'email' => 'student1@example.com',
            'usn' => 'STU001',
            'password' => Hash::make('password'),
            'role' => 'student',
            'phone' => '9876543210',
            'address' => 'Student Hostel Block, City',
        ]);
        User::create([
            'name' => 'Student Two',
            'email' => 'student2@example.com',
            'usn' => 'STU002',
            'password' => Hash::make('password'),
            'role' => 'student',
            'phone' => '9876543211',
            'address' => 'Student Hostel Block 2, City',
        ]);
    }
}
