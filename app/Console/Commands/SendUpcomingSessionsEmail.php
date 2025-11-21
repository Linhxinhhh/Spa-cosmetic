<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TreatmentSession;
use App\Mail\UpcomingSessionReminderMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendUpcomingSessionsEmail extends Command
{
    protected $signature = 'sessions:remind';
    protected $description = 'Gửi email nhắc các buổi spa sắp diễn ra';

    public function handle()
    {
        $from = now();
        $to   = now()->addDay(); // 24h tới

        $sessions = TreatmentSession::with(['plan.customer','plan.packageService','plan.singleService'])
            ->whereBetween('scheduled_start', [$from, $to])
            ->whereIn('status', ['scheduled','confirmed'])
            ->get();

        foreach ($sessions as $s) {
            $email = $s->plan->customer->email ?? null;
            if (!$email) continue;
            Mail::to($email)->send(new UpcomingSessionReminderMail($s));
        }

        $this->info('Done');
    }
}
