<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PendingFeesNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $pendingFees;

    public function __construct($student, $pendingFees)
    {
        $this->student = $student;
        $this->pendingFees = $pendingFees;
    }

    public function build()
    {
        return $this->subject('Pending Hostel Fees Notification')
            ->view('emails.pending_fees_notification');
    }
} 