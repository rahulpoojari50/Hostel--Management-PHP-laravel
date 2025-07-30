<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class SyncStudentPhones extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'students:sync-phones';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync phone numbers from student_profiles to users for all students who have a profile phone but no user phone.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = 0;
        $students = User::where('role', 'student')
            ->where(function($q) {
                $q->whereNull('phone')->orWhere('phone', '');
            })
            ->with('studentProfile')
            ->get();
        foreach ($students as $student) {
            if ($student->studentProfile && $student->studentProfile->phone) {
                $student->phone = $student->studentProfile->phone;
                $student->save();
                $count++;
            }
        }
        $this->info("Synced phone numbers for {$count} students.");
    }
} 