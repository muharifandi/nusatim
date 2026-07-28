<x-mail::message>
# Selamat, {{ $partner->name }}!

Pendaftaran Anda sebagai partner **{{ $siteSettings->company_name }}** sudah **disetujui**. Anda sekarang bisa login ke Portal Partner dan mulai menggunakan semua fiturnya.

<x-mail::button :url="url('/partner/login')">
Masuk ke Portal Partner
</x-mail::button>

Salam hangat,<br>
Tim {{ $siteSettings->company_name }}
</x-mail::message>
