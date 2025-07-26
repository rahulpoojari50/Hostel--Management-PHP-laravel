<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PendingFeeNotification extends Notification
{
    use Queueable;

    protected $student;
    protected $pendingFees;

    public function __construct($student, $pendingFees)
    {
        $this->student = $student;
        $this->pendingFees = $pendingFees;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        \Log::info('Sending mail to: ' . print_r($notifiable, true));
        // dd('Sending Mail!');

        $mail = (new \Illuminate\Notifications\Messages\MailMessage)
            ->subject('Pending Hostel Fee Notice')
            ->greeting('Dear Parent,')
            ->line("Your ward, {$this->student->name}, has the following pending hostel fees:");
        foreach ($this->pendingFees as $fee) {
            $mail->line(ucwords(str_replace('_', ' ', $fee->fee_type)) . ': ₹' . number_format($fee->amount, 2));
        }
        $mail->line('Kindly make the payment at the earliest.')
            ->salutation('Regards, Hostel Administration');
        return $mail;
    }
} 