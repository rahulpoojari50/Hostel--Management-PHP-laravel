<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class StudentAbsentNotification extends Notification
{
    use Queueable;

    protected $student;
    protected $date;

    public function __construct($student, $date)
    {
        $this->student = $student;
        $this->date = $date;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Absence Notification')
            ->greeting('Dear Parent,')
            ->line("Your ward, {$this->student->name}, was marked absent on {$this->date}.")
            ->line('Please contact the hostel administration if you have any questions.')
            ->salutation('Regards, Hostel Administration');
    }
} 