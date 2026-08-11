@extends('layouts.app')

@push('styles')
	<link rel="stylesheet" href="{{ asset('assets/css/legal-page.css') }}">
@endpush

@push('structured-data')
<script type="application/ld+json">
{
	"@@context": "https://schema.org",
	"@@type": "BreadcrumbList",
	"itemListElement": [
		{"@@type": "ListItem", "position": 1, "name": "Home", "item": "{{ route('home') }}"},
		{"@@type": "ListItem", "position": 2, "name": "Dokumen Legal", "item": "{{ route('legal.index') }}"},
		{"@@type": "ListItem", "position": 3, "name": "{{ $legalPage->title }}", "item": "{{ route('legal.show', $legalPage->slug) }}"}
	]
}
</script>
@endpush

@section('content')

	<section class="legal-header">
		<div class="container">
			<div class="legal-breadcrumb">
				<a href="{{ route('home') }}">Home</a>
				<span>/</span>
				<a href="{{ route('legal.index') }}">Dokumen Legal</a>
				<span>/</span>
				<span>{{ $legalPage->title }}</span>
			</div>

			@if($legalPage->isFormalDocument())
				<div class="legal-letterhead">
					<div class="legal-letterhead-address">
						<strong>{{ $siteSettings->legal_name ?: $siteSettings->company_name }}</strong>
						@if($siteSettings->address)<span>{{ $siteSettings->address }}</span>@endif
						<span>{{ collect([$siteSettings->phone, $siteSettings->email])->filter()->implode(' · ') }}</span>
					</div>
					@if($siteSettings->logo_dark || $siteSettings->logo_light)
						<img class="legal-letterhead-logo" src="{{ asset($siteSettings->logo_dark ?: $siteSettings->logo_light) }}" alt="{{ $siteSettings->company_name }}">
					@endif
				</div>
				<div class="legal-letterhead-rule"></div>
				<h1 class="legal-letterhead-title">{{ $legalPage->title }}</h1>
				@if($legalPage->document_number)
					<div class="legal-document-number">No: {{ $legalPage->document_number }}</div>
				@endif
				<div class="legal-letterhead-rule legal-letterhead-rule-thin"></div>
			@else
				<h1>{{ $legalPage->title }}</h1>
			@endif

			<div class="legal-header-meta">
				<div class="legal-updated">Terakhir diperbarui: {{ $legalPage->updated_at->translatedFormat('d F Y') }}</div>
				<a href="{{ route('legal.pdf', $legalPage) }}" class="legal-pdf-btn">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v12m0 0l-4-4m4 4l4-4M5 21h14"/></svg>
					Download PDF
				</a>
			</div>
		</div>
	</section>

	<section class="legal-body">
		<div class="container">
			<div class="row">
				<div class="col-lg-8">
					<div class="legal-content">
						{!! $legalPage->content !!}
					</div>

					@if($legalPage->isFormalDocument())
						<div class="legal-letterhead-rule legal-letterhead-rule-thin"></div>
						<div class="legal-document-footer">
							{{ $siteSettings->legal_name ?: $siteSettings->company_name }}
							@if($siteSettings->address) &middot; {{ $siteSettings->address }} @endif
							<br>Dokumen ini diterbitkan secara elektronik melalui sistem {{ $siteSettings->company_name }}.
						</div>
					@endif
				</div>
				@if($otherLegalPages->isNotEmpty())
					<div class="col-lg-4">
						<div class="legal-sidebar">
							<div class="legal-sidebar-title">Dokumen Legal Lainnya</div>
							<ul class="legal-sidebar-list">
								@foreach($otherLegalPages as $other)
									<li><a href="{{ route('legal.show', $other) }}">{{ $other->title }}</a></li>
								@endforeach
							</ul>
						</div>
					</div>
				@endif
			</div>
		</div>
	</section>

@endsection
