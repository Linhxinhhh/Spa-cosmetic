<?php
namespace App\Mail;

use App\Models\TreatmentPlan;
use Illuminate\Mail\Mailable;

class TreatmentScheduleMail extends Mailable
{
    public $plan;

    public function __construct(TreatmentPlan $plan)
    {
        $this->plan = $plan;
    }

    public function build()
    {
        return $this->subject('Lịch trình spa của bạn')
            ->view('Users.emails.treatment-schedule');
    }
}
