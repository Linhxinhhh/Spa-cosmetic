Xin chào {{ $contact->name }},

Chúng tôi đã phản hồi liên hệ của bạn ({{ $contact->created_at?->timezone('Asia/Ho_Chi_Minh')->format('d/m/Y H:i') }}).
@isset($contact->subject)
Chủ đề: {{ $contact->subject }}
@endisset

-------------------------
{{ $reply->message }}
-------------------------

Trân trọng,
{{ config('mail.from.name') }} - {{ config('app.url') }}
