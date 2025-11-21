<?php

namespace App\Mail;

use App\Models\Contact;
use App\Models\ContactReply;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Contact $contact,
        public ContactReply $reply,
        private string $subjectLine = 'Phản hồi liên hệ'
    ) {}
    

       public function build()
    {
        return $this->from(config('mail.from.address'), config('mail.from.name'))
            ->subject($this->subjectLine)
            ->markdown('dashboard.contacts.reply')   // view markdown của bạn
            ->text('dashboard.contacts.reply_text')  // (tuỳ chọn) bản text
            ->with([
                'contact' => $this->contact,
                'reply'   => $this->reply,
            ]);
    }
}
