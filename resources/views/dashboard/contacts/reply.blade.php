@component('mail::message')
# Xin chào {{ $contact->name }},

Chúng tôi đã phản hồi yêu cầu của bạn gửi lúc **{{ $contact->created_at?->format('d/m/Y H:i') }}**.

@isset($contact->subject)
**Chủ đề:** {{ $contact->subject }}
@endisset

<x-mail::panel>
  <p style="margin:0; white-space:pre-line;">
    {{ $reply->message }}
  </p>
</x-mail::panel>

@component('mail::button', ['url' => config('app.url')])
Truy cập website
@endcomponent

Trân trọng,  
**{{ config('mail.from.name') }}**
@endcomponent
