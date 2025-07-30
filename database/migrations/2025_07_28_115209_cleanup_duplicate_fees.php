<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\StudentFee;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get all duplicate fee combinations
        $duplicates = StudentFee::select('student_id', 'hostel_id', 'fee_type')
            ->groupBy('student_id', 'hostel_id', 'fee_type')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            // Get all fees for this combination
            $fees = StudentFee::where('student_id', $duplicate->student_id)
                ->where('hostel_id', $duplicate->hostel_id)
                ->where('fee_type', $duplicate->fee_type)
                ->orderBy('created_at', 'desc')
                ->get();

            // Keep the first (latest) record and delete the rest
            $keepFee = $fees->first();
            $deleteFees = $fees->skip(1);

            // If any fee is paid, mark the kept fee as paid
            $hasPaid = $fees->where('status', 'paid')->count() > 0;
            if ($hasPaid) {
                $paidFee = $fees->where('status', 'paid')->first();
                $keepFee->update([
                    'status' => 'paid',
                    'paid_at' => $paidFee->paid_at,
                ]);
            }

            // Delete duplicate records
            foreach ($deleteFees as $deleteFee) {
                $deleteFee->delete();
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration is for cleanup, no rollback needed
    }
};
