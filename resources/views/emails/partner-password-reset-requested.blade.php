<x-mail::message>
# Reset Password

Halo {{ $partner->name }},

Kami menerima permintaan reset password untuk akun Partner Anda di aplikasi mobile **{{ $siteSettings->company_name }}**. Masukkan kode berikut di halaman Reset Password pada aplikasi, beserta password baru Anda.

<x-mail::panel>
{{ $token }}
</x-mail::panel>

Kode ini berlaku selama {{ $expiresInMinutes }} menit. Jika Anda tidak merasa meminta reset password, abaikan email ini - password Anda tidak akan berubah.

Salam hangat,<br>
Tim {{ $siteSettings->company_name }}
</x-mail::message>
