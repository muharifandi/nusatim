<x-mail::message>
# Halo {{ $contactMessage->name }},

Terima kasih telah menghubungi **{{ $siteSettings->company_name }}**. Pesan Anda sudah kami terima dan tim kami akan segera meninjau serta membalasnya secepat mungkin.

Berikut ringkasan pesan yang Anda kirimkan:

<x-mail::panel>
@if($contactMessage->subject)
**Subjek:** {{ $contactMessage->subject }}

@endif
{{ $contactMessage->message }}
</x-mail::panel>

Jika ada informasi tambahan yang ingin Anda sampaikan, cukup balas email ini.

<x-mail::button :url="url('/')">
Kunjungi Website Kami
</x-mail::button>

Salam hangat,<br>
Tim {{ $siteSettings->company_name }}
</x-mail::message>
