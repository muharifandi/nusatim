<x-mail::message>
# Selamat, {{ $partner->name }}!

Klaim Anda atas project **{{ $project->name }}** sudah **disetujui**. Project ini sekarang jadi tanggung jawab Anda.

<x-mail::button :url="route('filament.partner.resources.partner-projects.view', $project)">
Lihat Detail Project
</x-mail::button>

Salam hangat,<br>
Tim {{ $siteSettings->company_name }}
</x-mail::message>
