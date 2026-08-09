<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>{{ $legalPage->title }}</title>
	<style>
		/*
		 * dompdf's CSS support is limited (no flexbox/grid) - this template
		 * intentionally uses only table/block layout, kept separate from the
		 * web view's real (browser-rendered) CSS in legal-page.css.
		 */
		body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #16142b; }

		.letterhead { width: 100%; margin-bottom: 16px; border-collapse: collapse; }
		.letterhead td { vertical-align: top; }
		.letterhead .address { font-size: 11px; line-height: 1.6; }
		.letterhead .address strong { font-size: 13px; }
		.letterhead .logo { text-align: right; }
		.letterhead .logo img { max-height: 44px; }

		.rule-thick { border-bottom: 3px solid #16142b; margin-bottom: 18px; }
		.rule-thin { border-bottom: 1px solid #cccccc; margin: 18px 0; }

		.doc-title { text-align: center; text-transform: uppercase; font-size: 18px; font-weight: bold; margin: 0 0 4px; }
		.doc-number { text-align: center; font-size: 12px; color: #555555; margin-bottom: 6px; }

		.plain-header { margin-bottom: 20px; }
		.plain-header .logo img { max-height: 40px; margin-bottom: 10px; }
		.plain-header h1 { font-size: 20px; margin: 0; }
		.plain-header .updated { font-size: 11px; color: #777777; margin-top: 4px; }

		.content { font-size: 12px; line-height: 1.7; }
		.content h2 { font-size: 15px; margin-top: 20px; }
		.content h3 { font-size: 13px; margin-top: 16px; }
		.content table { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
		.content table td, .content table th { border: 1px solid #cccccc; padding: 6px 8px; }

		.footer-note { font-size: 10px; color: #777777; margin-top: 20px; }
	</style>
</head>
<body>

	@if($legalPage->isFormalDocument())
		<table class="letterhead">
			<tr>
				<td class="address">
					<strong>{{ $siteSettings->legal_name ?: $siteSettings->company_name }}</strong><br>
					@if($siteSettings->address){{ $siteSettings->address }}<br>@endif
					{{ collect([$siteSettings->phone, $siteSettings->email])->filter()->implode(' · ') }}
				</td>
				<td class="logo">
					@if($logoDataUri)<img src="{{ $logoDataUri }}">@endif
				</td>
			</tr>
		</table>
		<div class="rule-thick"></div>
		<div class="doc-title">{{ $legalPage->title }}</div>
		@if($legalPage->document_number)
			<div class="doc-number">No: {{ $legalPage->document_number }}</div>
		@endif
		<div class="rule-thin"></div>
	@else
		<div class="plain-header">
			@if($logoDataUri)<div class="logo"><img src="{{ $logoDataUri }}"></div>@endif
			<h1>{{ $legalPage->title }}</h1>
			<div class="updated">Terakhir diperbarui: {{ $legalPage->updated_at->translatedFormat('d F Y') }}</div>
		</div>
	@endif

	<div class="content">
		{!! $legalPage->content !!}
	</div>

	@if($legalPage->isFormalDocument())
		<div class="rule-thin"></div>
		<div class="footer-note">
			{{ $siteSettings->legal_name ?: $siteSettings->company_name }}
			@if($siteSettings->address) &middot; {{ $siteSettings->address }} @endif
			<br>Dokumen ini diterbitkan secara elektronik melalui sistem {{ $siteSettings->company_name }}.
		</div>
	@endif

</body>
</html>
