<x-mail::message>
# Halo {{ $partner->name }},

Mohon maaf, klaim Anda atas project **{{ $project->name }}** belum bisa kami setujui saat ini. Project ini kembali terbuka untuk partner lain.

Silakan cek Project Board untuk melihat project lain yang tersedia.

Salam hangat,<br>
Tim {{ $siteSettings->company_name }}
</x-mail::message>
