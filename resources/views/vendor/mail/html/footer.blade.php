@props(['siteSettings' => null])
<tr>
<td>
<table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
@if (trim($slot) !== '')
<tr>
<td class="content-cell" align="center">
{{ Illuminate\Mail\Markdown::parse($slot) }}
</td>
</tr>
@endif
<tr>
<td class="content-cell" align="center">
@if ($siteSettings)
<p class="footer-company">{{ $siteSettings->legal_name ?: $siteSettings->company_name }}</p>
@if ($siteSettings->address)
<p class="footer-meta">{{ $siteSettings->address }}</p>
@endif
@if ($siteSettings->email || $siteSettings->phone)
<p class="footer-meta">
{{ collect([$siteSettings->email, $siteSettings->phone])->filter()->implode(' · ') }}
</p>
@endif
@php($socials = collect([
    'Facebook' => $siteSettings->facebook_url,
    'Instagram' => $siteSettings->instagram_url,
    'LinkedIn' => $siteSettings->linkedin_url,
    'Twitter' => $siteSettings->twitter_url,
    'YouTube' => $siteSettings->youtube_url,
])->filter())
@if ($socials->isNotEmpty())
<p class="footer-meta">
@foreach ($socials as $label => $link)
<a href="{{ $link }}" class="footer-social-link">{{ $label }}</a>@if (! $loop->last)&nbsp;&nbsp;@endif
@endforeach
</p>
@endif
<p class="footer-copyright">&copy; {{ date('Y') }} {{ $siteSettings->legal_name ?: $siteSettings->company_name }}. @lang('All rights reserved.')</p>
@else
<p class="footer-copyright">&copy; {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')</p>
@endif
<p class="footer-disclaimer">Email ini dikirim otomatis, mohon tidak membalas langsung ke alamat ini.</p>
</td>
</tr>
</table>
</td>
</tr>
