@extends('layouts.app')

@section('content')
	@include('partials.services-hero')

	@php
		$colors = ['california', 'emerald', 'royal-blue', 'dodger-blue', 'sunset-orange', 'turquoise'];
	@endphp

	{{-- ===== Service grid ===== --}}
	<section class="service-wrap-layout6 section-padding-md bg-color-light">
		<div class="container">
			<div class="section-heading heading-dark heading-layout1">
				<h2 class="heading-main-title">{{ $page?->field('heading', 'Layanan Kami') }}</h2>
				<p class="heading-paragraph">{{ $page?->field('heading_text') }}</p>
			</div>
			<div class="row">
				@forelse($services as $service)
					<div class="col-lg-4 col-sm-6 col-12 has-animation">
						<div class="translate-bottom-75 opacity-animation transition-150 transition-delay-100">
							<div class="service-box-layout3">
								<div class="item-icon {{ $colors[$loop->index % count($colors)] }}">
									<i class="{{ $service->icon ?: 'flaticon-shout' }}"></i>
								</div>
								<div class="item-content">
									<h3 class="item-title"><a href="{{ route('services.show', $service->slug) }}">{{ $service->title }}</a></h3>
									<p>{{ $service->short_description }}</p>
									@if($service->cta_visible)
										<a href="{{ $service->cta_url ?: route('services.show', $service->slug) }}" class="btn-fill btn-gradient">{{ $service->cta_text ?: 'Selengkapnya' }}<i class="flaticon-next"></i></a>
									@endif
								</div>
							</div>
						</div>
					</div>
				@empty
					<div class="col-12 text-center">
						<p>Belum ada layanan yang dipublikasikan.</p>
					</div>
				@endforelse
			</div>
		</div>
	</section>

	{{-- ===== Stats ===== --}}
	<section class="progress-wrap-layout1 bg-gradient-layout2">
		<div class="progress-inner-wrap bg-position-center bg-no-repeat bg-size-cover parallaxie" data-bg-image="{{ asset('media/element/element1.png') }}">
			<div class="container zindex-level-2">
				<div class="row">
					@for($i = 1; $i <= 4; $i++)
						@php
							$rawStat = (string) $page?->field("stat_{$i}_number", 0);
							$isPercentStat = str_ends_with($rawStat, '%');
							$numericStat = $isPercentStat ? rtrim($rawStat, '%') : $rawStat;
						@endphp
						<div class="col-3 services-stat-col">
							<div class="progress-box-layout1">
								<h2 class="counting-text counter{{ $isPercentStat ? ' is-percent' : '' }}" data-num="{{ $numericStat }}">{{ $numericStat }}</h2>
								<div class="item-label">{{ $page?->field("stat_{$i}_label") }}</div>
							</div>
						</div>
					@endfor
				</div>
			</div>
		</div>
	</section>

	{{-- ===== About blocks ===== --}}
	<section class="section-padding-md-equal about-wrap-layout6 overflow-hidden">
		<div class="container">
			<div class="row d-flex align-items-center mb--100">
				<div class="col-lg-6">
					<div class="about-box-layout6">
						<div class="figure-holder has-animation">
							<div class="animated-figure">
								<div class="translate-zoomout-50 opacity-animation transition-200 transition-delay-100">
									<img src="{{ $page?->about_1_image ? asset($page->about_1_image) : asset('media/illustration/illustration17.png') }}" alt="Tentang Kami">
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-6">
					<div class="about-box-layout6">
						<div class="content-holder has-animation">
							<div class="translate-bottom-75 opacity-animation transition-150 transition-delay-100">
								<h2 class="item-title">{{ $page?->field('about_1_title') }}</h2>
							</div>
							<div class="translate-bottom-75 opacity-animation transition-150 transition-delay-300">
								<p>{{ $page?->field('about_1_text') }}</p>
							</div>
							<div class="translate-bottom-75 opacity-animation transition-150 transition-delay-400">
								<a href="{{ $page?->field('about_1_button_url', route('about')) }}" class="btn-fill btn-gradient">{{ $page?->field('about_1_button_text', 'Selengkapnya') }}<i class="flaticon-next"></i></a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="row d-flex align-items-center">
				<div class="col-lg-6 order-lg-2">
					<div class="about-box-layout6">
						<div class="figure-holder has-animation">
							<div class="animated-figure">
								<div class="translate-zoomout-50 opacity-animation transition-200 transition-delay-100">
									<img src="{{ $page?->about_2_image ? asset($page->about_2_image) : asset('media/illustration/illustration18.png') }}" alt="Tentang Kami">
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-6 order-lg-1">
					<div class="about-box-layout6">
						<div class="content-holder has-animation">
							<div class="translate-bottom-75 opacity-animation transition-150 transition-delay-100">
								<h2 class="item-title">{{ $page?->field('about_2_title') }}</h2>
							</div>
							<div class="translate-bottom-75 opacity-animation transition-150 transition-delay-300">
								<p>{{ $page?->field('about_2_text') }}</p>
							</div>
							<div class="translate-bottom-75 opacity-animation transition-150 transition-delay-400">
								<a href="{{ $page?->field('about_2_button_url', route('about')) }}" class="btn-fill btn-gradient">{{ $page?->field('about_2_button_text', 'Selengkapnya') }}<i class="flaticon-next"></i></a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	{{-- ===== Proses kerja ===== --}}
	<section class="services-process">
		<div class="container">
			<div class="services-process-head">
				<h2 class="services-process-heading">{{ $page?->field('process_heading', 'Kolaborasi yang Terstruktur & Transparan') }}</h2>
				<p class="services-process-text">{{ $page?->field('process_text', 'Kami menerapkan proses kerja yang jelas agar setiap proyek berjalan efisien dan tepat sasaran.') }}</p>
			</div>

			@php
				$processStepDefaults = [
					1 => ['icon' => 'chat', 'title' => 'Diskusi', 'text' => 'Memahami kebutuhan dan tujuan bisnis Anda.'],
					2 => ['icon' => 'document', 'title' => 'Perencanaan', 'text' => 'Menyusun strategi, scope pekerjaan, dan timeline.'],
					3 => ['icon' => 'code', 'title' => 'Pengembangan', 'text' => 'Proses development dengan standar kualitas tinggi.'],
					4 => ['icon' => 'check', 'title' => 'Testing', 'text' => 'Pengujian menyeluruh untuk memastikan sistem berjalan optimal.'],
					5 => ['icon' => 'rocket', 'title' => 'Deploy & Support', 'text' => 'Peluncuran sistem dan dukungan berkelanjutan.'],
				];
			@endphp
			<div class="services-process-steps">
				@for ($i = 1; $i <= 5; $i++)
					@php $iconKey = $page?->field("process_step_{$i}_icon", $processStepDefaults[$i]['icon']); @endphp
					<div class="services-process-step">
						<div class="services-process-icon">
							@switch($iconKey)
								@case('chat')
									<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5.5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2H9l-4 3.5v-3.5H6a2 2 0 0 1-2-2v-9Z"/><circle cx="9" cy="10" r=".55" fill="currentColor" stroke="none"/><circle cx="12" cy="10" r=".55" fill="currentColor" stroke="none"/><circle cx="15" cy="10" r=".55" fill="currentColor" stroke="none"/></svg>
									@break
								@case('document')
									<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3.5h8l4 4v13a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1v-16a1 1 0 0 1 1-1Z"/><path d="M14 3.5v4h4"/><path d="M8 12h8"/><path d="M8 15.5h8"/><path d="M8 19h5"/></svg>
									@break
								@case('code')
									<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6.5 3.5 12 9 17.5"/><path d="M15 6.5 20.5 12 15 17.5"/></svg>
									@break
								@case('check')
									<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="8.5"/><path d="M8 12.5l2.5 2.5L16.5 9"/></svg>
									@break
								@case('rocket')
									<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M14.3 3.5c2.4 0 4.2 1.9 4.2 4.2 0 3.8-2.9 7.1-5.3 9l-2-2c1.9-2.4 5.2-5.7 5.2-9 0-1.3-.9-2.2-2.1-2.2-3.3 0-6.6 3.3-9 5.2l-2-2c1.9-2.4 5.2-5.3 9-5.3Z"/><path d="M9 15l-3 1 1-3"/><path d="M6.3 12.6c-1.4 1-2.3 2.9-2.3 4.9 2 0 3.9-.9 4.9-2.3"/><circle cx="14.3" cy="9.5" r="1.1" fill="currentColor" stroke="none"/></svg>
									@break
								@default
									<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="8"/></svg>
							@endswitch
						</div>
						<div class="services-process-number">{{ sprintf('%02d', $i) }}</div>
						<h3 class="services-process-step-title">{{ $page?->field("process_step_{$i}_title", $processStepDefaults[$i]['title']) }}</h3>
						<p class="services-process-step-text">{{ $page?->field("process_step_{$i}_text", $processStepDefaults[$i]['text']) }}</p>
					</div>
				@endfor
			</div>
		</div>
	</section>

	{{-- ===== CTA ===== --}}
	<section class="services-cta-section">
		<div class="container">
			<div class="services-cta">
				<div class="services-cta-inner">
					<div class="services-cta-copy">
						<h2>{{ $page?->field('cta_heading', 'Siap Mengembangkan Bisnis Anda?') }}</h2>
						<p>{{ $page?->field('cta_text', 'Diskusikan ide Anda bersama kami dan dapatkan solusi terbaik untuk kebutuhan bisnis Anda.') }}</p>
					</div>
					<a href="{{ $page?->field('cta_button_url', route('contact')) }}" class="services-cta-btn">{{ $page?->field('cta_button_text', 'Konsultasi Gratis Sekarang') }}<i class="fas fa-arrow-right"></i></a>
				</div>
			</div>
		</div>
	</section>
@endsection
