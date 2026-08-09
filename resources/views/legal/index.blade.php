@extends('layouts.app')

@push('styles')
	<link rel="stylesheet" href="{{ asset('assets/css/legal-page.css') }}">
@endpush

@section('content')

	<section class="legal-header">
		<div class="container">
			<div class="legal-breadcrumb">
				<a href="{{ route('home') }}">Home</a>
				<span>/</span>
				<span>Dokumen Legal</span>
			</div>
			<h1>Dokumen Legal</h1>
		</div>
	</section>

	<section class="legal-body">
		<div class="container">
			@if($legalPages->isEmpty())
				<p class="legal-index-empty">Belum ada dokumen legal yang dipublikasikan.</p>
			@else
				<ul class="legal-index-list">
					@foreach($legalPages as $legalPage)
						<li>
							<a href="{{ route('legal.show', $legalPage) }}">
								{{ $legalPage->title }}
								<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M13 5l7 7-7 7"/></svg>
							</a>
						</li>
					@endforeach
				</ul>
			@endif
		</div>
	</section>

@endsection
