<?php
namespace App\Mail;

use App\Models\TreatmentSession;
use Illuminate\Mail\Mailable;

class UpcomingSessionReminderMail extends Mailable
{
    public $session;

    public function __construct(TreatmentSession $session)
    {
        $this->session = $session;
    }

    public function build()
    {
        return $this->subject('Nhắc lịch spa')
            ->view('Users.emails.session-reminder');
    }
}
