<?php

namespace App\Mail;

use App\Models\TreatmentSession;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SessionStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;

    public TreatmentSession $session;
    public string $oldStatus;
    public string $newStatus;

    public function __construct(TreatmentSession $session, string $oldStatus, string $newStatus)
    {
        $this->session = $session->load(['plan.customer', 'plan.packageService', 'plan.singleService']);
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    public function envelope()
    {
        return new \Illuminate\Mail\Mailables\Envelope(
            subject: '[Lyn & Spa] Cập nhật trạng thái buổi điều trị'
        );
    }

    public function content()
    {
        return new \Illuminate\Mail\Mailables\Content(
            markdown: 'emails.sessions.status-updated',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
