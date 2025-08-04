<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'hostel_id',
        'room_type_id',
        'amount',
        'application_date',
        'status',
        'warden_remarks',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'application_date' => 'date',
        'processed_at' => 'datetime',
        'status' => 'string',
    ];

    /**
     * Get the student who submitted this application
     */
    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the hostel this application is for
     */
    public function hostel()
    {
        return $this->belongsTo(Hostel::class);
    }

    /**
     * Get the room type this application is for
     */
    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    /**
     * Get the warden who processed this application
     */
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Check if application is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if application is approved
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if application is rejected
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if application was reapproved (previously rejected then approved)
     */
    public function isReapproved(): bool
    {
        return $this->status === 'approved' && 
               $this->warden_remarks && 
               str_contains($this->warden_remarks, '[REAPPROVED');
    }

    /**
     * Get display status for the application
     */
    public function getDisplayStatus(): string
    {
        if ($this->isReapproved()) {
            return 'reapproved';
        }
        return $this->status;
    }

    /**
     * Approve the application
     */
    public function approve(User $warden, string $remarks = null): void
    {
        $this->update([
            'status' => 'approved',
            'warden_remarks' => $remarks,
            'processed_by' => $warden->id,
            'processed_at' => now(),
        ]);
    }

    /**
     * Reject the application
     */
    public function reject(User $warden, string $remarks = null): void
    {
        $this->update([
            'status' => 'rejected',
            'warden_remarks' => $remarks,
            'processed_by' => $warden->id,
            'processed_at' => now(),
        ]);
    }
}
