@props(['url', 'siteSettings' => null])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if ($siteSettings?->logo_dark)
<img src="{{ asset($siteSettings->logo_dark) }}" class="logo" alt="{{ $siteSettings->company_name ?? config('app.name') }}">
@elseif ($siteSettings?->logo_light)
<img src="{{ asset($siteSettings->logo_light) }}" class="logo" alt="{{ $siteSettings->company_name ?? config('app.name') }}">
@else
{{ $siteSettings->company_name ?? config('app.name') }}
@endif
</a>
</td>
</tr>
