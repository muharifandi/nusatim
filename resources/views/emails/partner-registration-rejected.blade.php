<x-mail::message>
# Halo {{ $partner->name }},

Mohon maaf, pendaftaran Anda sebagai partner **{{ $siteSettings->company_name }}** belum bisa kami setujui saat ini.

@if($partner->rejection_reason)
<x-mail::panel>
{{ $partner->rejection_reason }}
</x-mail::panel>
@endif

Jika Anda merasa ini adalah kekeliruan atau ingin melengkapi data yang kurang, silakan hubungi kami untuk informasi lebih lanjut.

Salam hangat,<br>
Tim {{ $siteSettings->company_name }}
</x-mail::message>
